<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Fp extends BP_Controller {

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

        $this->title = "MI Technologiesinc - Front Projection";

        $this->description = "Front Projection";

        $this->css = array('form.css', 'fluid.css', 'table.css', 'jqueryui/redmond/jquery-ui-1.8.21.custom.css', 'jqgrid/ui.jqgrid.css', 'menu.css',);

        // Define custom javascript
        $this->javascript = array('jqueryui/ui/jquery-ui-1.8.21.custom.js', 'jqgrid/i18n/grid.locale-en.js', 'jqgrid/jquery.jqGrid.min.js', 'jqgrid/jquery.jqGrid.fluid.js', 'popup.js');

        $this->load->library('Layout');
        $menu = new Layout;

        $data = array(
            'menu' => $menu->show_menu($this->MUsers->getUserFullName($this->session->userdata('userid'))),
            'from' => '/LampManufact/fp/',
            'nameGrid' => 'list',
            'namePager' => 'Pager',
            'caption' => 'Front Projection',
            'export' => 'frontprojection',
            'sort' => 'desc',
            'manufacturer' => $this->MCatalog->fillManufacturer(),
            'baseUrl' => base_url(),
        );

        $data['colNames'] = "['SKU','Name','Manufacturer','Kit','KitQty','Bulb','BulbQty','Burner','BurnerQty','Reflector','ReflectorQty','Glass','GlassQty']";

        $data['colModel'] = "[
                {name:'SKU',index:'SKU', width:60, align:'center', formatter:linksku},
                {name:'Name',index:'Name', width:250, align:'center' },
                {name:'Manufacturer',index:'Manufacturer', width:80, align:'center'},  
                {name:'Kit',index:'Kit', width:60, align:'center', formatter:linksku},  
                {name:'KitQty',index:'KitQty', width:70, align:'center'}, 
                {name:'Bulb',index:'Bulb', width:70, align:'center', formatter:linksku}, 
                {name:'BulbQty',index:'BulbQty', width:70, align:'center'}, 
                {name:'Burner',index:'Burner', width:70, align:'center', formatter:linksku}, 
                {name:'BurnerQty',index:'BurnerQty', width:70, align:'center'}, 
                {name:'Reflector',index:'Reflector', width:70, align:'center', formatter:linksku}, 
                {name:'ReflectorQty',index:'ReflectorQty', width:70, align:'center'}, 
                {name:'Glass',index:'Glass', width:70, align:'center', formatter:linksku}, 
                {name:'GlassQty',index:'GlassQty', width:70, align:'center'},  

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
        $manufacturer = $this->input->get('man');

        
        $where = '';
        $wherefields = array('PC.[ID]','PC.[Name]','PC.[Manufacturer]');

        $where .= $this->MCommon->concatAllWerefields($wherefields, $search);

        if ($manufacturer){
            $where .= " and (PC.[Manufacturer] = '{$manufacturer}') ";
        }


        $SQL = "SELECT  PC.[ID] AS 'SKU'
                          ,PC.[Name] AS 'Name'
                          ,PC.[Manufacturer] AS 'Manufacturer'

                          ,(Select TOP(1) ASSYKit.[SUBSKU]
                          FROM Inventory.[dbo].AssemblyDetails AS ASSYKit
                          LEFT OUTER JOIN [Inventory].[dbo].[ProductCatalog] AS PCKit ON (ASSYKit.[SubSKU] = PCKit.[ID])
                          WHERE PCKit.[CategoryID] = '60' AND ASSYKit.[ProductCatalogID] = PC.[ID]) AS 'Kit'

                          ,(Select Cast(GS.[TotalStock] as int) FROM [Inventory].[dbo].[Global_Stocks] AS GS WHERE GS.[ProductCatalogID] = (Select TOP(1) ASSYKit.[SUBSKU]
                          FROM Inventory.[dbo].AssemblyDetails AS ASSYKit
                          LEFT OUTER JOIN [Inventory].[dbo].[ProductCatalog] AS PCKit ON (ASSYKit.[SubSKU] = PCKit.[ID])
                          WHERE PCKit.[CategoryID] = '60' AND ASSYKit.[ProductCatalogID] = PC.[ID])) as 'KitQty'
                         
                         
                          ,(Select TOP(1) ASSYBulb.[SUBSKU]
                          FROM Inventory.[dbo].AssemblyDetails AS ASSYBulb
                          LEFT OUTER JOIN [Inventory].[dbo].[ProductCatalog] AS PCBulb ON (ASSYBulb.[SubSKU] = PCBulb.[ID])
                          WHERE PCBulb.[CategoryID] = '19' AND ASSYBulb.[ProductCatalogID] = PC.[ID]) AS 'Bulb'

                          ,(Select Cast(GS.[TotalStock] as int) FROM [Inventory].[dbo].[Global_Stocks] AS GS WHERE GS.[ProductCatalogID] = (Select TOP(1) ASSYBulb.[SUBSKU]
                          FROM Inventory.[dbo].AssemblyDetails AS ASSYBulb
                          LEFT OUTER JOIN [Inventory].[dbo].[ProductCatalog] AS PCBulb ON (ASSYBulb.[SubSKU] = PCBulb.[ID])
                          WHERE PCBulb.[CategoryID] = '19' AND ASSYBulb.[ProductCatalogID] = PC.[ID])) AS 'BulbQty'

                          ,(Select TOP(1) ASSYBurner.[SUBSKU]
                          FROM Inventory.[dbo].AssemblyDetails AS ASSYBurner
                          LEFT OUTER JOIN [Inventory].[dbo].[ProductCatalog] AS PCBurner ON (ASSYBurner.[SubSKU] = PCBurner.[ID])
                          WHERE PCBurner.[CategoryID] = '69' AND ASSYBurner.[ProductCatalogID] = (Select TOP(1) ASSYBulb.[SUBSKU]
                          FROM Inventory.[dbo].AssemblyDetails AS ASSYBulb
                          LEFT OUTER JOIN [Inventory].[dbo].[ProductCatalog] AS PCBulb ON (ASSYBulb.[SubSKU] = PCBulb.[ID])
                          WHERE PCBulb.[CategoryID] = '19' AND ASSYBulb.[ProductCatalogID] = PC.[ID])) AS 'Burner'

                          ,(Select Cast(GS.[TotalStock] as int) FROM [Inventory].[dbo].[Global_Stocks] AS GS WHERE GS.[ProductCatalogID] = (Select TOP(1) ASSYBurner.[SUBSKU]
                          FROM Inventory.[dbo].AssemblyDetails AS ASSYBurner
                          LEFT OUTER JOIN [Inventory].[dbo].[ProductCatalog] AS PCBurner ON (ASSYBurner.[SubSKU] = PCBurner.[ID])
                          WHERE PCBurner.[CategoryID] = '69' AND ASSYBurner.[ProductCatalogID] = (Select TOP(1) ASSYBulb.[SUBSKU]
                          FROM Inventory.[dbo].AssemblyDetails AS ASSYBulb
                          LEFT OUTER JOIN [Inventory].[dbo].[ProductCatalog] AS PCBulb ON (ASSYBulb.[SubSKU] = PCBulb.[ID])
                          WHERE PCBulb.[CategoryID] = '19' AND ASSYBulb.[ProductCatalogID] = PC.[ID]))) AS 'BurnerQty'
                         
                         
                          ,(Select TOP(1) ASSYRef.[SUBSKU]
                          FROM Inventory.[dbo].AssemblyDetails AS ASSYRef
                          LEFT OUTER JOIN [Inventory].[dbo].[ProductCatalog] AS PCRef ON (ASSYRef.[SubSKU] = PCRef.[ID])
                          WHERE PCRef.[CategoryID] = '70' AND ASSYRef.[ProductCatalogID] = (Select TOP(1) ASSYBulb.[SUBSKU]
                          FROM Inventory.[dbo].AssemblyDetails AS ASSYBulb
                          LEFT OUTER JOIN [Inventory].[dbo].[ProductCatalog] AS PCBulb ON (ASSYBulb.[SubSKU] = PCBulb.[ID])
                          WHERE PCBulb.[CategoryID] = '19' AND ASSYBulb.[ProductCatalogID] = PC.[ID])) AS 'Reflector'

                          ,(Select Cast(GS.[TotalStock] as int) FROM [Inventory].[dbo].[Global_Stocks] AS GS WHERE GS.[ProductCatalogID] = (Select TOP(1) ASSYRef.[SUBSKU]
                          FROM Inventory.[dbo].AssemblyDetails AS ASSYRef
                          LEFT OUTER JOIN [Inventory].[dbo].[ProductCatalog] AS PCRef ON (ASSYRef.[SubSKU] = PCRef.[ID])
                          WHERE PCRef.[CategoryID] = '70' AND ASSYRef.[ProductCatalogID] = (Select TOP(1) ASSYBulb.[SUBSKU]
                          FROM Inventory.[dbo].AssemblyDetails AS ASSYBulb
                          LEFT OUTER JOIN [Inventory].[dbo].[ProductCatalog] AS PCBulb ON (ASSYBulb.[SubSKU] = PCBulb.[ID])
                          WHERE PCBulb.[CategoryID] = '19' AND ASSYBulb.[ProductCatalogID] = PC.[ID]))) AS 'ReflectorQty'

                          ,(Select TOP(1) ASSYGlass.[SUBSKU]
                          FROM Inventory.[dbo].AssemblyDetails AS ASSYGlass
                          LEFT OUTER JOIN [Inventory].[dbo].[ProductCatalog] AS PCGlass ON (ASSYGlass.[SubSKU] = PCGlass.[ID])
                          WHERE PCGlass.[CategoryID] = '71' AND ASSYGlass.[ProductCatalogID] = (Select TOP(1) ASSYGlass.[SUBSKU]
                          FROM Inventory.[dbo].AssemblyDetails AS ASSYBulb
                          LEFT OUTER JOIN [Inventory].[dbo].[ProductCatalog] AS PCBulb ON (ASSYBulb.[SubSKU] = PCBulb.[ID])
                          WHERE PCBulb.[CategoryID] = '19' AND ASSYBulb.[ProductCatalogID] = PC.[ID])) AS 'Glass'

                          ,(Select Cast(GS.[TotalStock] as int) FROM [Inventory].[dbo].[Global_Stocks] AS GS WHERE GS.[ProductCatalogID] = (Select TOP(1) ASSYGlass.[SUBSKU]
                          FROM Inventory.[dbo].AssemblyDetails AS ASSYGlass
                          LEFT OUTER JOIN [Inventory].[dbo].[ProductCatalog] AS PCGlass ON (ASSYGlass.[SubSKU] = PCGlass.[ID])
                          WHERE PCGlass.[CategoryID] = '71' AND ASSYGlass.[ProductCatalogID] = (Select TOP(1) ASSYGlass.[SUBSKU]
                          FROM Inventory.[dbo].AssemblyDetails AS ASSYBulb
                          LEFT OUTER JOIN [Inventory].[dbo].[ProductCatalog] AS PCBulb ON (ASSYBulb.[SubSKU] = PCBulb.[ID])
                          WHERE PCBulb.[CategoryID] = '19' AND ASSYBulb.[ProductCatalogID] = PC.[ID]))) AS 'GlassQty'


                      FROM [Inventory].[dbo].[ProductCatalog] AS PC
                      $where  and Categoryid in (24)
                      Order by PC.[Manufacturer] ";


       

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
            $responce->rows[$i]['ID'] = $row['SKU'];
            $responce->rows[$i]['cell'] = array($row['SKU'],
                $row['Name'],
                $row['Manufacturer'],
                $row['Kit'],
                $row['KitQty'],
                $row['Bulb'],
                $row['BulbQty'],
                $row['Burner'],
                $row['BurnerQty'],
                $row['Reflector'],
                $row['ReflectorQty'],
                $row['Glass'],
                $row['GlassQty'],
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
