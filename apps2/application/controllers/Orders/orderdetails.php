<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Orderdetails extends BP_Controller {

    public function __construct() {
        parent::__construct();

        $is_logged_in = $this->session->userdata('is_logged_in');

        if (!isset($is_logged_in) || $is_logged_in != true) {
            redirect(base_url(), 'refresh');
        }
        if ($this->MUsers->isValidUser($this->session->userdata('userid'), 882300) != 1) {
            redirect('Catalog/prodcat', 'refresh');
        }
    }

    function index() {
        $cid = $this->input->get('cid'); //cartID
        $csid = $this->input->get('csid'); //CustomerID
        $on= $this->input->get('on');      //OrderNumber
        $from = $this->input->get('from'); //From

 

        // Define Meta
        $this->title = "MI Technologiesinc - Order Details";

        $this->description = "Order Details";

        // Define custom CSS
        $this->css = array('fluid.css', 'jqueryui/redmond/jquery-ui-1.8.21.custom.css', 'jqgrid/ui.jqgrid.css', 'form.css');

        // Define custom javascript
        $this->javascript = array('jqueryui/ui/jquery-ui-1.8.21.custom.js', 'jqgrid/i18n/grid.locale-en.js', 'jqgrid/jquery.jqGrid.min.js', 'jqgrid/jquery.jqGrid.fluid.js', 'popup.js');

        $this->hasNav = FALSE;

        $data = array(
            'title' => 'MI Technologiesinc - Details for Ordernumber',
            'from' => '/Orders/orderdetails/',
            'fromomc' => '/Orders/omc/',
        );

        $replyto = isset($_REQUEST['from']) ? $_REQUEST['from'] : 0;

        switch ($replyto) {
            case 0:
                $data['back'] = '/Orders/omc/';
                $data['retwhere'] = 'Orders';
                break;
            case 1:
                $data['back'] = '/Orders/bc/';
                $data['retwhere'] = 'Billing';
                break;
            case 2:
                $data['back'] = '/Reports/pendingorders/';
                $data['retwhere'] = 'Pend. Ord';
                break;
            case 3:
                $data['back'] = '/Reports/salesbysku/';
                $data['retwhere'] = 'SalesBySku';
                break;
            case 4:
                $data['back'] = '/Reports/projectorlampleads/';
                $data['retwhere'] = 'projlampleads';
                break;
        }


        //Obtenemos el usuario que tiene la sesion activa
        $ActiveUser = $this->session->userdata('userid');

        //verificamos los permisos del usuario activo
        $data['soldTo'] = $this->MUsers->isValidUser($ActiveUser, 890015);
        $data['shipTo'] = $this->MUsers->isValidUser($ActiveUser, 890016);
        $data['productList'] = $this->MUsers->isValidUser($ActiveUser, 890004);
        $data['orderComments'] = $this->MUsers->isValidUser($ActiveUser, 890005);
        $data['totals'] = $this->MUsers->isValidUser($ActiveUser, 890006);
        $data['serialofProductShipped'] = $this->MUsers->isValidUser($ActiveUser, 890007);
        $data['orderTrackingInformation'] = $this->MUsers->isValidUser($ActiveUser, 890008);
        $data['orderNotes'] = $this->MUsers->isValidUser($ActiveUser, 890009);
        $data['notes'] = $this->MUsers->isValidUser($ActiveUser, 890010);

        $this->load->model('morders', '', TRUE);

        //Obtenemos el nombre desde el carro de compras desde (cid)
      //  if ($_REQUEST['cid'] != 0) {
          $cartname = $this->morders->getSpecificCartName($_GET['on']);
            $data['cartname'] = $cartname ;
           
      // } else {
      //    $data['cartname'] = "All";
      //  }

         $salesrep = $this->morders->getSpecificSalesrep($_GET['on']);
            $data['salesrep'] =  $salesrep ;
         

        //Obtenemos los datos del cliente desde (csid)
        $customerid = isset($_GET['csid']) ? $_GET['csid'] : '';
        $data['customerid'] = $customerid;
        //$data['customerdata'] = $this->morders->getCustomerIDData($customerid);

        //Obtenemos el numero de Orden desde (on)
        $ordernumber = isset($_GET['on']) ? $_GET['on'] : '';
        $data['ordernumber'] = $ordernumber;
       
        $data['solddata'] = $this->morders->getSoldData($ordernumber);

        //Obtenemos los datos de envio.
        $data['shipdata'] = $this->morders->getShipData($ordernumber);

        //Obtenermos los comentarios de la orden
        $data['comments'] = $this->morders->getComments($ordernumber);

        //Obtenermos el status de la orden
        $data['StatusOrder'] = $this->morders->getStatusOrder($ordernumber);


        $this->build_content($data);
        $this->render_page();
    }

    /*
     * 
     * 
     * 
     * http://localhost/apps2/index.php/Orders/orderdetails/?cid=0&on=5382002&csid=57552&from=0
     * 
     * 
     * 
     * 
     * 
     */

    function getItemsData() {
        $this->load->model('morders', '', TRUE);
        $page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1; // get the requested page
        $limit = isset($_REQUEST['rows']) ? $_REQUEST['rows'] : 10; // get how many rows we want to have into the gri
        $sidx = isset($_REQUEST['sidx']) ? $_REQUEST['sidx'] : 'SKU'; // get index row - i.e. user click to sort
        $sord = isset($_REQUEST['sord']) ? $_REQUEST['sord'] : '';

        $totalrows = isset($_REQUEST['totalrows']) ? $_REQUEST['totalrows'] : false;
        if ($totalrows) {
            $limit = $totalrows;
        }

        if (!$sidx)
            $sidx = 1;

        $ordernumber = isset($_REQUEST['on']) ? $_GET['on'] : '';

        $SQL = "SELECT a.SKU, a.Product, a.PricePerUnit, a.QuantityOrdered, a.Status, (a.PricePerUnit * a.QuantityOrdered) as extprice 
                FROM [OrderManager].[dbo].[Order Details] a
                Where OrderNumber = {$ordernumber}";



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
            $responce->rows[$i]['id'] = $row['SKU'];
            $responce->rows[$i]['cell'] = array($row['SKU'],
                $row['Product'] = utf8_encode($row['Product']),
                $row['PricePerUnit'],
                $row['QuantityOrdered'],
                $row['Status'],
                $row['extprice'],
            );
            $i++;
        }

        echo json_encode($responce);
    }

    /*
     * 
     * 
     * 
     * 
     * 
     * 
     * 
     * 
     */

    function getSkuAndSerialNumber() {

        $this->load->model('morders', '', TRUE);
        $page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1; // get the requested page
        $limit = isset($_REQUEST['rows']) ? $_REQUEST['rows'] : 10; // get how many rows we want to have into the gri
        $sidx = isset($_REQUEST['sidx']) ? $_REQUEST['sidx'] : 'SKU'; // get index row - i.e. user click to sort
        $sord = isset($_REQUEST['sord']) ? $_REQUEST['sord'] : '';

        $totalrows = isset($_REQUEST['totalrows']) ? $_REQUEST['totalrows'] : false;
        if ($totalrows) {
            $limit = $totalrows;
        }

        if (!$sidx)
            $sidx = 1;

        $ordernumber = isset($_REQUEST['on']) ? $_GET['on'] : '';

        $SQL = "Select a.ordernumber, b.SKU,b.Product,b.ItemNumber as ITdetails, c.ItemNumber ITPacking, c.SerialNumber
                From OrderManager.dbo.Orders a,
                OrderManager.dbo.[Order Details] b,
                OrderManager.dbo.Packing c
                Where (a.ordernumber = b.ordernumber) and (a.OrderNumber = c.OrderNumber)
                and (b.ItemNumber = c.ItemNumber)
                and (a.OrderNumber = {$ordernumber})";

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
            $responce->rows[$i]['id'] = $row['SKU'];
            $responce->rows[$i]['cell'] = array($row['SKU'],
                $row['Product'],
                $row['SerialNumber'],
            );
            $i++;
        }

        echo json_encode($responce);
    }

    /*
     * 
     * 
     * 
     * 
     * 
     */

    function getTotals() {
        $this->load->model('morders', '', TRUE);
        $page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1; // get the requested page
        $limit = isset($_REQUEST['rows']) ? $_REQUEST['rows'] : 10; // get how many rows we want to have into the gri
        $sidx = isset($_REQUEST['sidx']) ? $_REQUEST['sidx'] : 'SKU'; // get index row - i.e. user click to sort
        $sord = isset($_REQUEST['sord']) ? $_REQUEST['sord'] : '';

        $totalrows = isset($_REQUEST['totalrows']) ? $_REQUEST['totalrows'] : false;
        if ($totalrows) {
            $limit = $totalrows;
        }

        if (!$sidx)
            $sidx = 1;

        $ordernumber = isset($_REQUEST['on']) ? $_GET['on'] : '';


        $SQL = "SELECT a.FinalProductTotal, a.Surcharge, a.Discount , a.CouponDiscount, a.ShippingTotal,a.TaxTotal, a.FinalGrandTotal
                FROM [OrderManager].[dbo].[Orders] a
                Where OrderNumber = {$ordernumber}";

        $result = $this->MCommon->getOneRecord($SQL);



        $arreglo = array(array('Charge' => 'Sub Total:', 'Total' => $result['FinalProductTotal']),
            array('Charge' => 'Surcharges:', 'Total' => $result['Surcharge']),
            array('Charge' => 'Discounts:', 'Total' => $result['Discount']),
            array('Charge' => 'Coupons:', 'Total' => $result['CouponDiscount']),
            array('Charge' => 'Shipping:', 'Total' => $result['ShippingTotal']),
            array('Charge' => 'Sales Tax', 'Total' => $result['TaxTotal']),
            array('Charge' => 'Grand Total:', 'Total' => $result['FinalGrandTotal']),
        );
        $count = count($arreglo);

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

        foreach ($arreglo as $row) {
            $responce->rows[$i]['id'] = $row['Charge'];
            $responce->rows[$i]['cell'] = array($row['Charge'],
                $row['Total'],
            );
            $i++;
        }

        echo json_encode($responce);
    }

    /*
     * 
     * 
     * 
     * 
     * 
     * 
     * 
     */

    function getOrderTrackingInformation() {
        $this->load->model('morders', '', TRUE);
        $page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1; // get the requested page
        $limit = isset($_REQUEST['rows']) ? $_REQUEST['rows'] : 10; // get how many rows we want to have into the gri
        $sidx = isset($_REQUEST['sidx']) ? $_REQUEST['sidx'] : 'SKU'; // get index row - i.e. user click to sort
        $sord = isset($_REQUEST['sord']) ? $_REQUEST['sord'] : '';

        $totalrows = isset($_REQUEST['totalrows']) ? $_REQUEST['totalrows'] : false;
        if ($totalrows) {
            $limit = $totalrows;
        }

        if (!$sidx)
            $sidx = 1;

        $ordernumber = isset($_REQUEST['on']) ? $_GET['on'] : '';

        $SQL = "SELECT a.DateAdded, a.TrackingID, a.Carrier, a.ShippersMethod, a.Pounds, a.Ounces, a.Notes
                FROM [OrderManager].[dbo].[Tracking] a
                where a.OrderNum = {$ordernumber}
                and (a.IsVoid is null )";

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
            $responce->rows[$i]['id'] = $row['DateAdded'];
            $responce->rows[$i]['cell'] = array($row['DateAdded'],
                $row['TrackingID'],
                $row['Carrier'],
                $row['ShippersMethod'],
                $row['Pounds'],
                $row['Ounces'],
                $row['Notes'],
            );
            $i++;
        }

        echo json_encode($responce);
    }

    function getOrderNotes() {
        $this->load->model('morders', '', TRUE);
        $page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1; // get the requested page
        $limit = isset($_REQUEST['rows']) ? $_REQUEST['rows'] : 10; // get how many rows we want to have into the gri
        $sidx = isset($_REQUEST['sidx']) ? $_REQUEST['sidx'] : 'SKU'; // get index row - i.e. user click to sort
        $sord = isset($_REQUEST['sord']) ? $_REQUEST['sord'] : '';

        $totalrows = isset($_REQUEST['totalrows']) ? $_REQUEST['totalrows'] : false;
        if ($totalrows) {
            $limit = $totalrows;
        }

        if (!$sidx)
            $sidx = 1;

        $ordernumber = isset($_REQUEST['on']) ? $_GET['on'] : '';


        $SQL = "SELECT a.AutoNumber,a.EntryDate, OrderManager.dbo.fn_Get_UserName(a.EnteredBy) as EnteredBy
                FROM [OrderManager].[dbo].[Notes] a
                where NumericKey =  {$ordernumber}";

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
            $responce->rows[$i]['id'] = $row['AutoNumber'];
            $responce->rows[$i]['cell'] = array($row['AutoNumber'],
                $row['EntryDate'],
                $row['EnteredBy'],
            );
            $i++;
        }

        echo json_encode($responce);
    }

    function getNotes() {
        $this->load->model('morders', '', TRUE);
        $page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1; // get the requested page
        $limit = isset($_REQUEST['rows']) ? $_REQUEST['rows'] : 10; // get how many rows we want to have into the gri
        $sidx = isset($_REQUEST['sidx']) ? $_REQUEST['sidx'] : 'SKU'; // get index row - i.e. user click to sort
        $sord = isset($_REQUEST['sord']) ? $_REQUEST['sord'] : '';
        $ids = isset($_REQUEST['ids']) ? $_REQUEST['ids'] : '';
        $totalrows = isset($_REQUEST['totalrows']) ? $_REQUEST['totalrows'] : false;
        if ($totalrows) {
            $limit = $totalrows;
        }

        if (!$sidx)
            $sidx = 1;

        $ordernumber = isset($_REQUEST['on']) ? $_GET['on'] : '';

        // $SQL = "SELECT a.EntryDate, OrderManager.dbo.fn_Get_UserName(a.EnteredBy) as EnteredBy, a.Notes
        //      FROM [OrderManager].[dbo].[Notes] a
        //      where NumericKey =  {$ordernumber}";

        
        $SQL = "SELECT a.Notes
                FROM [OrderManager].[dbo].[Notes] a
                where (NumericKey =  {$ordernumber}) and (AutoNumber='{$ids}')";
        
       
                
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
            $responce->rows[$i]['id'] = $row['Notes'];
            $responce->rows[$i]['cell'] = array($row['Notes']= utf8_encode($row['Notes']),
            );
            $i++;
        }

        echo json_encode($responce);
    }

}
?>

