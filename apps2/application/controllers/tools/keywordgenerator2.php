<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Keywordgenerator2 extends BP_Controller {

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

		$this->title = "MI Technologiesinc - Keyword Generator";

		$this->description = "Keyword Generator";

		$this->css = array('form.css', 'fluid.css', 'table.css', 'jqueryui/redmond/jquery-ui-1.8.21.custom.css', 'jqgrid/ui.jqgrid.css', 'menu.css',);

		// Define custom javascript
		$this->javascript = array('jqueryui/ui/jquery-ui-1.8.21.custom.js', 'jqgrid/i18n/grid.locale-en.js', 'jqgrid/jquery.jqGrid.min.js', 'jqgrid/jquery.jqGrid.fluid.js', 'popup.js');


		// If the page has menu
		$this->load->library('Layout');
		$menu = new Layout;


		$data = array(
		    'menu' => $menu->show_menu($this->MUsers->getUserFullName($this->session->userdata('userid'))),
		    'from' => '/tools/keywordgenerator2/',
		    'nameGrid' => 'keywordgenerator2',
		    'namePager' => 'keywordgeneratorPager',
		    'caption' => 'keyword Generator',
		    'export' => 'exportexcel',
		    'subgrid' => 'false', // true or false depends
		    'sort' => 'desc',
		    'manufacturer' => $this->MCatalog->fillManufacturer2(),
	        //'selectData' => Array('PartNumberSpecific' => "PartNumberSpecific", 'ModelSpecific' => "ModelSpecific"),
		    'customerid' => $this->MUsers->getCustomerId($this->session->userdata('userid')),
		);


		$data['colNames'] = "['ID','PartNumbers','ModelNumbers']";

		$data['colModel'] = "[  
								{name:'ID',index:'ID', width:50, align:'center', hidden: true},
								{name:'PartNumbers',index:'PartNumbers', width:50, align:'center'},
								{name:'ModelNumbers',index:'ModelNumbers', width:50, align:'center'}
	               			]";


		$this->build_content($data);
		$this->render_page();
    }

    function Data() {

		$page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1; // get the requested page
	    $limit = isset($_REQUEST['rows']) ? $_REQUEST['rows'] : 10; // get how many rows we want to have into the gri
	    $sidx = isset($_REQUEST['sidx']) ? $_REQUEST['sidx'] : 'ID'; // get index row - i.e. user click to sort
	    $sord = isset($_REQUEST['sord']) ? $_REQUEST['sord'] : 'asc';
	    
	    $manu = $this->input->get('man');
	    $part = $this->input->get('par');
	    //$type = $this->input->get('typ');
	   
	    //$SQL = "SELECT [Inventory].[dbo].[fn_GetCompatiblityListForEbay]('{$manu}','{$part}','PartNumberSpecific')";
	    $SQL = "EXEC Inventory.dbo.sp_GetTableCompatiblityListForEbay '{$manu}', '{$part}'";
		$result = $this->MCommon->getSomeRecords($SQL);
		//print_r($SQL);

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


	    $responce = new stdClass();
	    $responce->page = $page;
	    $responce->total = $total_pages;
	    $responce->records = $count;

	    $i = 0;
		foreach ($result as $row) {
		    $responce->rows[$i]['id'] = $row['ID'];
		    $responce->rows[$i]['cell'] = array(
				$row['ID']= utf8_encode($row['ID']),
				$row['PartNumbers']= utf8_encode($row['PartNumbers']),
				$row['ModelNumbers']= utf8_encode($row['ModelNumbers']),
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
        header("Content-Disposition: attachment; filename=keyWordGenerator_" . date("D-M-j") . ".xls");
        header("Pragma: no-cache");

        $buffer = $_POST['csvBuffer'];

        try {
            echo $buffer;
        } catch (Exception $e) {
            
        }
    }

    function fillPN(){

		$SQL = "SELECT DISTINCT TOP 10 [PartNumber] FROM [Inventory].[dbo].[Compatibility] 
		WHERE Manufacturer = '{$_REQUEST['man']}' AND PartNumber LIKE '{$_REQUEST['term']}%' ORDER BY [PartNumber] ASC ";

		echo json_encode($this->MCommon->fillAutoComplete($SQL, 'PartNumber'));
    }

}
?>