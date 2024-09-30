<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class InventoryCost extends BP_Controller {

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

        $this->title = "MI Technologiesinc - Inventory Cost";

        $this->description = "Inventory Cost";

        $this->css = array('form.css', 'fluid.css', 'table.css', 'jqueryui/redmond/jquery-ui-1.8.21.custom.css', 'jqgrid/ui.jqgrid.css', 'menu.css',);

        // Define custom javascript
        $this->javascript = array('jqueryui/ui/jquery-ui-1.8.21.custom.js', 'jqgrid/i18n/grid.locale-en.js', 'jqgrid/jquery.jqGrid.min.js', 'jqgrid/jquery.jqGrid.fluid.js', 'site.js');

        $this->load->library('Layout');
        $menu = new Layout;

        $data = array(
            'menu' => $menu->show_menu($this->MUsers->getUserFullName($this->session->userdata('userid'))),
            'from' => '/Reports/inventorycost/',
            'nameGrid' => 'list',
            'namePager' => 'Pager',
            'caption' => 'Inventory Cost',
            'subgrid' => 'true',
            'export' => 'inventorycost',
            'sort' => 'desc',
            'baseUrl' => base_url(),
        );

        $data['colNames'] = "['Category','Total Inventory']";

        $data['colModel'] = "[
                {name:'Category',index:'Category', width:250, align:'left'},
                {name:'TotalInventory',index:'Total Inventory', width:100, align:'center' }, 
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

        $SQL = "SELECT DISTINCT CAT.Name AS 'Category'
                ,(Select Sum([Cost ALL]) FROM Inventory.dbo.CostOfInventoryDetail AS COID Where COID.Category = Cat.Name) AS 'TotalInventory'
                FROM [Inventory].[dbo].[ProductCatalog] AS PC
                LEFT OUTER JOIN [Inventory].[dbo].[Global_Stocks] AS GS ON (PC.ID = GS.ProductCatalogId)
                LEFT OUTER JOIN [Inventory].[dbo].Categories AS CAT ON (PC.CategoryID = CAT.ID)
                Order by TotalInventory Desc";


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
            $responce->rows[$i]['ID'] = $row['Category'];
            $responce->rows[$i]['cell'] = array($row['Category'],
                '$ '.number_format($row['TotalInventory'],2),
            );
            $i++;
        }

        echo json_encode($responce);
    }

    function categoryData() {

        $category = isset($_REQUEST['on']) ? $_GET['on'] : '';
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

        $SQL = "SELECT [SKU], [Item], [QOH], [Cost X1], [Cost ALL], [Category] FROM [Inventory].[dbo].CostOfInventoryDetail
                where Category = '{$category}'
                Order by Category, [Cost ALL] desc";

        //print_r($SQL);


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
            $responce->rows[$i]['id'] = $row['SKU'];
            $responce->rows[$i]['cell'] = array($row['SKU'],
                $row['Item'],
                $row['QOH'],
                '$ '.number_format($row['Cost X1'],2),
                '$ '.number_format($row['Cost ALL'],2),
                $row['Category'],
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
