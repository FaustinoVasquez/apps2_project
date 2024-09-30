<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Vqohdetails extends BP_Controller {

    public function __construct() {
        parent::__construct();

        $is_logged_in = $this->session->userdata('is_logged_in');

        if (!isset($is_logged_in) || $is_logged_in != true) {
            redirect(base_url(), 'refresh');
        }
    }

    function index() {


        $this->title = "MI Technologiesinc - vQOH Details";
        $this->description = " vQOH Details";
        // Define custom CSS
        $this->css = array('menu.css', 'form.css', 'fluid.css', 'table.css', 'jqueryui/redmond/jquery-ui-1.8.21.custom.css', 'jqgrid/ui.jqgrid.css');
        // Define custom javascript
        $this->javascript = array('jqueryui/ui/jquery-ui-1.8.21.custom.js', 'jqgrid/i18n/grid.locale-en.js', 'jqgrid/jquery.jqGrid.min.js', 'jqgrid/jquery.jqGrid.fluid.js' , 'site.js');
        $this->hasNav = False;

        $data['sku'] = $this->uri->segment(3);
        $data['cat'] = $this->MCatalog->getCategoryId($data['sku']);
        $data['title'] = 'vQOH Details'; //Page Title
        $data['caption'] = 'vQOH Details';
        $data['from'] = '/Tabs/vqohdetails/';

        //Cargamos la libreria comumn
        $this->load->library('common');
        
        $columns = array(
            'ParentSKU' => array('colName' => 'ParentSKU', 'colModel' => "{name:'ParentSKU',index:'ParentSKU', width:55, align:'center' , formatter:linksku }"),
            'Item' => array('colName' => 'Item', 'colModel' => "{name:'Item',index:'Item', width:90, align:'center'}"),
            'QOH' => array('colName' => 'QOH', 'colModel' => "{name:'QOH',index:'QOH', width:90, align:'center'}"),
            'vQOH' => array('colName' => 'vQOH', 'colModel' => "{name:'vQOH',index:'vQOH', width:90, align:'center'}"),
            'tQOH' => array('colName' => 'tQOH', 'colModel' => "{name:'tQOH',index:'tQOH', width:90, align:'center'}"),
            );

        $data['headers'] = $this->common->CreateColname($columns, 'colName');
        $data['body'] = $this->common->CreateColmodel($columns, 'colModel');

        $this->build_content($data);
        $this->render_page();
    }


    function gridDataVqoh() {

        $page = isset($_GET['page']) ? $_GET['page'] : 1; // get the requested page
        $limit = isset($_GET['rows']) ? $_GET['rows'] : 10; // get how many rows we want to have into the grid
        $sidx = isset($_GET['sidx']) ? $_GET['sidx'] : 'id'; // get index row - i.e. user click to sort
        $sord = isset($_GET['sord']) ? $_GET['sord'] : 'desc'; // get the direction

        $sku = $this->input->get('sku');
        $cat = $this->input->get('cat');

        $firstCatId= array('5','6','7','8','9','15','16','17','18','19','59','60');
        if (in_array($cat, $firstCatId)){
             // $select = "Select   AD.ProductCatalogID AS 'ParentSKU'
             //                    ,PC.Name AS 'Item'
             //                    ,Cast(GS.GlobalStock AS Int) AS 'QOH'
             //                    ,Cast(GS.VirtualStock AS Int) AS 'vQOH'
             //                    ,Cast(GS.TotalStock AS Int) AS 'tQOH'
             //            FROM [Inventory].[dbo].AssemblyDetails AS AD
             //            LEFT OUTER JOIN [Inventory].[dbo].[ProductCatalog] AS PC ON (AD.ProductCatalogID = PC.ID)
             //            LEFT OUTER JOIN [Inventory].[dbo].[Global_Stocks] AS GS ON (AD.ProductCatalogID = GS.ProductCatalogId)
             //            WHERE AD.SubSKU = '{$sku}'
             //            ORDER BY QOH DESC";
            $select = "EXEC Inventory.dbo.sp_GetVQOHDetails '{$sku}'";
        }


        $secondCatId= array('10','11','12','13','14','20','21','22','23','24');
        if (in_array($cat, $secondCatId)){
            $select = "Select    AD.SubSKU AS 'ParentSKU'
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



        $result = $this->MCommon->getSomeRecords($select);


        $count = count($result);


        if ($count > 0) {
            $total_pages = ceil($count / $limit);
        } else {
            $total_pages = 0;
        }
        if ($page > $total_pages)
            $page = $total_pages;

        $start = $limit * $page - $limit; // do not put $limit*($page - 1)
        $start = ($start <= 0) ? 1 : $start ;
        $finish = $start + $limit;


        $responce = new stdClass();
        $responce->page = $page;
        $responce->total = $total_pages;
        $responce->records = $count;

        $i=0;
        foreach ($result as $row) {
            $responce->rows[$i]['id'] = $row['ParentSKU'];
            $responce->rows[$i]['cell'] = array($row['ParentSKU'],
                $row['Item'] = utf8_encode($row['Item']),
                $row['QOH'] = utf8_encode($row['QOH']),
                $row['vQOH'] = utf8_encode($row['vQOH']),
                $row['tQOH'] = utf8_encode($row['tQOH']),
                );
            $i++;
        }

        echo json_encode($responce);
    }

    
    
    function csvExportHistory($name) {

        header('Content-type: application/vnd.ms-excel');
        header("Content-Disposition: attachment; filename= history_" . $name . ".xls");
        header("Pragma: no-cache");

        $buffer = $_POST['csvBuffer'];

        try {
            echo $buffer;
        } catch (Exception $e) {

        }
    }

}

?>