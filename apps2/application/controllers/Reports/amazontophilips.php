<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Amazontophilips extends BP_Controller {

    public function __construct() {
	parent::__construct();

	$is_logged_in = $this->session->userdata('is_logged_in');

	if (!isset($is_logged_in) || $is_logged_in != true) {
	    redirect(base_url(), 'refresh');
	}

	// if ($this->MUsers->isValidUser($this->session->userdata('userid'), 883550) != 1) {// Access Code
	//     redirect('Catalog/prodcat', 'refresh');
	// }
    }

    function index() {

	$this->title = "MI Technologiesinc - AmazonASIN to PhilipsSKU";

	$this->description = "AmazonASIN to PhilipsSKU";

	$this->css = array('form.css', 'fluid.css', 'table.css', 'jqueryui/redmond/jquery-ui-1.8.21.custom.css', 'jqgrid/ui.jqgrid.css', 'menu.css',);

	// Define custom javascript
	$this->javascript = array('jqueryui/ui/jquery-ui-1.8.21.custom.js', 'jqgrid/i18n/grid.locale-en.js', 'jqgrid/jquery.jqGrid.min.js', 'jqgrid/jquery.jqGrid.fluid.js', 'popup.js');


	// If the page has menu
	$this->load->library('Layout');
	$menu = new Layout;

	// If not have menu

	$data = array(
	    'menu' => $menu->show_menu($this->MUsers->getUserFullName($this->session->userdata('userid'))),
	    'from' => '/Reports/amazontophilips/',
	    'nameGrid' => 'amazontophilips',
	    'namePager' => 'amazontophilipsPager',
	    'caption' => 'AmazonASIN to PhilipsSKU',
	    'export' => 'exportexcel',
	    'subgrid' => 'false', // true or false depends
	    'sort' => 'desc',
	    'search' => '',
	    'customerid' => $this->MUsers->getCustomerId($this->session->userdata('userid')),
	);

	if (empty($_POST)) {
	    $data['instock'] = '1';
	} else {
	    if (!isset($_POST['instock'])) {
		$data['instock'] = '';
	    } else {
		$data['instock'] = '1';
	    }
	}



	$data['colNames'] = "['ASIN','Manufacturer','PartNumber','MappedToSKU','PhilipsSKU','BuildablePhilips']";

	$data['colModel'] = "[
		{name:'ASIN',index:'ASIN',  align:'left',align:'left'},
                {name:'Manufacturer',index:'Manufacturer',  align:'left'},
                {name:'PartNumber',index:'PartNumber', align:'left'},
                {name:'MappedToSKU',index:'MappedToSKU', align:'left', align:'center'},
                {name:'PhilipsSKU',index:'PhilipsSKU',  align:'left', align:'center'},
                {name:'BuildablePhilips',index:'BuildablePhilips', align:'center', formatter:'number', formatoptions:{decimalSeparator:'.',thousandsSeparator: ',', decimalPlaces: 2, prefix: '' }}
               ]";

	//Cargamos la libreria comumn
	$this->load->library('common');

	//Reemplazamos los campos del formulario por los que estan en el arreglo $_POST.
	$data = $this->common->fillPost('ALL', $data);

	//cadena de busqueda del grid 
	$data['gridSearch'] = 'Data?ds=' . $data['search'] . '&is=' . $data['instock'];


	$this->build_content($data);
	$this->render_page();
    }

    function Data() {

	$page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1; // get the requested page
	$limit = isset($_REQUEST['rows']) ? $_REQUEST['rows'] : 10; // get how many rows we want to have into the gri
	$sidx = isset($_REQUEST['sidx']) ? $_REQUEST['sidx'] : 'Brand'; // get index row - i.e. user click to sort
	$sord = isset($_REQUEST['sord']) ? $_REQUEST['sord'] : 'desc';
	$search = !empty($_GET['ds']) ? $_REQUEST['ds'] : '';
	$instock = !empty($_GET['is']) ? $_REQUEST['is'] : '';


	$table = '[Inventory].[dbo].[AmazonASIN-to-PhilipsFP] ';

	$where = '';
	$from = ' FROM ';


	$select = "SELECT
		    ASIN,
		    Manufacturer, 
		    PartNumber, 
		    MappedToSKU, 
		    PhilipsSKU, 
		    BuildablePhilips";


	$wherefields = array('ASIN', 'Manufacturer', 'Manufacturer', 'MappedToSKU', 'PhilipsSKU', 'BuildablePhilips');

	$where .=$this->MCommon->concatAllWerefields($wherefields, $search);

	if ($instock) {
	    $where .=" and (PhilipsSKU != '-' and BuildablePhilips > 1)";
	}


	$SQL = "{$select}{$from}{$table}{$where}";
	//echo $SQL;

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


	$SQL = " WITH mytable AS (
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
	    $responce->rows[$i]['id'] = $row['ASIN'];
	    $responce->rows[$i]['cell'] = array($row['ASIN'],
		$row['Manufacturer'] = utf8_encode($row['Manufacturer']),
		$row['PartNumber'] = utf8_encode($row['PartNumber']),
		$row['MappedToSKU'] = utf8_encode($row['MappedToSKU']),
		$row['PhilipsSKU'] = utf8_encode($row['PhilipsSKU']),
		$row['BuildablePhilips'] = utf8_encode($row['BuildablePhilips']),
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
