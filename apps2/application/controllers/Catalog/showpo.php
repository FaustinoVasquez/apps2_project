<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Showpo extends BP_Controller {

    public function __construct() {
	parent::__construct();

	$is_logged_in = $this->session->userdata('is_logged_in');

	if (!isset($is_logged_in) || $is_logged_in != true) {
	    redirect(base_url(), 'refresh');
	}

	if ($this->MUsers->isValidUser($this->session->userdata('userid'), 881150) != 1) {// Access Code
	    redirect('Catalog/prodcat', 'refresh');
	}
    }

    function index($sku) {

	$this->title = "MI Technologiesinc - Purchase Orders";

	$this->description = "Purchase Orders";

	$this->css = array('form.css', 'fluid.css', 'table.css', 'jqueryui/redmond/jquery-ui-1.8.21.custom.css', 'jqgrid/ui.jqgrid.css', 'menu.css',);

	// Define custom javascript
	$this->javascript = array('jqueryui/ui/jquery-ui-1.8.21.custom.js', 'jqgrid/i18n/grid.locale-en.js', 'jqgrid/jquery.jqGrid.min.js', 'jqgrid/jquery.jqGrid.fluid.js', 'popup.js');


	// If the page has menu

	$this->hasNav = False;

	 $data = array(
	    'showpoData' => '/Catalog/showpo/showpoData/',
	    'caption' => "Purcharse Order to sku ".$sku,
	    'sku' => $sku
        );
	
	// If not have menu

	$this->build_content($data);
	$this->render_page();
    }

    
    
     function showpoData($sku){
        $page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1; // get the requested page
        $limit = isset($_REQUEST['rows']) ? $_REQUEST['rows'] : 10; // get how many rows we want to have into the gri
        $sidx = isset($_REQUEST['sidx']) ? $_REQUEST['sidx'] : 'OrderNumber'; // get index row - i.e. user click to sort
        $sord = isset($_REQUEST['sord']) ? $_REQUEST['sord'] : 'Desc';
      

	$SQL = "select 
		    vendor, 
		    PO# ,
		    [PO Qty] as PoQty, 
		    [PO Received] as PoReceived, 
		    backorder, 
		    convert(date,orderdate) as orderdate, 
		    convert(date, expecteddate ) as expecteddate,
		    [AZ Order#] as OrderNo
		from 
		    inventory.dbo.QBPurchaseOrderBackorders
		where 
		CAST('{$sku}' as nvarchar) = CAST(inventory.dbo.QBPurchaseOrderBackorders.SKU AS nvarchar)";
       
       
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
        
   
                 
        $responce = new stdClass();      
        $responce->page = $page;
        $responce->total = $total_pages;
        $responce->records = $count;

        $i = 0;

        foreach ($result as $row) {
            $responce->rows[$i]['id'] = $row['vendor'];
            $responce->rows[$i]['cell'] = array($row['vendor'],
		$row['PO#'],
                $row['PoQty'],
                $row['PoReceived'],
                $row['backorder'],
                $row['orderdate'],
                $row['expecteddate'],
		$row['OrderNo'],

            );
            $i++;
        }


        echo json_encode($responce);
	

    }
    
}


