<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Youtubevideos extends BP_Controller {

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

	$this->title = "MI Technologiesinc - Youtuve Videos";

	$this->description = "Youtuve Videos";

	$this->css = array('form.css', 'fluid.css', 'table.css', 'jqueryui/redmond/jquery-ui-1.8.21.custom.css', 'jqgrid/ui.jqgrid.css', 'menu.css',);

	// Define custom javascript
	$this->javascript = array('jqueryui/ui/jquery-ui-1.8.21.custom.js', 'jqgrid/i18n/grid.locale-en.js', 'jqgrid/jquery.jqGrid.min.js', 'jqgrid/jquery.jqGrid.fluid.js', 'popup.js');


	// If the page has menu
	$this->load->library('Layout');
	$menu = new Layout;

	// If not have menu




	$data = array(
	    'menu' => $menu->show_menu($this->MUsers->getUserFullName($this->session->userdata('userid'))),
	    'from' => '/tools/youtubevideos/',
	    'nameGrid' => 'youtubevideos',
	    'namePager' => 'youtubevideosPager',
	    'caption' => 'Youtuve Videos',
	    'export' => 'exportexcel',
	    'subgrid' => 'false', // true or false depends
	    'sort' => 'desc',
	    'search' => '',
	    'customerid' => $this->MUsers->getCustomerId($this->session->userdata('userid')),
	);


	$data['colNames'] = "['ID','Brand','PartNumber','PartNumberV2','PartNumberV3','DM_Youtube','DM_Vimeo','DM_Facebook','DTVL_Youtube','DTVL_Vimeo','DTVL_Facebook','FixYourDLP','Type']";

	$data['colModel'] = "[
		{name:'ID',index:'ID', width:50, align:'left',align:'left', hidden:true},
                {name:'Brand',index:'Brand', width:45, align:'left',editable:true,editoptions:{readonly: 'readonly' }},
                {name:'PartNumber',index:'PartNumber', width:55, align:'left',editable:true,editoptions:{readonly: 'readonly' }},
                {name:'PartNumberV2',index:'PartNumberV2', width:55, align:'left',editable:true,editoptions:{readonly: 'readonly' }},
                {name:'PartNumberV3',index:'PartNumberV3', width:55, align:'left',editable:true,editoptions:{readonly: 'readonly' }},
                {name:'DM_Youtube',index:'DM_Youtube', width:80, align:'center',editable:true,editoptions:{size:50, maxlength: 250}},		
				{name:'DM_Vimeo',index:'DM_Vimeo', width:80, align:'center',editable:true,editoptions:{size:50, maxlength: 250}},
                {name:'DM_Facebook',index:'DM_Facebook', width:80, align:'center',editable:true,editoptions:{size:50, maxlength: 250}},
				{name:'DTVL_Youtube',index:'DTVL_Youtube', width:80, align:'center',editable:true,editoptions:{size:50, maxlength: 250}},
                {name:'DTVL_Vimeo',index:'DTVL_Vimeo', width:80, align:'center',editable:true,editoptions:{size:50, maxlength: 250}},
				{name:'DTVL_Facebook',index:'DTVL_Facebook', width:80, align:'center',editable:true,editoptions:{size:50, maxlength: 250}},
				{name:'FixYourDLP',index:'FixYourDLP', width:80, align:'center',editable:true,editoptions:{size:50, maxlength: 250}},
                {name:'Type',index:'Type', width:40, align:'center',editable:true,editoptions:{readonly: 'readonly' }}
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


	$table = 'MITDB.dbo.ProjectorData ';

	$where = '';
	$from = ' FROM ';


	$select = "SELECT
		    ID,
		    Brand, 
		    PartNumber, 
		    PartNumberV2, 
		    PartNumberV3, 
		    DM_Youtube, 
		    DM_Vimeo, 
		    DM_Facebook, 
		    DTVL_Youtube, 
		    DTVL_Vimeo, 
		    DTVL_Facebook,
		    FixYourDLP,
		    Type";


	$wherefields = array('Brand', 'PartNumber', 'PartNumberV2','PartNumberV3','DM_Youtube','DM_Vimeo','DM_Facebook','DTVL_Youtube','DTVL_Vimeo','DTVL_Facebook','FixYourDLP','Type');

	$where .=$this->MCommon->concatAllWerefields($wherefields, $search);


	$SQL = "{$select}{$from}{$table}{$where}";


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
	


	$result = $this->MCommon->getSomeRecords($SQL);


	$responce = new stdClass();
	$responce->page = $page;
	$responce->total = $total_pages;
	$responce->records = $count;

		
	$i = 0;
	foreach ($result as $row) {
	    $responce->rows[$i]['id'] = $row['ID'];
	    $responce->rows[$i]['cell'] = array($row['ID'],
		$row['Brand'] = utf8_encode($row['Brand']),
		$row['PartNumber'] = utf8_encode($row['PartNumber']),
		$row['PartNumberV2'] = utf8_encode($row['PartNumberV2']),
		$row['PartNumberV3'] = utf8_encode($row['PartNumberV3']),
		$row['DM_Youtube'] = utf8_encode($row['DM_Youtube']),
		$row['DM_Vimeo'] = utf8_encode($row['DM_Vimeo']),
		$row['DM_Facebook'] = utf8_encode($row['DM_Facebook']),
		$row['DTVL_Youtube'] = utf8_encode($row['DTVL_Youtube']),
		$row['DTVL_Vimeo'] = utf8_encode($row['DTVL_Vimeo']),
		$row['DTVL_Facebook'] = utf8_encode($row['DTVL_Facebook']),
		$row['Type'] = utf8_encode($row['Type']),

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