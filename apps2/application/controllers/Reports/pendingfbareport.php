<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Pendingfbareport extends BP_Controller {

    public function __construct() {
	parent::__construct();

	$is_logged_in = $this->session->userdata('is_logged_in');

	if (!isset($is_logged_in) || $is_logged_in != true) {
	    redirect(base_url(), 'refresh');
	}

	// if ($this->MUsers->isValidUser($this->session->userdata('userid'), 883800) != 1) {// Access Code
	//     redirect('Catalog/prodcat', 'refresh');
	// }
    }

    function index() {

	$this->title = "MI Technologiesinc - Pending FBA Report";

	$this->description = "Pending FBA Report";

	$this->css = array('form.css', 'fluid.css', 'table.css', 'jqueryui/redmond/jquery-ui-1.8.21.custom.css', 'jqgrid/ui.jqgrid.css', 'menu.css',);

	// Define custom javascript
	$this->javascript = array('jqueryui/ui/jquery-ui-1.8.21.custom.js', 'jqgrid/i18n/grid.locale-en.js', 'jqgrid/jquery.jqGrid.min.js', 'jqgrid/jquery.jqGrid.fluid.js', 'popup.js');


	// If the page has menu
	$this->load->library('Layout');
	$menu = new Layout;

	// If not have menu




	$data = array(
	    'menu' => $menu->show_menu($this->MUsers->getUserFullName($this->session->userdata('userid'))),
	    'from' => '/Reports/pendingfbareport/',
	    'nameGrid' => 'pendingfbareport',
	    'namePager' => 'pendingfbareportPager',
	    'caption' => 'Pending FBA Report',
	    'export' => 'exportexcel',
	    'subgrid' => 'false', // true or false depends
	    'sort' => 'desc',
	    'search' => '',
	    'customerid' => $this->MUsers->getCustomerId($this->session->userdata('userid')),
	   
	);


	$data['colNames'] = "['ID','Name','Category','CurrentStock','Pending Qty','RealQTYRemaining']";

	$data['colModel'] = "[
		{name:'ID',index:'ID', width:50, align:'left',align:'center'},
		{name:'Name',index:'Name', width:250, align:'left',align:'left'},
                {name:'Category',index:'Category', width:90, align:'left'},
                {name:'CurrentStock',index:'CurrentStock', width:55, align:'center'},
                {name:'PendingQty',index:'PartNumberV2', width:55, align:'center'},
                {name:'RealQTYRemaining',index:'RealQTYRemaining', width:55, align:'center'},
                ]";

	//Cargamos la libreria comumn
	$this->load->library('common');

	//Reemplazamos los campos del formulario por los que estan en el arreglo $_POST.
	$data = $this->common->fillPost('ALL', $data);

	//cadena de busqueda del grid 
	$data['gridSearch'] = 'Data?ds=' . $data['search'];


	$this->build_content($data);
	$this->render_page();
    }

    function Data() {

	$page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1; // get the requested page
	$limit = isset($_REQUEST['rows']) ? $_REQUEST['rows'] : 10; // get how many rows we want to have into the gri
	$sidx = isset($_REQUEST['sidx']) ? $_REQUEST['sidx'] : 'Brand'; // get index row - i.e. user click to sort
	$sord = isset($_REQUEST['sord']) ? $_REQUEST['sord'] : 'asc';
	$search = !empty($_GET['ds']) ? $_REQUEST['ds'] : '';
	

	$table = 'Inventory.dbo.ProductCatalog PC ';

	$where = '';
	$from = ' FROM ';
        $left =' LEFT OUTER JOIN Inventory.dbo.Categories as Cat on (PC.CategoryID = Cat.ID)
		 LEFT OUTER JOIN Inventory.dbo.Global_Stocks as GS on (PC.ID = GS.ProductCatalogID)';
	$orderby =' ORDER BY PC.ID ASC';

	
	$select = "Select
		 PC.ID as ID 
		,PC.Name as Name
		,Cat.Name as Category
		,Cast(GS.TotalStock as int) as CurrentStock
		,dbo.[fn_getFBA_Pending_Orders_BareLamps](PC.ID) AS PendingQty
		,(Cast(GS.TotalStock as int) - Cast(dbo.[fn_getFBA_Pending_Orders_BareLamps](PC.ID) as int)) as RealQTYRemaining
	    ";


	$wherefields = array('PC.ID', 'PC.Name', 'Cat.Name');

	$where .=$this->MCommon->concatAllWerefields($wherefields, $search);
	
	$where.= " and (pc.CategoryID IN ('5' , '9'))";
	

	
	$SQL = "{$select}{$from}{$table}{$left}{$where}{$orderby}";


	$result = $this->MCommon->getSomeRecords($SQL);

	
	$count = count($result);


	if ($count > 0) {
	    $total_pages = ceil($count / $limit);
	} else {
	    $total_pages = 0;
	}
	if ($page > $total_pages)
	    $page = $total_pages;

	$start = $limit * $page - $limit; // do not put $limit*($page - 1)
	$start = ($start < 0) ? 0 : $start;
	$finish = $start + $limit;


	$SQL =" WITH mytable AS (
                Select *, ROW_NUMBER() OVER (ORDER BY {$sidx} {$sord}) AS RowNumber
                FROM {$table}{$where}
		)
		Select * from mytable WHERE RowNumber BETWEEN {$start} AND {$finish}";
	


	//$result = $this->MCommon->getSomeRecords($SQL);


	$responce = new stdClass();
	$responce->page = $page;
	$responce->total = $total_pages;
	$responce->records = $count;

		
	$i = 0;
	foreach ($result as $row) {
	    $responce->rows[$i]['id'] = $row['ID'];
	    $responce->rows[$i]['cell'] = array($row['ID'],
		$row['Name'] = utf8_encode($row['Name']),
		$row['Category'] = utf8_encode($row['Category']),
		$row['CurrentStock'] = utf8_encode($row['CurrentStock']),
		$row['PendingQty'] = utf8_encode($row['PendingQty']),
		$row['RealQTYRemaining'] = utf8_encode($row['RealQTYRemaining']),
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
    
    function saveData(){
	
	if ($_POST['oper']== 'edit'){
	    
	    $SQL = "update MITDB.dbo.ProjectorData 
		    set 
			Brand ='{$_POST['Brand']}',
			PartNumber='{$_POST['PartNumber']}', 
			PartNumberV2='{$_POST['PartNumberV2']}', 
			PartNumberV3='{$_POST['PartNumberV3']}',
			DM_Youtube='{$_POST['DM_Youtube']}',
			DM_Vimeo='{$_POST['DM_Vimeo']}',
			DM_Facebook='{$_POST['DM_Facebook']}',
			DTVL_Youtube='{$_POST['DTVL_Youtube']}',
			DTVL_Vimeo='{$_POST['DTVL_Vimeo']}',
			DTVL_Facebook='{$_POST['DTVL_Facebook']}',
			Type='{$_POST['Type']}' 
		    where
		    ID = {$_POST['id']}";
	}

	
	$this->MCommon->executeQuery($SQL);
	    
    }

}
?>