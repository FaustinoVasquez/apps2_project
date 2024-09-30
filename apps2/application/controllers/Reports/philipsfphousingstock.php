<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Philipsfphousingstock extends BP_Controller {

    public function __construct() {
        parent::__construct();

        $is_logged_in = $this->session->userdata('is_logged_in');

        if (!isset($is_logged_in) || $is_logged_in != true) {
            redirect(base_url(), 'refresh');
        }

        // if ($this->MUsers->isValidUser($this->session->userdata('userid'), 883500) != 1) {// 881100 prodcat Access
        //     redirect('Catalog/prodcat', 'refresh');
        // }
    }

    function index() {

        $this->title = "MI Technologiesinc - Philips FP Housing Stock";

        $this->description = "Philips FP Housing Stock";

        $this->css = array('form.css', 'fluid.css', 'table.css', 'jqueryui/redmond/jquery-ui-1.8.21.custom.css', 'jqgrid/ui.jqgrid.css', 'menu.css',);

        // Define custom javascript
        $this->javascript = array('jqueryui/ui/jquery-ui-1.8.21.custom.js', 'jqgrid/i18n/grid.locale-en.js', 'jqgrid/jquery.jqGrid.min.js', 'jqgrid/jquery.jqGrid.fluid.js', 'popup.js');

        $this->load->library('Layout');
        $menu = new Layout;

        $data = array(
            'menu' => $menu->show_menu($this->MUsers->getUserFullName($this->session->userdata('userid'))),
            'from' => '/Reports/philipsfphousingstock/',
            'nameGrid' => 'list',
            'namePager' => 'Pager',
            'caption' => 'Philips FP Housing Stock',
            'export' => 'philipsfphousingstock',
            'sort' => 'desc',
        );

        $data['colNames'] = "['KitSKU','KitQOH','LowestCost','Supplier']";

        $data['colModel'] = "[
                {name:'KitSKU',index:'KitSKU', width:80, align:'center' , sorttype:'int'},
                {name:'KitQOH',index:'KitQOH', width:80, align:'center' , sorttype:'int'},
                {name:'LowestCost',index:'LowestCost', width:80, align:'center' , sorttype:'int'},  
                {name:'Supplier',index:'Supplier', width:80, align:'center'},  
          	]";

       
        $this->build_content($data);
        $this->render_page();
    }

    function getData() {

        $page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1; // get the requested page
        $limit = isset($_REQUEST['rows']) ? $_REQUEST['rows'] : 10; // get how many rows we want to have into the gri
        $sidx = $_GET['sidx']; // get index row - i.e. user click to sort
        $sord = $_GET['sord'];

        //$search = !empty($_GET['ds']) ? $_REQUEST['ds'] : '';
        $search = $this->input->get('ds');

        //Select utilizado para realizar el conteo del numero de registros devueltos por la consulta.
        $selectCount = 'SELECT COUNT(*) AS rowNum';

        $select = "SELECT A.KitSKU
                          ,A.KitQOH
                          ,A.LowestCost
                          ,A.Supplier ";

        $selectSlice = "SELECT KitSKU
                          ,KitQOH
                          ,LowestCost
                          ,Supplier  ";
        $from = " FROM (
                        SELECT [KitSKU], [KitQOH]
                        ,(
                            SELECT TOP 1 SUP.[UnitCost] 
                            FROM [Inventory].[dbo].[Suppliers] SUP 
                            WHERE FPPSKU.[KitSKU] = SUP.[ProductCatalogId] 
                            ORDER BY CASE WHEN SUP.UnitCost = '0' THEN '99999' WHEN SUP.UnitCost is Null THEN '99999' END, SUP.[UnitCost] ASC
                        ) AS 'LowestCost'
                        ,(
                            SELECT TOP 1 SUPINF.[SupplierName] 
                            FROM [Inventory].[dbo].[Suppliers] SUP 
                            LEFT OUTER JOIN [Inventory].[dbo].[SupplierInfo] SUPINF 
                            ON (SUP.SupplierID = SUPINF.SupplierID) 
                            WHERE FPPSKU.[KitSKU] = SUP.[ProductCatalogId] 
                            ORDER BY CASE WHEN SUP.UnitCost = '0' THEN '99999' WHEN SUP.UnitCost is Null THEN '99999' END, SUP.[UnitCost] ASC
                        ) AS 'Supplier'
                        FROM [Inventory].[dbo].[FP-Philips-SKU-NEW-Step2] FPPSKU
                        LEFT OUTER JOIN [Inventory].[dbo].[ProductCatalog] PC 
                        ON [BulbSKU] = PC.ID
                        WHERE ISNULL([KitSKU],'') <> ''
                        GROUP BY [KitSKU], [KitQOH]
                    ) A ";
        
        $where = " ";
        $wherefields = array('A.KitSKU','A.KitQOH');

        $where .=$this->MCommon->concatAllWerefields($wherefields, $search);


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
            $responce->rows[$i]['ID'] = $row['KitSKU'];
            $responce->rows[$i]['cell'] = array($row['KitSKU'],
                $row['KitQOH'] = utf8_encode($row['KitQOH']),
                '$ '.number_format($row['LowestCost'],2),
                $row['Supplier'] = utf8_encode($row['Supplier']),
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
