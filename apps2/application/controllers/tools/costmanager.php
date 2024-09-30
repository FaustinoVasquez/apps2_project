<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class CostManager extends BP_Controller {

    public function __construct() {
        parent::__construct();

        $is_logged_in = $this->session->userdata('is_logged_in');

        if (!isset($is_logged_in) || $is_logged_in != true) {
            redirect(base_url(), 'refresh');
        }

//        if ($this->MUsers->isValidUser($this->session->userdata('userid'), 882310) != 1) {// 881100 prodcat Access
//            redirect('Catalog/prodcat', 'refresh');
//        }
    }

    function index() { 

        $this->title = "MI Technologiesinc - Cost Manager";

        $this->description = "Cost Manager";

        $this->css = array('form.css', 'fluid.css', 'table.css', 'jqueryui/redmond/jquery-ui-1.8.21.custom.css', 'jqgrid/ui.jqgrid.css', 'menu.css',);

        // Define custom javascript
        $this->javascript = array('jqueryui/ui/jquery-ui-1.8.21.custom.js', 'jqgrid/i18n/grid.locale-en.js', 'jqgrid/jquery.jqGrid.min.js', 'jqgrid/jquery.jqGrid.fluid.js', 'site.js');

        $this->load->library('Layout');
        $menu = new Layout;

        $data = array( 
            'menu' => $menu->show_menu($this->MUsers->getUserFullName($this->session->userdata('userid'))),
            'from' => '/tools/costmanager/',
            'caption' => 'Cost Manager',
            'export' => 'Cost_Manager',
            'sort' => 'desc',
            'category' => $this->MCatalog->fillCategories(),
            'baseUrl' => base_url(),
        );

        //$x = $data['category'];
        //print_r(array_keys($data['category']));
            
        $this->build_content($data);
        $this->render_page();
    }

    function getData() {

        $page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1; // get the requested page
        $limit = isset($_REQUEST['rows']) ? $_REQUEST['rows'] : 10; // get how many rows we want to have into the gri
        $sidx = isset($_REQUEST['sidx']) ? $_REQUEST['sidx'] : 'ID'; // get index row - i.e. user click to sort
        $sord = isset($_REQUEST['sord']) ? $_REQUEST['sord'] : 'asc';
        
        $search = $this->input->get('ds');
        $category = $this->input->get('cat');


        switch ($category) {
            case 60:
                $select = "SELECT FPHVCI.[MITSKU] AS SKU
                          ,PC.Name AS Name
                          ,Cast(inventory.dbo.fn_GetLowestPriceFromSuppliersTable (FPHVCI.[MITSKU]) as Decimal(10,2))  AS 'LowestCost'
                          ,Cast(IsNull(PC.UnitCostAvgPO,0) AS Decimal(10,2)) AS AvgPOCost
                          ,FPHVCI.[MICost] AS MITCost
                          ,FPHVCI.[MoldGateSKU] AS MgtSKU
                          ,FPHVCI.[MoldGateCost] AS MgtCost
                          ,FPHVCI.[GrandBulbSKU] AS GBSKU
                          ,FPHVCI.[GrandBulbCost] AS GBCost
                          ,FPHVCI.[SouthernTechSKU] AS STCSKU
                          ,FPHVCI.[SouthernTechCost] AS STCCost
                          ,FPHVCI.[KWSKU] AS KWSKU
                          ,FPHVCI.[KWCost] AS KWCost
                          ,FPHVCI.[LeaderSKU] AS LeaderSKU
                          ,FPHVCI.[LeaderCost] AS LeaderCost
                          ,FPHVCI.[YitaSKU] AS YitaSKU
                          ,FPHVCI.[YitaCost] AS YitaCost ";
               
                $selectSlice = "SELECT SKU
                                ,Name
                                ,LowestCost
                                ,AvgPOCost
                                ,MITCost
                                ,MgtSKU
                                ,MgtCost
                                ,GBSKU
                                ,GBCost
                                ,STCSKU
                                ,STCCost
                                ,KWSKU
                                ,KWCost
                                ,LeaderSKU
                                ,LeaderCost
                                ,YitaSKU
                                ,YitaCost";
               
                $from = " FROM ";
                $table = " [Inventory].[dbo].[FP-Housing-Vendor-CostInfo] AS FPHVCI";
                $join = " LEFT OUTER JOIN [Inventory].[dbo].[ProductCatalog] AS PC ON (FPHVCI.MITSKU = PC.ID)";

                $where = '';
                $wherefields = array('FPHVCI.[MITSKU]','PC.Name');
                $where .= $this->MCommon->concatAllWerefields($wherefields, $search);

            break;

            case 24:
                $select = "SELECT FPGVCI.[ID] AS SKU
                            ,PC.Name AS Name
                            ,Cast(inventory.dbo.fn_GetLowestPriceFromSuppliersTable (FPGVCI.[ID]) as Decimal(10,2)) AS LowestCost
                            ,Cast(IsNull(PC.UnitCostAvgPO,0) AS Decimal(10,2)) AS AvgPOCost
                            ,FPGVCI.[MiTechCost] AS MITCost
                            ,FPGVCI.[ArcliteSKU] AS ArcliteSKU
                            ,FPGVCI.[ArcLiteUnitCost] AS ArcliteCost
                            ,FPGVCI.[GlorySKU] AS GlorySKU
                            ,FPGVCI.[GloryUnitCost] AS GloryCost
                            ,FPGVCI.[ClpSKU] AS CLPSKU
                            ,FPGVCI.[ClpUnitCost] AS CLPCost
                            ,FPGVCI.[GrandBulbsSKU] AS GBSKU
                            ,FPGVCI.[GrandBulbsCost] AS GBCost
                            ,FPGVCI.[LeaderCoSKU] AS LeaderSKU
                            ,FPGVCI.[LeaderCoCost] AS LeaderCost
                            ,FPGVCI.[YitaSKU] AS YitaSKU
                            ,FPGVCI.[YitaCost] AS YitaCost ";
               
                $selectSlice = "SELECT SKU
                                ,Name
                                ,LowestCost
                                ,AvgPOCost
                                ,MITCost
                                ,ArcliteSKU
                                ,ArcliteCost
                                ,GlorySKU
                                ,GloryCost
                                ,CLPSKU
                                ,CLPCost
                                ,GBSKU
                                ,GBCost
                                ,LeaderSKU
                                ,LeaderCost
                                ,YitaSKU
                                ,YitaCost ";
               
                $from = " FROM ";
                $table = " [Inventory].[dbo].[FP-Generic-Vendor-CostInfo] AS FPGVCI";
                $join = "  LEFT OUTER JOIN [Inventory].[dbo].[ProductCatalog] AS PC ON (FPGVCI.ID = PC.ID)";

                $where = '';
                $wherefields = array('FPGVCI.[ID]','PC.Name');
                $where .= $this->MCommon->concatAllWerefields($wherefields, $search);
            break;
            
            default:
                $select = "SELECT PC.ID AS SKU
                            ,PC.Name AS Name
                            ,Cast(PC.UnitCost as Decimal(10,2)) AS LastQuotedPrice
                            ,Cast(IsNull(PC.UnitCostAvgPO,0) AS Decimal(10,2)) AS AvgPOCost ";
               
                $selectSlice = "SELECT SKU
                            ,Name
                            ,LastQuotedPrice
                            ,AvgPOCost ";
               
                $from = " FROM ";
                $table = " [Inventory].[dbo].[ProductCatalog] AS PC";
                $join = " INNER JOIN [Inventory].[dbo].[Categories] AS CA ON PC.CategoryID = CA.ID";

                $where = '';
                $wherefields = array('PC.[ID]','PC.[Name]');
                $where .= $this->MCommon->concatAllWerefields($wherefields, $search);
                
                if($category)
                { $where .= " and (PC.[CategoryID] = '{$category}') "; }
            break;
        }		          
        
        //if($shipping){
        //    $where .= " and (PS.[ShippingMethod] = '{$shipping}') ";
        //}

        $SQL = "{$select}{$from}{$table}{$join}{$where}";
        
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
        
        if($category == 60 || $category == 24)
        {
            $SQL = "WITH mytable AS ($select , ROW_NUMBER() OVER (ORDER BY PC.[Name] {$sord}) AS RowNumber
                                FROM {$table}{$join}{$where})
               {$selectSlice}, RowNumber
               FROM mytable
                WHERE RowNumber BETWEEN {$start} AND {$finish}";
        }
        else
        {
            $where = '';
            $wherefields = array('PC.[ID]','PC.[Name]');
            $where .= $this->MCommon->concatAllWerefields($wherefields, $search);
            if($category)
            { $where .= " and (PC.[CategoryID] = '{$category}') "; }

            $SQL = "SELECT PC.ID AS 'SKU'
                        ,PC.Name AS 'Name'
                        ,Cast(PC.UnitCost as Decimal(10,2)) AS 'LastQuotedPrice'
                        ,Cast(IsNull(PC.UnitCostAvgPO,0) AS Decimal(10,2)) AS 'AvgPOCost'
                    FROM [Inventory].[dbo].[ProductCatalog] AS PC
                    INNER JOIN [Inventory].[dbo].[Categories] AS CA ON PC.CategoryID = CA.ID 
                    $where ORDER BY PC.[ID]";
        }

        $result = $this->MCommon->getSomeRecords($SQL);

        //print_r($SQL);

        $responce = new stdClass();
        $responce->page = $page;
        $responce->total = $total_pages;
        $responce->records = $count;


        $i = 0;

        switch ($category) {
            case 60:
                foreach ($result as $row) {
                    $responce->rows[$i]['id'] = $row['SKU'];
                    $responce->rows[$i]['cell'] = array($row['SKU'],
                        $row['Name'] = utf8_encode($row['Name']),
                        $row['LowestCost'] = utf8_encode($row['LowestCost']),
                        $row['AvgPOCost'] = utf8_encode($row['AvgPOCost']),
                        $row['MITCost'] = utf8_encode($row['MITCost']),
                        $row['MgtSKU'] = utf8_encode($row['MgtSKU']),
                        $row['MgtCost']= utf8_encode($row['MgtCost']),
                        $row['GBSKU'] = utf8_encode($row['GBSKU']),
                        $row['GBCost'] = utf8_encode($row['GBCost']),
                        $row['STCSKU']= utf8_encode($row['STCSKU']),
                        $row['STCCost'] = utf8_encode($row['STCCost']),
                        $row['KWSKU']= utf8_encode($row['KWSKU']),
                        $row['KWCost'] = utf8_encode($row['KWCost']),
                        $row['LeaderSKU'] = utf8_encode($row['LeaderSKU']),
                        $row['LeaderCost']= utf8_encode($row['LeaderCost']),
                        $row['YitaSKU'] = utf8_encode($row['YitaSKU']),
                        $row['YitaCost']= utf8_encode($row['YitaCost']),
                        );
                    $i++;
                }
            break;

            case 24:
                foreach ($result as $row) {
                    $responce->rows[$i]['id'] = $row['SKU'];
                    $responce->rows[$i]['cell'] = array($row['SKU'],
                        $row['Name'] = utf8_encode($row['Name']),
                        $row['LowestCost'] = utf8_encode($row['LowestCost']),
                        $row['AvgPOCost'] = utf8_encode($row['AvgPOCost']),
                        $row['MITCost'] = utf8_encode($row['MITCost']),
                        $row['ArcliteSKU'] = utf8_encode($row['ArcliteSKU']),
                        $row['ArcliteCost'] = utf8_encode($row['ArcliteCost']),
                        $row['GlorySKU']= utf8_encode($row['GlorySKU']),
                        $row['GloryCost'] = utf8_encode($row['GloryCost']),
                        $row['CLPSKU'] = utf8_encode($row['CLPSKU']),
                        $row['CLPCost']= utf8_encode($row['CLPCost']),
                        $row['GBSKU'] = utf8_encode($row['GBSKU']),
                        $row['GBCost']= utf8_encode($row['GBCost']),
                        $row['LeaderSKU'] = utf8_encode($row['LeaderSKU']),
                        $row['LeaderCost']= utf8_encode($row['LeaderCost']),
                        $row['YitaSKU'] = utf8_encode($row['YitaSKU']),
                        $row['YitaCost']= utf8_encode($row['YitaCost']),
                        );
                    $i++;
                }
            break;
            
            default:
                foreach ($result as $row) {
                    $responce->rows[$i]['id'] = $row['SKU'];
                    $responce->rows[$i]['cell'] = array($row['SKU'],
                        $row['Name'] = utf8_encode($row['Name']),
                        $row['LastQuotedPrice'] = utf8_encode($row['LastQuotedPrice']),
                        $row['AvgPOCost'] = utf8_encode($row['AvgPOCost']),
                        );
                    $i++;
                }
            break;
        }

        echo json_encode($responce);
    }

    /*
     * CSV Export
     */

    

    function saveHousing()
    {
        $sku = $this->input->post('id');

        if (isset($_POST['MITCost'])) {
                $MITCost = $_POST['MITCost'];
                $SQL = "UPDATE [Inventory].[dbo].[FP-Housing-Vendor-CostInfo] SET MICost={$MITCost} WHERE MITSKU={$sku}";
        }

        if (isset($_POST['MgtSKU'])) {
                $MgtSKU = $_POST['MgtSKU'];
                $SQL = "UPDATE [Inventory].[dbo].[FP-Housing-Vendor-CostInfo] SET MoldGateSKU='{$MgtSKU}' WHERE MITSKU={$sku}";
        }

        if (isset($_POST['MgtCost'])) {
            $MgtCost = $_POST['MgtCost'];
            $SQL = "UPDATE [Inventory].[dbo].[FP-Housing-Vendor-CostInfo] SET MoldGateCost={$MgtCost} WHERE MITSKU = {$sku}";
        }

        if (isset($_POST['GBSKU'])) {
                $GBSKU = $_POST['GBSKU'];
                $SQL = "UPDATE [Inventory].[dbo].[FP-Housing-Vendor-CostInfo] SET GrandBulbSKU='{$GBSKU}' WHERE MITSKU={$sku}";
        }

        if (isset($_POST['GBCost'])) {
                $GBCost = $_POST['GBCost'];
                $SQL = "UPDATE [Inventory].[dbo].[FP-Housing-Vendor-CostInfo] SET GrandBulbCost={$GBCost} WHERE MITSKU={$sku}";
        }

        if (isset($_POST['STCSKU'])) {
                $STCSKU = $_POST['STCSKU'];
                $SQL = "UPDATE [Inventory].[dbo].[FP-Housing-Vendor-CostInfo] SET SouthernTechSKU='{$STCSKU}' WHERE MITSKU={$sku}";
        }

        if (isset($_POST['STCCost'])) {
                $STCCost = $_POST['STCCost'];
                $SQL = "UPDATE [Inventory].[dbo].[FP-Housing-Vendor-CostInfo] SET SouthernTechCost={$STCCost} WHERE MITSKU={$sku}";
        }

        if (isset($_POST['KWSKU'])) {
                $KWSKU = $_POST['KWSKU'];
                $SQL = "UPDATE [Inventory].[dbo].[FP-Housing-Vendor-CostInfo] SET KWSKU='{$KWSKU}' WHERE MITSKU={$sku}";
        }

        if (isset($_POST['KWCost'])) {
                $KWCost = $_POST['KWCost'];
                $SQL = "UPDATE [Inventory].[dbo].[FP-Housing-Vendor-CostInfo] SET KWCost={$KWCost} WHERE MITSKU={$sku}";
        }

        if (isset($_POST['LeaderSKU'])) {
                $LeaderSKU = $_POST['LeaderSKU'];
                $SQL = "UPDATE [Inventory].[dbo].[FP-Housing-Vendor-CostInfo] SET LeaderSKU='{$LeaderSKU}' WHERE MITSKU={$sku}";
        }

        if (isset($_POST['LeaderCost'])) {
                $LeaderCost = $_POST['LeaderCost'];
                $SQL = "UPDATE [Inventory].[dbo].[FP-Housing-Vendor-CostInfo] SET LeaderCost={$LeaderCost} WHERE MITSKU={$sku}";
        }

        if (isset($_POST['YitaSKU'])) {
                $YitaSKU = $_POST['YitaSKU'];
                $SQL = "UPDATE [Inventory].[dbo].[FP-Housing-Vendor-CostInfo] SET YitaSKU='{$YitaSKU}' WHERE MITSKU={$sku}";
        }

        if (isset($_POST['YitaCost'])) {
                $YitaCost = $_POST['YitaCost'];
                $SQL = "UPDATE [Inventory].[dbo].[FP-Housing-Vendor-CostInfo] SET YitaCost={$YitaCost} WHERE MITSKU={$sku}";
        }
        
        $this->MCommon->saveRecord($SQL,'Inventory');
    }

    function saveGeneric()
    {
        $sku = $this->input->post('id');

        if (isset($_POST['MITCost'])) {
                $MITCost = $_POST['MITCost'];
                $SQL = "UPDATE [Inventory].[dbo].[FPU-Generic-Vendor-CostInfo] SET MiTechCost={$MITCost} WHERE ID={$sku}";
        }

        if (isset($_POST['ArcliteSKU'])) {
                $ArcliteSKU = $_POST['ArcliteSKU'];
                $SQL = "UPDATE [Inventory].[dbo].[FPU-Generic-Vendor-CostInfo] SET ArcliteSKU='{$ArcliteSKU}' WHERE ID={$sku}";
        }

        if (isset($_POST['ArcliteCost'])) {
            $ArcliteCost = $_POST['ArcliteCost'];
            $SQL = "UPDATE [Inventory].[dbo].[FPU-Generic-Vendor-CostInfo] SET ArcLiteUnitCost={$ArcliteCost} WHERE ID = {$sku}";
        }

        if (isset($_POST['GlorySKU'])) {
                $GlorySKU = $_POST['GlorySKU'];
                $SQL = "UPDATE [Inventory].[dbo].[FPU-Generic-Vendor-CostInfo] SET GlorySKU='{$GlorySKU}' WHERE ID={$sku}";
        }

        if (isset($_POST['GloryCost'])) {
                $GloryCost = $_POST['GloryCost'];
                $SQL = "UPDATE [Inventory].[dbo].[FPU-Generic-Vendor-CostInfo] SET GloryUnitCost={$GloryCost} WHERE ID={$sku}";
        }

        if (isset($_POST['CLPSKU'])) {
                $CLPSKU = $_POST['CLPSKU'];
                $SQL = "UPDATE [Inventory].[dbo].[FPU-Generic-Vendor-CostInfo] SET ClpSKU='{$CLPSKU}' WHERE ID={$sku}";
        }

        if (isset($_POST['CLPCost'])) {
                $CLPCost = $_POST['CLPCost'];
                $SQL = "UPDATE [Inventory].[dbo].[FPU-Generic-Vendor-CostInfo] SET ClpUnitCost={$CLPCost} WHERE ID={$sku}";
        }

        if (isset($_POST['GBSKU'])) {
                $GBSKU = $_POST['GBSKU'];
                $SQL = "UPDATE [Inventory].[dbo].[FPU-Generic-Vendor-CostInfo] SET GrandBulbsSKU='{$GBSKU}' WHERE ID={$sku}";
        }

        if (isset($_POST['GBCost'])) {
                $GBCost = $_POST['GBCost'];
                $SQL = "UPDATE [Inventory].[dbo].[FPU-Generic-Vendor-CostInfo] SET GrandBulbsCost={$GBCost} WHERE ID={$sku}";
        }

        if (isset($_POST['LeaderSKU'])) {
                $LeaderSKU = $_POST['LeaderSKU'];
                $SQL = "UPDATE [Inventory].[dbo].[FPU-Generic-Vendor-CostInfo] SET LeaderCoSKU='{$LeaderSKU}' WHERE ID={$sku}";
        }

        if (isset($_POST['LeaderCost'])) {
                $LeaderCost = $_POST['LeaderCost'];
                $SQL = "UPDATE [Inventory].[dbo].[FPU-Generic-Vendor-CostInfo] SET LeaderCoCost={$LeaderCost} WHERE ID={$sku}";
        }

        if (isset($_POST['YitaSKU'])) {
                $YitaSKU = $_POST['YitaSKU'];
                $SQL = "UPDATE [Inventory].[dbo].[FPU-Generic-Vendor-CostInfo] SET YitaSKU='{$YitaSKU}' WHERE ID={$sku}";
        }

        if (isset($_POST['YitaCost'])) {
                $YitaCost = $_POST['YitaCost'];
                $SQL = "UPDATE [Inventory].[dbo].[FPU-Generic-Vendor-CostInfo] SET YitaCost={$YitaCost} WHERE ID={$sku}";
        }
        
        $this->MCommon->saveRecord($SQL,'Inventory');
    }

    function saveDefault()
    {
        $sku = $this->input->post('id');

        if (isset($_POST['LastQuotedPrice'])) {
                $LastQuotedPrice = $_POST['LastQuotedPrice'];
                $SQL = "UPDATE [Inventory].[dbo].[ProductCatalog] SET UnitCost={$LastQuotedPrice} WHERE ID={$sku}";
        }

        $this->MCommon->saveRecord($SQL,'Inventory');
    }

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