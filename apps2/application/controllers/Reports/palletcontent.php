<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Palletcontent extends BP_Controller {

    public function __construct() {
        parent::__construct();

        $is_logged_in = $this->session->userdata('is_logged_in');

        if (!isset($is_logged_in) || $is_logged_in != true) {
            redirect(base_url(), 'refresh');
        }

        // if ($this->MUsers->isValidUser($this->session->userdata('userid'), 884060) != 1) {
        //     redirect('Catalog/prodcat', 'refresh');
        // }
    }

    public function index() { 

        // Define Meta
        $this->title = "MI Technologiesinc - Pallet Content";

        $this->description = "Pallet Content";

        // Define custom CSS
        $this->css = array('menu.css', 'fluid.css', 'jqueryui/redmond/jquery-ui-1.8.21.custom.css', 'jqgrid/ui.jqgrid.css', 'form.css','jquery.datetimepicker.css');

        // Define custom javascript
        $this->javascript = array('jqueryui/ui/jquery-ui-1.8.21.custom.js', 'jqgrid/i18n/grid.locale-en.js', 'jqgrid/jquery.jqGrid.min.js', 'jqgrid/jquery.jqGrid.fluid.js', 'popup.js','jquery.datetimepicker.js');
        //Cargamos la libreria donde esta el menu
        $this->load->library('Layout');
        //Creamos un nuevo layout->menu
        $menu = new Layout;

        $data = array(
            'menu' => $menu->show_menu($this->MUsers->getUserFullName($this->session->userdata('userid'))),
            'from' => 'Reports/palletcontent',
            'caption' => 'Pallet Content',
            'sort' => 'asc',
            'dateFrom' => date('Y/m/d 0:0',strtotime("-1 days")),
            'dateTo' =>date('Y/m/d 23:59'),
        );


        $data['headers'] = "['SKU','QuantityShipped','OrderNum','Carrier','Notes','ShippersMethod','DateAdded','TrackingID','NumericKey','CartName','Packer']";

        $data['body'] = "[
                {name:'SKU',index:'SKU', width:60, align:'center'},
                {name:'QuantityShipped',index:'QuantityShipped', width:60, align:'center'},
                {name:'OrderNum',index:'OrderNum', width:60, align:'center'},
                {name:'Carrier',index:'Carrier', width:60, align:'center'},
                {name:'Notes',index:'Notes', width:100, align:'center'},
                {name:'ShippersMethod',index:'ShippersMethod', width:60, align:'center'},
                {name:'DateAdded',index:'DateAdded', width:90, align:'center'},
                {name:'TrackingID',index:'TrackingID', width:90, align:'center'},
                {name:'NumericKey',index:'NumericKey', width:60, align:'center'},
                {name:'CartName',index:'CartName', width:90, align:'center'},
                {name:'Packer',index:'Packer', width:60, align:'center'},
  	]";
    
        //Generar el contenido....
        $this->build_content($data);
        $this->render_page();
    }

    function GridData() {
        $page   = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1; // get the requested page
        $limit  = isset($_REQUEST['rows']) ? $_REQUEST['rows'] : 10; // get how many rows we want to have into the gri
        $sidx   = isset($_REQUEST['sidx']) ? $_REQUEST['sidx'] : 'COD.SKU'; // get index row - i.e. user click to sort
        $sord   = isset($_REQUEST['sord']) ? $_REQUEST['sord'] : 'desc';

        
        $dataSearch = !empty($_GET['ds']) ? $_GET['ds'] : '';
        $carrier = $this->input->get('carrier');
        $from = $this->input->get('from');
        $to = $this->input->get('to');

        $where = '';

        //Campos en sobre los que se haran busquedas de palabras..
        $wherefields = array('OD.[SKU]', 'TRK.[OrderNum]', 'TRK.[TrackingID]', 'TRK.[NumericKey]', 'CART.[CartName]');
        //creamos el were concatenando todos los nombres de campos y las palabras de busqueda
        $where .= $this->MCommon->concatAllWerefields($wherefields, $dataSearch);

        if ($carrier == "USPS"){

        //Si el carrier es USPS
        $SQL = " SELECT 
                     OD.[SKU]
                    ,OD.[QuantityShipped]
                    ,TRK.[OrderNum]
                    ,TRK.[Carrier]
                    ,TRK.[Notes]
                    ,TRK.[ShippersMethod]
                    ,TRK.[DateAdded]
                    ,TRK.[TrackingID]
                    ,TRK.[NumericKey]
                    ,CART.[CartName]
                    ,PK.[Packer]
              FROM [OrderManager].[dbo].[Tracking] AS TRK
              LEFT OUTER JOIN OrderManager.[DBO].Orders AS O ON (TRK.[OrderNum] = O.[OrderNumber])
              LEFT OUTER JOIN OrderManager.[dbo].[Order Details] AS OD ON (TRK.[OrderNum] = OD.[OrderNumber])
              LEFT OUTER JOIN OrderManager.[dbo].[ShoppingCarts] AS CART ON (Cast(O.[CartID] as nvarchar(max)) = Cast(CART.[ID] as nvarchar(max)))
              LEFT OUTER JOIn [OrderManager].[dbo].[Packing] AS PK On (Trk.[OrderNum] = PK.[OrderNumber])
              $where and (dateadded between '{$from}' and '{$to}') and carrier = 'USPS' and OD.[Adjustment] = '0' and O.[CartID] = '48'  and (PK.[Packer] = 'FCC' or PK.[Packer] = 'IV' or PK.[Packer] is null)
              order by cartname, shippersmethod  ";
        }else{

         $SQL ="SELECT 
                 OD.[SKU]
                ,OD.[QuantityShipped]
                ,TRK.[OrderNum]
                ,TRK.[Carrier]
                ,TRK.[Notes]
                ,TRK.[ShippersMethod]
                ,TRK.[DateAdded]
                ,TRK.[TrackingID]
                ,TRK.[NumericKey]
                ,CART.[CartName]
                ,PK.[Packer]
          FROM [OrderManager].[dbo].[Tracking] AS TRK
          LEFT OUTER JOIN OrderManager.[DBO].Orders AS O ON (TRK.[OrderNum] = O.[OrderNumber])
          LEFT OUTER JOIN OrderManager.[dbo].[Order Details] AS OD ON (TRK.OrderNum = OD.[OrderNumber])
          LEFT OUTER JOIN OrderManager.[dbo].[ShoppingCarts] AS CART ON (Cast(O.[CartID] as nvarchar(max)) = Cast(CART.[ID] as nvarchar(max)))
          LEFT OUTER JOIn [OrderManager].[dbo].[Packing] AS PK On (Trk.OrderNum = PK.[OrderNumber])
          $where and (dateadded between '{$from}' and '{$to}') and carrier != 'FXM' and carrier != 'PU' and carrier != 'RRTS' and carrier != 'TWNE' and carrier != 'DPHE' and carrier != 'ITSE' and carrier != 'SAIA'  and OD.[Adjustment] = '0' and (O.CartID = '48' and Trk.carrier != 'USPS')   and (PK.[Packer] = 'FCC' or PK.[Packer] = 'IV' or PK.Packer is null)
          --order by cartname, shippersmethod

          UNION

           SELECT 
                 OD.[SKU]
                ,OD.[QuantityShipped]
                ,TRK.[OrderNum]
                ,TRK.[Carrier]
                ,TRK.[Notes]
                ,TRK.[ShippersMethod]
                ,TRK.[DateAdded]
                ,TRK.[TrackingID]
                ,TRK.[NumericKey]
                ,CART.[CartName]
                ,PK.[Packer]
          FROM [OrderManager].[dbo].[Tracking] AS TRK
          LEFT OUTER JOIN OrderManager.[DBO].Orders AS O ON (TRK.OrderNum = O.[OrderNumber])
          LEFT OUTER JOIN OrderManager.[dbo].[Order Details] AS OD ON (TRK.[OrderNum] = OD.[OrderNumber])
          LEFT OUTER JOIN OrderManager.[dbo].[ShoppingCarts] AS CART ON (Cast(O.[CartID] as nvarchar(max)) = Cast(CART.[ID] as nvarchar(max)))
          LEFT OUTER JOIn [OrderManager].[dbo].[Packing] AS PK On (Trk.OrderNum = PK.[OrderNumber])
          $where and (dateadded between '{$from}' and '{$to}') and carrier != 'FXM' and carrier != 'PU' and carrier != 'RRTS' and carrier != 'TWNE' and carrier != 'DPHE' and carrier != 'ITSE' and carrier != 'SAIA' and OD.[Adjustment] = '0' and O.CartID != '48'  and (PK.[Packer] = 'FCC' or PK.[Packer] = 'IV' or PK.[Packer] is null)
          order by cartname, shippersmethod ";
        }


    //  echo $SQL;

       //  $wherefields = array('csp.ProductCatalogID', 'pc.Manufacturer', 'pc.Name');
         
      //   $where =$this->MCommon->concatAllWerefields($wherefields, $datasearch);

        // $where .= " and CSP.CustomerID = {$customerid}";

    
        
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
            $responce->rows[$i]['id'] = $row['SKU'];
            $responce->rows[$i]['cell'] = array($row['SKU'],
                $row['QuantityShipped'],
                $row['OrderNum'],
                $row['Carrier'],
                $row['Notes'],
                $row['ShippersMethod'],
                $row['DateAdded'],
                $row['TrackingID'],
                $row['NumericKey'],
                $row['CartName'],
                $row['Packer'],
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

    //  function loadCatalog(){
    //    $data = array(
    //         'from' => '/tools/usermanagement/ajax_refresh',
    //         'productLineOptions' => $this->MCatalog->fillProductLines(),
    //     ); 
    //   $this->load->view('/pages/catalog',$data);
    // }  
    
    
    
    // function validateSku(){
    //     $sku = $_GET['sku'];
    //     $customerID = $_GET['cuid'];
    //     $response = 0;
        
    //     $SQL = "Select count(ID) as existe from Inventory.dbo.ProductCatalog where ID={$sku}" ;
        
    //     $result = $this->MCommon->getOneRecord($SQL);
        
    //     if ($result['existe']){ // Si el Sku existe en ProductCatalog Continuamos
    //         $SQL="Select count(ProductCatalogID) as existe from Inventory.dbo.CustomerSpecificPricing where ProductCatalogID={$sku} and CustomerID = {$customerID} ";
            
    //         $result = $this->MCommon->getOneRecord($SQL);
            
    //         if(!$result['existe']){ // Si el Sku no existe en CustomerSpecificPricing continuamos
    //           $response = 1;
    //         }
    //     }
            
    //     echo json_encode($response);
    // }
    
    // function getMakeAndName(){
    //     $sku = $_GET['sku'];
    //     $SQL = "Select Manufacturer, Name from Inventory.dbo.ProductCatalog where ID={$sku}" ;
    //     $result = $this->MCommon->getOneRecord($SQL);
        
    //     echo json_encode($result);
        
    // }
    


    // function editSKU(){
    //     $ProductCatalogID   =$this->input->post('id');
    //     $CustomerID         =$this->input->post('CustomerID');
    //     $SalePrice          =$this->input->post('Price');
    //     $DSEconomyPrice     =$this->input->post('DSEconomy');
    //     $DS2ndDayPrice      =$this->input->post('DS2ndDay');
    //     $DS1DayPrice        =$this->input->post('DSOvernight');
    //     $Rebate             =$this->input->post('Rebate');
    //     $Active             =$this->input->post('Active');
       

    //     $SQL= "update Inventory.dbo.CustomerSpecificPricing
    //             set 
    //                  SalePrice=$SalePrice
    //                 ,DSEconomyPrice=$DSEconomyPrice
    //                 ,DS2ndDayPrice=$DS2ndDayPrice
    //                 ,DS1DayPrice=$DS1DayPrice
    //                 ,Rebate=$Rebate
    //                 ,Active = $Active
    //             where
    //                 ProductCatalogID = {$ProductCatalogID} and CustomerID = $CustomerID";

                    
    //     $this->MCommon->saveRecord($SQL,'Inventory');
    // }

}
?>
