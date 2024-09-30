<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Catalogtosku extends BP_Controller {

    public function __construct() {
	parent::__construct();

	$is_logged_in = $this->session->userdata('is_logged_in');

	if (!isset($is_logged_in) || $is_logged_in != true) {
	    redirect(base_url(), 'refresh');
	}

	if ($this->MUsers->isValidUser($this->session->userdata('userid'), 884400) != 1) {// Access Code
	    redirect('Catalog/prodcat', 'refresh');
	}
    }

    function index() {

	$this->title = "MI Technologiesinc - Catalog to SKU";

	$this->description = "Catalog to SKU";

	$this->css = array('form.css', 'fluid.css', 'table.css', 'jqueryui/redmond/jquery-ui-1.8.21.custom.css', 'jqgrid/ui.jqgrid.css', 'menu.css',);

	// Define custom javascript
	$this->javascript = array('jqueryui/ui/jquery-ui-1.8.21.custom.js', 'jqgrid/i18n/grid.locale-en.js', 'jqgrid/jquery.jqGrid.min.js', 'jqgrid/jquery.jqGrid.fluid.js', 'popup.js');


	// If the page has menu
	$this->load->library('Layout');
	$menu = new Layout;

	// If not have menu

	$data = array(
	    'menu' => $menu->show_menu($this->MUsers->getUserFullName($this->session->userdata('userid'))),
	    'from' => '/tools/catalogtosku/',
	    'nameGrid' => 'catalogtosku',
	    'namePager' => 'catalogtoskuPager',
	    'caption' => 'Catalog to SKU',
	    'export' => 'exportexcel',
	    'sort' => 'desc',
	    'search' => '',
	);


	$data['colNames'] = "['EncSKU','EncSKUPH','BareSKU','BareSKUPH','BareSKUOS','CatalogID_G','CatalogID_O']";

	$data['colModel'] = "[
		{name:'EncSKU',index:'EncSKU', width:50, align:'center',align:'center',formatter: formatLink},
                {name:'EncSKUPH',index:'EncSKUPH', width:50, align:'center',formatter: formatLink},
                {name:'BareSKU',index:'BareSKU', width:50, align:'center',formatter: formatLink},
                {name:'BareSKUPH',index:'BareSKUPH', width:55, align:'center',formatter: formatLink},
                {name:'BareSKUOS',index:'PartNumberV3', width:50, align:'center',formatter: formatLink},
                {name:'CatalogID_G',index:'CatalogID_G', width:60, align:'center'},		
		{name:'CatalogID_O',index:'CatalogID_O', width:60, align:'center'}
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
	$sidx = isset($_REQUEST['sidx']) ? $_REQUEST['sidx'] : 'EncSKU'; // get index row - i.e. user click to sort
	$sord = isset($_REQUEST['sord']) ? $_REQUEST['sord'] : 'asc';
	$search = !empty($_GET['ds']) ? $_REQUEST['ds'] : '';


	$table = 'inventory.dbo.Catalog_Pager ';

	$where = 'WHERE ';
	$from = ' FROM ';


	$select = "SELECT
		    EncSKU
                   ,EncSKUPH
                   ,BareSKU
                   ,BareSKUPH
                   ,BareSKUOS
                   ,CatalogID_G
                   ,CatalogID_O";


	$wherefields = array('EncSKU', 'EncSKUPH', 'BareSKU','BareSKUPH','BareSKUOS','CatalogID_G','CatalogID_O');

	//$where .=$this->MCommon->concatAllWerefields($wherefields, $search);
   
        $where .= " (CatalogID_O like '{$search}%') OR (CatalogID_G like '{$search}%')";

	$SQL = "{$select}{$from}{$table}{$where}";
   
	$result = $this->MCommon->getOneRecordFromServer($SQL,'InventorySave');

        
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

	//$result = $this->MCommon->getSomeRecordsFromServer($SQL,'InventorySave');


	$responce = new stdClass();
	$responce->page = $page;
	$responce->total = $total_pages;
	$responce->records = $count;

	$i = 0;
	foreach ($result as $row) {
	    $responce->rows[$i]['id'] = $row['EncSKU'];
	    $responce->rows[$i]['cell'] = array($row['EncSKU'],
		$row['EncSKUPH'] = utf8_encode($row['EncSKUPH']),
		$row['BareSKU'] = utf8_encode($row['BareSKU']),
		$row['BareSKUPH'] = utf8_encode($row['BareSKUPH']),
		$row['BareSKUOS'] = utf8_encode($row['BareSKUOS']),
		$row['CatalogID_G'] = utf8_encode($row['CatalogID_G']),
		$row['CatalogID_O'] = utf8_encode($row['CatalogID_O']),
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

	 $this->MCommon->saveRecord($SQL,'Mitdb');
	    
    }

}
?>