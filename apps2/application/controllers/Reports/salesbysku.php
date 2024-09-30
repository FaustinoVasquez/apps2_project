<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Salesbysku extends BP_Controller {

    public function __construct() {
        parent::__construct();

        $is_logged_in = $this->session->userdata('is_logged_in');

        if (!isset($is_logged_in) || $is_logged_in != true) {
            redirect(base_url(), 'refresh');
        }
        
        // if ($this->MUsers->isValidUser($this->session->userdata('userid'), 883300) != 1) {
        //        redirect('Catalog/prodcat', 'refresh');
        //    }
    }

    public function index() {
        //Cargamos la base de datos de OrderManager
        $this->load->model('MOrders', '', TRUE);

        // Define Meta
        $this->title = "MI Technologiesinc - Sales by SKU";

        $this->description = "Sales by SKU";

        // Define custom CSS
        $this->css = array('menu.css', 'fluid.css', 'jqueryui/redmond/jquery-ui-1.8.21.custom.css', 'jqgrid/ui.jqgrid.css', 'form.css', 'ventanas-modales.css');

        // Define custom javascript
        $this->javascript = array('jqueryui/ui/jquery-ui-1.8.21.custom.js', 'jqgrid/i18n/grid.locale-en.js', 'jqgrid/jquery.jqGrid.min.js', 'jqgrid/jquery.jqGrid.fluid.js', 'popup.js');
        //Cargamos la libreria donde esta el menu
        $this->load->library('Layout');
        //Creamos un nuevo layout->menu
        $menu = new Layout;

        
        
        $data = array(
            'menu' => $menu->show_menu($this->MUsers->getUserFullName($this->session->userdata('userid'))),
            'from' => '/Reports/salesbysku/',
            'nameGrid' => 'salesbysku',
            'namePager' => 'salesbyskuPager',
            'caption' => 'Sales by SKU',
            'subgrid' => 'false',
            'sort' => 'desc',
            'search' => '',
            'statusOptions' => Array(0 => "All Status", 1 => "Shipped", 2 => "Pending"),
            'status' => 0,
            'datefrom' => $this->MCommon->lastweek(),
            'dateto' => date("m/d/Y"),
            'export' => 'salesbysku',
            'cartid' => $this->MUsers->getCartName($this->session->userdata('userid')),
            'customerid' => $this->MUsers->getCustomerId($this->session->userdata('userid')),
            'productLines' => '0',
            'productLineOptions' => $this->MCatalog->fillProductLines(),
            'selectedcart' => 0,
            
        );


        //Cargamos al arreglo los colnames
        $data['colNames'] = "['SKU','ProductName','Orders Qty','Items Ordered ','Total Sale', 'Avg Sales Price']";
        //Cargamos al arreglo los colmodels
        $data['colModel'] = "[
                {name:'SKU',index:'SKU', width:60, align:'center'},
                {name:'ProductName',index:'ProductName', width:250, align:'left'},
                {name:'Oderqty',index:'Oderqty', width:55, align:'center'},
                {name:'SKUqty',index:'SKUqty', width:50, align:'center'},
                {name:'MoneyQty',index:'MoneyQty', width:55, align:'right',formatter:'currency', formatoptions:{prefix:'$', suffix:' ', thousandsSeparator:','}}, 
                {name:'AVGSPr',index:'AVGSPr', width:55, align:'right',formatter:'currency', formatoptions:{prefix:'$', suffix:' ', thousandsSeparator:','}},
  	]";



        
          //Cargamos la libreria comumn
        $this->load->library('common');

        //Reemplazamos los campos del formulario por los que estan en el arreglo $_POST.
        $data = $this->common->fillPost('ALL', $data);
        
        
        
        // Verificamos si el usuario activo no es un partner
        if ($data['customerid'] !=0) {
             //Obtenemos solo el nombre de un carro para usuario y enviamos el Id del usuario activo
            $data['cartOptions'] = $this->MOrders->getCartName($data['cartid']);
         //   $data['customerId'] = $this->MUsers->getCustomerId($this->session->userdata('userid'));

        } else {
            $data['cartOptions'] = $this->MOrders->getCartNames();
        }


        //cadena de busqueda del grid 
        $data['gridSearch'] = 'dataSalesbySku?caid=' . $data['selectedcart'] . '&pl=' . $data['productLines'] . '&ds=' . $data['search'] . '&is=' . $data['status'] .'&cuid=' . $data['customerid']. '&df=' . $data['datefrom'] . '&dt=' . $data['dateto'];

        //datos extra para el detalle de la orden
        $data['formatLink'] = '/Orders/orderdetails/?cid=' . $data['selectedcart'];

        //Generar el contenido....
        $this->build_content($data);
        $this->render_page();
    }

    function dataSalesbySku() {
        $page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1; // get the requested page
        $limit = isset($_REQUEST['rows']) ? $_REQUEST['rows'] : 10; // get how many rows we want to have into the gri
        $sidx = isset($_REQUEST['sidx']) ? $_REQUEST['sidx'] : 'SKU'; // get index row - i.e. user click to sort
        $sord = isset($_REQUEST['sord']) ? $_REQUEST['sord'] : 'desc';

        $this->load->model('MOrders', '', TRUE);

        $dataSearch = isset($_REQUEST['ds']) ? $_GET['ds'] : '';
        $status = '2';
        $cartid = isset($_REQUEST['caid']) ? $_GET['caid'] : '0';
        $customerid = isset($_GET['cuid']) ? $_GET['cuid'] : '';
        $InitialDate = isset($_REQUEST['df']) ? $_GET['df'] : $this->MCommon->lastweek();
        $FinalDate = isset($_REQUEST['dt']) ? $_GET['dt'] : date("m/d/Y");
        $productLine = !empty($_REQUEST['pl']) ? $_REQUEST['pl'] : '0';


        if (!$sidx)
            $sidx = 1;

        $table = '[OrderManager].[dbo].[Orders] a, ';
        $table1 ='[OrderManager].[dbo].[Order Details] b ';
        $where = '';
        $from = ' FROM ';

        $selectCount = 'Select count(*) as rowNum';

             
        
        $select = "SELECT b.sku as SKU, 
                          count(b.sku) as Oderqty, 
                          sum(QuantityShipped) SKUqty, 
                          sum(QuantityShipped * PriceperUnit) as  MoneyQty";
 
         $selectSlice = "SELECT  SKU,[OrderManager].[dbo].fn_Get_OrderDetails_ProductName(SKU) as ProductName, Oderqty, SKUqty, MoneyQty";


        $wherefields = array('SKU', 'product');

         $where .=$this->MCommon->concatAllWerefields($wherefields, $dataSearch);

        
        $where .=' and (a.OrderNumber = b.OrderNumber) and (isnumeric(SKU) = 1 ) and (a.cancelled = 0) and (b.Adjustment = 0) ';

        if ($cartid) {
            $where.=$this->MOrders->filterbycartId($cartid);
        }
       
        if ($customerid != 0) {
            $where.=' and (customerID = ' . $customerid . ')';
        }

        $where.= $this->MOrders->filterbyDate($InitialDate, $FinalDate);
        
        if ($cartid) {
            $where.=$this->MOrders->filterbycartId($cartid);
        }
        
         if ($productLine != 0) {
            $where.= " and ( inventory.dbo.fn_GetProductLineID(SKU) = {$productLine})";
        }

        
        $SQL = "{$select}{$from}{$table}{$table1}{$where} GROUP BY {$sidx}";
        
    
        
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

        
        $SQL= "WITH mytable AS ({$select},ROW_NUMBER() OVER (ORDER BY {$sidx} {$sord}) AS RowNumber
	FROM {$table}{$table1} {$where}	GROUP BY {$sidx}) 
               {$selectSlice},RowNumber
               FROM mytable
               WHERE RowNumber BETWEEN {$start} AND {$finish}";
               
                 
        $result = $this->MCommon->getSomeRecords($SQL);

        
       
        $responce = new stdClass();
        $responce->page = $page;
        $responce->total = $total_pages;
        $responce->records = $count;
        $i = 0;


        foreach ($result as $row) {
            $responce->rows[$i]['id'] = $row['SKU'];
            $responce->rows[$i]['cell'] = array($row['SKU'],
                $row['ProductName'],
                $row['Oderqty'],
                $row['SKUqty'],
                $row['MoneyQty'],
                $row['AVGSPr'] =  $row['MoneyQty']/$row['SKUqty'],
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

    function OrdersData() {
       
        $this->load->model('MOrders', '', TRUE);
        $sku = isset($_REQUEST['sku']) ? $_GET['sku'] : '';
        $page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1; // get the requested page
        $limit = isset($_REQUEST['rows']) ? $_REQUEST['rows'] : 10; // get how many rows we want to have into the gri
        $sidx = isset($_REQUEST['sidx']) ? $_REQUEST['sidx'] : 'OrderNumber'; // get index row - i.e. user click to sort
        $sord = isset($_REQUEST['sord']) ? $_REQUEST['sord'] : 'Desc';
        $InitialDate = isset($_REQUEST['df']) ? $_GET['df'] : $this->MCommon->lastweek();
        $FinalDate = isset($_REQUEST['dt']) ? $_GET['dt'] : date("m/d/Y");
        $cartid = isset($_REQUEST['caid']) ? $_GET['caid'] : '0';

        $totalrows = isset($_REQUEST['totalrows']) ? $_REQUEST['totalrows'] : false;
        if ($totalrows) {
            $limit = $totalrows;
        }

        if (!$sidx)
            $sidx = 1;


        
        $table = '[OrderManager].[dbo].[Orders] a, ';
        $table1 ='[OrderManager].[dbo].[Order Details] b ';
        $where = 'WHERE ';
        $from = ' FROM ';

        $select = "SELECT a.OrderNumber,
                          a.CartId, 
                          b.priceperunit, 
                          b.QuantityOrdered, 
                          name,
                          OrderDate,
                          a.CustomerID,
                          a.Shipping,
                          a.OrderStatus";
 

        $selectSlice = "SELECT OrderNumber,
                               OrderManager.dbo.fn_Get_Shopping_Cart_Name(CartId) as cart, 
                               priceperunit, 
                               QuantityOrdered, 
                               name,
                               CAST(OrderDate as date) AS OrderDate,
                               CustomerID,
                               Shipping,
                               OrderStatus";
        
        $where .='(a.OrderNumber = b.OrderNumber) and (isnumeric(SKU) = 1 and b.sku = '.$sku.') and (a.cancelled = 0) ';
        $where.= $this->MOrders->filterbyDate($InitialDate, $FinalDate);

        if ($cartid) {
            $where.=$this->MOrders->filterbycartId($cartid);
        }
       
        $SQL = "{$select}{$from}{$table}{$table1}{$where}";
        
        
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
        
        

        $SQL = "WITH mytable AS ({$select}, ROW_NUMBER() OVER (ORDER BY b.{$sidx} {$sord}) AS RowNumber
                FROM {$table}{$table1}{$where} )
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
            $responce->rows[$i]['cell'] = array($row['OrderNumber'],
                $row['cart'],
                $row['priceperunit'],
                $row['QuantityOrdered'],
                $row['name'],
                $row['OrderDate'],
                $row['CustomerID'],
                $row['Shipping'],
                $row['OrderStatus'],
            );
            $i++;
        }


        echo json_encode($responce);
    }

}
?>




