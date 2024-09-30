<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Bc extends BP_Controller {

    public function __construct() {
        parent::__construct();

        $is_logged_in = $this->session->userdata('is_logged_in');

        if (!isset($is_logged_in) || $is_logged_in != true) {
            redirect(base_url(), 'refresh');
        }
         if ($this->MUsers->isValidUser($this->session->userdata('userid'), 882100) != 1) {
               redirect('Catalog/prodcat', 'refresh');
           }
       
    }

    function index() { 
        // Define Meta
        $this->title = "MI Technologiesinc - Billing Center";

        $this->description = "Billing Center";

        // Define custom CSS
        $this->css = array('menu.css', 'form.css', 'fluid.css', 'table.css', 'jqueryui/redmond/jquery-ui-1.8.21.custom.css', 'jqgrid/ui.jqgrid.css');

        // Define custom javascript
        $this->javascript = array('jqueryui/ui/jquery-ui-1.8.21.custom.js', 'jqgrid/i18n/grid.locale-en.js', 'jqgrid/jquery.jqGrid.min.js', 'jqgrid/jquery.jqGrid.fluid.js', 'popup.js');
        //Cargamos la libreria donde esta el menu
        $this->load->library('Layout');
        //Creamos un nuevo layout->menu
        $menu = new Layout;

        $data = array(
            'menu' => $menu->show_menu($this->MUsers->getUserFullName($this->session->userdata('userid'))),
            'administrator' => $this->MUsers->isadminuser($this->session->userdata('userid')),
            'search' => '',
            'customerId' => '',
            'status' => 0,
            'datefrom' => $this->MCommon->InitMonth(date("m/d/Y")),
            'dateto' => date("m/d/Y"),
            'selectedcart' => 0,
            'statusOptions' => Array(0 => "All", 1 => "Paid in Full", 2 => "Balance Due", 3 => "Credit Due"),
            'selectedstatus' => 0,
            'userid' => $this->session->userdata('userid'),
            'cartid' => $this->MUsers->getCartName($this->session->userdata('userid')),
            'customerid' => $this->MUsers->getCustomerId($this->session->userdata('userid')),
            'from' => '/Orders/bc/',
            'caption' => 'Billing Center',
            'nameGrid' => 'billing',

            
        );

     
        
        //Cargamos la libreria comumn
        $this->load->library('common');

        //Reemplazamos los campos del formulario por los que estan en el arreglo $_POST.
        $data = $this->common->fillPost('ALL', $data); 
        

        //Cargamos el modelo con acceso a OM
        $this->load->model('MOrders', '', TRUE);
        
        

        //Obtenemos los datos para el desplegado del balance
        //Si $data['status']=1 Obtenemos Paid in Full
        //Si $data['status']=2 Obteneos Balance Due
        //Si $data['status']=3 Obteneos Credit Due
        $data['paidinfull'] = $this->MOrders->paidform($data['datefrom'], $data['dateto'], 1, $data['customerid'], $data['selectedcart'] );
        $data['balancedue'] = $this->MOrders->paidform($data['datefrom'], $data['dateto'], 2, $data['customerid'], $data['selectedcart']);
        $data['creditdue'] = $this->MOrders->paidform($data['datefrom'], $data['dateto'], 3, $data['customerid'], $data['selectedcart']);


        // Verificamos si el usuario activo no es un partner
        if ($data['customerid'] !=0) {
             //Obtenemos solo el nombre de un carro para usuario y enviamos el Id del usuario activo
            $data['cartOptions'] = $this->MOrders->getCartName($data['cartid']);
            $data['customerId'] = $this->MUsers->getCustomerId($this->session->userdata('userid'));

        } else {
            $data['cartOptions'] = $this->MOrders->getCartNames();
        }


        $data['headers'] = "['Order#','Reference#','OrderStatus','Order Date','Order Total','Paid Amount','Balance Due','Days Old','CustomerID','CartID']";

        $data['body'] = "[
                {name:'OrderNumber',index:'OrderNumber', width:80, align:'center', formatter: formatLink,sortable:true},
                {name:'Reference',index:'Reference', width:80, align:'left'},
                {name:'OrderStatus',index:'OrderStatus', width:80, align:'left'},
                {name:'OrderDate',index:'OrderDate', width:80, align:'center',formatter:'date',formatoptions: {newformat:'F j, Y'}},
                {name:'OrderTotal',index:'OrderTotal', width:80, align:'right',formatter:'currency', formatoptions:{decimalSeparator:'.', thousandsSeparator: ',', decimalPlaces: 2, prefix: '$ '}},
                {name:'PaidAmount',index:'PaidAmount', width:80, align:'right',formatter:'currency', formatoptions:{decimalSeparator:'.', thousandsSeparator: ',', decimalPlaces: 2, prefix: '$ '}},
                {name:'BalanceDue',index:'BalanceDue', width:80, align:'center',formatter:'currency', formatoptions:{decimalSeparator:'.', thousandsSeparator: ',', decimalPlaces: 2, prefix: '$ '}},
                {name:'DaysOld',index:'DaysOld', width:80, align:'center'}, 
                {name:'CustomerID',index:'CustomerID', width:80, align:'left',hidden:true},
                {name:'CartID',index:'CartID', width:80, align:'left',hidden:true}
  	]";

        //Datos para el link que extrae los datos y los ingresa al grid
        $data['gridSearch'] = 'GridDatabilling?ds=' . $data['search'] . '&st=' . $data['status'] . '&caid=' . $data['selectedcart'] . '&cuid=' . $data['customerId'] . '&df=' . $data['datefrom'] . '&dt=' . $data['dateto'];
        //datos extra para el detalle de la orden
        $data['formatLink'] = '/Orders/orderdetails/?cid=' . $data['selectedcart'].'&from=1';

        //Generar el contenido....
        $this->build_content($data);
        $this->render_page();
    }

    function GridDatabilling() {

        $InitialDate = isset($_REQUEST['df']) ? $_GET['df'] : $this->MCommon->InitMonth(date("m/d/Y"));
        $FinalDate = isset($_REQUEST['dt']) ? $_GET['dt'] : date("m/d/Y");
        $FinalDate = $this->MCommon->fixFinalDate($FinalDate, 0);
        $dataSearch = isset($_REQUEST['ds']) ? $_GET['ds'] : '';
        $customerid = isset($_GET['cuid']) ? $_GET['cuid'] : '';
        $status = isset($_REQUEST['st']) ? $_GET['st'] : '';
        $cartid = isset($_REQUEST['caid']) ? $_GET['caid'] : '';

        $page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1; // get the requested page
        $limit = isset($_REQUEST['rows']) ? $_REQUEST['rows'] : 10; // get how many rows we want to have into the gri
        $sidx = isset($_REQUEST['sidx']) ? $_REQUEST['sidx'] : 'OrderNumber'; // get index row - i.e. user click to sort
        $sord = isset($_REQUEST['sord']) ? $_REQUEST['sord'] : 'desc';


        //Select utilizado para realizar el conteo del numero de registros devueltos por la consulta.
        $selectCount = 'Select count(*) as rowNum';
        //Select General con los campos necesarios para la vista
        $select = "select OrderNumber,
                    SourceOrderID,
                    OrderStatus,
                    OrderDate,
                    GrandTotal,
                    BalanceDue,
                    CustomerID,
                    CartID";


        //Se utiliza en la seccion que extrae los registros de la hoja activa
        $selectSlice = "select OrderNumber,
                    SourceOrderID as Reference,
                    OrderStatus,
                    CAST(OrderDate as date) AS OrderDate,
                    GrandTotal as OrderTotal,
                    [OrderManager].[dbo].fn_Sum_Amount(OrderNumber) as PaidAmount,
                    BalanceDue,
                    DATEDIFF ( DAY, OrderDate, GETDATE() ) as DaysOld,
                    CustomerID,
                    CartID";


        $from = ' from ';
        $table = '[OrderManager].[dbo].[Orders]';
        $where = '';

        //Campos en sobre los que se haran busquedas de palabras..
        $wherefields = array('OrderNumber', 'SourceOrderID', 'OrderDate', 'GrandTotal', 'OrderStatus');
        //creamos el were concatenando todos los nombres de campos y las palabras de busqueda
        $where .= $this->MCommon->concatAllWerefields($wherefields, $dataSearch);

        if ($status == 1) {
            $where.=" and BalanceDue = 0";
        }
        if ($status == 2) {
            $where.=" and BalanceDue > 0";
        }
        if ($status == 3) {
            $where.=" and BalanceDue < 0";
        }
        if ($cartid != 0) {
            $where.=" and (CartID = {$cartid})";
        }
        if ($customerid != 0) {
            $where.=' and (customerID = ' . $customerid . ')';
        }
        //incluimos las fechas entre las que se buscara la informacion
        $where .= " and (OrderDate between '{$InitialDate}' and '{$FinalDate}')";

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
                $row['Reference'],
                $row['OrderStatus'],
                $row['OrderDate'],
                $row['OrderTotal'],
                $row['PaidAmount'],
                $row['BalanceDue'],
                $row['DaysOld'],
                $row['CustomerID'],
                $row['CartID']
            );
            $i++;
        }

        echo json_encode($responce);
    }

    function csvExport() {

        header('Content-type: application/vnd.ms-excel');
        header("Content-Disposition: attachment; filename=Catalog-" . date("D-M-j") . ".xls");
        header("Pragma: no-cache");

        $buffer = $_POST['csvBuffer'];

        try {
            echo $buffer;
        } catch (Exception $e) {
            
        }
    }

}


//kylejundt
//jundt4325
//105903
//oscar.gutierrez
//oscar.gutierrez
?>

