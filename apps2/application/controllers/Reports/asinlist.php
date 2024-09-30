<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Asinlist extends BP_Controller {

    public function __construct() {
	parent::__construct();

	$is_logged_in = $this->session->userdata('is_logged_in');

	if (!isset($is_logged_in) || $is_logged_in != true) {
	    redirect(base_url(), 'refresh');
	}

	// if ($this->MUsers->isValidUser($this->session->userdata('userid'), 883600) != 1) {// Access Code
	//     redirect('Catalog/prodcat', 'refresh');
	// }
    }

    function index() {

	$this->title = "MI Technologiesinc - AsinList";

	$this->description = "Asin List";

	$this->css = array('form.css', 'fluid.css', 'table.css', 'jqueryui/redmond/jquery-ui-1.8.21.custom.css', 'jqgrid/ui.jqgrid.css', 'menu.css');

	// Define custom javascript
	$this->javascript = array('jqueryui/ui/jquery-ui-1.8.21.custom.js', 'jqgrid/i18n/grid.locale-en.js', 'jqgrid/jquery.jqGrid.min.js', 'jqgrid/jquery.jqGrid.fluid.js', 'popup.js');


	// If the page has menu
	$this->load->library('Layout');
	$menu = new Layout;

	// If not have menu




	$data = array(
	    'menu' => $menu->show_menu($this->MUsers->getUserFullName($this->session->userdata('userid'))),
	    'from' => '/Reports/asinlist/',
	    'nameGrid' => 'asinlist',
	    'namePager' => 'asinlistPager',
	    'caption' => 'Asin List',
	    'export' => 'exportexcel',
	    'subgrid' => 'false', // true or false depends
	    'sort' => 'desc',
	    'search' => '',
	    'channelsOptions'=> $this->MCatalog->fillChannelName(),
	    'channel' => '',
	    'countryCodeOptions'=> $this->MCatalog->fillCountryCode(),
	    'countryCode' => '',
	    'categoryOptions' => $this->MCatalog->fillCategory(),
            'category' => '',
	    'datefrom' => $this->MCommon->lastweek(),
            'dateto' => date("m/d/Y"),
	);


	$data['colNames'] = "['ASIN','Manufacturer','ManufacturerPN','DartFBMSKU','DartFBASKU','Name','MappedToSKU','ChannelName','CountryCode','Category','Stamp']";

	$data['colModel'] = "[
                {name:'ASIN',index:'ASIN', width:60, align:'center'},
                {name:'Manufacturer',index:'Manufacturer', width:80, align:'left'},
                {name:'ManufacturerPN',index:'ManufacturerPN', width:80, align:'left'},
		        {name:'DartFBMSKU',index:'DartFBMSKU', width:80, align:'left'},
                {name:'DartFBASKU',index:'DartFBASKU', width:80, align:'left'},
                {name:'Name',index:'Name', width:250, align:'left',editable:true},
                {name:'MappedToSKU',index:'MappedToSKU', width:70, align:'center'},		
		        {name:'ChannelName',index:'ChannelName', width:70, align:'center'},
                {name:'CountryCode',index:'CountryCode', width:50, align:'center'},
                {name:'Category',index:'Category', width:50, align:'center'},
		        {name:'stamp',index:'stamp',width:90, align:'center'}
               ]";

	//Cargamos la libreria comumn
	$this->load->library('common');

	//Reemplazamos los campos del formulario por los que estan en el arreglo $_POST.
	$data = $this->common->fillPost('ALL', $data);

	//cadena de busqueda del grid 
	$data['gridSearch'] = 'Data?ds=' . $data['search'].'&ch='.$data['channel'].'&cc='.$data['countryCode'].'&cat='.$data['category']. '&df=' . $data['datefrom'] . '&dt=' . $data['dateto'];


	$this->build_content($data);
	$this->render_page();
    }

    function Data() {

	$page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1; // get the requested page
	$limit = isset($_REQUEST['rows']) ? $_REQUEST['rows'] : 10; // get how many rows we want to have into the gri
	$sidx = isset($_REQUEST['sidx']) ? $_REQUEST['sidx'] : 'a.ASIN'; // get index row - i.e. user click to sort
	$sord = isset($_REQUEST['sord']) ? $_REQUEST['sord'] : 'asc';
	$search = !empty($_GET['ds']) ? $_REQUEST['ds'] : '';
	
	$channel = !empty($_GET['ch']) ? $_REQUEST['ch'] : '';
	$countryCode=!empty($_GET['cc']) ? $_REQUEST['cc'] : '';
	$category=!empty($_GET['cat']) ? $_REQUEST['cat'] : '';
	$InitialDate = isset($_REQUEST['df']) ? $_GET['df'] : $this->MCommon->lastweek();
        $FinalDate = isset($_REQUEST['dt']) ? $_GET['dt'] : date("m/d/Y");
	
	$InitialDateFixed = $this->MCommon->fixInitialDate($InitialDate, 1);
	$FinalDateFixed = $this->MCommon->fixFinalDate($FinalDate, 1);
	
	$table = '[Inventory].[dbo].Amazon AS AZ LEFT OUTER JOIN [Inventory].[dbo].ProductCatalog AS PC ON AZ.ProductCatalogId = PC.ID ';

	$where = '';
	$from = ' FROM ';

	$selectcount = "SELECT count(AZ.ASIN) as records";

	$selectslice = "SELECT
			AZ.ASIN, 
			AZ.manufacturer AS Manufacturer, 
			AZ.manufacturerpn AS ManufacturerPN, 
			PC.Name, 
			AZ.ProductCatalogId AS MappedToSKU,
            AZ.ChannelName, 
			AZ.CountryCode,
			AZ.Category,
			AZ.stamp";



	$wherefields = array('a.ASIN', 'a.Manufacturer', 'a.ManufacturerPN','a.Name','a.MappedToSKU','a.ChannelName','a.CountryCode','b.DartFBMSKU','b.DartFBASKU');

	$where .=$this->MCommon->concatAllWerefields($wherefields, $search);
	
	if ($channel !=''){
	    $where .= " and (a.ChannelName='{$channel}')";
	}
	if ($countryCode !=''){
	    $where .= " and (a.CountryCode ='{$countryCode}')";
	}
	if ($category !=''){
	    $where .= " and (a. Category ='{$category}')";
	}
	
//	$where .= " and (ChannelName='{$channel}') and (CountryCode ='{$countryCode}') and (Category ='{$category}') ";

	
	$where.= "  and (a.stamp between '{$InitialDateFixed}' and '{$FinalDateFixed}')";
	
	
	$orderby = " ORDER BY a.Manufacturer, a.ManufacturerPN DESC, a.MappedToSKU";

	//$SQL = "{$selectcount}{$from}{$table}{$where}";

	$SQL =" WITH mytable AS (
                {$selectslice}, ROW_NUMBER() OVER (ORDER BY {$sidx}) AS RowNumber
                FROM {$table}
		)
		Select a.*,b.DartFBMSKU,b.DartFBASKU From mytable a,Inventory.dbo.AmazonMerchantSKU b {$where} and (a.ASIN = b.ASIN)";

   // echo $SQL;    
        
	
	$result = $this->MCommon->getSomeRecords($SQL);

	//print_r($result);
	
	
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
                {$selectslice}, ROW_NUMBER() OVER (ORDER BY {$sidx}) AS RowNumber
                FROM {$table}
		)
		Select a.*,b.DartFBMSKU,b.DartFBASKU From mytable a,Inventory.dbo.AmazonMerchantSKU b {$where}  and (a.ASIN = b.ASIN) {$orderby}";


	// $SQL =" WITH mytable AS (
 //                {$selectslice}, ROW_NUMBER() OVER (ORDER BY {$sidx}) AS RowNumber
 //                FROM {$table}{$where}
	// 	)
	// 	Select a.*,b.DartFBMSKU,b.DartFBASKU From mytable a,Inventory.dbo.AmazonMerchantSKU b  WHERE (RowNumber BETWEEN {$start} AND {$finish}) and (a.ASIN = b.ASIN) {$orderby}";
	//echo $SQL;


	$result = $this->MCommon->getSomeRecords($SQL);

	//print_r($result);
	$responce = new stdClass();
	$responce->page = $page;
	$responce->total = $total_pages;
	$responce->records = $count;

	
		
	$i = 0;
	foreach ($result as $row) {
	    $responce->rows[$i]['id'] = $row['ASIN'];
	    $responce->rows[$i]['cell'] = array($row['ASIN'],
		$row['Manufacturer'] = utf8_encode($row['Manufacturer']),
		$row['ManufacturerPN'] = utf8_encode($row['ManufacturerPN']),
		$row['DartFBMSKU'] = utf8_encode($row['DartFBMSKU']),
        $row['DartFBASKU'] = utf8_encode($row['DartFBASKU']),
		$row['Name'] = utf8_encode($row['Name']),
		$row['MappedToSKU'] = utf8_encode($row['MappedToSKU']),
		$row['ChannelName'] = utf8_encode($row['ChannelName']),
		$row['CountryCode'] = utf8_encode($row['CountryCode']),
                $row['Category'] = utf8_encode($row['Category']),
		$row['stamp'] 
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
