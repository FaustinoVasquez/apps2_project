<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Productsearch extends BP_Controller {

    public function __construct() {
        parent::__construct();

        $is_logged_in = $this->session->userdata('is_logged_in');

        if (!isset($is_logged_in) || $is_logged_in != true) {
            redirect(base_url(), 'refresh');
        }
                
         if ($this->MUsers->isValidUser($this->session->userdata('userid'), 881200) != 1) {
               redirect('Catalog/prodcat', 'refresh');
           }
    }

    function index() {
        // Define Meta
        $this->title = "MI Technologiesinc - Configurator";

        $this->description = "Configurator";

        // Define custom CSS
        $this->css = array('menu.css', 'form.css', 'fluid.css', 'table.css', 'jqueryui/redmond/jquery-ui-1.8.21.custom.css', 'jqgrid/ui.jqgrid.css');

        // Define custom javascript
        $this->javascript = array('jqueryui/ui/jquery-ui-1.8.21.custom.js', 'jqgrid/i18n/grid.locale-en.js', 'jqgrid/jquery.jqGrid.min.js', 'jqgrid/jquery.jqGrid.fluid.js', 'popup.js','site.js');

        $this->load->library('Layout');
        $menu = new Layout;


        $userid = $this->session->userdata('userid');

        $data = array(
            'menu' => $menu->show_menu($this->MUsers->getUserFullName($this->session->userdata('userid'))),
            //Datos para el grid
            'caption' => 'Product Search II',
            'from' => '/Catalog/productsearch/',
            'search' => '',
            'export' => 'product_search',
        );

        $data['colNames'] = "['CatalogID','Brand', 'PartNumber','GenericBareSKU','GenericBareCost','GenericBare-tQOH','GenericEncSKU','GenericEncCost','GenericEnc-tQOH'
        ,'PhilipsBareSKU','PhilipsBareCost','PhilipsBare-tQOH','PhilipsEncSKU','PhilipsEncCost','PhilipsEnc-tQOH','OsramBareSKU','OsramBareCost','OsramBare-tQOH'
        ,'OsramEncSKU','OsramEncCost','OsramEnc-tQOH','NeoLuxBareSKU','NeoLuxBareCost','NeoLuxBare-tQOH','NeoLuxEncSKU','NeoLuxEncCost','NeoLuxEnc-tQOH','PhoenixBareSKU'
        ,'PhoenixBareCost','PhoenixBare-tQOH','PhoenixEncSKU','PhoenixEncCost','PhoenixEnc-tQOH','UshioBareSKU','UshioBareCost','UshioBare-tQOH','UshioEncSKU','UshioEncCost'
        ,'UshioEnc-tQOH','OEMBareSKU','OEMBareCost','OEMBare-tQOH','OEMEncSKU','OEMEncCost','OEMEnc-tQOH','SearchTerms']";


        $data['colModel'] = "[
                {name:'CatalogID',index:'CatalogID', width:70, align:'center',sorttype:'int' },
                {name:'BrandI',index:'Brand', width:100, align:'left'},
                {name:'PartNumber',index:'PartNumber', width:90, align:'left'},
                {name:'GenericBareSKU',index:'GenericBareSKU', width:100, align:'center'},
                {name:'GenericBareCost',index:'GenericBareCost', width:100, align:'right',formatter:'number', formatoptions:{decimalSeparator:'.',thousandsSeparator: ',', decimalPlaces: 2, prefix: '$ ' }},
                {name:'GenericBare-tQOH',index:'GenericBare-tQOH', width:100, align:'center'},
                {name:'GenericEncSKU',index:'GenericEncSKU', width:100, align:'center'},
                {name:'GenericEncCost',index:'GenericEncCost', width:100, align:'right',formatter:'number', formatoptions:{decimalSeparator:'.',thousandsSeparator: ',', decimalPlaces: 2, prefix: '$ ' }},
                {name:'GenericEnc-tQOH',index:'GenericEnc-tQOH', width:100, align:'center'},   
                {name:'PhilipsBareSKU',index:'PhilipsBareSKU', width:100, align:'center'},   
                {name:'PhilipsBareCost',index:'PhilipsBareCost', width:100, align:'right',formatter:'number', formatoptions:{decimalSeparator:'.',thousandsSeparator: ',', decimalPlaces: 2, prefix: '$ ' }},   
                {name:'PhilipsBare-tQOH',index:'PhilipsBare-tQOH', width:100, align:'center'},   
                {name:'PhilipsEncSKU',index:'PhilipsEncSKU', width:100, align:'center'},   
                {name:'PhilipsEncCost',index:'PhilipsEncCost', width:100, align:'right',formatter:'number', formatoptions:{decimalSeparator:'.',thousandsSeparator: ',', decimalPlaces: 2, prefix: '$ ' }},   
                {name:'PhilipsEnc-tQOH',index:'PhilipsEnc-tQOH', width:100, align:'center'},   
                {name:'OsramBareSKU',index:'OsramBareSKU', width:100, align:'center'},   
                {name:'OsramBareCost',index:'OsramBareCost', width:100, align:'right',formatter:'number', formatoptions:{decimalSeparator:'.',thousandsSeparator: ',', decimalPlaces: 2, prefix: '$ ' }},   
                {name:'OsramBare-tQOH',index:'OsramBare-tQOH', width:100, align:'center'},   
                {name:'OsramEncSKU',index:'OsramEncSKU', width:100, align:'center'},    
                {name:'OsramEncCost',index:'OsramEncCost', width:100, align:'right',formatter:'number', formatoptions:{decimalSeparator:'.',thousandsSeparator: ',', decimalPlaces: 2, prefix: '$ ' }},   
                {name:'OsramEnc-tQOH',index:'OsramEnc-tQOH', width:100, align:'center'},   
                {name:'NeoLuxBareSKU',index:'NeoLuxBareSKU', width:100, align:'center'},   
                {name:'NeoLuxBareCost',index:'NeoLuxBareCost', width:100, align:'right',formatter:'number', formatoptions:{decimalSeparator:'.',thousandsSeparator: ',', decimalPlaces: 2, prefix: '$ ' }},   
                {name:'NeoLuxBare-tQOH',index:'NeoLuxBare-tQOH', width:100, align:'center'},   
                {name:'NeoLuxEncSKU',index:'NeoLuxEncSKU', width:100, align:'center'},   
                {name:'NeoLuxEncCost',index:'NeoLuxEncCost', width:100, align:'right',formatter:'number', formatoptions:{decimalSeparator:'.',thousandsSeparator: ',', decimalPlaces: 2, prefix: '$ ' }},   
                {name:'NeoLuxEnc-tQOH',index:'NeoLuxEnc-tQOH', width:100, align:'center'},   
                {name:'PhoenixBareSKU',index:'PhoenixBareSKU', width:100, align:'center'},   
                {name:'PhoenixBareCost',index:'PhoenixBareCost', width:100, align:'right',formatter:'number', formatoptions:{decimalSeparator:'.',thousandsSeparator: ',', decimalPlaces: 2, prefix: '$ ' }},   
                {name:'PhoenixBare-tQOH',index:'PhoenixBare-tQOH', width:100, align:'center'},   
                {name:'PhoenixEncSKU',index:'PhoenixEncSKU', width:100, align:'center'},               
                {name:'PhoenixEncCost',index:'PhoenixEncCost', width:100, align:'right',formatter:'number', formatoptions:{decimalSeparator:'.',thousandsSeparator: ',', decimalPlaces: 2, prefix: '$ ' }},   
                {name:'PhoenixEnc-tQOH',index:'PhoenixEnc-tQOH', width:100, align:'center'},   
                {name:'UshioBareSKU',index:'UshioBareSKU', width:100, align:'center'},   
                {name:'UshioBareCost',index:'UshioBareCost', width:100, align:'right',formatter:'number', formatoptions:{decimalSeparator:'.',thousandsSeparator: ',', decimalPlaces: 2, prefix: '$ ' }},               
                {name:'UshioBare-tQOH',index:'UshioBare-tQOH', width:100, align:'center'},   
                {name:'UshioEncSKU',index:'UshioEncSKU', width:100, align:'center'}, 
                {name:'UshioEncCost',index:'UshioEncCost', width:100, align:'right',formatter:'number', formatoptions:{decimalSeparator:'.',thousandsSeparator: ',', decimalPlaces: 2, prefix: '$ ' }},   
                {name:'UshioEnc-tQOH',index:'UshioEnc-tQOH', width:100, align:'center'},               
                {name:'OEMBareSKU',index:'OEMBareSKU', width:100, align:'center'},   
                {name:'OEMBareCost',index:'OEMBareCost', width:100, align:'right',formatter:'number', formatoptions:{decimalSeparator:'.',thousandsSeparator: ',', decimalPlaces: 2, prefix: '$ ' }},  
                {name:'OEMBare-tQOH',index:'OEMBare-tQOH', width:100, align:'center'},   
                {name:'OEMEncSKU',index:'OEMEncSKU', width:100, align:'center'},
                {name:'OEMEncCost',index:'OEMEncCost', width:100, align:'right',formatter:'number', formatoptions:{decimalSeparator:'.',thousandsSeparator: ',', decimalPlaces: 2, prefix: '$ ' }},   
                {name:'OEMEnc-tQOH',index:'OEMEnc-tQOH', width:800, align:'center'}, 
                {name:'SearchTerms',index:'SearchTerms', width:100, align:'center'}, 

  	]";



        $this->build_content($data);
        $this->render_page();
    }



    public function getData() {


        $examp = isset($_GET['q']) ? $_GET['q'] : 1;  //query number
        $page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1; // get the requested page
        $limit = isset($_REQUEST['rows']) ? $_REQUEST['rows'] : 10; // get how many rows we want to have into the gri
        $sidx = isset($_REQUEST['sidx']) ? $_REQUEST['sidx'] : 'PJD.[CatalogID]'; // get index row - i.e. user click to sort
        $sord = isset($_REQUEST['sord']) ? $_REQUEST['sord'] : '';


        $search = $this->input->get('ds');

        //Select utilizado para realizar el conteo del numero de registros devueltos por la consulta.
        $selectCount = 'SELECT COUNT(*) AS rowNum';

        //Select General con los campos necesarios para la vista
        $select = "SELECT PJD.[CatalogID]
                          ,PJD.[Brand]
                          ,PJD.[PartNumber]
                          
                          ,PJD.[BareSKU] AS 'GenericBareSKU'
                          ,CAST(IsNull((SELECT [UnitCost] FROM [Inventory].[dbo].[ProductCatalog] WHERE [ID] = PJD.[BareSKU]),0) AS Decimal(10,2)) AS 'GenericBareCost'
                          ,CAST(IsNull((SELECT [TotalStock] FROM [Inventory].[dbo].[Global_Stocks] WHERE [ProductCatalogID] = PJD.[BareSKU]),0) AS INT) AS 'GenericBare-tQOH'
                          
                          ,PJD.[EncSKU] AS 'GenericEncSKU'
                          ,CAST(IsNull((SELECT [UnitCost] FROM [Inventory].[dbo].[ProductCatalog] WHERE [ID] = PJD.[EncSKU]),0) AS Decimal(10,2)) AS 'GenericEncCost'
                          ,CAST(IsNull((SELECT [TotalStock] FROM [Inventory].[dbo].[Global_Stocks] WHERE [ProductCatalogID] = PJD.[EncSKU]),0) AS INT) AS 'GenericEnc-tQOH'
                          
                          ,PJD.[BareSKUPH] AS 'PhilipsBareSKU'
                          ,CAST(IsNull((SELECT [UnitCost] FROM [Inventory].[dbo].[ProductCatalog] WHERE [ID] = PJD.[BareSKUPH]),0) AS Decimal(10,2)) AS 'PhilipsBareCost'
                          ,CAST(IsNull((SELECT [TotalStock] FROM [Inventory].[dbo].[Global_Stocks] WHERE [ProductCatalogID] = PJD.[BareSKUPH]),0) AS INT) AS 'PhilipsBare-tQOH'
                          
                          ,PJD.[EncSKUPH] AS 'PhilipsEncSKU'
                          ,CAST(IsNull((SELECT [UnitCost] FROM [Inventory].[dbo].[ProductCatalog] WHERE [ID] = PJD.[EncSKUPH]),0) AS Decimal(10,2)) AS 'PhilipsEncCost'
                          ,CAST(IsNull((SELECT [TotalStock] FROM [Inventory].[dbo].[Global_Stocks] WHERE [ProductCatalogID] = PJD.[EncSKUPH]),0) AS INT) AS 'PhilipsEnc-tQOH'
                          
                          ,PJD.[BareSKUOS] AS 'OsramBareSKU'
                          ,CAST(IsNull((SELECT [UnitCost] FROM [Inventory].[dbo].[ProductCatalog] WHERE [ID] = PJD.[BareSKUOS]),0) AS Decimal(10,2)) AS 'OsramBareCost'
                          ,CAST(IsNull((SELECT [TotalStock] FROM [Inventory].[dbo].[Global_Stocks] WHERE [ProductCatalogID] = PJD.[BareSKUOS]),0) AS INT) AS 'OsramBare-tQOH'
                          
                          ,PJD.[EncSKUOS] AS 'OsramEncSKU'
                          ,CAST(IsNull((SELECT [UnitCost] FROM [Inventory].[dbo].[ProductCatalog] WHERE [ID] = PJD.[EncSKUOS]),0) AS Decimal(10,2)) AS 'OsramEncCost'
                          ,CAST(IsNull((SELECT [TotalStock] FROM [Inventory].[dbo].[Global_Stocks] WHERE [ProductCatalogID] = PJD.[EncSKUOS]),0) AS INT) AS 'OsramEnc-tQOH'
                          
                          
                          ,PJD.[BareSKUNL] AS 'NeoLuxBareSKU'
                          ,CAST(IsNull((SELECT [UnitCost] FROM [Inventory].[dbo].[ProductCatalog] WHERE [ID] = PJD.[BareSKUNL]),0) AS Decimal(10,2)) AS 'NeoLuxBareCost'
                          ,CAST(IsNull((SELECT [TotalStock] FROM [Inventory].[dbo].[Global_Stocks] WHERE [ProductCatalogID] = PJD.[BareSKUNL]),0) AS INT) AS 'NeoLuxBare-tQOH'
                          
                          ,PJD.[EncSKUNL] AS 'NeoLuxEncSKU'
                          ,CAST(IsNull((SELECT [UnitCost] FROM [Inventory].[dbo].[ProductCatalog] WHERE [ID] = PJD.[EncSKUNL]),0) AS Decimal(10,2)) AS 'NeoLuxEncCost'
                          ,CAST(IsNull((SELECT [TotalStock] FROM [Inventory].[dbo].[Global_Stocks] WHERE [ProductCatalogID] = PJD.[EncSKUNL]),0) AS INT) AS 'NeoLuxEnc-tQOH'
                          
                          ,PJD.[BareSKUPX] AS 'PhoenixBareSKU'
                          ,CAST(IsNull((SELECT [UnitCost] FROM [Inventory].[dbo].[ProductCatalog] WHERE [ID] = PJD.[BareSKUPX]),0) AS Decimal(10,2)) AS 'PhoenixBareCost'
                          ,CAST(IsNull((SELECT [TotalStock] FROM [Inventory].[dbo].[Global_Stocks] WHERE [ProductCatalogID] = PJD.[BareSKUPX]),0) AS INT) AS 'PhoenixBare-tQOH'
                          
                          ,PJD.[EncSKUPX] AS 'PhoenixEncSKU'
                          ,CAST(IsNull((SELECT [UnitCost] FROM [Inventory].[dbo].[ProductCatalog] WHERE [ID] = PJD.[EncSKUPX]),0) AS Decimal(10,2)) AS 'PhoenixEncCost'
                          ,CAST(IsNull((SELECT [TotalStock] FROM [Inventory].[dbo].[Global_Stocks] WHERE [ProductCatalogID] = PJD.[EncSKUPX]),0) AS INT) AS 'PhoenixEnc-tQOH'
                          
                          ,PJD.[BareSKUUSH] AS 'UshioBareSKU'
                          ,CAST(IsNull((SELECT [UnitCost] FROM [Inventory].[dbo].[ProductCatalog] WHERE [ID] = PJD.[BareSKUUSH]),0) AS Decimal(10,2)) AS 'UshioBareCost'
                          ,CAST(IsNull((SELECT [TotalStock] FROM [Inventory].[dbo].[Global_Stocks] WHERE [ProductCatalogID] = PJD.[BareSKUUSH]),0) AS INT) AS 'UshioBare-tQOH'
                          
                          ,PJD.[EncSKUUSH] AS 'UshioEncSKU'
                          ,CAST(IsNull((SELECT [UnitCost] FROM [Inventory].[dbo].[ProductCatalog] WHERE [ID] = PJD.[EncSKUUSH]),0) AS Decimal(10,2)) AS 'UshioEncCost'
                          ,CAST(IsNull((SELECT [TotalStock] FROM [Inventory].[dbo].[Global_Stocks] WHERE [ProductCatalogID] = PJD.[EncSKUUSH]),0) AS INT) AS 'UshioEnc-tQOH'
                         
                          ,PJD.[BareSKUOEM] AS 'OEMBareSKU'
                          ,CAST(IsNull((SELECT [UnitCost] FROM [Inventory].[dbo].[ProductCatalog] WHERE [ID] = PJD.[BareSKUOEM]),0) AS Decimal(10,2)) AS 'OEMBareCost'
                          ,CAST(IsNull((SELECT [TotalStock] FROM [Inventory].[dbo].[Global_Stocks] WHERE [ProductCatalogID] = PJD.[BareSKUOEM]),0) AS INT) AS 'OEMBare-tQOH'
                          
                          ,PJD.[EncSKUOEM] AS 'OEMEncSKU'
                          ,CAST(IsNull((SELECT [UnitCost] FROM [Inventory].[dbo].[ProductCatalog] WHERE [ID] = PJD.[EncSKUOEM]),0) AS Decimal(10,2)) AS 'OEMEncCost'
                          ,CAST(IsNull((SELECT [TotalStock] FROM [Inventory].[dbo].[Global_Stocks] WHERE [ProductCatalogID] = PJD.[EncSKUOEM]),0) AS INT) AS 'OEMEnc-tQOH'
                          
                          
                          ,([Inventory].[dbo].[fn_GetCompatiblityListForEbay](PJD.[Brand],PJD.[PartNumber],'PartNumberSpecific')) AS 'SearchTerms'";

        $selectSlice = "SELECT  [CatalogID]
                               ,[Brand]
                               ,[PartNumber]
                               ,[GenericBareSKU]
                               ,[GenericBareCost]
                               ,[GenericBare-tQOH]
                               ,[GenericEncSKU]
                               ,[GenericEncCost]
                               ,[GenericEnc-tQOH]
                               ,[PhilipsBareSKU]
                               ,[PhilipsBareCost]
                               ,[PhilipsBare-tQOH]
                               ,[PhilipsEncSKU]
                               ,[PhilipsEncCost]
                               ,[PhilipsEnc-tQOH]
                               ,[OsramBareSKU]
                               ,[OsramBareCost]
                               ,[OsramBare-tQOH]
                               ,[OsramEncCost]
                               ,[OsramEnc-tQOH]     
                               ,[NeoLuxBareSKU]
                               ,[NeoLuxBareCost]
                               ,[NeoLuxBare-tQOH]
                               ,[NeoLuxEncSKU]
                               ,[NeoLuxEncCost]
                               ,[NeoLuxEnc-tQOH]
                               ,[PhoenixBareSKU]
                               ,[PhoenixBareCost]
                               ,[PhoenixBare-tQOH]
                               ,[PhoenixEncSKU]
                               ,[PhoenixEncCost]
                               ,[PhoenixEnc-tQOH]
                               ,[UshioBareSKU]
                               ,[UshioBareCost]
                               ,[UshioBare-tQOH]
                               ,[UshioEncSKU]
                               ,[UshioEncCost]
                               ,[UshioEnc-tQOH]
                               ,[OEMBareSKU]
                               ,[OEMBareCost]
                               ,[OEMBare-tQOH]
                               ,[OEMEncSKU]
                               ,[OEMEncCost]
                               ,[OEMEnc-tQOH]
                               ,[SearchTerms]";
        $from = ' FROM ';
        $table = ' [MITDB].[dbo].[ProjectorData] AS PJD ';
        $where =' WHERE ';
       

       if ($search){

            
            $words = (explode(" ",$search));
            $countw = count($words);

            if($countw > 1){
                foreach ($words as $word) {
                    $where .= " ([Inventory].[dbo].[fn_GetCompatiblityListForEbay](PJD.[Brand],PJD.[PartNumber],'PartNumberSpecific')) LIKE '%{$word}%' or";
                }
                $where = substr($where, 0, -2);

            }else{
                $where .= "([Inventory].[dbo].[fn_GetCompatiblityListForEbay](PJD.[Brand],PJD.[PartNumber],'PartNumberSpecific')) LIKE '%{$search}%' ";

            }


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

            $SQL = "WITH mytable AS ($select , ROW_NUMBER() OVER (ORDER BY {$sidx} {$sord}) AS RowNumber
                                    FROM {$table}{$where})
                   {$selectSlice}, RowNumber
                   FROM mytable
                    WHERE RowNumber BETWEEN {$start} AND {$finish}";


           $result = $this->MCommon->getSomeRecords($SQL);
        }


        $responce = new stdClass();
        $responce->page = $page;
        $responce->total = $total_pages;
        $responce->records = $count;

        $i = 0;
        foreach ($result as $row) {
            
            $responce->rows[$i]['id'] = $row['RowNumber'];
            $responce->rows[$i]['cell'] = array($row['CatalogID']
                ,$row['Brand']
                ,$row['PartNumber']
                ,$row['GenericBareSKU']
                ,$row['GenericBareCost']
                ,$row['GenericBare-tQOH']
                ,$row['GenericEncSKU']
                ,$row['GenericEncCost']
                ,$row['GenericEnc-tQOH']
                ,$row['PhilipsBareSKU']
                ,$row['PhilipsBareCost']
                ,$row['PhilipsBare-tQOH']
                ,$row['PhilipsEncSKU']
                ,$row['PhilipsEncCost']
                ,$row['PhilipsEnc-tQOH']
                ,$row['OsramBareSKU']
                ,$row['OsramBareCost']
                ,$row['OsramBare-tQOH']
                ,$row['OsramEncCost']
                ,$row['OsramEnc-tQOH']
                ,$row['NeoLuxBareSKU']
                ,$row['NeoLuxBareCost']
                ,$row['NeoLuxBare-tQOH']
                ,$row['NeoLuxEncSKU']
                ,$row['NeoLuxEncCost']
                ,$row['NeoLuxEnc-tQOH']
                ,$row['PhoenixBareSKU']
                ,$row['PhoenixBareCost']
                ,$row['PhoenixBare-tQOH']
                ,$row['PhoenixEncSKU']
                ,$row['PhoenixEncCost']
                ,$row['PhoenixEnc-tQOH']
                ,$row['UshioBareSKU']
                ,$row['UshioBareCost']
                ,$row['UshioBare-tQOH']
                ,$row['UshioEncSKU']
                ,$row['UshioEncCost']
                ,$row['UshioEnc-tQOH']
                ,$row['OEMBareSKU']
                ,$row['OEMBareCost']
                ,$row['OEMBare-tQOH']
                ,$row['OEMEncSKU']
                ,$row['OEMEncCost']
                ,$row['OEMEnc-tQOH']
                ,$row['SearchTerms'] 
            );
            $i++;
        }
        echo json_encode($responce);

            
            
    }

     public function csvExport($name) {

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

