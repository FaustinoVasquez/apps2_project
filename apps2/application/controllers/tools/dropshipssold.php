<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Dropshipssold extends BP_Controller {

    public function __construct() {
        parent::__construct();

        $is_logged_in = $this->session->userdata('is_logged_in');

        if (!isset($is_logged_in) || $is_logged_in != true) {
            redirect(base_url(), 'refresh');
        }
        
        if ($this->MUsers->isValidUser($this->session->userdata('userid'), 884800) != 1) {
               redirect('Catalog/prodcat', 'refresh');
           }
    }

    public function index() {
        //Cargamos la base de datos de OrderManager
        $this->load->model('MOrders', '', TRUE);

        // Define Meta
        $this->title = "MI Technologiesinc - Drop Ships Sold";

        $this->description = "Drop Ships Sold";

        // Define custom CSS
        $this->css = array('menu.css', 'fluid.css', 'jqueryui/redmond/jquery-ui-1.8.21.custom.css', 'jqgrid/ui.jqgrid.css', 'form.css');

        // Define custom javascript
        $this->javascript = array('jqgrid/jquery.jqGrid.min.js','jqueryui/ui/jquery-ui-1.8.21.custom.js','jqgrid/i18n/grid.locale-en.js',  'jqgrid/jquery.jqGrid.fluid.js', 'popup.js');
        //Cargamos la libreria donde esta el menu
        $this->load->library('Layout');
        //Creamos un nuevo layout->menu
        $menu = new Layout;

        $data = array(
            'menu' => $menu->show_menu($this->MUsers->getUserFullName($this->session->userdata('userid'))),
            'from' => '/tools/dropshipssold/',
            'nameGrid' => 'dropshipssold',
            'namePager' => 'dsspager',
            'caption' => 'Drop Ships Sold',
            'subgrid' => 'true',
            'sort' => 'desc',
            'export' => 'DropShipsSold',
            'customerid' => $this->MUsers->getCustomerId($this->session->userdata('userid')),
            'selectedcart' => 0,
            'datasearch'=>'',
            
        );


        //Cargamos al arreglo los colnames
        $data['colNames'] = "['OrderNumber','OrderDate','SKU','MerchantSKU','Description','QtyOrdered','BackOrders','SoldPrice','SoldTotal','CartName']";
        //Cargamos al arreglo los colmodels
        $data['colModel'] = "[
                {name:'OrderNumber',index:'OrderNumber', width:50, align:'center'},
                {name:'OrderDate',index:'OrderDate', width:55, align:'center',formatter:'date', formatoptions: { srcformat:'Y/m/d', newformat:'m/d/Y'}},
                {name:'SKU',index:'SKU', width:55, align:'center' ,formatter: formatLink},
                {name:'MerchantSKU',index:'MerchantSKU', width:70, align:'center'},
                {name:'Description',index:'Description', width:150, align:'left'},
        		{name:'QtyOrdered',index:'QtyOrdered', width:50, align:'center'},
        		{name:'BackOrders',index:'BackOrders', width:60, align:'center',formatter:formatLink2},
                {name:'SoldPrice',index:'SoldPrice', width:60, align:'right',formatter:'currency', formatoptions:{prefix:'$', suffix:' ', thousandsSeparator:','}},
                {name:'SoldTotal',index:'SoldTotal', width:60, align:'right',formatter:'currency', formatoptions:{prefix:'$', suffix:' ', thousandsSeparator:','}},
                {name:'CartName',index:'CartName', width:70, align:'center'} 
  	]";


          //Cargamos la libreria comumn
        $this->load->library('common');

        //Reemplazamos los campos del formulario por los que estan en el arreglo $_POST.
        $data = $this->common->fillPost('ALL', $data);
              
        //cadena de busqueda del grid 
        $data['gridSearch'] = 'getData?&ds=' . $data['datasearch'];


        //Generar el contenido....
        $this->build_content($data);
        $this->render_page();
    }

    function getData() {
        $page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1; // get the requested page
        $limit = isset($_REQUEST['rows']) ? $_REQUEST['rows'] : 10; // get how many rows we want to have into the gri
        $sidx = isset($_REQUEST['sidx']) ? $_REQUEST['sidx'] : 'ProductCatalogId'; // get index row - i.e. user click to sort
        $sord = isset($_REQUEST['sord']) ? $_REQUEST['sord'] : 'desc';

        $dataSearch = !empty($_REQUEST['ds']) ? $_GET['ds'] : '';
        
        $where = '';
        
        $select = "Select O.[OrderNumber] AS OrderNumber
                        ,Cast(O.[OrderDate] AS date) AS OrderDate
                        ,OD.[SKU] AS SKU
                        ,OD.[WebSKU] AS MerchantSKU
                        ,OD.[Product] AS [Description]
                        ,OD.[QuantityOrdered] AS QtyOrdered
                        ,BO.BackOrders AS BackOrders
                        ,OD.[PricePerUnit] AS SoldPrice
                        ,Cast((OD.[QuantityOrdered] * OD.[PricePerUnit]) as money) AS SoldTotal
                        ,SC.[CartName]
                   FROM OrderManager.dbo.Orders AS O
                   LEFT OUTER JOIN OrderManager.dbo.[Order Details] AS OD ON (O.[OrderNumber] = OD.[OrderNumber])
                   LEFT OUTER JOIN OrderManager.dbo.[ShoppingCarts] AS SC ON (O.[CartID] = SC.[ID])
                   LEFT OUTER JOIN Inventory.dbo.[QBProductCatalogBackorders] AS BO ON (CAST(OD.SKU as NVARCHAR(MAX)) = CAST(BO.SKU as NVARCHAR(MAX)))";
                  

        $wherefields = array('OD.[SKU]','OD.[WebSKU]','OD.[Product]','SC.[CartName]');

        $where .=$this->MCommon->concatAllWerefields($wherefields, $dataSearch);


         $where.= " AND O.[OrderSource] = 'AS'
                    AND O.OrderStatus != 'Order Canceled'
                    AND O.OrderStatus != 'Shipped'
                    AND O.OrderStatus != 'Item Returned'
                    AND O.OrderStatus != 'Item Exchanged'
                    AND O.OrderStatus != 'Credit Issued'
                    AND O.Shipping != 'Customer Pickup'
                    AND OD.Adjustment = '0'
                    AND OD.WebSKU LIKE 'DS%'
                   ORDER BY O.OrderDate";

	$SQL = "{$select}{$where}";
         
  
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
            $responce->rows[$i]['id'] = $row['OrderNumber'];
            $responce->rows[$i]['cell'] = array($row['OrderNumber'],
                $row['OrderDate'],
                $row['SKU'],
                $row['MerchantSKU'],
                $row['Description'],
                $row['QtyOrdered'],
		$row['BackOrders'],
                $row['SoldPrice'],
                $row['SoldTotal'],
                $row['CartName'],
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

    function adjustemenIDData() {
        $this->load->model('morders', '', TRUE);
        $adjustmentID = isset($_REQUEST['aid']) ? $_GET['aid'] : '';
        $page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1; // get the requested page
        $limit = isset($_REQUEST['rows']) ? $_REQUEST['rows'] : 10; // get how many rows we want to have into the gri
        $sidx = isset($_REQUEST['sidx']) ? $_REQUEST['sidx'] : 'InventoryAdjustmentsID'; // get index row - i.e. user click to sort
        $sord = isset($_REQUEST['sord']) ? $_REQUEST['sord'] : 'Desc';


        $totalrows = isset($_REQUEST['totalrows']) ? $_REQUEST['totalrows'] : false;
        if ($totalrows) {
            $limit = $totalrows;
        }

        if (!$sidx)
            $sidx = 1;


        $SQL = "SELECT ProductCatalogID,
		       ProductName,
                       Quantity,
                       UserName,
		       Comments
		      FROM [Inventory].[dbo].[vw_InventoryDetails]
			where  InventoryAdjustmentsID = {$adjustmentID} 
		    ";


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
            $responce->rows[$i]['id'] = $row['ProductCatalogID'];
            $responce->rows[$i]['cell'] = array($row['ProductCatalogID'],
             
		$row['ProductName'],
                $row['Quantity'],
                $row['UserName'] = utf8_encode($row['UserName']),
		$row['Comments'] = utf8_encode($row['Comments']),
            );
            $i++;
        }


        echo json_encode($responce);
    }
    
    
    function fillselect(){
        $SQL = "SELECT top 10 ProductCatalogID FROM [Inventory].[dbo].[vw_InventoryDetails] where ProductCatalogID  LIKE '{$_GET['term']}%' group by ProductCatalogID order by ProductCatalogID asc ";
        echo json_encode($this->MCommon->fillAutoComplete($SQL, 'ProductCatalogID'));
    }
    
 

}
?>




