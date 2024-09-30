<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Pohistory extends BP_Controller {

    public function __construct() {
	parent::__construct();

	$is_logged_in = $this->session->userdata('is_logged_in');

	if (!isset($is_logged_in) || $is_logged_in != true) {
	    redirect(base_url(), 'refresh');
	}
    }

    function index($sku) {

	$this->title = "MI Technologiesinc - Po History";

	$this->description = "Po History";

	$this->css = array('form.css', 'fluid.css', 'table.css', 'jqueryui/redmond/jquery-ui-1.8.21.custom.css', 'jqgrid/ui.jqgrid.css', 'menu.css',);

	// Define custom javascript
	$this->javascript = array('jqueryui/ui/jquery-ui-1.8.21.custom.js', 'jqgrid/i18n/grid.locale-en.js', 'jqgrid/jquery.jqGrid.min.js', 'jqgrid/jquery.jqGrid.fluid.js', 'popup.js');


	// If the page has menu

	$this->hasNav = False;

	 $data = array(
	    'getData' => '/Tabs/pohistory/pohistoryData/',
	    'caption' => "Purcharse Order History to Sku ".$sku,
	    'sku' => $sku
        );
	
	// If not have menu

	$this->build_content($data);
	$this->render_page();
    }

    
    
     function pohistoryData($sku){
        $page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1; // get the requested page
        $limit = isset($_REQUEST['rows']) ? $_REQUEST['rows'] : 10; // get how many rows we want to have into the gri
        $sidx = isset($_REQUEST['sidx']) ? $_REQUEST['sidx'] : 'Sku'; // get index row - i.e. user click to sort
        $sord = isset($_REQUEST['sord']) ? $_REQUEST['sord'] : 'Desc';
      
	
	$table =" [Inventory].[dbo].[QBPuchaseOrderHistory] ";
	$where = " WHERE (SKU='{$sku}') and (LEFT(SKU,3) NOT LIKE '[a-z]%') ";
	
	$selectSlice =" SELECT [SKU] as Sku
		        ,[PO#] as PO
		        ,convert(varchar,[Date Issued],101) as DateIssue
		        ,[Date Expected] as DateExpected
		        ,[Qty Ordered] as QtyOrdered
		        ,[Qty Received] as QtyReceived
		        ,[Qty Missing] as QtyMissing
		        ,[Fully Received?] as FullyReceived";
	
	
	$SQL = "SELECT COUNT(*) AS COUNT  FROM (
		SELECT [SKU] as Sku
		    ,[PO#] as PO
		    ,convert(varchar,[Date Issued],101) as DateIssue
		    ,[Date Expected] as DateExpected
		    ,[Qty Ordered] as QtyOrdered
		    ,[Qty Received] as QtyReceived
		    ,[Qty Missing] as QtyMissing
		    ,[Fully Received?] as FullyReceived
		FROM [Inventory].[dbo].[QBPuchaseOrderHistory]
		WHERE (SKU='{$sku}') and (LEFT(SKU,3) NOT LIKE '[a-z]%')
		) gridalias";


        $result = $this->MCommon->getOneRecord($SQL);

        $count = $result['COUNT'];
	
	
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
        
	
	$SQL = "
	    WITH mytable AS (select *, ROW_NUMBER() OVER (ORDER BY SKU) AS RowNumber FROM {$table}{$where})
                      {$selectSlice}, RowNumber
               FROM mytable
                WHERE RowNumber BETWEEN {$start} AND {$finish} ORDER BY [Date Issued] DESC";



	
	$result = $this->MCommon->getSomeRecords($SQL);	
	

                 
        $responce = new stdClass();      
        $responce->page = $page;
        $responce->total = $total_pages;
        $responce->records = $count;
	
        $i = 0;
		
        foreach ($result as $row) {
            $responce->rows[$i]['id'] = $row['Sku'];
            $responce->rows[$i]['cell'] = array($row['Sku'],
		$row['PO'],
		$row['DateIssue'],
                $row['DateExpected'],
                $row['QtyOrdered'],
                $row['QtyReceived'],
                $row['QtyMissing'],
                $row['FullyReceived'],
            );
            $i++;
        }


        echo json_encode($responce);
	

    }
    
}



