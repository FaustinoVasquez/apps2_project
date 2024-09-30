<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Ordersalesdata extends BP_Controller {

    public function __construct() {
        parent::__construct();

        $is_logged_in = $this->session->userdata('is_logged_in');

        if (!isset($is_logged_in) || $is_logged_in != true) {
            redirect(base_url(), 'refresh');
        }

    }

    function index() {

        // Define Meta
        $this->title = "MI Technologiesinc - Order and Sales Data";

        $this->description = "AOrder and Sales Data";

        $this->css = array('menu.css', 'form.css', 'fluid.css', 'table.css', 'jqueryui/redmond/jquery-ui-1.8.21.custom.css', 'jqgrid/ui.jqgrid.css');

        // Define custom javascript
        $this->javascript = array('jqueryui/ui/jquery-ui-1.8.21.custom.js', 'jqgrid/i18n/grid.locale-en.js', 'jqgrid/jquery.jqGrid.min.js', 'jqgrid/jquery.jqGrid.fluid.js', 'popup.js','site.js');

        //Cargamos la libreria donde esta el menu
        $this->load->library('Layout');
        //Creamos un nuevo layout->menu
        $menu = new Layout;

        $data = array(
            'menu' => $menu->show_menu($this->MUsers->getUserFullName($this->session->userdata('userid'))),
            'title' => 'MI Technologiesinc - Order and Sales Data',
            'from' => '/Orders/ordersalesdata/',
            'caption' => 'Sales Data',
            'caption1' => 'Catalog',
            'caption2' => 'Categories',
            'caption3' => 'Mapping',
            'datefrom' => $this->MCommon->InitMonth(date("m/d/Y")),
            'dateto' => date("m/d/Y"),
        );


        $columns = array(
            'OrderDate'=>
                array('colName'  => 'OrderDate',
                      'colModel' => "{ name:'OrderDate',index:'OrderDate',width:100,align:'center'}"),
            'OrderNumber'=>
                array('colName'  => 'OrderNumber',
                      'colModel' => "{ name:'OrderNumber',index:'OrderNumber',width:70,align:'center'}"),
            'DateShipped'=>
                array('colName'  => 'DateShipped',
                      'colModel' => "{ name:'DateShipped',index:'DateShipped',width:100, align:'center'}"),
            'CompanyName'=>
                array('colName'  => 'CompanyName',
                      'colModel' => "{ name:'CompanyName',index:'CompanyName',width:90, align:'center'}"),
            'Name'=>
                array('colName'  => 'Name',
                      'colModel' => "{ name:'Name',index:'Name', width:150,align:'left'}"),
            'CartName'=>
                array('colName'  => 'CartName',
                      'colModel' => "{ name:'CartName',index:'CartName',width:150,align:'left'}"),
            'SKU'=>
                array('colName'  => 'SKU',
                      'colModel' => "{ name:'SKU',index:'SKU',width:60,align:'center'}"),
            'Product'=>
                array('colName'  => 'Product',
                      'colModel' => "{ name:'Product',index:'Product',width:270,align:'left'}"),
            'LineItem'=>
                array('colName'  => 'LineItem',
                      'colModel' => "{ name:'LineItem',index:'LineItem',width:60,align:'center'}"),
            'PricePerUnit'=>
                array('colName'  => 'PricePerUnit',
                      'colModel' => "{ name:'PricePerUnit',index:'PricePerUnit',width:80,align:'right'}"),
            'QuantityShipped'=>
                array('colName'  => 'QuantityShipped',
                      'colModel' => "{ name:'QuantityShipped',index:'QuantityShipped',width:70, align:'center'}"),
            'QuantityReturned'=>
                array('colName'  => 'QuantityReturned',
                      'colModel' => "{ name:'QuantityReturned',index:'QuantityReturned',width:70, align:'center'}"),
            'ShippingPrice'=>
                array('colName'  => 'Shipping Price',
                      'colModel' => "{ name:'ShippingPrice',index:'ShippingPrice', width:70,align:'right'}"),
            'SalesTax'=>
                array('colName'  => 'Sales Tax',
                      'colModel' => "{ name:'SalesTax',index:'SalesTax',width:70,align:'right'}"),
            'SalesTaxAdjustment'=>
                array('colName'  => 'Sales Tax Adjustment',
                      'colModel' => "{ name:'SalesTaxAdjustment',index:'SalesTaxAdjustment',width:70,align:'right'}"),
            'Discount'=>
                array('colName'  => 'Discount',
                      'colModel' => "{ name:'Discount',index:'Discount',width:70,align:'right'}"),
            'Coupon'=>
                array('colName'  => 'Coupon',
                      'colModel' => "{ name:'Coupon',index:'Coupon',width:80,align:'right'}"),
            'Surcharge'=>
                array('colName'  => 'Surcharge',
                      'colModel' => "{ name:'Surcharge',index:'Surcharge',width:80,align:'right'}"),
            'OrderTotal'=>
                array('colName'  => 'Order Total',
                      'colModel' => "{ name:'OrderTotal',index:'OrderTotal',width:80, align:'right'}"),
            'OrderStatus'=>
                array('colName'  => 'Order Status',
                      'colModel' => "{ name:'OrderStatus',index:'OrderStatus',width:80, align:'center'}"),
            'PayMethod'=>
                array('colName'  => 'Pay Method',
                      'colModel' => "{ name:'PayMethod',index:'PayMethod', width:80,align:'center'}"),

        );

        $columns1 = array(
            'ID'=>
                array('colName'  => 'ID',
                      'colModel' => "{ name:'ID',index:'ID',align:'center'}"),
            'Name'=>
                array('colName'  => 'Name',
                      'colModel' => "{ name:'Name',index:'Name',align:'center'}"),
        );

        $columns2 = array(
            'ID'=>
                array('colName'  => 'ID',
                      'colModel' => "{ name:'ID',index:'ID',width:70,align:'center'}"),
             'ParentID'=>
                array('colName'  => 'ParentID',
                      'colModel' => "{ name:'ParentID',index:'ParentID',width:70,align:'center'}"),
            'Name'=>
                array('colName'  => 'Name',
                      'colModel' => "{ name:'Name',index:'Name',width:270,align:'left'}"),
            'Description'=>
                array('colName'  => 'Description',
                      'colModel' => "{ name:'Description',index:'Description',width:170,align:'left'}"),
             'Active'=>
                array('colName'  => 'Active',
                      'colModel' => "{ name:'Active',index:'Active',width:70,align:'center'}"),
            'CategoryName'=>
                array('colName'  => 'CategoryName',
                      'colModel' => "{ name:'CategoryName',index:'CategoryName',width:70,align:'center'}"),
            'RIAIsActive'=>
                array('colName'  => 'RIAIsActive',
                      'colModel' => "{ name:'RIAIsActive',index:'RIAIsActive',width:70,align:'center'}"),
             'RIATargetDays'=>
                array('colName'  => 'RIATargetDays',
                      'colModel' => "{ name:'RIATargetDays',index:'RIATargetDays',width:70,align:'center'}"),
            'RIAFrequency'=>
                array('colName'  => 'RIAFrequency',
                      'colModel' => "{ name:'RIAFrequency',index:'RIAFrequency',width:70,align:'center'}"),
            'RIALastRunDate'=>
                array('colName'  => 'RIALastRunDate',
                      'colModel' => "{ name:'RIALastRunDate',index:'RIALastRunDate',width:100,align:'center'}"),
             'RIALastRunDuration'=>
                array('colName'  => 'RIALastRunDuration',
                      'colModel' => "{ name:'RIALastRunDuration',index:'RIALastRunDuration',width:70,align:'center'}"),
            'RIAOrderingDay'=>
                array('colName'  => 'RIAOrderingDay',
                      'colModel' => "{ name:'RIAOrderingDay',index:'RIAOrderingDay',width:70,align:'center'}"),
        );

        $columns3 = array(
            'ID'=>
                array('colName'  => 'ID',
                      'colModel' => "{ name:'ID',index:'ID',align:'center'}"),
            'SUBSKU'=>
                array('colName'  => 'SubSKU',
                      'colModel' => "{ name:'SUBSKU',index:'SUBSKU',align:'center'}"),
        );




        //Cargamos la libreria comumn
        $this->load->library('common');

        $data['colNames'] = $this->common->CreateColname($columns, 'colName');
        $data['colModel'] = $this->common->CreateColmodel($columns, 'colModel');

        $data['colNames1'] = $this->common->CreateColname($columns1, 'colName');
        $data['colModel1'] = $this->common->CreateColmodel($columns1, 'colModel');

        $data['colNames2'] = $this->common->CreateColname($columns2, 'colName');
        $data['colModel2'] = $this->common->CreateColmodel($columns2, 'colModel');

        $data['colNames3'] = $this->common->CreateColname($columns3, 'colName');
        $data['colModel3'] = $this->common->CreateColmodel($columns3, 'colModel');

        $this->build_content($data);
        $this->render_page();
    }






    function getData() {
        //Variables que envia el grid, incializarlas si estan vacias
        $page = !empty($_REQUEST['page']) ? $_REQUEST['page'] : '1'; // get the requested page
        $limit = !empty($_REQUEST['rows']) ? $_REQUEST['rows'] : '10'; // get how many rows we want to have into the gri
        $sidx = !empty($_REQUEST['sidx']) ? $_REQUEST['sidx'] : 'o.OrderDate'; // get index row - i.e. user click to sort
        $sord = !empty($_REQUEST['sord']) ? $_REQUEST['sord'] : 'asc';
        $examp = isset($_REQUEST['q']) ? $_REQUEST['q'] : 1;  //query number
        //variables que envia el formulario inicializarlas si no existen
        $search = !empty($_GET['ds']) ? $_GET['ds'] : '';
        $datefrom = !empty($_GET['df']) ? $_GET['df'] : $this->MCommon->InitMonth(date("m/d/Y"));
        $dateto = !empty($_GET['dt']) ? $_GET['dt'] : date("m/d/Y");



        //Select utilizado para realizar el conteo del numero de registros devueltos por la consulta.
        $selectCount = 'Select count(*) as rowNum';

        $select = "SELECT
                  o.[OrderDate], 
                  o.[OrderNumber], 
                  trk.[PickupDate] AS 'DateShipped',
                  ISNULL(o.[Company], '') AS 'CompanyName',
                  o.[Name],
                  sc.[CartName],
                  od.[SKU],
                  od.[Product],
                  od.[ItemNumber] AS 'LineItem',
                  od.[PricePerUnit],
                  od.[QuantityShipped], 
                  od.[QuantityReturned], 
                  ISNULL(CAST((Select TOP(1) PricePerUnit FROM OrderManager.[dbo].[Order Details] WHERE SKU = 'Shipping' AND OrderNumber = od.[OrderNumber]) AS VARCHAR), '-')  AS 'ShippingPrice',
                  ISNULL(CAST((Select TOP(1) PricePerUnit FROM OrderManager.[dbo].[Order Details] WHERE SKU LIKE 'Sales tax 1' AND OrderNumber = od.[OrderNumber]) AS VARCHAR), '-')  AS 'SalesTax',
                  ISNULL(CAST((Select TOP(1) PricePerUnit FROM OrderManager.[dbo].[Order Details] WHERE SKU LIKE 'Sales Tax 3' AND OrderNumber = od.[OrderNumber]) AS VARCHAR), '-') AS 'SalesTaxAdjustment',
                  ISNULL(CAST((Select TOP(1) PricePerUnit FROM OrderManager.[dbo].[Order Details] WHERE SKU LIKE 'Discount' AND OrderNumber = od.[OrderNumber]) AS VARCHAR), '-') AS 'Discount',
                  ISNULL(CAST((Select TOP(1) PricePerUnit FROM OrderManager.[dbo].[Order Details] WHERE SKU LIKE 'Coupon' AND OrderNumber = od.[OrderNumber]) AS VARCHAR), '-') AS 'Coupon',
                  ISNULL(CAST((Select TOP(1) PricePerUnit FROM OrderManager.[dbo].[Order Details] WHERE SKU LIKE 'Surcharge' AND OrderNumber = od.[OrderNumber]) AS VARCHAR), '-') AS 'Surcharge',
                  o.[GrandTotal] AS 'OrderTotal',
                  o.[OrderStatus] AS 'OrderStatus',
                  ISNULL(pm.[Method], '-') AS 'PayMethod' ";

        $selectSlice = "SELECT
                --ORDER INFORMATION
                       OrderDate
                      ,OrderNumber
                      ,DateShipped
                      ,CompanyName
                      ,Name
                      ,CartName
                      ,SKU
                      ,Product
                      ,LineItem 
                      ,PricePerUnit 
                      ,QuantityShipped 
                      ,QuantityReturned 
                      ,ShippingPrice 
                      ,SalesTax 
                      ,SalesTaxAdjustment 
                      ,Discount 
                      ,Coupon 
                      ,Surcharge 
                      ,OrderTotal 
                      ,OrderStatus 
                      ,PayMethod ";

        $from = ' from ';
        $table = '  OrderManager.dbo.[Order Details] od ';
        $join =  ' LEFT JOIN OrderManager.dbo.Orders o
                    ON od.OrderNumber = o.OrderNumber
                   LEFT JOIN OrderManager.dbo.Tracking trk
                    ON o.OrderNumber = trk.OrderNum
                   LEFT JOIN OrderManager.dbo.ShoppingCarts sc
                    ON o.CartID = sc.ID
                   LEFT JOIN [OrderManager].[dbo].[PaymentMethods] pm
                    ON o.PayType = pm.ID ';
        $where = '';
        $wherefields = array('o.OrderDate'
                            ,'o.OrderNumber'
                            ,'o.Company'
                            ,'o.Name'
                            ,'sc.CartName'
                            ,'od.SKU'
                            ,'od.Product'
                            ,'od.ItemNumber'
                            ,'pm.Method'
                            );

        //creamos el were concatenando todos los nombres de campos y las palabras de busqueda
        $where .=$this->MCommon->concatAllWerefields($wherefields, $search);

        $where .= " and ( (o.OrderDate >= '{$datefrom}' AND o.OrderDate <= '{$dateto}') OR (trk.PickupDate >= '{$datefrom}' AND trk.PickupDate <= '{$dateto}')  )
                    AND od.Adjustment = '0' ";

       // $orderby = ' ORDER BY o.OrderDate ';

        $SQL = "{$selectCount}{$from}{$table}{$join}{$where}";


        $query = $this->db->query($SQL);

        $count = $query->row('rowNum');




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


        $query = $this->db->query($SQL);

       // print_r($query->result_array());

        $responce = new stdClass();
        $responce->page = $page;
        $responce->total = $total_pages;
        $responce->records = $count;
        $i = 0;


        foreach ($query->result_array() as $row) {
            $responce->rows[$i]['id'] = $row['RowNumber'];
            $responce->rows[$i]['cell'] = array($row['OrderDate'],
                      $row['OrderNumber'],
                      $row['DateShipped'],
                      $row['CompanyName'],
                      $row['Name'] = utf8_encode($row['Name']),
                      $row['CartName'],
                      $row['SKU'],
                      $row['Product'],
                      $row['LineItem'],
                      $row['PricePerUnit'],
                      $row['QuantityShipped'],
                      $row['QuantityReturned'],
                      $row['ShippingPrice'],
                      $row['SalesTax'],
                      $row['SalesTaxAdjustment'],
                      $row['Discount'],
                      $row['Coupon'],
                      $row['Surcharge'],
                      $row['OrderTotal'],
                      $row['OrderStatus'],
                      $row['PayMethod'],
            );
            $i++;
        }

        echo json_encode($responce);
    }



        function getData1() {
        //Variables que envia el grid, incializarlas si estan vacias
        $page = !empty($_REQUEST['page']) ? $_REQUEST['page'] : '1'; // get the requested page
        $limit = !empty($_REQUEST['rows']) ? $_REQUEST['rows'] : '10'; // get how many rows we want to have into the gri
        $sidx = !empty($_REQUEST['sidx']) ? $_REQUEST['sidx'] : 'pc.ID'; // get index row - i.e. user click to sort
        $sord = !empty($_REQUEST['sord']) ? $_REQUEST['sord'] : 'asc';
        $examp = isset($_REQUEST['q']) ? $_REQUEST['q'] : 1;  //query number
        //variables que envia el formulario inicializarlas si no existen
        $search = !empty($_GET['ds']) ? $_REQUEST['ds'] : '';


        //Select utilizado para realizar el conteo del numero de registros devueltos por la consulta.
        $selectCount = 'Select count(*) as rowNum';

        $select = "Select pc.ID, pc.Name ";

        $selectSlice = "SELECT ID,Name ";

        $from = ' from ';
        $table = '  Inventory.dbo.ProductCatalog pc ';
        $join =  ' LEFT JOIN Inventory.dbo.Categories ctg 
                    ON pc.CategoryID = ctg.ID  ';
        $where = '';
        $wherefields = array( );

        //creamos el were concatenando todos los nombres de campos y las palabras de busqueda
        $where .=$this->MCommon->concatAllWerefields($wherefields, $search);

        $where .= " and ctg.ParentID <> '1' AND ctg.ParentID <> '2' AND ctg.ParentID <> '3' AND ctg.ParentID <> '4' ";

        $SQL = "{$selectCount}{$from}{$table}{$join}{$where}";


        $query = $this->db->query($SQL);

        $count = $query->row('rowNum');



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


        $query = $this->db->query($SQL);

        $responce = new stdClass();
        $responce->page = $page;
        $responce->total = $total_pages;
        $responce->records = $count;
        $i = 0;


        foreach ($query->result_array() as $row) {
            $responce->rows[$i]['id'] = $row['RowNumber'];
            $responce->rows[$i]['cell'] = array($row['ID'],
                      $row['Name'] = utf8_encode($row['Name']),
            );
            $i++;
        }

        echo json_encode($responce);
    }




        function getData2() {
        //Variables que envia el grid, incializarlas si estan vacias
        $page = !empty($_REQUEST['page']) ? $_REQUEST['page'] : '1'; // get the requested page
        $limit = !empty($_REQUEST['rows']) ? $_REQUEST['rows'] : '10'; // get how many rows we want to have into the gri
        $sidx = !empty($_REQUEST['sidx']) ? $_REQUEST['sidx'] : 'ctg.ID'; // get index row - i.e. user click to sort
        $sord = !empty($_REQUEST['sord']) ? $_REQUEST['sord'] : 'asc';
        $examp = isset($_REQUEST['q']) ? $_REQUEST['q'] : 1;  //query number
        //variables que envia el formulario inicializarlas si no existen
        $search = !empty($_GET['ds']) ? $_REQUEST['ds'] : '';


        //Select utilizado para realizar el conteo del numero de registros devueltos por la consulta.
        $selectCount = 'Select count(*) as rowNum';



        $select = "Select ctg.ID
                        ,ctg.ParentID
                        ,ctg.Name
                        ,ctg.Description
                        ,ctg.Active
                        ,ctg.CategoryName
                        ,ctg.RIAIsActive
                        ,ctg.RIATargetDays
                        ,ctg.RIAFrequency
                        ,ctg.RIALastRunDate
                        ,ctg.RIALastRunDuration
                        ,ctg.RIAOrderingDay
                        ";

        $selectSlice = "SELECT ID
                              ,ParentID
                              ,Name
                              ,Description
                              ,Active
                              ,CategoryName
                              ,RIAIsActive
                              ,RIATargetDays
                              ,RIAFrequency
                              ,RIALastRunDate
                              ,RIALastRunDuration
                              ,RIAOrderingDay ";

        $from = ' from ';
        $table = '  Inventory.dbo.Categories ctg ';
        $where = '';
        $wherefields = array( );

        //creamos el were concatenando todos los nombres de campos y las palabras de busqueda
        $where .=$this->MCommon->concatAllWerefields($wherefields, $search);

        $where .= " and  ctg.ParentID <> '1' OR ctg.ID = '10' ";

        $SQL = "{$selectCount}{$from}{$table}{$where}";


        $query = $this->db->query($SQL);

        $count = $query->row('rowNum');



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


        $query = $this->db->query($SQL);

        $responce = new stdClass();
        $responce->page = $page;
        $responce->total = $total_pages;
        $responce->records = $count;
        $i = 0;


        foreach ($query->result_array() as $row) {
            $responce->rows[$i]['id'] = $row['RowNumber'];
            $responce->rows[$i]['cell'] = array($row['ID'],
                              $row['ParentID'],
                              $row['Name'],
                              $row['Description'],
                              $row['Active'],
                              $row['CategoryName'],
                              $row['RIAIsActive'],
                              $row['RIATargetDays'],
                              $row['RIAFrequency'],
                              $row['RIALastRunDate'],
                              $row['RIALastRunDuration'],
                              $row['RIAOrderingDay'],
            );
            $i++;
        }

        echo json_encode($responce);
    }



        function getData3() {
        //Variables que envia el grid, incializarlas si estan vacias
        $page = !empty($_REQUEST['page']) ? $_REQUEST['page'] : '1'; // get the requested page
        $limit = !empty($_REQUEST['rows']) ? $_REQUEST['rows'] : '10'; // get how many rows we want to have into the gri
        $sidx = !empty($_REQUEST['sidx']) ? $_REQUEST['sidx'] : ' PC.ID'; // get index row - i.e. user click to sort
        $sord = !empty($_REQUEST['sord']) ? $_REQUEST['sord'] : 'asc';
        $examp = isset($_REQUEST['q']) ? $_REQUEST['q'] : 1;  //query number
        //variables que envia el formulario inicializarlas si no existen
        $search = !empty($_GET['ds']) ? $_REQUEST['ds'] : '';


        //Select utilizado para realizar el conteo del numero de registros devueltos por la consulta.
        $selectCount = 'Select count(*) as rowNum';

        $select = " SELECT PC.[ID], ADPARENT.[SUBSKU] ";

        $selectSlice = "SELECT ID,SUBSKU ";

        $from = ' from ';
        $table = ' Inventory.dbo.ProductCatalog AS PC ';
        $join =  ' LEFT OUTER JOIN Inventory.dbo.AssemblyDetails AS ADPARENT ON (PC.ID = ADPARENT.ProductCatalogID) 
                   LEFT OUTER JOIN Inventory.dbo.ProductCatalog AS PCSUBSKU ON (ADPARENT.SUBSKU = PCSUBSKU.ID)
                   LEFT OUTER JOIN Inventory.dbo.Categories AS SUBSKUCAT ON (PCSUBSKU.CategoryID = SUBSKUCAT.ID)  ';
        $where = '';
        $wherefields = array( );

        //creamos el were concatenando todos los nombres de campos y las palabras de busqueda
        $where .=$this->MCommon->concatAllWerefields($wherefields, $search);

        $where .= " and PC.CategoryID IN ('5','10') AND SUBSKUCAT.ParentID IN ('1','3') ";

        $SQL = "{$selectCount}{$from}{$table}{$join}{$where}";


        $query = $this->db->query($SQL);

        $count = $query->row('rowNum');



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


        $query = $this->db->query($SQL);

        $responce = new stdClass();
        $responce->page = $page;
        $responce->total = $total_pages;
        $responce->records = $count;
        $i = 0;


        foreach ($query->result_array() as $row) {
            $responce->rows[$i]['id'] = $row['RowNumber'];
            $responce->rows[$i]['cell'] = array($row['ID'],
                      $row['SUBSKU'] = utf8_encode($row['SUBSKU']),
            );
            $i++;
        }

        echo json_encode($responce);
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


}
?>

