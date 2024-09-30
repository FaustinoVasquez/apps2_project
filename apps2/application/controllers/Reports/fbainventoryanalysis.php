<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class FbaInventoryAnalysis extends BP_Controller {

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

        $this->title = "MI Technologiesinc - FBA Inventory Analysis";
        $this->description = "FBA Inventory Analysis";
        $this->css = array('form.css', 'fluid.css', 'table.css', 'jqueryui/redmond/jquery-ui-1.8.21.custom.css', 'jqgrid/ui.jqgrid.css', 'menu.css',);

        // Define custom javascript
        $this->javascript = array('jqueryui/ui/jquery-ui-1.8.21.custom.js', 'jqgrid/i18n/grid.locale-en.js', 'jqgrid/jquery.jqGrid.min.js', 'jqgrid/jquery.jqGrid.fluid.js', 'site.js');

        $this->load->library('Layout');
        $menu = new Layout;

        $data = array( 
            'menu' => $menu->show_menu($this->MUsers->getUserFullName($this->session->userdata('userid'))),
            'from' => '/Reports/fbainventoryanalysis',
            'caption' => 'FBA Inventory Analysis',
            'export' => 'FBAInventoryAnalysis',
            'sort' => 'asc',
            'category' => $this->MCatalog->fillCategoryName(),
            'baseUrl' => base_url(),
        );

        $data['colNames'] = "['ASIN','SKU','Name','PriceCheck','MerchantSKUFBM','MerchantSKUFBA','FBMSold30Days','FBASold30Days','TotalSold30Days',
                            'FBMSold60Days','FBASold60Days','TotalSold60Days','FBMSold90Days','FBASold90Days',
                            'TotalSold90Days','QOH','vQOH','tQOH','FBAQty','FBAInbound','NeedToSend','NeedToRequestBack','PendingFBAOrders']";

        $data['colModel'] = "[
                {name:'ASIN',index:'ASIN', width:80, align:'center'},
                {name:'SKU',index:'SKU', width:80, align:'center', sorttype: 'int'},
                {name:'Name',index:'Name', width:250, align:'center'},
                {name:'PriceCheck',index:'PriceCheck', width:80, align:'center'}, 
                {name:'MerchantSKUFBM',index:'MerchantSKUFBM', width:80, align:'center', sorttype: 'int'}, 
                {name:'MerchantSKUFBA',index:'MerchantSKUFBA', width:80, align:'center', sorttype: 'int'},             
                {name:'FBMSold30Days',index:'FBMSold30Days', width:80, align:'center', sorttype: 'int'}, 
                {name:'FBASold30Days',index:'FBASold30Days', width:80, align:'center', sorttype: 'int'}, 
                {name:'TotalSold30Days',index:'TotalSold30Days', width:80, align:'center', sorttype: 'int'},               
                {name:'FBMSold60Days',index:'FBMSold60Days', width:80, align:'center', sorttype: 'int'},
                {name:'FBASold60Days',index:'FBASold60Days', width:80, align:'center', sorttype: 'int'},
                {name:'TotalSold60Days',index:'TotalSold60Days', width:80, align:'center', sorttype: 'int'},           
                {name:'FBMSold90Days',index:'FBMSold90Days', width:80, align:'center', sorttype: 'int'}, 
                {name:'FBASold90Days',index:'FBASold90Days', width:80, align:'center', sorttype: 'int'},  
                {name:'TotalSold90Days',index:'TotalSold90Days', width:80, align:'center', sorttype: 'int'},    
                {name:'QOH',index:'QOH', width:80, align:'center', sorttype: 'int'}, 
                {name:'vQOH',index:'vQOH', width:80, align:'center', sorttype: 'int'},  
                {name:'tQOH',index:'tQOH', width:80, align:'center', sorttype: 'int'},               
                {name:'FBAQty',index:'FBAQty', width:80, align:'center', sorttype: 'int'}, 
                {name:'FBAInbound',index:'FBAInbound', width:80, align:'center', sorttype: 'int'},
                {name:'NeedToSend',index:'NeedToSend', width:80, align:'center', sorttype: 'int'},
                {name:'NeedToRequestBack',index:'NeedToRequestBack', width:80, align:'center', sorttype: 'int'}, 
                {name:'PendingFBAOrders',index:'PendingFBAOrders', width:80, align:'center', sorttype: 'int'},
                
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
        $category = $this->input->get('cat');

        //Select utilizado para realizar el conteo del numero de registros devueltos por la consulta.
        $selectCount = 'SELECT COUNT(*) AS rowNum';

        //Select General con los campos necesarios para la vista
       $select = "SELECT A.[ASIN]
                        ,A.[SKU]
                        ,A.[Name]
                        ,A.[PriceCheck]
                        ,A.[MerchantSKUFBM]
                        ,A.[MerchantSKUFBA]                   
                        ,A.[FBMSold30Days]
                        ,A.[FBASold30Days]
                        ,A.[TotalSold30Days]                   
                        ,A.[FBMSold60Days]
                        ,A.[FBASold60Days]
                        ,A.[TotalSold60Days]                   
                        ,A.[FBMSold90Days]
                        ,A.[FBASold90Days]
                        ,A.[TotalSold90Days]                  
                        ,A.[QOH]
                        ,A.[vQOH]
                        ,A.[tQOH]                       
                        ,A.[FBAQty]
                        ,A.[FBAInbound]                     
                        ,A.[NeedToSend]
                        ,A.[NeedToRequestBack]
                        ,A.[PendingFBAOrders] ";

        $selectSlice = "SELECT ASIN
                        ,SKU
                        ,Name
                        ,PriceCheck
                        ,MerchantSKUFBM
                        ,MerchantSKUFBA
                        ,FBMSold30Days
                        ,FBASold30Days
                        ,TotalSold30Days
                        ,FBMSold60Days
                        ,FBASold60Days
                        ,TotalSold60Days
                        ,FBMSold90Days
                        ,FBASold90Days
                        ,TotalSold90Days
                        ,QOH
                        ,vQOH
                        ,tQOH
                        ,FBAQty
                        ,FBAInbound
                        ,NeedToSend
                        ,NeedToRequestBack
                        ,PendingFBAOrders ";

        $from = " FROM  (
                    SELECT AAT.[ASIN]
                        ,AAT.[SKU]
                        ,CAT.[Name]
                        ,CAST(ISNULL(CASE WHEN ISNULL(AAT.PriceCheck,'') <> '' THEN AAT.PriceCheck ELSE '0' END,0) AS DECIMAL(18,5)) AS PriceCheck
                        ,AAT.[MerchantSKUFBM]
                        ,AAT.[MerchantSKUFBA]                   
                        ,AAT.[FBMSold30Days]
                        ,AAT.[FBASold30Days]
                        ,AAT.[TotalSold30Days]                   
                        ,AAT.[FBMSold60Days]
                        ,AAT.[FBASold60Days]
                        ,AAT.[TotalSold60Days]                   
                        ,AAT.[FBMSold90Days]
                        ,AAT.[FBASold90Days]
                        ,AAT.[TotalSold90Days]                  
                        ,AAT.[QOH]
                        ,AAT.[vQOH]
                        ,AAT.[tQOH]                       
                        ,AAT.[FBAQty]
                        ,AAT.[FBAInbound]                     
                        ,AAT.[NeedToSend]
                        ,AAT.[NeedToRequestBack]
                    ,IsNull((SELECT SUM(AZFBAO2.OrderQTY) FROM [Inventory].[dbo].[AmazonFBAOrders] 
                        AS AZFBAO2 WHERE AZFBAO2.[MerchantSKU] = AAT.[MerchantSKUFBA] 
                        AND AZFBAO2.Completed != 'Yes'),0) AS 'PendingFBAOrders'

                    FROM [Inventory].[dbo].[AmazonAnalysisTable] AS AAT  
                    LEFT OUTER JOIN [Inventory].[dbo].[ProductCatalog] AS 
                        PC ON (AAT.[SKU] = PC.[ID]) 
                    LEFT OUTER JOIN [Inventory].[dbo].[Categories] AS 
                        CAT ON (PC.[CategoryID] = CAT.[ID]) 
                    ) A ";
                                
        $where =" ";
        $wherefields = array('A.[ASIN]','A.[SKU]','A.[PriceCheck]','A.[MerchantSKUFBM]','A.[MerchantSKUFBA]');
        $where .= $this->MCommon->concatAllWerefields($wherefields, $search);

        if ($category)
        {
            $where .= " and (A.[Name] = '{$category}') ";
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

        //print_r($SQL);
        $result = $this->MCommon->getSomeRecords($SQL);
        $responce = new stdClass();
        $responce->page = $page;
        $responce->total = $total_pages;
        $responce->records = $count;

        $i = 0;
        foreach ($result as $row) {
            
            $responce->rows[$i]['id'] = $row['RowNumber'];
            $responce->rows[$i]['cell'] = array($row['ASIN'],
                $row['SKU'],
                $row['Name'] = utf8_encode($row['Name']),
                '$ '.number_format($row['PriceCheck'],2),
                $row['MerchantSKUFBM'], 
                $row['MerchantSKUFBA'],
                $row['FBMSold30Days'],
                $row['FBASold30Days'], 
                $row['TotalSold30Days'],            
                $row['FBMSold60Days'],
                $row['FBASold60Days'],
                $row['TotalSold60Days'],
                $row['FBMSold90Days'],
                $row['FBASold90Days'],
                $row['TotalSold90Days'],            
                $row['QOH'],
                $row['vQOH'],
                $row['tQOH'],           
                $row['FBAQty'],
                $row['FBAInbound'],           
                $row['NeedToSend'],
                $row['NeedToRequestBack'],
                $row['PendingFBAOrders'],
               
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
