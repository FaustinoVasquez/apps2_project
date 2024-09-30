<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Amazonflooroverrides extends BP_Controller {

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

	$this->title = "MI Technologiesinc - Amazon Floor Overrides";

	$this->description = "Amazon Floor Overrides";

	$this->css = array('form.css', 'fluid.css', 'table.css', 'jqueryui/redmond/jquery-ui-1.8.21.custom.css', 'jqgrid/ui.jqgrid.css', 'menu.css',);

	// Define custom javascript
	$this->javascript = array('jqueryui/ui/jquery-ui-1.8.21.custom.js', 'jqgrid/i18n/grid.locale-en.js', 'jqgrid/jquery.jqGrid.min.js', 'jqgrid/jquery.jqGrid.fluid.js', 'site.js');

	// If the page has menu
	$this->load->library('Layout');
	$menu = new Layout;


	$data = array(
	    'menu' => $menu->show_menu($this->MUsers->getUserFullName($this->session->userdata('userid'))),
	    'from' => '/tools/amazonflooroverrides/',
	    'caption' => 'Amazon Floor Overrides',
	    'export' => 'exportexcel',
	    'subgrid' => 'false', // true or false depends
	    'sort' => 'desc',
	    'search' => '',
	    'customerid' => $this->MUsers->getCustomerId($this->session->userdata('userid')),
	);


	$data['colNames'] = "['ID','ASIN','SKU','Title','Category','Type','FloorPriceOverride','FloorPriceOverrideValue','CountryCode']";

	$data['colModel'] = "[
				{name:'ID',index:'ID', width:30, align:'left',align:'left'},
                {name:'ASIN',index:'ASIN', width:80, align:'center', formatter: formatLink},
                {name:'SKU',index:'SKU', width:55, align:'center'},
                {name:'Title',index:'Title', width:250, align:'left'},
                {name:'Category',index:'Category', width:55, align:'center'},
                {name:'Type',index:'Type', width:80, align:'center'},
				{name:'FloorPriceOverride',index:'FloorPriceOverride', width:80, align:'center', editable:true},
                {name:'FloorPriceOverrideValue',index:'FloorPriceOverrideValue', width:80, align:'center', editable:true},
				{name:'CountryCode',index:'CountryCode', width:80, align:'center'},
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

	$table = ' [Inventory].[dbo].[Amazon]  ';

	$where = '';
	$from = ' FROM ';


	$select = "SELECT [ID]
			  ,[ASIN]
			  ,[ProductCatalogId] AS 'SKU'
			  ,[Title]
			  ,[Category]
			  ,(CASE
			  		WHEN [LampType] = 0 THEN 'Bare'
			  		WHEN [LampType] = 1 THEN 'With Housing'
			  		ELSE '' END)
			  		AS 'Type'
			  ,[FloorPriceOverride]
			  ,[FloorPriceOverrideValue]
			  ,[CountryCode]";

	$selectSlice = "SELECT [ID]
				  ,[ASIN]
				  ,[SKU]
				  ,[Title]
				  ,[Category]
				  ,Type
				  ,[FloorPriceOverride]
				  ,[FloorPriceOverrideValue]
				  ,[CountryCode]";

	$wherefields = array('ASIN','ProductCatalogId','Title','Category','CountryCode');
	$where .=$this->MCommon->concatAllWerefields($wherefields, $search);

	

    if($fpor){
    	$where.= " and FloorPriceOverride = 1 ";
    }else{
    	$where.= " and FloorPriceOverride = 0 ";
    }
	

	$SQL = "{$selectCount}{$from}{$table}{$where}";

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
	//echo $SQL;

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
		$row['SKU'],
		$row['Title'],
		$row['Category'],
		$row['Type'] ,
		$row['FloorPriceOverride'],
		$row['FloorPriceOverrideValue'],
		$row['CountryCode'],
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
     
        if (isset($_POST['FloorPriceOverrideValue'])) {
        	$fieldName = 'FloorPriceOverrideValue';
            $data = $_POST['FloorPriceOverrideValue'];
        }

 		if (isset($_POST['FloorPriceOverride'])) {
        	$fieldName = 'FloorPriceOverride';
            $data = $_POST['FloorPriceOverride'];
        }



		$SQL = "update [Inventory].[dbo].[Amazon] set {$fieldName}={$data} where ID= {$id}";

		echo $SQL;
        
       $this->MCommon->saveRecord($SQL,'Inventory');

    }
    

}
?>
