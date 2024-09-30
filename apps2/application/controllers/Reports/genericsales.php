<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class GenericSales extends BP_Controller {

    public function __construct() {
        parent::__construct();

        $is_logged_in = $this->session->userdata('is_logged_in');

        if (!isset($is_logged_in) || $is_logged_in != true) {
            redirect(base_url(), 'refresh');
        }

        // if ($this->MUsers->isValidUser($this->session->userdata('userid'), 881215) != 1) {// 881100 prodcat Access
        //     redirect('Catalog/prodcat', 'refresh');
        // }
    }

    function index() {  

        $this->title = "MI Technologiesinc - FP Generic Sales Report";
        $this->description = "FP Generic Sales Report";
        $this->css = array('form.css', 'fluid.css', 'table.css', 'jqueryui/redmond/jquery-ui-1.8.21.custom.css', 'jqgrid/ui.jqgrid.css', 'menu.css',);

        // Define custom javascript
        $this->javascript = array('jqueryui/ui/jquery-ui-1.8.21.custom.js', 'jqgrid/i18n/grid.locale-en.js', 'jqgrid/jquery.jqGrid.min.js', 'jqgrid/jquery.jqGrid.fluid.js', 'site.js');

        $this->load->library('Layout');
        $menu = new Layout;

        $data = array( 
            'menu' => $menu->show_menu($this->MUsers->getUserFullName($this->session->userdata('userid'))),
            'from' => '/Reports/genericsales',
            'caption' => 'Generic Sales',
            'export' => 'GenericSales',
            'sort' => 'asc',
            'baseUrl' => base_url(),
        );

        $data['colNames'] = "['SKU','Name','Sold30Days','Sold60Days','Sold90Days','AvgPrice30Days','LowestCost','LowestCostSupplier',
                             'QOH','vQOH','TQOH',
                            'FBAQty','FBAInboundQty','PendingFBANeeded','BulbSKU','Bulb-QOH','Bulb-vQOH','Bulb-TQOH',
                            'KitSKU','Kit-QOH','Kit-vQOH','Kit-TQOH']";

        $data['colModel'] = "[
                {name:'SKU',index:'SKU', width:80, align:'center'},
                {name:'Name',index:'Name', width:450, align:'center'},
                {name:'Sold30Days',index:'Sold30Days', width:80, align:'center', sorttype: 'int'}, 
                {name:'Sold60Days',index:'Sold60Days', width:80, align:'center', sorttype: 'int'}, 
                {name:'Sold90Days',index:'Sold90Days', width:80, align:'center', sorttype: 'int'},
                {name:'AvgPrice30Days',index:'AvgPrice30Days', width:80, align:'center', sorttype: 'double'},  
                {name:'LowestCost',index:'LowestCost', width:80, align:'center', sorttype: 'double'},  
                {name:'LowestCostSupplier',index:'LowestCostSupplier', width:80, align:'center'},
                {name:'QOH',index:'QOH', width:50, align:'center', sorttype: 'int'}, 
                {name:'vQOH',index:'vQOH', width:50, align:'center', sorttype: 'int'}, 
                {name:'TQOH',index:'TQOH', width:50, align:'center', sorttype: 'int'},
                {name:'FBAQty',index:'FBAQty', width:50, align:'center', sorttype: 'int'},
                {name:'FBAInboundQty',index:'FBAInboundQty', width:80, align:'center', sorttype: 'int'},
                {name:'PendingFBANeeded',index:'PendingFBANeeded', width:80, align:'center', sorttype: 'int'},
                {name:'BulbSKU',index:'BulbSKU', width:80, align:'center', sorttype: 'int'}, 
                {name:'Bulb-QOH',index:'Bulb-QOH', width:80, align:'center', sorttype: 'int'}, 
                {name:'Bulb-vQOH',index:'Bulb-vQOH', width:80, align:'center', sorttype: 'int'}, 
                {name:'Bulb-TQOH',index:'Bulb-TQOH', width:80, align:'center', sorttype: 'int'},  
                {name:'KitSKU',index:'KitSKU', width:80, align:'center', sorttype: 'int'}, 
                {name:'Kit-QOH',index:'Kit-QOH', width:80, align:'center', sorttype: 'int'}, 
                {name:'Kit-vQOH',index:'Kit-vQOH', width:80, align:'center', sorttype: 'int'}, 
                {name:'Kit-TQOH',index:'Kit-TQOH', width:80, align:'center', sorttype: 'int'},  
                
            ]";
            
        $this->build_content($data);
        $this->render_page();
    }

    function getData() {

        $page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1; // get the requested page
        $limit = isset($_REQUEST['rows']) ? $_REQUEST['rows'] : 10; // get how many rows we want to have into the gri
        $sidx = $_GET['sidx']; // get index row - i.e. user click to sort
        $sord = $_GET['sord'];
        
        $search = $this->input->get('ds');

        //Select utilizado para realizar el conteo del numero de registros devueltos por la consulta.
        $selectCount = 'SELECT COUNT(*) AS rowNum';

        //Select General con los campos necesarios para la vista
       $select = "SELECT A.[SKU]
                      ,A.[Name]
                      ,A.[Sold30Days]
                      ,A.[Sold60Days]
                      ,A.[Sold90Days]
                      ,A.[AvgPrice30Days]
                      ,A.[LowestCost]
                      ,A.[LowestCostSupplier]
                      ,A.[QOH]
                      ,A.[vQOH]
                      ,A.[TQOH]
                      ,A.[FBAQty]
                      ,A.[FBAInboundQty]
                      ,A.[PendingFBANeeded]
                      ,A.[BulbSKU]
                      ,A.[Bulb-QOH]
                      ,A.[Bulb-vQOH]
                      ,A.[Bulb-TQOH]
                      ,A.[KitSKU]
                      ,A.[Kit-QOH]
                      ,A.[Kit-vQOH]
                      ,A.[Kit-TQOH] ";

        $selectSlice = "SELECT SKU
                              ,Name
                              ,Sold30Days
                              ,Sold60Days
                              ,Sold90Days
                              ,AvgPrice30Days
                              ,LowestCost
                              ,LowestCostSupplier
                              ,QOH
                              ,vQOH
                              ,TQOH
                              ,FBAQty
                              ,FBAInboundQty
                              ,PendingFBANeeded
                              ,BulbSKU
                              ,[Bulb-QOH]
                              ,[Bulb-vQOH]
                              ,[Bulb-TQOH]
                              ,[KitSKU]
                              ,[Kit-QOH]
                              ,[Kit-vQOH]
                              ,[Kit-TQOH]";
        $from = " FROM (
                  SELECT FPGSR.[SKU]
                    ,FPGSR.[Name]
                    ,FPGSR.[Sold30Days]
                    ,FPGSR.[Sold60Days]
                    ,FPGSR.[Sold90Days]
                    ,FPGSR.[AvgPrice30Days]
                    ,FPGSR.[LowestCost]
                    ,FPGSR.[LowestCostSupplier]
                    ,FPGSR.[QOH]
                    ,FPGSR.[vQOH]
                    ,FPGSR.[TQOH]
                    ,FPGSR.[FBAQty]
                    ,FPGSR.[FBAInboundQty]
                    ,FPGSR.[PendingFBANeeded]
                    ,FPGSR.[BulbSKU]
                    ,IsNull(Cast([GSBulb].[GlobalStock] as int),0) AS 'Bulb-QOH'
                    ,IsNull(Cast([GSBulb].[VirtualStock] as int),0) AS 'Bulb-vQOH'
                    ,IsNull(Cast([GSBulb].[TotalStock] as int),0) AS 'Bulb-TQOH'
                    ,FPGSR.[KitSKU]
                    ,IsNull(Cast([GSKit].[GlobalStock] as int),0) AS 'Kit-QOH'
                    ,IsNull(Cast([GSKit].[VirtualStock] as int),0) AS 'Kit-vQOH'
                    ,IsNull(Cast([GSKit].[TotalStock] as int),0) AS 'Kit-TQOH' 

                  FROM [Inventory].[dbo].[FP-Generic-Sales-Report] AS FPGSR
                  LEFT OUTER JOIN [Inventory].[dbo].[Global_Stocks] AS GSBulb ON (FPGSR.[BulbSKU] = GSBulb.ProductCatalogId)
                  LEFT OUTER JOIN [Inventory].[dbo].[Global_Stocks] AS GSKit ON (FPGSR.[KitSKU] = GSKit.ProductCatalogId)
                ) A ";
        
        $where ='';
        $wherefields = array('A.[SKU]','A.[Sold30Days]','A.[Sold60Days]','A.[Sold90Days]','A.[Name]','A.[BulbSKU]','A.[KitSKU]');


        //Obtenemos todos los nombres de campos de la tabla productCatalog
        //$fields = $this->MCommon->getAllfields($table);
        //creamos el where concatenando todos los nombres de campos y las palabras de busqueda
        $where .= $this->MCommon->concatAllWerefields($wherefields, $search);

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

        //print_r($SQL);
        $result = $this->MCommon->getSomeRecords($SQL);
        $responce = new stdClass();
        $responce->page = $page;
        $responce->total = $total_pages;
        $responce->records = $count;

        $i = 0;
        foreach ($result as $row) {
            
            $responce->rows[$i]['id'] = $row['SKU'];
            $responce->rows[$i]['cell'] = array($row['SKU'],
                $row['Name'] = utf8_encode($row['Name']),
                $row['Sold30Days'] ,
                $row['Sold60Days'], 
                $row['Sold90Days'],
                '$ '.number_format($row['AvgPrice30Days'],2),
                '$ '.number_format($row['LowestCost'],2),
                $row['LowestCostSupplier'],
                $row['QOH'], 
                $row['vQOH'],
                $row['TQOH'],
                $row['FBAQty'],
                $row['FBAInboundQty'],
                $row['PendingFBANeeded'],
                $row['BulbSKU'],
                $row['Bulb-QOH'],
                $row['Bulb-vQOH'],
                $row['Bulb-TQOH'], 
                $row['KitSKU'],
                $row['Kit-QOH'],
                $row['Kit-vQOH'],
                $row['Kit-TQOH'],
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
}

?>