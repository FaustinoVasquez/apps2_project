<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Amazonforce extends BP_Controller {

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

	$this->title = "MI Technologiesinc - Amazon Force Disactivate";

	$this->description = "Amazon Force Disactivate";

	$this->css = array('form.css', 'fluid.css', 'table.css', 'jqueryui/redmond/jquery-ui-1.8.21.custom.css', 'jqgrid/ui.jqgrid.css', 'menu.css',);

	// Define custom javascript
	$this->javascript = array('jqueryui/ui/jquery-ui-1.8.21.custom.js', 'jqgrid/i18n/grid.locale-en.js', 'jqgrid/jquery.jqGrid.min.js', 'jqgrid/jquery.jqGrid.fluid.js', 'site.js');

	// If the page has menu
	$this->load->library('Layout');
	$menu = new Layout;


	$data = array(
	    'menu' => $menu->show_menu($this->MUsers->getUserFullName($this->session->userdata('userid'))),
	    'from' => '/Amazon/amazonforce/',
	    'caption' => 'Amazon Force Disactivate',
	    'export' => 'exportexcel',
	    'subgrid' => 'false', // true or false depends
	    'sort' => 'desc',
	    'search' => '',
	    'customerid' => $this->MUsers->getCustomerId($this->session->userdata('userid')),
	    'channels' => $this->MCatalog->fillAmazonChannels(),
	    'channelsSelect' => 0,
	    'floorprice' => '',
	);


	$data['colNames'] = "['id','ASIN','Title','Manufacturer','CountryCode','STAMP']";

	$data['colModel'] = "[
				{name:'id',index:'AZFD.[id]', width:80, align:'center',hidden: true,viewable: true,  editrules: {edithidden: true}},
				{name:'ASIN',index:'AZFD.[ASIN]', width:80, align:'center',editable:true, editrules: {custom: true, custom_func: validateAsin}},
                {name:'Title',index:'AZ.[Title]', width:80, align:'center'},
                {name:'Manufacturer',index:'AZ.[manufacturer]', width:80, align:'center'},
                {name:'CountryCode',index:'AZFD.[CountryCode]', width:80, align:'center',editable:true, edittype: 'select', editoptions:{value: 'US:US;CA:CA;MX:MX;UK:UK;DE:DE;FR:FR;IT:IT;ES:ES;JP:JP;CN:CN;IN:IN'}},
                {name:'STAMP',index:'AZFD.[STAMP]', width:80, align:'center'},
               ]";

	$this->build_content($data);
	$this->render_page();
    }

    function getData() {

	$page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1; // get the requested page
	$limit = isset($_REQUEST['rows']) ? $_REQUEST['rows'] : 10; // get how many rows we want to have into the gri
	$sidx = isset($_REQUEST['sidx']) ? $_REQUEST['sidx'] : 'AZFD.[ASIN]'; // get index row - i.e. user click to sort
	$sord = isset($_REQUEST['sord']) ? $_REQUEST['sord'] : 'asc';

	$search = $this->input->get('ds');


	//Select utilizado para realizar el conteo del numero de registros devueltos por la consulta.
	$selectCount = 'SELECT COUNT(*) AS rowNum';

	$table = ' [Inventory].[dbo].[AmazonForceDisactivate] AS AZFD  ';
	$join = ' LEFT OUTER JOIN [Inventory].[dbo].[Amazon] AS AZ ON (AZFD.[ASIN] = AZ.[ASIN] AND AZFD.[CountryCode] = AZ.[CountryCode]) ';

	$where = '';
	$from = ' FROM ';


	$select = "SELECT AZFD.[id]
					 ,AZFD.[ASIN]
			         ,AZ.[Title]
			         ,AZ.[manufacturer]
			         ,AZFD.[CountryCode]
			         ,AZFD.[STAMP] ";

	$selectSlice = "SELECT  id,ASIN, 
        Title, 
        manufacturer, 
        CountryCode, 
        STAMP ";

	$wherefields = array('AZFD.[ASIN]','AZ.[Title]','AZ.[manufacturer]','AZFD.[CountryCode]');
	$where .=$this->MCommon->concatAllWerefields($wherefields, $search);



	$SQL = "{$selectCount}{$from}{$table}{$join}{$where}";

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
							FROM {$table}{$join}{$where})
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
	    $responce->rows[$i]['id'] = $row['id'];
	    $responce->rows[$i]['cell'] = array($row['id'],
	    $row['ASIN'],
		$row['Title'],
		$row['manufacturer'],
		$row['CountryCode'],
		$row['STAMP']);
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

       // $asin = $_POST['ASIN'];
       // $countryCode = $_POST['CountryCode'];
        $oper = $_POST['oper'];
       // $id = $_POST['id'];

        if($oper == 'add'){
        	$asin = $_POST['ASIN'];
        	$countryCode = $_POST['CountryCode'];

        	$SQL = "insert into [Inventory].[dbo].[AmazonForceDisactivate] (ASIN,CountryCode) values ('{$asin}','{$countryCode}')";

        }
        if($oper == 'del'){
        	$id = $_POST['id'];
        	$SQL = " DELETE FROM [Inventory].[dbo].[AmazonForceDisactivate] WHERE Id='{$id}'";
        }

        
      $this->MCommon->saveRecord($SQL,'Inventory');

    }

    function validateAsin(){

      $asin = $this->input->get('asin');

      $SQL = "select count(*) as mycount from Inventory.dbo.Amazon where ASIN ='{$asin}'";
      $result = $this->MCommon->getOneRecord($SQL);


      if ($result['mycount'] == 0){
      	echo 'false';
      }else{
      	echo 'true';
      };
    }

     function validateAsin1(){

      $data = $this->input->get('data');

      $asin = $data['ASIN'];
      $countryCode = $data['CountryCode'];

      $SQL = "select count(*) as mycount from [Inventory].[dbo].[AmazonForceDisactivate] where ASIN ='{$asin}' and CountryCode = '{$countryCode}'";
    
      $result = $this->MCommon->getOneRecord($SQL);

      if ($result['mycount'] == 0){
      	echo 'true';
      }else{
      	echo 'false';
      };

    }
   
}
?>

