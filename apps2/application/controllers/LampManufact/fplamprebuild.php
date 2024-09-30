<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Fplamprebuild extends BP_Controller {

    public function __construct() {
        parent::__construct();

        $is_logged_in = $this->session->userdata('is_logged_in');

        if (!isset($is_logged_in) || $is_logged_in != true) {
            redirect(base_url(), 'refresh');
        }

        if ($this->MUsers->isValidUser($this->session->userdata('userid'), 883500) != 1) {// 881100 prodcat Access
            redirect('Catalog/prodcat', 'refresh');
        }
    }

    function index() { 

        $this->title = "MI Technologiesinc - FP Lamp Rebuild";

        $this->description = "FP Lamp Rebuild";

        $this->css = array('form.css', 'fluid.css', 'table.css', 'jqueryui/redmond/jquery-ui-1.8.21.custom.css', 'jqgrid/ui.jqgrid.css', 'menu.css',);

        // Define custom javascript
        $this->javascript = array('jqueryui/ui/jquery-ui-1.8.21.custom.js', 'jqgrid/i18n/grid.locale-en.js', 'jqgrid/jquery.jqGrid.min.js', 'jqgrid/jquery.jqGrid.fluid.js', 'site.js');

        $this->load->library('Layout');
        $menu = new Layout;

        $data = array(
            'menu' => $menu->show_menu($this->MUsers->getUserFullName($this->session->userdata('userid'))),
            'from' => '/LampManufact/fplamprebuild/',
            'caption' => 'FP Lamp Rebuild',
            'subgrid' => 'true',
            'export' => 'FPLampRebuild',
            'sort' => 'desc',
        );

        $data['colNames'] = "['ToBuild','ParentSKU','Item','QOH','vQOH','tQOH']";

        $data['colModel'] = "[
                {name:'ToBuild',index:'ToBuild', width:70, align:'center'},
                {name:'ParentSKU',index:'ParentSKU', width:70, align:'left'},
                {name:'Item',index:'Item', width:230, align:'left'},  
                {name:'QOH',index:'QOH', width:70, align:'center'}, 
                {name:'vQOH',index:'vQOH', width:70, align:'center'}, 
                {name:'tQOH',index:'tQOH', width:70, align:'center'}, 
          	]";

       
        $this->build_content($data);
        $this->render_page();
    }

    function getData() {

        $page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1; // get the requested page
        $limit = isset($_REQUEST['rows']) ? $_REQUEST['rows'] : 10; // get how many rows we want to have into the gri
        $sidx = isset($_REQUEST['sidx']) ? $_REQUEST['sidx'] : 'ID'; // get index row - i.e. user click to sort
        $sord = isset($_REQUEST['sord']) ? $_REQUEST['sord'] : 'asc';
        
        $search = $this->input->get('ds');

        
        $where = '';
       // $wherefields = array('PC.[ID]','PC.[Name]');

       // $where .= $this->MCommon->concatAllWerefields($wherefields, $search);

        $SQL = "EXECUTE [Inventory].[dbo].[sp_ExplodeReBuildFPLamp] '{$search}' ";
   

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
            $responce->rows[$i]['ID'] = $row['ToBuild'];
            $responce->rows[$i]['cell'] = array($row['ToBuild'],
                $row['ParentSKU'],
                $row['Item'],
                $row['QOH'],
                $row['vQOH'],
                $row['tQOH'],
            );
            $i++;
        }

        echo json_encode($responce);
    }

        function categoryData() {

        $sku = isset($_REQUEST['on']) ? $_GET['on'] : '';
        $page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1; // get the requested page
        $limit = isset($_REQUEST['rows']) ? $_REQUEST['rows'] : 10; // get how many rows we want to have into the gri
        $sidx = isset($_REQUEST['sidx']) ? $_REQUEST['sidx'] : 'ID'; // get index row - i.e. user click to sort
        $sord = isset($_REQUEST['sord']) ? $_REQUEST['sord'] : 'Desc';


        $totalrows = isset($_REQUEST['totalrows']) ? $_REQUEST['totalrows'] : false;
        if ($totalrows) {
            $limit = $totalrows;
        }

        if (!$sidx)
            $sidx = 1;

        $cat = $this->MCatalog->getCategoryId($sku);

        $firstCatId= array('5','6','7','8','9','15','16','17','18','19','59','60');
        if (in_array($cat, $firstCatId)){
             $SQL = "Select   AD.ProductCatalogID AS 'ParentSKU'
                                ,PC.Name AS 'Item'
                                ,Cast(GS.GlobalStock AS Int) AS 'QOH'
                                ,Cast(GS.VirtualStock AS Int) AS 'vQOH'
                                ,Cast(GS.TotalStock AS Int) AS 'tQOH'
                        FROM [Inventory].[dbo].AssemblyDetails AS AD
                        LEFT OUTER JOIN [Inventory].[dbo].[ProductCatalog] AS PC ON (AD.ProductCatalogID = PC.ID)
                        LEFT OUTER JOIN [Inventory].[dbo].[Global_Stocks] AS GS ON (AD.ProductCatalogID = GS.ProductCatalogId)
                        WHERE AD.SubSKU = '{$sku}'
                        ORDER BY QOH DESC";
        }


        $secondCatId= array('10','11','12','13','14','20','21','22','23','24');
        if (in_array($cat, $secondCatId)){
            $SQL = "Select   AD.SubSKU AS 'ParentSKU'
                                ,PC.Name AS 'Item'
                                 ,Cast((Select GS2.GlobalStock FROM Inventory.dbo.Global_Stocks AS GS2 Where GS2.ProductCatalogId = (Select AD2.SubSKU FROM Inventory.dbo.AssemblyDetails AS AD2
                                LEFT OUTER JOIN Inventory.dbo.ProductCatalog AS PC2 ON (AD2.SubSKU = PC2.ID)
                                WHERE PC2.CategoryID IN ('5','6','7','8','9','15','16','17','18','19') AND (AD2.ProductCatalogID = AD.ProductCatalogID))) AS Int) AS 'QOH'  
                               
                                ,Cast((Select GS2.VirtualStock FROM Inventory.dbo.Global_Stocks AS GS2 Where GS2.ProductCatalogId = (Select AD2.SubSKU FROM Inventory.dbo.AssemblyDetails AS AD2
                                LEFT OUTER JOIN Inventory.dbo.ProductCatalog AS PC2 ON (AD2.SubSKU = PC2.ID)
                                WHERE PC2.CategoryID IN ('5','6','7','8','9','15','16','17','18','19') AND (AD2.ProductCatalogID = AD.ProductCatalogID))) - GS.GlobalStock AS Int) AS 'vQOH'
                               
                               
                                ,Cast((Select GS2.TotalStock FROM Inventory.dbo.Global_Stocks AS GS2 Where GS2.ProductCatalogId = (Select AD2.SubSKU FROM Inventory.dbo.AssemblyDetails AS AD2
                                LEFT OUTER JOIN Inventory.dbo.ProductCatalog AS PC2 ON (AD2.SubSKU = PC2.ID)
                                WHERE PC2.CategoryID IN ('5','6','7','8','9','15','16','17','18','19') AND (AD2.ProductCatalogID = AD.ProductCatalogID))) - GS.GlobalStock AS Int) AS 'tQOH'  

                        FROM [Inventory].[dbo].AssemblyDetails AS AD
                        LEFT OUTER JOIN [Inventory].[dbo].[ProductCatalog] AS PC ON (AD.SubSKU = PC.ID)
                        LEFT OUTER JOIN [Inventory].[dbo].[Global_Stocks] AS GS ON (AD.ProductCatalogID = GS.ProductCatalogId)
                        WHERE AD.ProductCatalogID = '{$sku}' AND PC.CategoryID IN ('5','6','7','8','9','15','16','17','18','19')

                        UNION

                        Select    AD.SubSKU AS 'ParentSKU'
                                ,PC.Name AS 'Item'
                                ,Cast((Select GS2.GlobalStock FROM Inventory.dbo.Global_Stocks AS GS2 Where GS2.ProductCatalogId = (Select AD2.SubSKU FROM Inventory.dbo.AssemblyDetails AS AD2
                                LEFT OUTER JOIN Inventory.dbo.ProductCatalog AS PC2 ON (AD2.SubSKU = PC2.ID)
                                WHERE PC2.CategoryID IN ('59','60') AND (AD2.ProductCatalogID = AD.ProductCatalogID))) AS Int) AS 'QOH'
                                ,Cast((Select GS2.VirtualStock FROM Inventory.dbo.Global_Stocks AS GS2 Where GS2.ProductCatalogId = (Select AD2.SubSKU FROM Inventory.dbo.AssemblyDetails AS AD2
                                LEFT OUTER JOIN Inventory.dbo.ProductCatalog AS PC2 ON (AD2.SubSKU = PC2.ID)
                                WHERE PC2.CategoryID IN ('59','60') AND (AD2.ProductCatalogID = AD.ProductCatalogID))) - GS.GlobalStock AS Int) AS 'vQOH'
                                ,Cast((Select GS2.TotalStock FROM Inventory.dbo.Global_Stocks AS GS2 Where GS2.ProductCatalogId = (Select AD2.SubSKU FROM Inventory.dbo.AssemblyDetails AS AD2
                                LEFT OUTER JOIN Inventory.dbo.ProductCatalog AS PC2 ON (AD2.SubSKU = PC2.ID)
                                WHERE PC2.CategoryID IN ('59','60') AND (AD2.ProductCatalogID = AD.ProductCatalogID))) - GS.GlobalStock AS Int) AS 'tQOH'
                        FROM [Inventory].[dbo].AssemblyDetails AS AD
                        LEFT OUTER JOIN [Inventory].[dbo].[ProductCatalog] AS PC ON (AD.SubSKU = PC.ID)
                        LEFT OUTER JOIN [Inventory].[dbo].[Global_Stocks] AS GS ON (AD.ProductCatalogID = GS.ProductCatalogId)
                        WHERE AD.ProductCatalogID = '{$sku}'  AND PC.CategoryID IN ('59','60')

                        ORDER BY QOH DESC
            ";
        }


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
            $responce->rows[$i]['id'] = $row['ParentSKU'];
            $responce->rows[$i]['cell'] = array($row['ParentSKU'],
                $row['Item'],
                $row['QOH'],
                $row['vQOH'],
                $row['tQOH'],
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
