<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Amazoninv extends BP_Controller {

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


	$this->title = "MI Technologiesinc - Amazon Inventory & Price Feed";

	$this->description = "Amazon Inventory & Price Feed";

	$this->css = array('form.css', 'fluid.css', 'table.css', 'jqueryui/redmond/jquery-ui-1.8.21.custom.css', 'jqgrid/ui.jqgrid.css', 'menu.css',);

	// Define custom javascript
	$this->javascript = array('jqueryui/ui/jquery-ui-1.8.21.custom.js', 'jqgrid/i18n/grid.locale-en.js', 'jqgrid/jquery.jqGrid.min.js', 'jqgrid/jquery.jqGrid.fluid.js', 'site.js');

	// If the page has menu
	$this->load->library('Layout');
	$menu = new Layout;


	$data = array(
	    'menu' => $menu->show_menu($this->MUsers->getUserFullName($this->session->userdata('userid'))),
	    'from' => '/Amazon/amazoninv/',
	    'caption' => 'Amazon Inventory & Price Feed',
	    'export' => 'exportexcel',
	    'subgrid' => 'false', // true or false depends
	    'sort' => 'desc',
	    'search' => '',
	    'customerid' => $this->MUsers->getCustomerId($this->session->userdata('userid')),
	    'channels' => $this->MCatalog->fillAmazonChannels(),
	    'channelsSelect' => 0,
	    'floorprice' => '',
	);



	$data['colNames'] = "['SKU','Quantity','PriceFloor','PriceCeiling','Channel','ASIN','MITSKU','FloorPriceOverride','FloorPriceOverrideValue']";

	$data['colModel'] = "[
				{name:'SKU',index:'FNAmazonFeed.[SKU]', width:80, align:'center'},
                {name:'Quantity',index:'FNAmazonFeed.[Quantity]', width:80, align:'center'},
                {name:'PriceFloor',index:'FNAmazonFeed.[PriceFloor]', width:80, align:'center'},
                {name:'PriceCeiling',index:'FNAmazonFeed.[PriceCeiling]', width:80, align:'center'},
                {name:'Channel',index:'FNAmazonFeed.[Channel]', width:80, align:'center'},
                {name:'ASIN',index:'FNAmazonFeed.[ASIN]', width:80, align:'center'},
				{name:'MITSKU',index:'FNAmazonFeed.[MITSKU]', width:80, align:'center'},
                {name:'FloorPriceOverride',index:'FNAmazonFeed.[FloorPriceOverride?]', width:80, align:'center'},
				{name:'FloorPriceOverrideValue',index:'FNAmazonFeed.[FloorPriceOverrideValue]', width:80, align:'center'},
               ]";

	$this->build_content($data);
	$this->render_page();
    }

    function getData() {

	$page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1; // get the requested page
	$limit = isset($_REQUEST['rows']) ? $_REQUEST['rows'] : 10; // get how many rows we want to have into the gri
	$sidx = isset($_REQUEST['sidx']) ? $_REQUEST['sidx'] : 'FNAmazonFeed.[SKU]'; // get index row - i.e. user click to sort
	$sord = isset($_REQUEST['sord']) ? $_REQUEST['sord'] : 'asc';

		$search = $this->input->get('ds');
		$chan= $this->input->get('chan');
		$fp= $this->input->get('fp');


	//Select utilizado para realizar el conteo del numero de registros devueltos por la consulta.
	$selectCount = 'SELECT COUNT(*) AS rowNum';

	$table = ' [Inventory].[dbo].[fn_GetAmazonInventoryFeed]() AS FNAmazonFeed  ';

	$where = '';
	$from = ' FROM ';


	$select = "SELECT  FNAmazonFeed.[SKU], 
        FNAmazonFeed.[Quantity], 
        FNAmazonFeed.[PriceFloor], 
        FNAmazonFeed.[PriceCeiling], 
        FNAmazonFeed.[Channel],
        FNAmazonFeed.[ASIN],
        FNAmazonFeed.[MITSKU],
        FNAmazonFeed.[FloorPriceOverride],
        FNAmazonFeed.[FloorPriceOverrideValue] ";

	$selectSlice = "SELECT  SKU, 
        Quantity, 
        PriceFloor, 
        PriceCeiling, 
        Channel,
        ASIN,
        MITSKU,
        FloorPriceOverride,
        FloorPriceOverrideValue ";

	$wherefields = array('FNAmazonFeed.[SKU]','FNAmazonFeed.[MITSKU]','FNAmazonFeed.[ASIN]');
	$where .=$this->MCommon->concatAllWerefields($wherefields, $search);

	if($chan){
		$where .= " and FNAmazonFeed.[Channel] = '{$chan}'";
	}

	if($fp =='checked'){
		$where .= " and FNAmazonFeed.[FloorPriceOverride]= 1 ";
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

	$result = $this->MCommon->getSomeRecords($SQL);

	//	print_r($result);
	$responce = new stdClass();
	$responce->page = $page;
	$responce->total = $total_pages;
	$responce->records = $count;

	$i = 0;
	foreach ($result as $row) {
	    $responce->rows[$i]['id'] = $row['SKU'];
	    $responce->rows[$i]['cell'] = array($row['SKU'],
		$row['Quantity'],
		$row['PriceFloor'],
		$row['PriceCeiling'],
		$row['Channel'],
		$row['ASIN'] ,
		$row['MITSKU'],
	    $row['FloorPriceOverride'],
		$row['FloorPriceOverrideValue'],
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
