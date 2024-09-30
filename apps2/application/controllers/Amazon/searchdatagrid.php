<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Searchdatagrid extends BP_Controller {

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

	$this->title = "MI Technologiesinc - Search Data Grid";

	$this->description = "Search Data Grid";

	$this->css = array('form.css', 'fluid.css', 'table.css', 'jqueryui/redmond/jquery-ui-1.8.21.custom.css', 'jqgrid/ui.jqgrid.css', 'menu.css',);

	// Define custom javascript
	$this->javascript = array('jqueryui/ui/jquery-ui-1.8.21.custom.js', 'jqgrid/i18n/grid.locale-en.js', 'jqgrid/jquery.jqGrid.min.js', 'jqgrid/jquery.jqGrid.fluid.js', 'site.js');

	// If the page has menu
	$this->load->library('Layout');
	$menu = new Layout;


	$data = array(
	    'menu' => $menu->show_menu($this->MUsers->getUserFullName($this->session->userdata('userid'))),
	    'from' => '/Amazon/searchdatagrid/',
	    'caption' => 'Search Data Grid',
	    'export' => 'exportexcel',
	    'subgrid' => 'false', // true or false depends
	    'sort' => 'desc',
	    'search' => '',
	    'customerid' => $this->MUsers->getCustomerId($this->session->userdata('userid')),
	);


	$data['colNames'] = "['ID','ASIN','SearchTerm','PotentialSKU','MapToSKU','Manufacturer','Brand','Status','CountryCode','StatusUserID']";

	$data['colModel'] = "[
				{name:'ID',index:'ID', width:80, align:'left',align:'left'},
                {name:'ASIN',index:'ASIN', width:80, align:'center', formatter: formatLink},
                {name:'SearchTerm',index:'SearchTerm', width:80, align:'center'},
                {name:'PotentialSKU',index:'PotentialSKU', width:80, align:'center'},
                {name:'MapToSKU',index:'MapToSKU', width:80, align:'center'},
                {name:'Manufacturer',index:'Manufacturer', width:80, align:'center'},
				{name:'Brand',index:'Brand', width:80, align:'center'},
                {name:'Status',index:'Status', width:80, align:'center', editable:true, edittype:'select', editoptions:{value:'Approved:Aproved;Skipped:Skipped;Declined:Declined'}},
				{name:'CountryCode',index:'CountryCode', width:80, align:'center'},
				{name:'StatusUserID',index:'StatusUserID', width:80, align:'center'},
               ]";

	$this->build_content($data);
	$this->render_page();
    }

    function getData() {

	$page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1; // get the requested page
	$limit = isset($_REQUEST['rows']) ? $_REQUEST['rows'] : 10; // get how many rows we want to have into the gri
	$sidx = isset($_REQUEST['sidx']) ? $_REQUEST['sidx'] : 'ID'; // get index row - i.e. user click to sort
	$sord = isset($_REQUEST['sord']) ? $_REQUEST['sord'] : 'asc';

		$search = $this->input->get('ds');
		$fpor= $this->input->get('fpo');


	//Select utilizado para realizar el conteo del numero de registros devueltos por la consulta.
	$selectCount = 'SELECT COUNT(*) AS rowNum';

	$table = ' [Inventory].[dbo].[AmazonSearchDATA]  ';

	$where = '';
	$from = ' FROM ';


	$select = "SELECT [ID],[ASIN],[SearchTerm],[PotentialSKU],[MapToSKU],[Manufacturer],[Brand],[Status],[CountryCode],[StatusUserID]";

	$selectSlice = "SELECT [ID],[ASIN],[SearchTerm],[PotentialSKU],[MapToSKU],[Manufacturer],[Brand],[Status],[CountryCode],[StatusUserID]";

	$wherefields = array('ID','ASIN','SearchTerm','PotentialSKU','MapToSKU','Manufacturer','Brand','Status','StatusUserID');
	$where .=$this->MCommon->concatAllWerefields($wherefields, $search);

	$where .= " AND  StatusUserID IS NOT NULL ";

	$SQL = "{$select}{$from}{$table}{$where}";
	
	//echo $SQL;
	
	$result = $this->MCommon->getOneRecord($SQL);
	$count = $result['rowNum'];


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


	$SQL = "WITH mytable AS ($select , ROW_NUMBER() OVER (ORDER BY {$sidx} {$sord}) AS RowNumber
							FROM {$table}{$where})
		   {$selectSlice}, RowNumber
		   FROM mytable
			WHERE RowNumber BETWEEN {$start} AND {$finish}";

	$result = $this->MCommon->getSomeRecords($SQL);

	//	print_r($result);
	$responce = new stdClass();
	$responce->page = $page;
	$responce->total = $total_pages;
	$responce->records = $count;

	$i = 0;
	foreach ($result as $row) {
	    $responce->rows[$i]['id'] = $row['ID'];
	    $responce->rows[$i]['cell'] = array($row['ID'],
		$row['ASIN'],
		$row['SearchTerm'],
		$row['PotentialSKU'],
		$row['MapToSKU'],
		$row['Manufacturer'] ,
		$row['Brand'],
		$row['Status'],
		$row['CountryCode'],
		$row['StatusUserID'],
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


     function editData(){

        $id = $_POST['id'];
     
        if (isset($_POST['Status'])) {
        	$fieldName = 'Status';
            $data = $_POST['Status'];
        }




		$SQL = "update [Inventory].[dbo].[AmazonSearchDATA] set {$fieldName}={$data} where ID= {$id}";

		echo $SQL;
        
      // $this->MCommon->saveRecord($SQL,'Inventory');

    }
    

}
?>
