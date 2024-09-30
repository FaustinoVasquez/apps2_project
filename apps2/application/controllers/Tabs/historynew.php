<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Historynew extends BP_Controller {

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

        
     
        $data['dateFrom'] = isset($_POST['dateFrom']) ? $_POST['dateFrom'] : $this->MCommon->fixInitialDate(date('m/d/Y', strtotime("-1 month")), 0);
        $data['dateTo'] = isset($_POST['dateTo']) ? $_POST['dateTo'] : $this->MCommon->fixFinalDate(date("m/d/Y"), 0);
        $data['filtered'] = isset($_POST['filtered']) ? $_POST['filtered'] : 0;
        $data['search'] = $sku;
        $data['title'] = 'History'; //Page Title
        $data['caption'] = 'History NEW';
        $data['from'] = '/Tabs/historynew/';
       
	
		$data['headers'] = "['TransactionID','User','Movement','Qty','Reason','Comments','Date','BinID','SN','SKU']";
        $data['body'] = "[  {name:'TransactionID',index:'TransactionID',width:60, align:'center'},
                            {name:'User',index:'User',width:150, align:'left'},
                            {name:'Movement',index:'Movement', width:80, align:'center'},
                            {name:'Qty',index:'Qty', width:60, align:'center'},
                            {name:'Reason',index:'Reason', width:150, align:'left'},
                            {name:'Comments',index:'Comments', width:150, align:'left'},
                            {name:'Date',index:'Date', width:100, align:'center'},
                            {name:'BinID',index:'BinID', width:60, align:'center'},
                            {name:'SN',index:'SN', width:60, align:'left'},
                            {name:'id',index:'id', width:60, align:'left', hidden:true}]";

        $this->build_content($data);
        $this->render_page();
    }


    function gridData() {
        $InitialDate = isset($_GET['from']) ? $_GET['from'] : date('m/d/Y', strtotime("-1 month"));
        $FinalDate = isset($_GET['to']) ? $_GET['to'] : date("m/d/Y");
        $Filtered = isset($_GET['filtered']) ? $_GET['filtered'] : 0;
        $FinalDateFixed = $this->MCommon->fixFinalDate($FinalDate, 1);
        $InitialDateFixed = $this->MCommon->fixInitialDate($InitialDate, 1);


        $page = isset($_GET['page']) ? $_GET['page'] : 1; // get the requested page
        $limit = isset($_GET['rows']) ? $_GET['rows'] : 10; // get how many rows we want to have into the grid
        $sidx = isset($_GET['sidx']) ? $_GET['sidx'] : 'Adjustment_Id'; // get index row - i.e. user click to sort
        $sord = isset($_GET['sord']) ? $_GET['sord'] : 'desc'; // get the direction



        $sku = $this->input->get('sku');
        $user = $this->input->get('user');
        $flow = $this->input->get('flow');
        $qty = $this->input->get('qty');
        $comments = $this->input->get('comments');
        $bin = $this->input->get('bin');
        $transaction = $this->input->get('transaction');
	
	
        $from = ' FROM ';
        $table = '[Inventory].[dbo].[Bins_History] AS BH ';
	    $leftJoin = ' LEFT OUTER JOIN [Inventory].dbo.Users As USERS ON (BH.[User_Id] = USERS.ID)
                    LEFT OUTER JOIN [Inventory].DBO.All_List AS REASONS ON (BH.ScanCode = REASONS.listvalue) ';
        $where = " WHERE BH.Product_Catalog_ID LIKE '%{$sku}%' ";
        $orderby = ' order by BH.Stamp desc';

        $select = "SELECT BH.Product_Catalog_ID As [SKU]
                   ,USERS.FullName As [User]
                   ,Case When BH.Flow = 1 AND BH.ScanCode NOT LIKE 'TR%' Then 'Added' When BH.Flow = 2  AND BH.ScanCode NOT LIKE 'TR%' Then 'Removed' When  BH.Flow = 1 AND BH.ScanCode LIKE 'TR%' Then 'Transfer In' When  BH.Flow = 2 AND BH.ScanCode LIKE 'TR%' Then 'Transfer Out' End As [Movement] 
                   ,BH.Qty As [Qty]
                   ,REASONS.listdisplay as [Reason]
                   ,BH.Comments as [Comments]
                   ,BH.Stamp As [Date]   
                   ,BH.Bin_Id As [BinID]
                   ,BH.SerialNumber As [SN]
                   ,BH.ID As [TransactionID]";



       $where .= " AND CONVERT(datetime,BH.Stamp,1) BETWEEN '$InitialDate' AND '$FinalDateFixed'";

        if($user){
            $where .= " AND USERS.FullName LIKE '%{$user}%'";
        }

        if($flow){
            if ($flow != 3){
                $where .= " And (BH.ScanCode NOT LIKE 'TR%') AND (BH.Flow = '{$flow}')";
            }else{
                $where .= " AND (BH.ScanCode LIKE 'TR%') AND (BH.Flow = 1 or BH.Flow = 2)";
            }
        }

        if($qty){
            $where .= " AND BH.Qty >= $qty";
        }  

         if($qty){
            $where .= " AND BH.Qty >= $qty";
        }  

        if($comments){
            $where .= " AND BH.Comments LIKE '%{$comments}%'";
        }  

        if($bin){
            $where .= " AND BH.Bin_Id LIKE '%{$bin}%'";
        }  

         if($transaction){
            $where .= " AND BH.ID LIKE '%{$transaction}%'";
        }  



        $query = "{$select}{$from}{$table}{$leftJoin}{$where}{$orderby}"; 


        $result = $this->MCommon->getSomeRecords($query);

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


      // $result = $this->MCommon->getSomeRecords($select);
	

        $responce = new stdClass();
        $responce->page = $page;
        $responce->total = $total_pages;
        $responce->records = $count;

        $i=0;
	foreach ($result as $row) {
            $responce->rows[$i]['id'] = $row['TransactionID'];
            $responce->rows[$i]['cell'] = array($row['TransactionID'],
                $row['User'] = utf8_encode($row['User']),
                $row['Movement'] = utf8_encode($row['Movement']),
                $row['Qty'] = utf8_encode($row['Qty']),
                $row['Reason'] = utf8_encode($row['Reason']),
                $row['Comments'] = utf8_encode($row['Comments']),
                $row['Date'] = utf8_encode($row['Date']),
                $row['BinID'] = utf8_encode($row['BinID']),
                $row['SN'] = utf8_encode($row['SN']),
                $row['SKU'] = utf8_encode($row['SKU']),
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
