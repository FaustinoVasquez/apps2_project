<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Fbmreportbyqtysold extends BP_Controller {

    public function __construct() {
	parent::__construct();

	$is_logged_in = $this->session->userdata('is_logged_in');

	if (!isset($is_logged_in) || $is_logged_in != true) {
	    redirect(base_url(), 'refresh');
	}

	// if ($this->MUsers->isValidUser($this->session->userdata('userid'), 883700) != 1) {// 881100 prodcat Access
	//     redirect('Catalog/prodcat', 'refresh');
	// }
    }

    function index() {

	$this->title = "MI Technologiesinc - FBM Report by Qty Sold";

	$this->description = "FBM Report by Qty Sold";

	$this->css = array('form.css', 'fluid.css', 'table.css', 'jqueryui/redmond/jquery-ui-1.8.21.custom.css', 'jqgrid/ui.jqgrid.css', 'menu.css',);

	// Define custom javascript
	$this->javascript = array('jqueryui/ui/jquery-ui-1.8.21.custom.js', 'jqgrid/i18n/grid.locale-en.js', 'jqgrid/jquery.jqGrid.min.js', 'jqgrid/jquery.jqGrid.fluid.js', 'popup.js');

	$this->load->library('Layout');
	$menu = new Layout;

	$data = array(
	    'menu' => $menu->show_menu($this->MUsers->getUserFullName($this->session->userdata('userid'))),
	    'from' => '/Reports/fbmreportbyqtysold/',
	    'nameGrid' => 'list',
	    'namePager' => 'pager',
	    'caption' => 'FBM Report by Qty Sold',
	    'export' => 'fbmreport',
	    'sortname' => 'OMOD.SKU',
	    'sort' => 'desc',
	    'search' =>'',
	    'categoriesOptions' => $this->MCatalog->fillCategories(),
	    'categories' => '0',
	    'datefrom' => $this->MCommon->lastweek(),
            'dateto' => date("m/d/Y"),
	);

	$data['colNames'] = "['myuniqueid','SKU','Name','Qty','ASIN','Category','AvgSalePrice','SalesPerDay']";

	$data['colModel'] = "[
                {name:'myuniqueid',index:'myuniqueid', width:50, align:'left',hidden:true},
                {name:'SKU',index:'SKU', width:60, align:'center'},
                {name:'Name',index:'Name', width:300, align:'left'},
                {name:'Qty',index:'Qty', width:70, align:'center'},
                {name:'ASIN',index:'ASIN', width:80, align:'center'},
                {name:'Category',index:'Category', width:130, align:'left'},
                {name:'AvgSalePrice',index:'AvgSalePrice', width:70, align:'right',formatter:'currency', formatoptions:{decimalSeparator:'.', thousandsSeparator: ',', decimalPlaces: 2, prefix: '$ '}},
                {name:'SalesPerDay',index:'SalesPerDay', width:70,align:'center'},
              	]";

	//Cargamos la libreria comumn
	$this->load->library('common');

	//Reemplazamos los campos del formulario por los que estan en el arreglo $_POST.
	$data = $this->common->fillPost('ALL', $data);

	//cadena de busqueda del grid 
	$data['getData'] = 'getData?ds=' . $data['search'] .'&df='.$data['datefrom'].'&dt='.$data['dateto'].'&ct='.$data['categories'];


	$this->build_content($data);
	$this->render_page();
    }

    function getData() {

	$page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1; // get the requested page
	$limit = isset($_REQUEST['rows']) ? $_REQUEST['rows'] : 20; // get how many rows we want to have into the gri
	$sidx = isset($_REQUEST['sidx']) ? $_REQUEST['sidx'] : 'OMOD.SKU'; // get index row - i.e. user click to sort
	$sord = isset($_REQUEST['sord']) ? $_REQUEST['sord'] : 'DESC';
	
	//Dataos pasados al metodo desde el view
	$search = !empty($_GET['ds']) ? $_REQUEST['ds'] : '';
	$categories = !empty($_GET['ct']) ? $_REQUEST['ct'] : 0;
	$InitialDate = isset($_REQUEST['df']) ? $_GET['df'] : $this->MCommon->lastweek();
        $FinalDate = isset($_REQUEST['dt']) ? $_GET['dt'] : date("m/d/Y");
	
	//Corregimos las fechas para ajustar el tiempo
	$InitialDateFixed = $this->MCommon->fixInitialDate($InitialDate, 1);
	$FinalDateFixed = $this->MCommon->fixFinalDate($FinalDate, 1);
       
	
	//Descoponemos el query para ajustarlo a cada cambio que venga desde el view
	
	
	
	$select = " OMOD.SKU,
		    INPC.Name,
		    SUM(OMOD.QuantityOrdered) AS Qty, 
		    RIGHT(OMOD.WebSKU, 10) AS ASIN,
		    Inventory.dbo.Categories.Name AS Category,
		    SUM(OMOD.PricePerUnit * QuantityOrdered) / SUM(OMOD.QuantityOrdered) AS AvgSalePrice,
		    Cast((CAST(SUM(OMOD.QuantityOrdered) as Decimal(10,2)) / DATEDIFF(DAY, '{$InitialDateFixed}', '{$FinalDateFixed}')) as Decimal(10,2)) as SalesPerDay";

		    
	$table = " FROM Inventory.dbo.ProductCatalog AS INPC LEFT OUTER JOIN
                        Inventory.dbo.Categories ON INPC.CategoryID = Inventory.dbo.Categories.ID 
			RIGHT OUTER JOIN OrderManager.dbo.[Order Details] AS OMOD ON CAST(LEFT(OMOD.SKU, 6) AS nvarchar) = CAST(LEFT(INPC.ID, 6) AS nvarchar)
                        LEFT OUTER JOIN OrderManager.dbo.[Orders] AS O ON (OMOD.OrderNumber = O.OrderNumber)";

	$where = '';

	$wherefields = array('OMOD.SKU', 'INPC.Name','Inventory.dbo.Categories.Name','OMOD.WebSKU' );

	$where .=$this->MCommon->concatAllWerefields($wherefields, $search);

	if ($categories != 0){
	    $where .= " AND (Inventory.dbo.Categories.ID = {$categories})";
	}
		     
	$where .= "  AND (OMOD.DetailDate > CONVERT(DATETIME, '{$InitialDateFixed}', 102)) 
		     AND (OMOD.DetailDate < CONVERT(DATETIME, '{$FinalDateFixed}', 102)) 
		     AND O.OrderSource = 'AS' AND OMOD.Adjustment = '0'";

	$group = " GROUP BY OMOD.SKU, INPC.Name, RIGHT(OMOD.WebSKU, 10), Inventory.dbo.Categories.Name ";

	$orderby = " ORDER BY Qty DESC";


	$SQL = " SELECT COUNT(*) AS COUNT FROM ( SELECT {$select}{$table}{$where}{$group} ) gridalias";


	$result = $this->MCommon->getOneRecord($SQL);
	


	$count = $result['COUNT'];


	if ($count > 0) {
	    $total_pages = ceil($count / $limit);
	} else {
	    $total_pages = 0;
	}
	if ($page > $total_pages)
	    $page = $total_pages;

	$start = $limit * $page - $limit; // do not put $limit*($page - 1)
	$start = ($start <= 0) ? 1 : $start;
	$finish = $start + $limit ;

	$SQL = "SELECT  top {$limit} * from (
			SELECT
                        ROW_NUMBER() over (ORDER BY OMOD.SKU) AS myuniqueid,
                        {$select}
			{$table}
			{$where}
			{$group}
		) temp  where myuniqueid >= {$start}{$orderby}";

		
	$result = $this->MCommon->getSomeRecords($SQL);


	$responce = new stdClass();
	$responce->page = $page;
	$responce->total = $total_pages;
	$responce->records = $count;

	$i = 0;
	foreach ($result as $row) {
	    $responce->rows[$i]['ID'] = $row['myuniqueid'];
	    $responce->rows[$i]['cell'] = array($row['myuniqueid'],
		$row['SKU'] = utf8_encode($row['SKU']),
		$row['Name'] = utf8_encode($row['Name']),
		$row['Qty'] = utf8_encode($row['Qty']),
		$row['ASIN'] = utf8_encode($row['ASIN']),
		$row['Category'] = utf8_encode($row['Category']),
		$row['AvgSalePrice'] = utf8_encode($row['AvgSalePrice']),
		$row['SalesPerDay'] = utf8_encode($row['SalesPerDay']),
	    );
	    $i++;
	}

	echo json_encode($responce);
    }

    /*
     * CSV Export
     */

    function csvExport($name) {

	header('Content-type: application/vnd.ms-excel');
	header("Content-Disposition: attachment; filename=" . $name . '_' . date("D-M-j") . ".xls");
	header("Pragma: no-cache");

	$buffer = $_POST['csvBuffer'];

	try {
	    echo $buffer;
	} catch (Exception $e) {
	    
	}
    }

}


?>
