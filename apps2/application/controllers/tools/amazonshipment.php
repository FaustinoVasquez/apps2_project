<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Amazonshipment extends BP_Controller {

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
        $this->css = array('menu.css', 'fluid.css', 'jqueryui/redmond/jquery-ui-1.8.21.custom.css', 'jqgrid/ui.jqgrid.css', 'form.css');

        // Define custom javascript
        $this->javascript = array('jqueryui/ui/jquery-ui-1.8.21.custom.js', 'jqgrid/i18n/grid.locale-en.js', 'jqgrid/jquery.jqGrid.min.js', 'jqgrid/jquery.jqGrid.fluid.js');
        //Cargamos la libreria donde esta el menu
        $this->load->library('Layout');
        //Creamos un nuevo layout->menu
        $menu = new Layout;

        $data = array(
            'menu' => $menu->show_menu($this->MUsers->getUserFullName($this->session->userdata('userid'))),
            'from' => 'tools/amazonshiptment',
            'caption' => 'Amazon Shipment',
            'sort' => 'asc',
        );


        $data['headers'] = "['MerchantSKU','Title','ASIN','FNSKU','Shipped','Pallet']";

        $data['body'] = "[
                {name:'MerchantSKU',index:'MerchantSKU', width:60, align:'center'},
                {name:'Title',index:'Title', width:200, align:'center'},
                {name:'ASIN',index:'ASIN', width:60, align:'center'},
                {name:'FNSKU',index:'FNSKU', width:60, align:'center'},
                {name:'Shipped',index:'Shipped', width:60, align:'center'},
                {name:'Pallet',index:'Pallet', width:60, align:'center'},
  	]";
    
        //Generar el contenido....
        $this->build_content($data);
        $this->render_page();
    }

    function GridData() {
        $page   = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1; // get the requested page
        $limit  = isset($_REQUEST['rows']) ? $_REQUEST['rows'] : 10; // get how many rows we want to have into the gri
        $sidx   = isset($_REQUEST['sidx']) ? $_REQUEST['sidx'] : 'MerchantSKU'; // get index row - i.e. user click to sort
        $sord   = isset($_REQUEST['sord']) ? $_REQUEST['sord'] : 'desc';

        
        $dataSearch = !empty($_GET['ds']) ? $_GET['ds'] : '';
        $carrier = $this->input->get('carrier');
        $from = $this->input->get('from');
        $to = $this->input->get('to');

        $where = '';

        //Campos en sobre los que se haran busquedas de palabras..
        $wherefields = array('MerchantSKU', 'Title', 'ASIN', 'FNSKU', 'Shipped','Pallet');
        //creamos el were concatenando todos los nombres de campos y las palabras de busqueda
        $where .= $this->MCommon->concatAllWerefields($wherefields, $dataSearch);

        //Si el carrier es USPS
        $SQL = " SELECT * FROM [Inventory].[dbo].[Amazon_Pallets]".$where;

    
        
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
            $responce->rows[$i]['id'] = $row['MerchantSKU'];
            $responce->rows[$i]['cell'] = array($row['MerchantSKU'],
                $row['Title'],
                $row['ASIN'],
                $row['FNSKU'],
                $row['Shipped'],
                $row['Pallet'],
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


    function loadExcel(){
        $this->load->library('excel_reader');
        $this->excel_reader->read('upload/Test.xls');

        $fileLocation = 'upload/Test.xls';
        if( !file_exists( $fileLocation ) ) die( 'File could not be found at: ' . $fileLocation );
 


       $worksheet = $this->excel_reader->sheets[0];

$numRows = $worksheet['numRows']; // ex: 14
$numCols = $worksheet['numCols']; // ex: 4
$cells = $worksheet['cells']; //

print_r($cells);

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
