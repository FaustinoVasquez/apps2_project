<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Serialscapture extends BP_Controller {

    public function __construct() {
        parent::__construct();

        $is_logged_in = $this->session->userdata('is_logged_in');

        if (!isset($is_logged_in) || $is_logged_in != true) {
            redirect(base_url(), 'refresh');
        }
        
        if ($this->MUsers->isValidUser($this->session->userdata('userid'), 884700) != 1) {
         redirect('Catalog/prodcat', 'refresh');
     }
 }

 public function index() {
        //Cargamos la base de datos de OrderManager
    $this->load->model('MOrders', '', TRUE);

        // Define Meta
    $this->title = "MI Technologiesinc - Unshiped Orders";

    $this->description = "Unshipped Ordersr";

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
        'from' => '/tools/serialscapture/',
        'nameGrid' => 'serialscap',
        'namePager' => 'serialpager',
        'caption' => 'Serials Capture',
        'sort' => 'asc',
        'adjustment' => '',
        'export' => 'Pending',
        );


        //Cargamos al arreglo los colnames
    $data['colNames'] = "['SKU','Name','Quantity','SerialsCaptured']";
        //Cargamos al arreglo los colmodels
    $data['colModel'] = "[
    {name:'ProductCatalogID',index:'ProductCatalogID', align:'center'},
    {name:'Name',index:'Name', align:'left'},
    {name:'Quantity',index:'Quantity',  align:'center'},
    {name:'Serials',index:'serials',  align:'center'},
    ]";


          //Cargamos la libreria comumn
    $this->load->library('common');

        //Reemplazamos los campos del formulario por los que estan en el arreglo $_POST.
    $data = $this->common->fillPost('ALL', $data);
    

        //cadena de busqueda del grid 
    $data['gridSearch'] = 'getData?&ds=' . $data['adjustment'];
    

        //Generar el contenido....
    $this->build_content($data);
    $this->render_page();
}

function getData() {
        $page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1; // get the requested page
        $limit = isset($_REQUEST['rows']) ? $_REQUEST['rows'] : 10; // get how many rows we want to have into the gri
        $sidx = isset($_REQUEST['sidx']) ? $_REQUEST['sidx'] : 'ProductCatalogID'; // get index row - i.e. user click to sort
        $sord = isset($_REQUEST['sord']) ? $_REQUEST['sord'] : 'asc';

        $this->load->model('MOrders', '', TRUE);

        $adjustment = isset($_REQUEST['ds']) ? $_GET['ds'] : '';
        $count=0;
        $result='';
        
        if($adjustment){
            $select = " SELECT ProductCatalogID, ProductName, Quantity FROM inventory.dbo.vw_InventoryDetails
            WHERE (InventoryAdjustmentsID = {$adjustment}); ";

            $result = $this->MCommon->getSomeRecords($select);

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
            $responce->rows[$i]['id'] = $row['ProductCatalogID'];
            $responce->rows[$i]['cell'] = array($row['ProductCatalogID'],
                $row['ProductName'],
                $row['Quantity'],
                $row['Serials'] = $this->getSerials($adjustment,$row['ProductCatalogID'])
                );
            $i++;
        }

        echo json_encode($responce);
    }
}


function getSerials($adjustment,$sku){
    $SQL= "SELECT count(SerialNumber) as noserials FROM [inventory].[dbo].SerialNumbers 
    WHERE (InventoryAdjustmentID = {$adjustment} )
    AND (ProductCatalogID = {$sku} )";

    $result = $this->MCommon->getOneRecord($SQL);
    
    return $result['noserials'];
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


        $SQL = "SELECT OrderNumber,
        sku,
        ItemNumber,
        Product,
        QuantityOrdered,
        QuantityShipped,
        QuantityReturned
        FROM 
        [OrderManager].[dbo].[Order Details]
        
        WHERE 
        OrderNumber = {$orderNumber}";


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
            $responce->rows[$i]['id'] = $row['sku'];
            $responce->rows[$i]['cell'] = array($row['sku'],
              $row['OrderNumber'],
              $row['ItemNumber'],
              $row['Product'],
              $row['QuantityOrdered'],
              $row['QuantityShipped'],
              $row['QuantityReturned'],
              );
            $i++;
        }


        echo json_encode($responce);
    }

    function saveItem() {
       
       $SQL="INSERT INTO [inventory].[dbo].SerialNumbers (InventoryAdjustmentID, ProductCatalogID, SerialNumber) 
       VALUES ( {$_POST['myAdjustment']}, {$_POST['mySku']}, '{$_POST['myItem']}')";
       
       $this->MCommon->saveRecord($SQL,'InventorySave');   
       
   }
   
   function validateCredentials($user =NULL, $pasw=NULL) {
       
    $SQL= "SELECT count ([UserID]) as userValid
    FROM [OrderManager].[dbo].[usysOMUsers]
    where (UserID = '{$user}') and (Password = '{$pasw}')"; 
    $result = $this->MCommon->getOneRecord($SQL);
    
    return $result['userValid'];
}

}
?>




