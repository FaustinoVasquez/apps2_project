<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class History extends BP_Controller {

    public function __construct() {
        parent::__construct();

        $is_logged_in = $this->session->userdata('is_logged_in');

        if (!isset($is_logged_in) || $is_logged_in != true) {
            redirect(base_url(), 'refresh');
        }
    }

    function index($sku) {

        $this->title = "MI Technologiesinc - History";
        $this->description = " History";
        // Define custom CSS
        $this->css = array('menu.css', 'form.css', 'fluid.css', 'table.css', 'jqueryui/redmond/jquery-ui-1.8.21.custom.css', 'jqgrid/ui.jqgrid.css');
        // Define custom javascript
        $this->javascript = array('jqueryui/ui/jquery-ui-1.8.21.custom.js', 'jqgrid/i18n/grid.locale-en.js', 'jqgrid/jquery.jqGrid.min.js', 'jqgrid/jquery.jqGrid.fluid.js');
        $this->hasNav = False;

        

        $data['dateFrom'] = date("m/d/Y", strtotime("-1 month"));
        $data['dateTo'] = date("m/d/Y");
    	$data['warehouseOptions'] =  $this->MCatalog->fillWarehouses();
        $data['sku'] = $sku;
        $data['title'] = 'History'; //Page Title
        $data['caption'] = 'History SKU';
        $data['from'] = '/Tabs/history/';
        
        $data['headers'] = "['id','TransactionId','Date','Bin_Id','Reason','Inputs','Outputs','Global_Qty','UserName','Comment']";

	
        $data['body'] = "[  {name:'id',index:'id',width:60, align:'center', key:true},
            	            {name:'TransactionId',index:'TransactionId',width:80, align:'center'},
                            {name:'mydate',index:'mydate', width:80, align:'center',sortable:false, formatter:'date', formatoptions: { srcformat:'Y/m/d', newformat:'m/d/Y'}},
                            {name:'Bin_Id',index:'Bin_Id', width:80, align:'left', sortable:false},
                            {name:'Reason',index:'Reason', width:100, align:'left', sortable:false,},
                            {name:'inputs',index:'inputs', width:60, align:'center', sortable:false,},
                            {name:'outputs',index:'outputs', width:60, align:'center', sortable:false, },
                            {name:'globalqty',index:'globalqty', width:70, align:'center', sortable:false },
                            {name:'username',index:'username', width:150, align:'left', sortable:false},
                            {name:'comment',index:'comment', width:140, align:'left', sortable:false},
  	]";

        $this->build_content($data);
        $this->render_page();
    }


    function gridDataHistory() {

        $page = isset($_GET['page']) ? $_GET['page'] : 1; // get the requested page
        $limit = isset($_GET['rows']) ? $_GET['rows'] : 10; // get how many rows we want to have into the grid
        $sidx = isset($_GET['sidx']) ? $_GET['sidx'] : 'id'; // get index row - i.e. user click to sort
        $sord = isset($_GET['sord']) ? $_GET['sord'] : 'desc'; // get the direction

        $from = $this->input->get('from');
        $to = $this->input->get('to');
        $sku = $this->input->get('sku');
    
        $InitialDate = isset($from) ? $from : date('m/d/Y', strtotime("-1 month"));
        $FinalDate = isset($to) ? $to : date("m/d/Y");	
	
	
        /**
    	 * Param1 = SKU
    	 * Param2= Almacen
    	 * Param3 = inicio del rango, -1 para razones de conteo
    	 * Param3 = Fin del rango, 0 para razones de conteo
         * 
    	 */

        $select = "exec Inventory.dbo.sp_Bins_History {$sku},'{$InitialDate}','{$FinalDate}'";



        $result = $this->MCommon->getSomeRecords($select);
//print_r($result);
       
	
	
        $count = count($result);


        if ($count > 0) {
            $total_pages = ceil($count / $limit);
        } else {
            $total_pages = 0;
        }
        if ($page > $total_pages)
            $page = $total_pages;

        $start = $limit * $page - $limit; // do not put $limit*($page - 1)
        $start = ($start <= 0) ? 1 : $start ;
        $finish = $start + $limit;
	

        $responce = new stdClass();
        $responce->page = $page;
        $responce->total = $total_pages;
        $responce->records = $count;


        $i=0;
	foreach ($result as $row) {
            $responce->rows[$i]['id'] = $row['id'];
            $responce->rows[$i]['cell'] = array($row['id'],
                $row['TransactionId'] = utf8_encode($row['TransactionId']),
                $row['mydate'] = utf8_encode($row['mydate']),
                $row['Bin_Id'] = utf8_encode($row['Bin_Id']),
                $row['Reason'] = utf8_encode($row['Reason']),
                $row['inputs'] = utf8_encode($row['inputs']),
                $row['outputs'] = utf8_encode($row['outputs']),
                $row['globalqty'] = utf8_encode($row['globalqty']),
                $row['username'] = utf8_encode($row['username']),
                $row['comments'] = utf8_encode($row['comments']),
              
            );
            $i++;
        }

        echo json_encode($responce);
    }

    
    function saveData(){
	
	$SQL="exec sp_save_audit_comment {$_POST['Adjustment_Id']},'{$_POST['Audit']}'";

	$this->MCommon->saveRecord($SQL,'InventorySave');

    }
    
    function csvExportHistory($name) {

        header('Content-type: application/vnd.ms-excel');
        header("Content-Disposition: attachment; filename= history_" . $name . ".xls");
        header("Pragma: no-cache");

        $buffer = $_POST['csvBuffer'];

        try {
            echo $buffer;
        } catch (Exception $e) {
            
        }
    }

    
}

?>
