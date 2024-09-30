<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Omc extends BP_Controller {

    public function __construct() {
        parent::__construct();

        $is_logged_in = $this->session->userdata('is_logged_in');

        if (!isset($is_logged_in) || $is_logged_in != true) {
            redirect(base_url(), 'refresh');
        }
        
        if ($this->MUsers->isValidUser($this->session->userdata('userid'), 882200) != 1) {
               redirect('Catalog/prodcat', 'refresh');
           }
    }

    public function index() {
        //Cargamos la base de datos de OrderManager
        $this->load->model('MOrders', '', TRUE);

        // Define Meta
        $this->title = "MI Technologiesinc - Order Management Center";

        $this->description = "Order Management Center";

        // Define custom CSS
        $this->css = array('menu.css', 'fluid.css', 'jqueryui/redmond/jquery-ui-1.8.21.custom.css', 'jqgrid/ui.jqgrid.css', 'form.css');

        // Define custom javascript
        $this->javascript = array('jqueryui/ui/jquery-ui-1.8.21.custom.js', 'jqgrid/i18n/grid.locale-en.js', 'jqgrid/jquery.jqGrid.min.js', 'jqgrid/jquery.jqGrid.fluid.js', 'popup.js');
        //Cargamos la libreria donde esta el menu
        $this->load->library('Layout');
        //Creamos un nuevo layout->menu
        $menu = new Layout;

        $data = array(
            'menu' => $menu->show_menu($this->MUsers->getUserFullName($this->session->userdata('userid'))),
            'from' => '/Orders/omc/',
            'nameGrid' => 'omc',
            'namePager' => 'omcpager',
            'caption' => 'Order Management Center',
            'subgrid' => 'true',
            'sort' => 'desc',
            'search' => '',
            'statusOptions' => Array(0 => "All Status", 1 => "Shipped", 2 => "Pending"),
            'status' => 0,
            'datefrom' => $this->MCommon->InitMonth(date("m/d/Y")),
            'dateto' => date("m/d/Y"),
            'export' => 'omc',
            'cartid' => $this->MUsers->getCartName($this->session->userdata('userid')),
            'customerid' => $this->MUsers->getCustomerId($this->session->userdata('userid')),
            'productLines' => '0',
            'productLineOptions' => $this->MCatalog->fillProductLines(),
            'selectedcart' => 0,
            
        );


        //Cargamos al arreglo los colnames
        $data['colNames'] = "['Order#','Reference#','Order Date ','Order Status','CustomerID','CartID','Shipping Method','Ship To','Tracking#']";
        //Cargamos al arreglo los colmodels
        $data['colModel'] = "[
                {name:'OrderNumber',index:'OrderNumber', width:90, align:'center', formatter: formatLink,sortable:true  },
                {name:'SourceOrderID',index:'SourceOrderID', width:55, align:'left'},
                {name:'OrderDate',index:'OrderDate', width:80, align:'center',formatter:'date',formatoptions: {newformat:'F j, Y'}},
                {name:'OrderStatus',index:'OrderStatus', width:80, align:'left'},
                {name:'CustomerID',index:'CustomerID', width:80, align:'left',hidden:true},
                {name:'CartID',index:'CartID', width:80, align:'left',hidden:true},
                {name:'Shipping',index:'Shipping', width:80, align:'left'},
                {name:'ShipName',index:'ShipName', width:80},    
                {name:'Tracking',index:'Tracking', width:80},   
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
        $data['gridSearch'] = 'dataOmc?caid=' . $data['selectedcart'] . '&ds=' . $data['search'] . '&is=' . $data['status'] .'&cuid=' . $data['customerid']. '&df=' . $data['datefrom'] . '&dt=' . $data['dateto'];

        //datos extra para el detalle de la orden
        $data['formatLink'] = '/Orders/orderdetails/?cid=' . $data['selectedcart'];

        //Generar el contenido....
        $this->build_content($data);
        $this->render_page();
    }

    function dataOmc() {
        $page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1; // get the requested page
        $limit = isset($_REQUEST['rows']) ? $_REQUEST['rows'] : 10; // get how many rows we want to have into the gri
        $sidx = isset($_REQUEST['sidx']) ? $_REQUEST['sidx'] : 'OrderNumber'; // get index row - i.e. user click to sort
        $sord = isset($_REQUEST['sord']) ? $_REQUEST['sord'] : 'desc';

        $this->load->model('MOrders', '', TRUE);

        $dataSearch = isset($_REQUEST['ds']) ? $_GET['ds'] : '';
        $status = isset($_REQUEST['is']) ? $_GET['is'] : '0';
        $cartid = isset($_REQUEST['caid']) ? $_GET['caid'] : '0';
        $customerid = isset($_GET['cuid']) ? $_GET['cuid'] : '';
        $InitialDate = isset($_REQUEST['df']) ? $_GET['df'] : $this->MCommon->InitMonth(date("m/d/Y"));
        $FinalDate = isset($_REQUEST['dt']) ? $_GET['dt'] : date("m/d/Y");


        if (!$sidx)
            $sidx = 1;

        $table = '[OrderManager].[dbo].[Orders] AS O ';
        $where = '';
        $from = ' FROM ';

        $selectCount = 'Select count(*) as rowNum';

        $select = "SELECT O.OrderNumber,
                       O.SourceOrderID,
                       O.OrderDate, 
                       O.OrderStatus,
                       O.CustomerID,
                       O.CartID,
                       O.Shipping,
                       O.ShipName,
                       (Select TOP(1) TRK.TrackingID from [OrderManager].[dbo].[Tracking] AS TRK where O.Ordernumber = TRK.NumericKey) AS 'Tracking'";

        $selectSlice = "SELECT OrderNumber,
                       SourceOrderID,
                       CAST(OrderDate as date) AS OrderDate, 
                       OrderStatus,
                       CustomerID,
                       CartID,
                       Shipping,
                       ShipName,
                       Tracking";

        $wherefields = array('OrderNumber', 'SourceOrderID', 'OrderDate', 'OrderStatus', 'CustomerID','Shipping', 'ShipName');

        $where .=$this->MCommon->concatAllWerefields($wherefields, $dataSearch);


        if ($status) {
            $where.=$this->MOrders->filterbystatus($status);
        }

        if ($cartid) {
            $where.=$this->MOrders->filterbycartId($cartid);
        }
       
        if ($customerid != 0) {
            $where.=' and (customerID = ' . $customerid . ')';
        }

        $where.= $this->MOrders->filterbyDate($InitialDate, $FinalDate);

        $SQL = "{$selectCount}{$from}{$table}{$where}";


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

        $SQL = "WITH mytable AS ({$select}, ROW_NUMBER() OVER (ORDER BY {$sidx} {$sord}) AS RowNumber
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
            $responce->rows[$i]['id'] = $row['OrderNumber'];
            $responce->rows[$i]['cell'] = array($row['OrderNumber'],
                $row['SourceOrderID'],
                $row['OrderDate'],
                $row['OrderStatus'],
                $row['CustomerID'],
                $row['CartID'],
                $row['Shipping'],
                $row['ShipName'] = utf8_encode($row['ShipName']),
                $row['Tracking'],
               
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

    function trackingData() {
        $this->load->model('morders', '', TRUE);
        $orderNumber = isset($_REQUEST['on']) ? $_GET['on'] : '';
        $page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1; // get the requested page
        $limit = isset($_REQUEST['rows']) ? $_REQUEST['rows'] : 10; // get how many rows we want to have into the gri
        $sidx = isset($_REQUEST['sidx']) ? $_REQUEST['sidx'] : 'OrderNumber'; // get index row - i.e. user click to sort
        $sord = isset($_REQUEST['sord']) ? $_REQUEST['sord'] : 'Desc';


        $totalrows = isset($_REQUEST['totalrows']) ? $_REQUEST['totalrows'] : false;
        if ($totalrows) {
            $limit = $totalrows;
        }

        if (!$sidx)
            $sidx = 1;


        $SQL = "SELECT b.OrderNum,
                       b.Trackingid,
                       b.Carrier,
                       b.ShippersMethod,
                       CAST(b.DateAdded as date) AS DateAdded
                FROM 
                      [OrderManager].[dbo].[tracking] b
                      Where (b.ordernum ={$orderNumber})";


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

        $responce = new stdClass();
        $responce->page = $page;
        $responce->total = $total_pages;
        $responce->records = $count;


        $i = 0;

        foreach ($result as $row) {
            $responce->rows[$i]['id'] = $row['OrderNum'];
            $responce->rows[$i]['cell'] = array($row['OrderNum'],
                $row['Trackingid'],
                $row['Carrier'],
                $row['ShippersMethod'],
                $row['DateAdded'],
            );
            $i++;
        }


        echo json_encode($responce);
    }

}
?>




