<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Invdetailsnew extends BP_Controller {

    public function __construct() {
        parent::__construct();

        $is_logged_in = $this->session->userdata('is_logged_in');

        if (!isset($is_logged_in) || $is_logged_in != true) {
            redirect(base_url(), 'refresh');
        }
    }

    function index($sku) {

        $this->title = "MI Technologiesinc - History";
        $this->description = " History";
        // Define custom CSS
        $this->css = array('menu.css', 'form.css', 'fluid.css', 'table.css', 'jqueryui/redmond/jquery-ui-1.8.21.custom.css', 'jqgrid/ui.jqgrid.css');
        // Define custom javascript
        $this->javascript = array('jqueryui/ui/jquery-ui-1.8.21.custom.js', 'jqgrid/i18n/grid.locale-en.js', 'jqgrid/jquery.jqGrid.min.js', 'jqgrid/jquery.jqGrid.fluid.js');
        $this->hasNav = False;

    
        $data['search'] = $sku;
        $data['title'] = 'Inventory Details New'; //Page Title
        $data['caption'] = 'Inventory Details NEW';
        $data['from'] = '/Tabs/inventorynew/';


        $data['headers'] = "['SKU','Warehouse','Qty']";
        $data['body'] = "[  {name:'id',index:'id',width:100, align:'center', hidden:true},
                            {name:'Warehouse',index:'Warehouse',width:60, align:'center'},
                            {name:'Qty',index:'Qty',width:100, align:'center'}]";

        $this->build_content($data);
        $this->render_page();
    }


    function gridData() {

        $page = isset($_GET['page']) ? $_GET['page'] : 1; // get the requested page
        $limit = isset($_GET['rows']) ? $_GET['rows'] : 10; // get how many rows we want to have into the grid
        $sidx = isset($_GET['sidx']) ? $_GET['sidx'] : 'Adjustment_Id'; // get index row - i.e. user click to sort
        $sord = isset($_GET['sord']) ? $_GET['sord'] : 'desc'; // get the direction



        $sku = $this->input->get('sku');

        $from       = ' FROM ';
        $table      = ' [Inventory].[dbo].[Bin_Content] AS BC ';
        $leftJoin   = ' LEFT OUTER JOIN Inventory.dbo.Bins as BIN on (BC.Bin_id = BIN.Bin_Id) ';
        $where      = "  WHERE BC.[ProductCatalog_Id] = '{$sku}' ";
        $groupby    = ' GROUP BY BC.ProductCatalog_Id, BIN.WarehouseID';
        $orderby    = ' ORDER BY Warehouse ASC';

        $select = "SELECT BC.[ProductCatalog_Id] AS [SKU]
                   ,BIN.WarehouseID as [Warehouse]
                   ,SUM(BC.[Counter]) AS [Qty]";


        $query = "{$select}{$from}{$table}{$leftJoin}{$where}{$groupby}{$orderby}"; 


        $result = $this->MCommon->getSomeRecords($query);

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
            $responce->rows[$i]['id'] = $row['Warehouse'];
            $responce->rows[$i]['cell'] = array($row['SKU'],
                $row['Warehouse'] = utf8_encode($row['Warehouse']),
                $row['Qty'] = utf8_encode($row['Qty']),
                );
            $i++;
        }

        echo json_encode($responce);
    }

    function subGridData(){


        $page = isset($_GET['page']) ? $_GET['page'] : 1; // get the requested page
        $limit = isset($_GET['rows']) ? $_GET['rows'] : 10; // get how many rows we want to have into the grid
        $sidx = isset($_GET['sidx']) ? $_GET['sidx'] : 'Adjustment_Id'; // get index row - i.e. user click to sort
        $sord = isset($_GET['sord']) ? $_GET['sord'] : 'desc'; // get the direction

        $sku = $this->input->get('sku');
        $wh = $this->input->get('wh');

        $from       = ' FROM ';
        $table      = ' [Inventory].[dbo].[Bin_Content] AS BC ';
        $leftJoin   = ' LEFT OUTER JOIN Inventory.dbo.Bins as BIN on (BC.Bin_id = BIN.Bin_Id) ';
        $where      = " WHERE WarehouseID = '{$wh}' AND BC.ProductCatalog_Id = '{$sku}' ";
        $orderby    = ' Order by BC.Bin_Id asc';


        $select = "SELECT BC.[ProductCatalog_Id] AS [SKU]
                        ,BC.[Bin_Id] AS [BinID]
                        ,BC.[Counter] AS [Qty]
                        ,BIN.Location AS [Location]";


        $query = "{$select}{$from}{$table}{$leftJoin}{$where}{$orderby}"; 


        $result = $this->MCommon->getSomeRecords($query);

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
            $responce->rows[$i]['id'] = $row['SKU'];
            $responce->rows[$i]['cell'] = array($row['SKU'],
                $row['BinID'] = utf8_encode($row['BinID']),
                $row['Qty'] = utf8_encode($row['Qty']),
                $row['Location'] = utf8_encode($row['Location']),
            );
            $i++;
        }
        echo json_encode($responce);
    }
}

?>
