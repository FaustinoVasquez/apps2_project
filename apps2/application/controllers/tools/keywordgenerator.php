<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Keywordgenerator extends BP_Controller {

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
	    'from' => '/tools/keywordgenerator/',
	    'nameGrid' => 'keywordgenerator',
	    'namePager' => 'keywordgeneratorPager',
	    'caption' => 'keyword Generator',
	    'export' => 'exportexcel',
	    'subgrid' => 'false', // true or false depends
	    'sort' => 'desc',
	    'search' => '',
	    'customerid' => $this->MUsers->getCustomerId($this->session->userdata('userid')),
	);


	$data['colNames'] = "['SKU','Manufacturer PN','Keywords']";

	$data['colModel'] = "[  
							{name:'SKU',index:'SKU', width:50, align:'center'},
					       	{name:'manufacturerpn',index:'manufacturerpn', width:80, align:'center'},
					        {name:'Keywords',index:'Keywords', width:80, align:'center'},
               			 ]";


	$this->build_content($data);
	$this->render_page();
    }

    function Data() {

	$page = isset($_GET['page']) ? $_GET['page'] : 1; // get the requested page
	$limit = isset($_GET['rows']) ? $_GET['rows'] : 10; // get how many rows we want to have into the gri
	$sidx = isset($_GET['sidx']) ? $_GET['sidx'] : 'SKU'; // get index row - i.e. user click to sort
	$sord = isset($_GET['sord']) ? $_GET['sord'] : 'asc';
	$sku = !empty($_GET['sku']) ? $_GET['sku'] : '101001';
	$pn = !empty($_GET['pn']) ? "and manufacturerpn like '%".$_GET['pn']."%'" : '';


	$select = " SELECT DISTINCT [SKU]
			      ,[manufacturerpn]
			      ,[Keyword1] as [Keywords]
			     FROM [Inventory].[dbo].[AmazonCampaignKewords]
			     where Keyword1 is not null and sku = '{$sku}' $pn

			     UNION

			     SELECT DISTINCT [SKU]
			      ,[manufacturerpn]
			      ,[Keyword2] as [Keywords]
			     FROM [Inventory].[dbo].[AmazonCampaignKewords]
			     where Keyword2 is not null and sku = '{$sku}' $pn

			     UNION

			     SELECT DISTINCT [SKU]
			      ,[manufacturerpn]
			      ,[Keyword3] as [Keywords]
			     FROM [Inventory].[dbo].[AmazonCampaignKewords]
			     where Keyword3 is not null and sku = '{$sku}' $pn

			     UNION

			     SELECT DISTINCT [SKU]
			      ,[manufacturerpn]
			      ,[Keyword4] as [Keywords]
			     FROM [Inventory].[dbo].[AmazonCampaignKewords]
			     where Keyword4 is not null and sku = '{$sku}' $pn

			     UNION

			     SELECT DISTINCT [SKU]
			      ,[manufacturerpn]
			     ,[Keyword5] as [Keywords]
			     FROM [Inventory].[dbo].[AmazonCampaignKewords]
			     where Keyword5 is not null and sku = '{$sku}' $pn

			     UNION

			     SELECT DISTINCT [SKU]
			      ,[manufacturerpn]
			      ,[Keyword6] as [Keywords]
			     FROM [Inventory].[dbo].[AmazonCampaignKewords]
			     where Keyword6 is not null and sku = '{$sku}' $pn
			    
			     UNION

			     SELECT DISTINCT * FROM (
			     
			     SELECT DISTINCT [SKU]
			      ,[manufacturerpn]
			      ,REPLACE([manufacturer]+ ' '+ [manufacturerpn], '-', ' ') as [Keywords]
			     FROM [Inventory].[dbo].[AmazonCampaignKewords]
			     where Keyword6 is not null and sku = '{$sku}' $pn
			     
			     UNION
			     
			     SELECT DISTINCT [SKU]
			      ,[manufacturerpn]
			      ,[manufacturer]+ ' '+ REPLACE([manufacturerpn], '-', '')  as [Keywords]
			     FROM [Inventory].[dbo].[AmazonCampaignKewords]
			     where Keyword6 is not null and sku = '{$sku}' $pn
			     
			     UNION
			     
			     SELECT DISTINCT [SKU]
			      ,[manufacturerpn]
			      ,REPLACE([manufacturerpn], '-', '')  as [Keywords]
			     FROM [Inventory].[dbo].[AmazonCampaignKewords]
			     where Keyword6 is not null and sku = '{$sku}' $pn
			     
			     UNION
			     
			     SELECT DISTINCT [SKU]
			      ,[manufacturerpn] as [manufacturerpn]
			      ,REPLACE([manufacturerpn], '-', ' ')  as [Keywords]
			     FROM [Inventory].[dbo].[AmazonCampaignKewords]
			     where Keyword6 is not null and sku = '{$sku}' $pn
			     
			     ) AS T3";


	$result = $this->db->query($select);


	$count = count($result->result_array());


	
	if ($count > 0) {
        $total_pages = ceil($count / $limit);
    } else {
        $total_pages = 0;
    }
    if ($page > $total_pages)
        $page = $total_pages;

    $start = $limit * $page - $limit; 
    $start = ($start < 0) ? 0 : $start;
    $finish = $start + $limit;
    

     $SQL = "SELECT * FROM 
     		 	(
					SELECT row_number() OVER (ORDER BY {$sidx} ASC) AS rowNum, SKU, manufacturerpn, Keywords 
						FROM ( $select ) AS A
				) AS B WHERE B.rowNum BETWEEN ($start) AND ($finish)";

  
 	$query = $this->db->query($SQL);


	$responce = new stdClass();
	$responce->page = $page;
	$responce->total = $total_pages;
	$responce->records = $count;

	$i = 0;
	foreach ($query->result_array() as $row) {
	    $responce->rows[$i]['id'] = $row['rowNum'];
	    $responce->rows[$i]['cell'] = array(
	    	$row['SKU'],
			$row['manufacturerpn']= utf8_encode($row['manufacturerpn']),
			$row['Keywords']= utf8_encode($row['Keywords']),
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


    function fillSKU(){
        $SQL = "SELECT top 10 ProductCatalogID FROM [Inventory].[dbo].[vw_InventoryDetails] where ProductCatalogID  LIKE '{$_GET['term']}%' group by ProductCatalogID order by ProductCatalogID asc ";
        echo json_encode($this->MCommon->fillAutoComplete($SQL, 'ProductCatalogID'));
    }

    function fillPN(){


		$SQL = "SELECT top 10 manufacturerpn FROM [Inventory].[dbo].[AmazonCampaignKewords]  WHERE ([SKU] = {$_REQUEST['sku']}) AND (manufacturerpn like '{$_REQUEST['term']}%') group by manufacturerpn order by manufacturerpn asc";
		 echo json_encode($this->MCommon->fillAutoComplete($SQL, 'manufacturerpn'));
    }

}
?>