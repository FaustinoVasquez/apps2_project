<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Myconsole extends BP_Controller {

    public function __construct() {
	parent::__construct();

	$is_logged_in = $this->session->userdata('is_logged_in');

	if (!isset($is_logged_in) || $is_logged_in != true) {
	    redirect(base_url(), 'refresh');
	}

	if ($this->MUsers->isValidUser($this->session->userdata('userid'), 884500) != 1) {// Access Code
	    redirect('Catalog/prodcat', 'refresh');
	}
    }

    function index() {

	$this->title = "MI Technologiesinc - Console";

	$this->description = "Console";

	$this->css = array('form.css', 'fluid.css', 'table.css', 'jqueryui/redmond/jquery-ui-1.8.21.custom.css', 'jqgrid/ui.jqgrid.css', 'menu.css',);

	// Define custom javascript
	$this->javascript = array('jqueryui/ui/jquery-ui-1.8.21.custom.js', 'jqgrid/i18n/grid.locale-en.js', 'jqgrid/jquery.jqGrid.min.js', 'jqgrid/jquery.jqGrid.fluid.js', 'popup.js');

	$this->load->library('Layout');
	//Creamos un nuevo layout->menu
	$menu = new Layout;


	$data = array(
	    'menu' => $menu->show_menu($this->MUsers->getUserFullName($this->session->userdata('userid'))),
	    'getData' => '/Console/myconsole/getData/',
	    'nameGrid' => 'consolegrid',
	    'namePager' => 'consolepage',
	    'caption' => 'Query Result',
	);


	$this->build_content($data);
	$this->render_page();
    }

    function createGrid() {

	$SQL = str_ireplace("insert", "",$_POST['search']);
	$SQL = str_ireplace("delete", "",$_POST['search']);
	$SQL = str_ireplace("update", "",$_POST['search']);
	$SQL = str_ireplace("drop", "",$_POST['search']);
	
	$SQL = preg_replace("/select/i", "select top(1)",$_POST['search'],1 );

	
	$result = $this->MCommon->getSomeRecords($SQL);

	$colnames = "";
	$colmodel = "";
	foreach ($result as $value) {
	    foreach ($value as $key => $value1) {
		$expcolnames[] = $key;
		$colnames .= "'" . $key . "',";
		$colmodel .= "{name:'{$key}',index:'{$key}', align:'center'},";
	    }
	}
	
	$id = reset($expcolnames);



	$data = array(
	    'grid' => "
		    <script type='text/javascript'>

		    $(document).ready(function(){		    
		    var dataString = $('#consoleform').serialize();
	    
		    jQuery('#list').jqGrid({   
		    url: '". base_url()."index.php/Console/myconsole/getData?id={$id}',
		    datatype: 'json', 
		    mtype: 'post',
		    postData: dataString,
		    colNames: [" . substr($colnames, 0, -1) . "],
		    colModel: [" . substr($colmodel, 0, -1) . "],
		    autowidth: true,
		    height:600,
		    rowNum:50, 
		    rowList:[50,300,1000],
		    pager: '#pager', 
		    caption: 'Result of Query',
		    sortorder: 'asc', 
		    rownumbers: true,
		    loadonce: true,
		});
		jQuery('#list').jqGrid('navGrid','#pager',{edit:false,add:false,del:false}) 		
	    });
	    </script>"
	);

	echo json_encode($data);
    }

    function getData() {
	$page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1; // get the requested page
	$limit = isset($_REQUEST['rows']) ? $_REQUEST['rows'] : 10; // get how many rows we want to have into the gri
	$sidx = isset($_REQUEST['sidx']) ? $_REQUEST['sidx'] : 'ID'; // get index row - i.e. user click to sort
	$sord = isset($_REQUEST['sord']) ? $_REQUEST['sord'] : 'asc';
	$search = !empty($_POST['search']) ? $_REQUEST['search'] : '';
	$id = !empty($_GET['id']) ? $_GET['id'] : '';

	
	$result = $this->MCommon->getSomeRecords($search);
	
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
	$otroo= $page * $limit;
	
	$search = preg_replace("/select/i", "",$_POST['search'],1 );
	

	$responce = new stdClass();
	$responce->page = $page;
	$responce->total = $total_pages;
	$responce->records = $count;
	


	$i = 0;
	foreach ($result as $row) {
	    $responce->rows[$i]['id'] = $row[$id];
	    foreach ($row as $key => $value) {
		$responce->rows[$i]['cell'][] = $row[$key];
	    }
	    $i++;
	}


	echo json_encode($responce);
    }

}


