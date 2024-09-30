<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class PendingShipment extends BP_Controller {

    public function __construct() {
        parent::__construct();

        $is_logged_in = $this->session->userdata('is_logged_in');

        if (!isset($is_logged_in) || $is_logged_in != true) {
            redirect(base_url(), 'refresh');
        }

//        if ($this->MUsers->isValidUser($this->session->userdata('userid'), 882310) != 1) {// 881100 prodcat Access
//            redirect('Catalog/prodcat', 'refresh');
//        }
    }

    function index() { 

        $this->title = "MI Technologiesinc - Pending Shipment";

        $this->description = "Pending Shipment";

        $this->css = array('form.css', 'fluid.css', 'table.css', 'jqueryui/redmond/jquery-ui-1.8.21.custom.css', 'jqgrid/ui.jqgrid.css', 'menu.css',);

        // Define custom javascript
        $this->javascript = array('jqueryui/ui/jquery-ui-1.8.21.custom.js', 'jqgrid/i18n/grid.locale-en.js', 'jqgrid/jquery.jqGrid.min.js', 'jqgrid/jquery.jqGrid.fluid.js', 'site.js');

        $this->load->library('Layout');
        $menu = new Layout;

        $data = array( 
            'menu' => $menu->show_menu($this->MUsers->getUserFullName($this->session->userdata('userid'))),
            'from' => '/Orders/pendingshipment/',
            'caption' => 'Pending Shipment',
            'export' => 'Pending_Shipment',
            'sort' => 'desc',
            'cartname' => $this->MCatalog->fillCartName(),
            'shipping' => $this->MCatalog->fillShipingMethod(),
            'baseUrl' => base_url(),
            'selectedcart' => 0,
        );

        $data['colNames'] = "['OrderDate','OrderNumber','WebOrderNumber','ShippingMethod','OrderStatus','Company','Name','CustomerID','Cart','Notes','Comments']";

        $data['colModel'] = "[
                {name:'OrderDate',index:'OrderDate', width:60, align:'center'},
                {name:'OrderNumber',index:'OrderNumber', width:60, align:'center', formatter: formatLink,sortable:true  },
                {name:'WebOrderNumber',index:'WebOrderNumber', width:80, align:'center'},  
                {name:'ShippingMethod',index:'ShippingMethod', width:60, align:'center'},  
                {name:'OrderStatus',index:'OrderStatus', width:70, align:'center'}, 
                {name:'Company',index:'Company', width:70, align:'center'}, 
                {name:'Name',index:'Name', width:70, align:'center'}, 
                {name:'CustomerID',index:'CustomerID', width:80, align:'left',hidden:true}, 
                {name:'Cart',index:'Cart', width:70, align:'center'}, 
                {name:'Notes',index:'Notes', width:150, align:'left', editable:true}, 
                {name:'Comments',index:'Comments', width:150, align:'left'},   

          	]";


        $data['colNames1'] = "['ShippingMethod','PendingQty']";

        $data['colModel1'] = "[
                {name:'ShippingMethod',index:'ShippingMethod', width:60, align:'center'},
                {name:'PendingQty',index:'PendingQty', width:60, align:'center'},
            ]";

        $data['colNames2'] = "['SKU','Product','Location','OrderDate','OrderNumber','ShippingMethod','Company','Name','Cart']";

        $data['colModel2'] = "[
                {name:'SKU',index:'SKU', width:60, align:'center'},
                {name:'Product',index:'Product', width:60, align:'center' },
                {name:'Location',index:'Location', width:80, align:'center'},  
                {name:'OrderDate',index:'OrderDate', width:60, align:'center'},  
                {name:'OrderNumber',index:'OrderNumber', width:70, align:'center'}, 
                {name:'ShippingMethod',index:'ShippingMethod', width:70, align:'center'}, 
                {name:'Company',index:'Company', width:70, align:'center'}, 
                {name:'Name',index:'Name', width:80, align:'left',hidden:true}, 
                {name:'Cart',index:'Cart', width:70, align:'center'}, 
            ]";



      //datos extra para el detalle de la orden
        $data['formatLink'] = '/Orders/orderdetails/?cid=' . $data['selectedcart'].'&from=1';
            
        $this->build_content($data);
        $this->render_page();
    }

    function getData() {

        $page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1; // get the requested page
        $limit = isset($_REQUEST['rows']) ? $_REQUEST['rows'] : 10; // get how many rows we want to have into the gri
        $sidx = isset($_REQUEST['sidx']) ? $_REQUEST['sidx'] : 'ID'; // get index row - i.e. user click to sort
        $sord = isset($_REQUEST['sord']) ? $_REQUEST['sord'] : 'asc';
        
        $search = $this->input->get('ds');
        $cartname = $this->input->get('car');
        $shipping = $this->input->get('shi');
        
        $select = "SELECT PS.[OrderDate]
        		   ,PS.[OrderNumber]
        		   ,PS.[WebOrderNumber]
        		   ,PS.[ShippingMethod]
        		   ,PS.[OrderStatus]
        		   ,PS.[Company]
        		   ,PS.[Name]
        		   ,O.[CustomerID]
        		   ,PS.[Cart]
                   ,PS.[Notes]
                   ,O.[Comments] ";
        		   
        $selectSlice = "SELECT OrderDate
        		   ,OrderNumber
        		   ,WebOrderNumber
        		   ,ShippingMethod
        		   ,OrderStatus
        		   ,Company
        		   ,Name
        		   ,CustomerID
        		   ,Cart 
                   ,Notes
                   ,Comments";
        		   
       	$from = " FROM ";
       	$table = " [Inventory].[dbo].[PendingShipments] AS PS";
       	$join = " INNER JOIN [OrderManager].[dbo].[Orders] as O ON O.OrderNumber = PS.OrderNumber";
        		          
        
        $where = '';
        $wherefields = array('PS.[OrderDate]','PS.[OrderNumber]','PS.[WebOrderNumber]','PS.[OrderStatus]','PS.[Company]','PS.[Name]','O.[Comments]');

        $where .= $this->MCommon->concatAllWerefields($wherefields, $search);

     //   $where .=  " and ((PS.OrderStatus <> 'Canceled') || (PS.OrderStatus <> 'Refunded'))";
        $where .=  " and (PS.OrderStatus <> 'Canceled') and (PS.OrderStatus <> 'Refunded') and (PS.[Cart] NOT LIKE '%FBA%')";

        if ($cartname){
            $where .= " and (PS.[Cart] = '{$cartname}') ";
        }



        if($shipping){
            $where .= " and (PS.[ShippingMethod] = '{$shipping}') ";
        }

		$SQL = "{$select}{$from}{$table}{$join}{$where}";
	
        
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
        
        
        $SQL = "WITH mytable AS ($select , ROW_NUMBER() OVER (ORDER BY PS.[OrderDate] {$sord}) AS RowNumber
                                FROM {$table}{$join}{$where})
               {$selectSlice}, RowNumber
               FROM mytable
                WHERE RowNumber BETWEEN {$start} AND {$finish}";
 
       
        $result = $this->MCommon->getSomeRecords($SQL);

        $responce = new stdClass();
        $responce->page = $page;
        $responce->total = $total_pages;
        $responce->records = $count;


        $i = 0;
        foreach ($result as $row) {
            $responce->rows[$i]['id'] = $row['OrderNumber'];
            $responce->rows[$i]['cell'] = array($row['OrderDate'],
                $row['OrderNumber'] = utf8_encode($row['OrderNumber']),
                $row['WebOrderNumber'] = utf8_encode($row['WebOrderNumber']),
                $row['ShippingMethod'] = utf8_encode($row['ShippingMethod']),
                $row['OrderStatus'] = utf8_encode($row['OrderStatus']),
                $row['Company'] = utf8_encode($row['Company']),
                $row['Name']= utf8_encode($row['Name']),
                $row['CustomerID'] = utf8_encode($row['CustomerID']),
                $row['Cart'] = utf8_encode($row['Cart']),
                $row['Notes']= utf8_encode($row['Notes']),
                $row['Comments']= utf8_encode($row['Comments']),
  
            );
            $i++;
        }

        echo json_encode($responce);
    }



    function getData1() {

        $page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1; // get the requested page
        $limit = isset($_REQUEST['rows']) ? $_REQUEST['rows'] : 10; // get how many rows we want to have into the gri
        $sidx = isset($_REQUEST['sidx']) ? $_REQUEST['sidx'] : 'ID'; // get index row - i.e. user click to sort
        $sord = isset($_REQUEST['sord']) ? $_REQUEST['sord'] : 'asc';
        
	$select = " SELECT DISTINCT PST.[ShippingMethod] AS 'ShippingMethod'
                        ,(SELECT Count(PST2.[ShippingMethod]) AS 'PendingQty'
                          FROM [Inventory].[dbo].[PendingShipmentsTable] AS PST2
                          WHERE [PST2].[ShippingMethod] = [PST].[ShippingMethod]
                          AND OrderStatus NOT IN ('Canceled',
                          'Drop Shipment Canceled',
                          'Drop Shipment Ordered',
                          'Item Returned',
                          'Refunded',
                          'Shipped'
                        )) as PendingQty
                    FROM [Inventory].[dbo].[PendingShipmentsTable] AS PST";        

       // $select = " SELECT DISTINCT PST.[ShippingMethod] AS 'ShippingMethod'
       //                 ,(SELECT Count(PST2.[ShippingMethod]) AS 'PendingQty'
        //                  FROM [Inventory].[dbo].[PendingShipmentsTable] AS PST2
        //                  WHERE [PST2].[ShippingMethod] = [PST].[ShippingMethod]) as PendingQty
        //            FROM [Inventory].[dbo].[PendingShipmentsTable] AS PST";
        
        $result = $this->MCommon->getSomeRecords($select);

       // print_r($result);
        

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
            $responce->rows[$i]['id'] = $row['ShippingMethod'];
            $responce->rows[$i]['cell'] = array($row['ShippingMethod'],
                $row['PendingQty']
            );
            $i++;
        }

        echo json_encode($responce);
    }


    function getData2() {

        $page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1; // get the requested page
        $limit = isset($_REQUEST['rows']) ? $_REQUEST['rows'] : 10; // get how many rows we want to have into the gri
        $sidx = isset($_REQUEST['sidx']) ? $_REQUEST['sidx'] : 'ID'; // get index row - i.e. user click to sort
        $sord = isset($_REQUEST['sord']) ? $_REQUEST['sord'] : 'asc';

        $select = "SELECT [SKU]
                   ,[Product]
                   ,Replace((CASE WHEN LEFT([Location],2) = ',' THEN Replace([Location],',','')
                   WHEN LEFT([Location],1) = '-' THEN Right([Location],LEN([Location])-1)
                   ELSE [Location] END),'-',',') AS Location
                   ,[OrderDate]
                   ,[OrderNumber]
                   ,[ShippingMethod]
                   ,[Company]
                   ,[Name]
                   ,[Cart] ";
                   
        $selectSlice = "SELECT SKU
                   ,Product
                   ,Location
                   ,OrderDate
                   ,OrderNumber
                   ,ShippingMethod
                   ,Company
                   ,Name
                   ,Cart ";
                   
        $from = " FROM ";
        $table = " [Inventory].[dbo].[PendingShipmentItems] ";
        $where = " WHERE Cart NOT LIKE '%FBA%'";

        $SQL = "{$select}{$from}{$table}";
        
        $result = $this->MCommon->getSomeRecords($SQL);
        
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
        $start = ($start < 0) ? 0 : $start;
        $finish = $start + $limit;
        
        
        $SQL = "WITH mytable AS ($select , ROW_NUMBER() OVER (ORDER BY Location {$sord}) AS RowNumber
                                FROM {$table}{$where})
               {$selectSlice}, RowNumber
               FROM mytable
                WHERE RowNumber BETWEEN {$start} AND {$finish}";


 
       
        $result = $this->MCommon->getSomeRecords($SQL);


        $responce = new stdClass();
        $responce->page = $page;
        $responce->total = $total_pages;
        $responce->records = $count;

        $i = 0;
        foreach ($result as $row) {
            $responce->rows[$i]['id'] = $row['RowNumber'];
            $responce->rows[$i]['cell'] = array($row['SKU'],
                $row['Product'] = utf8_encode($row['Product']),
                $row['Location'] = utf8_encode($row['Location']),
                $row['OrderDate'] = utf8_encode($row['OrderDate']),
                $row['OrderNumber'] = utf8_encode($row['OrderNumber']),
                $row['ShippingMethod'] = utf8_encode($row['ShippingMethod']),
                $row['Company'] = utf8_encode($row['Company']),
                $row['Name'] = utf8_encode($row['Name']),
                $row['Cart'] = utf8_encode($row['Cart']),
  
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

    function saveInfo(){

        $message = $this->input->post('Notes');
        $order = $this->input->post('id');
        $SQL = "UPDATE [OrderManager].[dbo].[Orders] SET GiftMessage ='".$message."' WHERE OrderNumber = '".$order."'";

        echo $SQL;

         $this->MCommon->saveRecord($SQL,'OrderManager');
        
    }

}

?>
