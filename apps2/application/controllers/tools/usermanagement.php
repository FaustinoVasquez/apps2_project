<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Usermanagement extends BP_Controller {

    public function __construct() {
        parent::__construct();

        $is_logged_in = $this->session->userdata('is_logged_in');

        if (!isset($is_logged_in) || $is_logged_in != true) {
            redirect(base_url(), 'refresh');
        }

        if ($this->MUsers->isValidUser($this->session->userdata('userid'), 884060) != 1) {
            redirect('Catalog/prodcat', 'refresh');
        }
    }

    public function index() {

        //Cargamos la base de datos de OrderManager
     //   $this->load->model('MOrders', '', TRUE);

        // Define Meta
        $this->title = "MI Technologiesinc - User Management";

        $this->description = "User Management";

        // Define custom CSS
        $this->css = array('menu.css', 'fluid.css', 'jqueryui/redmond/jquery-ui-1.8.21.custom.css', 'jqgrid/ui.jqgrid.css', 'form.css');

        // Define custom javascript
        $this->javascript = array('jqueryui/ui/jquery-ui-1.8.21.custom.js', 'jqgrid/i18n/grid.locale-en.js', 'jqgrid/jquery.jqGrid.min.js', 'jqgrid/jquery.jqGrid.fluid.js', 'popup.js','site.js');
        //Cargamos la libreria donde esta el menu
        $this->load->library('Layout');
        //Creamos un nuevo layout->menu
        $menu = new Layout;

        $data = array(
            'menu' => $menu->show_menu($this->MUsers->getUserFullName($this->session->userdata('userid'))),
            'from' => '/tools/usermanagement/',
            'caption' => 'User Management',
            'sort' => 'asc',
            'categoriesOptions' => $this->MCatalog->fillCategories(),
            'fullNameOptions' => $this->MUsers->CustIdFullName(),
            'baseUrl' => base_url(),
        );


        $data['colNames'] = "['ID',
                              'SKU',
                              'Make',
                              'Name',
                              'CustomerID',
                              'Availability',
                              'Price',
                              'DSEconomy',
                              'DS2ndDay',
                              'DSOvernight',
                              'Rebate',
                              'Category',
                              'Active',
                              'AutoPrice',
                              'UseLiveRates'
                              ]";

        $data['colModel'] = "[
                {name:'ID',index:'ID', width:50, align:'center', hidden: true},
                {name:'SKU',index:'SKU', width:60, align:'center'},
                {name:'Make',index:'Make', width:60, align:'center'},
                {name:'Name',index:'Name', width:200, align:'left'},
                {name:'CustomerID',index:'CustomerID', width:80, align:'left',editable:true,editoptions:{readonly: 'readonly' }, hidden:true},
                {name:'Availability',index:'Availability', width:60, align:'center'},
                {name:'SalePrice',index:'SalePrice', width:60, align:'center',editable:true, formatter:'currency', formatoptions:{decimalSeparator:'.', thousandsSeparator: ',', decimalPlaces: 2, prefix: '$ '}},
                {name:'DSEconomyPrice',index:'DSEconomyPrice', width:60, align:'center',editable:true,formatter:'currency', formatoptions:{decimalSeparator:'.', thousandsSeparator: ',', decimalPlaces: 2, prefix: '$ '}},
                {name:'DS2ndDayPrice',index:'DS2ndDayPrice', width:60, align:'center',editable:true,formatter:'currency', formatoptions:{decimalSeparator:'.', thousandsSeparator: ',', decimalPlaces: 2, prefix: '$ '}},
                {name:'DS1DayPrice',index:'DS1DayPrice', width:60, align:'center',editable:true,formatter:'currency', formatoptions:{decimalSeparator:'.', thousandsSeparator: ',', decimalPlaces: 2, prefix: '$ '}},
                {name:'Rebate',index:'Rebate', width:60, align:'center',editable:true,formatter:'currency', formatoptions:{decimalSeparator:'.', thousandsSeparator: ',', decimalPlaces: 2, prefix: '$ '}},
                {name:'Category',index:'Category', width:70, align:'center'},
                {name:'Active',index:'Active', width:50, align:'center', formatter: checkboxFormatter,edittype: 'checkbox'}, 
                {name:'AutoPrice',index:'AutoPrice', width:50, align:'center', formatter: checkboxFormatter,edittype: 'checkbox'}, 
                {name:'UseLiveRates',index:'UseLiveRates', width:50, align:'center', formatter: checkboxFormatter,edittype: 'checkbox'},
  	         ]";


       
        //Generar el contenido....
        $this->build_content($data);
        $this->render_page();
    }

    function GridData() {
        $page   = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1; // get the requested page
        $limit  = isset($_REQUEST['rows']) ? $_REQUEST['rows'] : 10; // get how many rows we want to have into the gri
        $sidx = $_GET['sidx']; // get index row - i.e. user click to sort
        $sord = $_GET['sord'];
        //$sidx   = isset($_REQUEST['sidx']) ? $_REQUEST['sidx'] : 'CSP.[ProductCatalogID]'; // get index row - i.e. user click to sort
        //$sord   = isset($_REQUEST['sord']) ? $_REQUEST['sord'] : 'desc';

        
        $customerid = !empty($_GET['cuid']) ? $_GET['cuid'] : 0;
        $category   = !empty($_GET['cat']) ? $_GET['cat'] : 0;
        $datasearch = !empty($_GET['ds']) ? $_GET['ds'] : '';



        //Select utilizado para realizar el conteo del numero de registros devueltos por la consulta.
        $selectCount = 'SELECT COUNT(*) AS rowNum';


        //Se utiliza en la seccion que extrae los registros de la hoja activa
        $select = " SELECT A.[ID]
                        ,A.[SKU]
                        ,A.[Make]
                        ,A.[Name]
                        ,A.[CustomerID]
                        ,A.[Quantity]
                        ,A.[Price]
                        ,A.[DSEconomy]
                        ,A.[DS2ndDay]
                        ,A.[DSOvernight]
                        ,A.[Rebate]
                        ,A.[CategoryID]
                        ,A.[Active]
                        ,A.[AutoPrice]
                        ,A.[UseLiveRates] ";

      
        $selectSlice = " SELECT ID
                                ,SKU
                                ,Make
                                ,Name
                                ,CustomerID
                                ,Quantity
                                ,Price
                                ,DSEconomy
                                ,DS2ndDay
                                ,DSOvernight
                                ,Rebate
                                ,CategoryID
                                ,Inventory.[dbo].fn_GetCategoryName(CategoryID) as Category
                                ,Active
                                ,AutoPrice
                                ,UseLiveRates ";

        $from = " FROM (
                        SELECT csp.[ID] AS ID
                              ,csp.[ProductCatalogID] AS SKU 
                              ,pc.Manufacturer AS Make
                              ,pc.Name AS Name
                              ,csp.[CustomerID] AS CustomerID
                              ,CASE WHEN Inventory.dbo.fn_Get_Global_Stock(pc.ID) > '1' THEN 'In Stock' 
                               ELSE 'Out of Stock' END AS [Quantity] 
                              ,csp.[SalePrice] AS Price
                              ,csp.[DSEconomyPrice] AS DSEconomy
                              ,csp.[DS2ndDayPrice] AS DS2ndDay
                              ,csp.[DS1DayPrice] AS DSOvernight
                              ,csp.[Rebate]
                              ,pc.CategoryID AS [CategoryID]
                              ,csp.[Active]
                              ,csp.[AutoPrice]
                              ,csp.[UseLiveRates]

                        FROM [Inventory].[dbo].[CustomerSpecificPricing] csp
                        LEFT JOIN [Inventory].[dbo].[ProductCatalog] pc ON csp.ProductCatalogID = pc.ID
                        LEFT JOIN [Inventory].[dbo].[Categories] ctg ON pc.CategoryID = ctg.ID 
                    ) A ";



        $wherefields = array('A.SKU', 'A.Make', 'A.Name');
        $where =$this->MCommon->concatAllWerefields($wherefields, $datasearch);
        $where .= " and A.CustomerID = '{$customerid}'";

        if ($category){
            $where .= " and A.CategoryID = '{$category}'";
        }
        
        $SQL = "{$selectCount}{$from}{$where}";


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

        $SQL = "WITH mytable AS ($select , ROW_NUMBER() OVER (ORDER BY [$sidx] $sord) AS RowNumber
                    {$from}{$where})
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
            $responce->rows[$i]['id'] = $row['ID'];
            $responce->rows[$i]['cell'] = array($row['ID'],
                $row['SKU'],
                $row['Make'],
                $row['Name'],
                $row['CustomerID'],
                $row['Quantity'],
                $row['Price'],
                $row['DSEconomy'],
                $row['DS2ndDay'],
                $row['DSOvernight'],
                $row['Rebate'],
                $row['Category'],
                $row['Active'],
                $row['AutoPrice'],
                $row['UseLiveRates']
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

    function loadCatalog()
    {
        $data = array(
            'from' => '/tools/usermanagement/ajax_refresh',
            'productLineOptions' => $this->MCatalog->fillProductLines(),
        ); 
        $this->load->view('/pages/catalog',$data);
    }  
    
    function validateSku(){
        $sku = $_GET['sku'];
        $customerID = $_GET['cuid'];
        $response = 0;
        
        $SQL = "Select count(ID) as existe from Inventory.dbo.ProductCatalog where ID={$sku}" ;
        
        $result = $this->MCommon->getOneRecord($SQL);
        
        if ($result['existe']){ // Si el Sku existe en ProductCatalog Continuamos
            $SQL="Select count(ProductCatalogID) as existe from Inventory.dbo.CustomerSpecificPricing where ProductCatalogID={$sku} and CustomerID = {$customerID} ";
            
            $result = $this->MCommon->getOneRecord($SQL);
            
            if(!$result['existe']){ // Si el Sku no existe en CustomerSpecificPricing continuamos
              $response = 1;
            }
        }
            
        echo json_encode($response);
    }
    
    function getMakeAndName(){
        $sku = $_GET['sku'];
        $SQL = "Select Manufacturer, Name from Inventory.dbo.ProductCatalog where ID={$sku}" ;
        $result = $this->MCommon->getOneRecord($SQL);
        
        echo json_encode($result);
        
    }

    function editSKU()
    {
        $sup = $this->input->post('id');

        //$SQL = "SELECT * FROM [Inventory].[dbo].[CustomerSpecificPricingManagement] WHERE ID = {$cus}";

        if (isset($_POST['SalePrice'])) {
                $SalePrice = $_POST['SalePrice'];
                $SQL = "UPDATE [Inventory].[dbo].[CustomerSpecificPricing] SET SalePrice={$SalePrice},Autoprice = 0 WHERE ID={$sup}";
        }

        if (isset($_POST['DSEconomyPrice'])) {
                $DSEconomyPrice = $_POST['DSEconomyPrice'];
                $SQL = "UPDATE [Inventory].[dbo].[CustomerSpecificPricing] SET DSEconomyPrice='{$DSEconomyPrice}',Autoprice = 0 WHERE ID={$sup}";
        }

        if (isset($_POST['DS2ndDayPrice'])) {
                $DS2ndDayPrice = $_POST['DS2ndDayPrice'];
                $SQL = "UPDATE [Inventory].[dbo].[CustomerSpecificPricing] SET DS2ndDayPrice='{$DS2ndDayPrice}',Autoprice = 0 WHERE ID={$sup}";
        }

        if (isset($_POST['DS1DayPrice'])) {
                $DS1DayPrice = $_POST['DS1DayPrice'];
                $SQL = "UPDATE [Inventory].[dbo].[CustomerSpecificPricing] SET DS1DayPrice='{$DS1DayPrice}',Autoprice = 0 WHERE ID={$sup}";
        }

        if (isset($_POST['Rebate'])) {
                $Rebate = $_POST['Rebate'];
                $SQL = "UPDATE [Inventory].[dbo].[CustomerSpecificPricing] SET Rebate='{$Rebate}',Autoprice = 0  WHERE ID={$sup}";
        }

        if (isset($_POST['checkbox'])) {
               $name = ($_POST['checkbox']);
               $value = ($_POST['value']);

               $value = ($_POST['value'] === 'true') ? 1:0;

               $SQL = "UPDATE [Inventory].[dbo].[CustomerSpecificPricing] set $name='{$value}' where ID={$sup}";
          }
        print_r($SQL);
        $this->MCommon->saveRecord($SQL,'Inventory');
    }


   public function autoPrice(){
        $customerID = $this->input->post('cuid'); 
        $category = $this->input->post('cat');  
        $type = $this->input->post('mtype'); 

        $SQL = " UPDATE CSP SET CSP.[Autoprice] = '{$type}' FROM [Inventory].[dbo].[CustomerSpecificPricing] AS CSP
                 LEFT OUTER JOIN [Inventory].[dbo].[ProductCatalog] AS PC ON (CSP.[ProductCatalogID] = PC.[ID])
                 where customerid = '{$customerID}' ";
        if ($category){
         $SQL .= " and pc.[CategoryID] = '{$category}' ";
        }

        $this->MCommon->saveRecord($SQL,'Inventory');

    }


}
?>
