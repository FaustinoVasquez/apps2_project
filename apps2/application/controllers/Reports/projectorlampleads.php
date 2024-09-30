<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Projectorlampleads extends BP_Controller {

    public function __construct() {
        parent::__construct();

        $is_logged_in = $this->session->userdata('is_logged_in');

        if (!isset($is_logged_in) || $is_logged_in != true) {
            redirect(base_url(), 'refresh');
        }

        // if ($this->MUsers->isValidUser($this->session->userdata('userid'), 883400) != 1) {
        //     redirect('Catalog/prodcat', 'refresh');
        // }
    }

    public function index() {
        //Cargamos la base de datos de OrderManager
        $this->load->model('MOrders', '', TRUE);

        // Define Meta
        $this->title = "MI Technologiesinc - Projector Lamp Leads";

        $this->description = "Projector Lamp Leads";

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
            'from' => '/Reports/projectorlampleads/',
            'nameGrid' => 'projectorlampleads',
            'namePager' => 'projectorlampleadsPager',
            'caption' => 'Projector Lamp Leads',
            'subgrid' => 'false',
            'sort' => 'desc',
            'search' => '',
            'qtyorderedoptions' => Array(0 => "Qty Ord", 2 => "2 Units", 3 => "3 Units", 4 => "4 or More"),
            'statusOptions' => Array(0 => "All Status", 1 => "Shipped", 2 => "Pending"),
            'status' => 0,
            'datefrom' => $this->MCommon->lastweek(),
            'dateto' => date("m/d/Y"),
            'export' => 'projectorlampleads',
            'cartid' => $this->MUsers->getCartName($this->session->userdata('userid')),
            'customerid' => $this->MUsers->getCustomerId($this->session->userdata('userid')),
            'selectedcart' => 0,
            'qtyOrdered' => 0,
        );


        //Cargamos al arreglo los colnames
        $data['colNames'] = "['OrderNum','OrdNum','SKU','WebSKU','EncSKUPH','Contacted','Comments','Product','CustomerID','QuantityOrdered','PricePerUnit', 'Name','Company','Phone','Email','ShipName', 'ShipCompany','ShipPhone','OrderStatus','TrackingNumber','CartName']";
        //Cargamos al arreglo los colmodels
        $data['colModel'] = "[
                {name:'OrderNumber',index:'OrderNumber', width:60, align:'center', formatter: formatLink, frozen : true},
                {name:'OrdNum',index:'OrdNum', width:60, align:'center', frozen : true, editable:true, editrules:{edithidden:true}, hidden:true, editoptions: { readonly:'readonly' }},
                {name:'SKU',index:'SKU', width:80, align:'left', frozen : true},
                {name:'WebSKU',index:'WebSKU', width:150, align:'left', frozen : true},
                {name:'EncSKUPH',index:'EncSKUPH', width:150, align:'left', frozen : true},          
                {name:'Contacted',index:'Contacted', width:60, align:'center', frozen : true, editable:true, edittype: 'checkbox', editoptions: { value: '1:0' }, formatter: 'checkbox', formatoptions: { disabled: true}},
                {name:'Comments',index:'Comments', width:180, align:'center',frozen : true, editable:true, edittype: 'textarea', editoptions: { rows:'4',cols:'50' }},
                {name:'Product',index:'Product', width:450, align:'left'},
                {name:'CustomerID',index:'CustomerID', width:80, align:'left',hidden:true},
                {name:'QuantityOrdered',index:'QuantityOrdered', width:90, align:'center'},
                {name:'PricePerUnit',index:'PricePerUnit', width:100, align:'center'}, 
                {name:'Name',index:'Name', width:200, align:'left'},
                {name:'Company',index:'Company', width:250, align:'left'},
                {name:'Phone',index:'Phone', width:100, align:'center'},
                {name:'Email',index:'Email', width:250, align:'left'},
                {name:'ShipName',index:'ShipName', width:250, align:'center'},
                {name:'ShipCompany',index:'ShipCompany', width:250, align:'left'},
                {name:'ShipPhone',index:'ShipPhone', width:150, align:'center'},
                {name:'OrderStatus',index:'OrderStatus', width:100, align:'center'},
                {name:'TrackingNumber',index:'TrackingNumber', width:150, align:'center'},
                {name:'CartName',index:'CartName', width:150, align:'center'},
  	]";


        //Cargamos la libreria comumn
        $this->load->library('common');

        //Reemplazamos los campos del formulario por los que estan en el arreglo $_POST.
        $data = $this->common->fillPost('ALL', $data);


        // Verificamos si el usuario activo no es un partner
        if ($data['customerid'] != 0) {
            //Obtenemos solo el nombre de un carro para usuario y enviamos el Id del usuario activo
            $data['cartOptions'] = $this->MOrders->getCartName($data['cartid']);
            //   $data['customerId'] = $this->MUsers->getCustomerId($this->session->userdata('userid'));
        } else {
            $data['cartOptions'] = $this->MOrders->getCartNames();
        }

        //  $data['selectedqtyordered']=$qtyOrdered;
        //cadena de busqueda del grid 
        $data['gridSearch'] = 'dataProjectorlampleads?caid=' . $data['selectedcart'] . '&ds=' . $data['search'] . '&is=' . $data['status'] . '&qo=' . $data['qtyOrdered'] . '&cuid=' . $data['customerid'] . '&df=' . $data['datefrom'] . '&dt=' . $data['dateto'];

        //datos extra para el detalle de la orden
        $data['formatLink'] = '/Orders/orderdetails/?cid=' . $data['selectedcart'].'&from=4'.'&csid=' . $data['customerid'];

        //Generar el contenido....
        $this->build_content($data);
        $this->render_page();
    }

    function dataProjectorlampleads() {
        $page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1; // get the requested page
        $limit = isset($_REQUEST['rows']) ? $_REQUEST['rows'] : 10; // get how many rows we want to have into the gri
        $sidx = isset($_REQUEST['sidx']) ? $_REQUEST['sidx'] : 'O.OrderNumber'; // get index row - i.e. user click to sort
        $sord = isset($_REQUEST['sord']) ? $_REQUEST['sord'] : 'desc';

        $this->load->model('MOrders', '', TRUE);

        $dataSearch = isset($_REQUEST['ds']) ? $_GET['ds'] : '';
        $status = isset($_REQUEST['is']) ? $_GET['is'] : 0;
        $cartid = isset($_REQUEST['caid']) ? $_GET['caid'] : 0;
        $customerid = isset($_GET['cuid']) ? $_GET['cuid'] : '';
        $InitialDate = isset($_REQUEST['df']) ? $_GET['df'] : $this->MCommon->lastweek();
        $FinalDate = isset($_REQUEST['dt']) ? $_GET['dt'] : date("m/d/Y");
        $qtyOrdered = !empty($_REQUEST['qo']) ? $_REQUEST['qo'] : 0;


        if (!$sidx)
            $sidx = 1;
        $table = "  OrderManager.dbo.Orders O 
                    LEFT JOIN OrderManager.dbo.[Order Details] OD 
                    ON O.OrderNumber = OD.OrderNumber 
                    LEFT JOIN OrderManager.dbo.Orders_MIT_ExtendedInfo C 
                    ON O.OrderNumber =  C.OrderNumber
                    LEFT OUTER JOIN Inventory.dbo.ProductCatalog PC 
                    ON OD.SKU =PC.ID ";
                  


        $where = ' ';
        $from = ' FROM ';
        
        $selectcount = " SELECT count( O.OrderNumber) as total";

    
       $select = "    SELECT 
                      O.OrderNumber
                    , OD.SKU
                    , ISNULL(OD.WebSKU,'-') AS WebSKU
                    , C.Contacted
                    , C.Comments                   
                    , OD.Product
                    , O.CustomerID
                    , OD.QuantityOrdered
                    , OD.PricePerUnit
                    , O.Name
                    , O.Company
                    , O.Phone
                    , O.Email
                    , O.ShipName
                    , O.ShipCompany
                    , O.ShipPhone
                    , O.OrderStatus ";

        $selectSlice = "SELECT  
                     OrderNumber
                    , SKU
                    , WebSKU
                    , mitdb.dbo.fn_Get_EncSKUPH(WebSKU) AS EncSKUPH
                    , Contacted
                    , Comments
                    , Product
                    , CustomerID
                    , QuantityOrdered
                    , PricePerUnit
                    , Name
                    , Company
                    , Phone
                    , Email
                    , ShipName
                    , ShipCompany
                    , ShipPhone
                    , OrderStatus                
                    , OrderManager.dbo.fn_Get_Tracking_Number(OrderNumber) as TrackingNumber
                    , OrderManager.dbo.fn_Get_CartName_From_Order(OrderNumber) as CartName
                    ,RowNumber
                    ";


        $wherefields = array('O.OrderNumber','OD.SKU', 'OD.WebSKU', 'C.Contacted', 'C.Comments', 'OD.Product', 'O.CustomerID', 'OD.QuantityOrdered', 'OD.PricePerUnit', 'O.Name', 'O.Company', 'O.Phone', 'O.Email', 'O.ShipName', 'O.ShipCompany', 'O.ShipPhone', 'O.OrderStatus' );
       
        $where .=$this->MCommon->concatAllWerefields($wherefields, $dataSearch);

        $where .=" and (O.oRDERdATE between '{$InitialDate}' and '{$FinalDate}') 
                   and (OD.Adjustment = '0') 
                   and (ISNUMERIC(OD.SKU) = 1) 
                   and (PC.categoryID in (20,21,22,23,24))";



        if ($cartid) {
            $where.=$this->MOrders->filterbycartId($cartid);
        }

        if ($qtyOrdered) {
            if (($qtyOrdered == 2) or ($qtyOrdered == 3)) {
                $where .=" and (QuantityOrdered = {$qtyOrdered})";
            } else {
                $where .=" and (QuantityOrdered >= {$qtyOrdered})";
            }
        }

         if ($status) {
            $where.=$this->MOrders->filterbystatus($status);
        }

        $SQL = "{$select}{$from}{$table}{$where}";

        $query = $this->db->query($SQL);

        $count = $query->num_rows();


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


        $SQL = "WITH mytable AS 
                    (
                    {$select},
                    ROW_NUMBER() OVER (ORDER BY {$sidx} {$sord}) AS RowNumber
               FROM {$table} {$where}) 
               {$selectSlice},RowNumber
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
            $responce->rows[$i]['cell'] = array($row['OrderNumber'],
                $row['OrdNum'] = $row['OrderNumber'],
                $row['SKU'],
                $row['WebSKU'],
                $row['EncSKUPH'],                
                $row['Contacted'],
                $row['Comments'],
                $row['Product']= utf8_encode($row['Product']),
                $row['CustomerID'],
                $row['QuantityOrdered'],
                $row['PricePerUnit'],
                $row['Name']= utf8_encode($row['Name']),
                $row['Company']= utf8_encode($row['Company']),
                $row['Phone']= utf8_encode($row['Phone']),
                $row['Email']= utf8_encode($row['Email']),
                $row['ShipName']=utf8_encode($row['ShipName']),
                $row['ShipCompany']=utf8_encode($row['ShipCompany']),
                $row['ShipPhone']=utf8_encode($row['ShipPhone']),
                $row['OrderStatus'],
                $row['TrackingNumber'],
                $row['CartName'],
            );
            $i++;
        }

        echo json_encode($responce);
    }

    
    function saveData(){
        $ordeNum = $_POST['OrdNum'];
        $contacted = $_POST['Contacted'];
        $comments = $_POST['Comments'];
         
        $SQL = "OrderManager.dbo.sp_Order_ExtendedInfo_Insert {$ordeNum}, {$contacted}, '{$comments}'";
        
        $this->MCommon->saveRecord($SQL,'OrderManagerSave');
        
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

}
?>





