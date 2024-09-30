<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

class OrderCount extends BP_Controller {

    public function __construct() {
        parent::__construct();

        $is_logged_in = $this->session->userdata('is_logged_in');

        if (!isset($is_logged_in) || $is_logged_in != true) {
            redirect(base_url(), 'refresh');
        }
    }

    function index() { 

        $this->title = "MI TechnologiesInc - Orders Count";
        $this->description = "Orders Count";
        $this->css = array('form.css', 'fluid.css', 'table.css', 'jqueryui/redmond/jquery-ui-1.8.21.custom.css', 'jqgrid/ui.jqgrid.css', 'menu.css',);

        // Define custom javascript
        $this->javascript = array('jqueryui/ui/jquery-ui-1.8.21.custom.js', 'jqgrid/i18n/grid.locale-en.js', 'jqgrid/jquery.jqGrid.min.js', 'jqgrid/jquery.jqGrid.fluid.js', 'site.js');

        $this->load->library('Layout');
        $menu = new Layout;

        $data = array(
            'menu' => $menu->show_menu($this->MUsers->getUserFullName($this->session->userdata('userid'))),
            'from' => '/Orders/ordercount',
            'caption' => 'Orders Count',
            'export' => 'orderscount',
            'sort' => 'desc',
            'selectData' => Array(0 => "Consolidate", 1 => "Mini Market", 2 => "Production", 3 => "Shipping"),
        );

        $data['colNames'] = "['SKU','Name','Qty','Status','OrderNumber','ProductionStatus','TrackingNumber','CommentField']";

        $data['colModel'] = "[
                {name:'SKU',index:'SKU', width:60, align:'center'},
                {name:'Name',index:'Name', width:200, align:'left' },
                {name:'Qty',index:'Qty', width:70, align:'center'}, 
                {name:'Status',index:'Status', width:70, align:'center'}, 
                {name:'OrderNumber',index:'OrderNumber', width:70, align:'center',editable:true}, 
                {name:'ProductionStatus',index:'ProductionStatus', width:70, align:'center'},
                {name:'TrackingNumber',index:'TrackingNumber', width:70, align:'center'},
                {name:'CommentField',index:'CommentField', width:70, align:'center',editable:true}, 
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
        $option = $this->input->get('op');
        $busqueda = $this->input->get('bu');

        $where = '';
        $wherefields = array('EL.[SKU]', 'EL.[OrderNumber]');
        $where .= $this->MCommon->concatAllWerefields($wherefields, $busqueda);

        if ($search){

            switch ($option) {
                case '1':
                     $rows = $this->countExportList($search); // verificamos si la orden esta repetida
                     
                     if($rows->num_rows() == 0){ // si La orden no esta repetida
                        $Skus = $this->getSkuOrders($search);//Obtenemos los SKus de la Orden 
                        $this->addSkuExportList($Skus); //Insertamos los SKU a ExportList
                     }
                    break;
                case '2':
                    $this->postStatus($search);
                    break;
                case '3':
                    $this->postTracking($search);
                    break;
            }

        }

        //Search Empty
        $SQL = "SELECT EL.[SKU],PC.[Name],SUM(EL.[Qty]) as Qty,EL.[Status],EL.[OrderNumber],EL.[ProductionStatus],EL.[TrackingNumber],EL.[CommentField]
                FROM [Inventory].[dbo].[ExportList] AS EL
                LEFT OUTER JOIN [Inventory].[dbo].[ProductCatalog] AS PC ON (EL.[SKU] = PC.[ID])
                $where
                GROUP BY EL.[SKU],PC.[Name],EL.[Qty],EL.[Status],EL.[OrderNumber],EL.[ProductionStatus],EL.[TrackingNumber],EL.[CommentField]";

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
            $responce->rows[$i]['ID'] = $row['SKU'];
            $responce->rows[$i]['cell'] = array($row['SKU'],
                $row['Name'],
                $row['Qty'],
                $row['Status'],
                $row['OrderNumber'],
                $row['ProductionStatus'],
                $row['TrackingNumber'],
                $row['CommentField'],
            );
            $i++;
        }
        echo json_encode($responce);
    }
    /*
     * CSV Export
     */


    function countExportList($search){
        $SQL = "SELECT [OrderNumber] FROM [Inventory].[dbo].[ExportList] WHERE [OrderNumber] = '{$search}'";
        $query = $this->db->query($SQL);

        return $query;
    }

    function getSkuOrders($search){
        $SQL = "SELECT a.[SKU]
                      ,a.[Product]
                      ,a.[QuantityOrdered]
                      ,a.[Status]
                      ,a.[OrderNumber]
                      ,a.[ProductionStatus]
                      ,(Select TOP(1) TRK.[TrackingID] from [OrderManager].[dbo].[Tracking] AS TRK where a.[Ordernumber] = TRK.[NumericKey]) AS 'TrackingID'
                      ,a.[CommentField]
                FROM [OrderManager].[dbo].[Order Details] a
                WHERE OrderNumber = {$search}";
        $query = $this->db->query($SQL);
        return $query;
    }

    function addSkuExportList($records){
        foreach ($records->result() as $row) {
            if (is_numeric($row->SKU)){ // Si el SKU es numerico
                $SQL= "INSERT INTO [Inventory].[dbo].[ExportList] VALUES ($row->SKU, $row->QuantityOrdered, '{$row->Status}',$row->OrderNumber,'{$row->TrackingID}','{$row->CommentField}','{$row->ProductionStatus}')";
                $this->MCommon->saveRecord($SQL,'Inventory');
            }
        }

    }

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

    function getDuplicated(){
        $responce = 0;
        $search = $this->input->get('sh');
        $SQL = "SELECT [OrderNumber] FROM [Inventory].[dbo].[ExportList] WHERE [OrderNumber] = '{$search}'";
        $this->responce($SQL);
        
    }

    function getExistence(){
        $responce = 0;
        $search = $this->input->get('sh');
        $SQL = "SELECT [OrderNumber] FROM [Inventory].[dbo].[ExportList] WHERE [OrderNumber] = '{$search}'";
        $this->responce($SQL);
    }

    function getTracking(){
        $search = $this->input->get('sh');
        $SQL = "Select TOP(1) [TrackingID] from [OrderManager].[dbo].[Tracking] where NumericKey = {$search}";
        $this->responce($SQL);
    }

    function responce($SQL){
        
        $responce = 0;
        $query = $this->db->query($SQL);
        if($query->num_rows() > 0){
            $responce =1;
        }
        echo json_encode($responce);
    }


    /*************************** Shipping Methods **************************/


    /**
     * [postTracking] Include Tracking Number on Order
     * @return [type] [description]
     */
    
    function postTracking($search){
        $SQL = "Select TOP(1) [TrackingID] from [OrderManager].[dbo].[Tracking] where NumericKey = {$search}";
        $tracking = $this->db->query($SQL)->row()->TrackingID;  

        $SQL= "UPDATE [Inventory].[dbo].[ExportList]  SET TrackingNumber ='{$tracking}',ProductionStatus = 'Complete' WHERE [OrderNumber] = '{$search}'";
        $this->MCommon->saveRecord($SQL,'Inventory'); 

        return false;
    }

    /*************************** Shipping Methods **************************/


    /*************************** Production Methods **************************/

    function postStatus($search){
        $SQL = "UPDATE [Inventory].[dbo].[ExportList] SET ProductionStatus = 'Processing' WHERE [OrderNumber] = {$search}";
        $this->MCommon->executeQuery($SQL, 'Inventory');

        return false;
    }

    /*************************** Production Methods **************************/

    function deleteInfo()
    {
        $eliminame = "DELETE FROM ExportList WHERE SKU > 1";
        $this->MCommon->executeQuery($eliminame,'Inventory');
    }

    function saveData()
    {
        $comment = $_POST['CommentField'];
        $order = $_POST['OrderNumber'];
         
        $SQL = "UPDATE ExportList SET CommentField = '{$comment}' WHERE OrderNumber = {$order}";
        $this->MCommon->executeQuery($SQL,'Inventory');
    }

}

?>