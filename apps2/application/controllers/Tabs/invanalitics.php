<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Invanalitics extends BP_Controller {

    public function __construct() {
        parent::__construct();

        $is_logged_in = $this->session->userdata('is_logged_in');

        if (!isset($is_logged_in) || $is_logged_in != true) {
            redirect(base_url(), 'refresh');
        }
    }

    function index($sku) {

        $this->title = "MI Technologiesinc -Inv Aanalitics";
        $this->description = " Inv Aanalitics";
        // Define custom CSS
        $this->css = array('menu.css', 'form.css', 'fluid.css', 'table.css', 'jqueryui/redmond/jquery-ui-1.8.21.custom.css', 'jqgrid/ui.jqgrid.css');
        // Define custom javascript
        $this->javascript = array('jqueryui/ui/jquery-ui-1.8.21.custom.js', 'jqgrid/i18n/grid.locale-en.js', 'jqgrid/jquery.jqGrid.min.js', 'jqgrid/jquery.jqGrid.fluid.js');
        $this->hasNav = False;

    
        $data['search'] = $sku;
        $data['title'] = 'Inventory Analytics Summary'; //Page Title
        $data['caption'] = 'Inventory Analytics Summary';
        $data['caption1'] = 'Inventory Analytics Details';

        $data['from'] = '/Tabs/invanalitics/';


        $data['headers'] = "['Sold','SoldFBA','SoldTotal','Removed','PendingFBA','MissedOpportunities','Days','SoldDaily','RemovedDaily']";
        $data['body'] = "[  {name:'Sold',index:'Sold',width:50, align:'center'},
                            {name:'SoldFBA',index:'SoldFBA',width:50, align:'center'},
                            {name:'SoldTotal',index:'SoldTotal',width:50, align:'center'},
                            {name:'Removed',index:'Removed',width:50, align:'center'},
                            {name:'PendingFBA',index:'PendingFBA',width:50, align:'center'},
                            {name:'MissedOpportunities',index:'MissedOpportunities',width:50, align:'center'},
                            {name:'Days',index:'Days',width:50, align:'center'},
                            {name:'SoldDaily',index:'SoldDaily',width:50, align:'center'},
                            {name:'RemovedDaily',index:'RemovedDaily',width:50, align:'center'},


                            ]";

        $data['headers1'] = "['SKU','Name','SoldQty','SoldQtyFBA','SoldTotal','RemovedQty','PendingFBA','MissedOpportunities','Days']";
        $data['body1'] = "[  
                             {name:'SKU',index:'SKU',width:50, align:'center',search:false},
                             {name:'Name',index:'Name',width:200, align:'left',search:false},
                             {name:'SoldQty',index:'SoldQty',width:50, align:'center',search:false},
                             {name:'SoldQtyFBA',index:'SoldQtyFBA',width:50, align:'center',search:false},
                             {name:'SoldTotal',index:'SoldTotal',width:50, align:'center',search:false},
                             {name:'RemovedQty',index:'RemovedQty',width:50, align:'center',search:false},
                             {name:'PendingFBA',index:'PendingFBA',width:50, align:'center',search:false},
                             {name:'MissedOpportunities',index:'MissedOpportunities',width:50, align:'center',search:false},
                             {name:'Days',index:'Days',width:50, align:'center', stype:'select', editoptions:{value:':30:30;60:60;90:90'}},
                            ]";

        $this->build_content($data);
        $this->render_page();
    }
 

    function gridData() {
        ini_set('max_execution_time', 300);

        $page = isset($_GET['page']) ? $_GET['page'] : 1; // get the requested page
        $limit = isset($_GET['rows']) ? $_GET['rows'] : 10; // get how many rows we want to have into the grid
        $sidx = isset($_GET['sidx']) ? $_GET['sidx'] : 'Sold'; // get index row - i.e. user click to sort
        $sord = isset($_GET['sord']) ? $_GET['sord'] : 'desc'; // get the direction



        $sku = $this->input->get('sku');


        $SQL = "EXECUTE [Inventory].[dbo].[sp_GetInventoryAnalyticsSummary] '{$sku}'";



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
        $start = ($start <= 0) ? 1 : $start ;
        $finish = $start + $limit;

        $responce = new stdClass();
        $responce->page = $page;
        $responce->total = $total_pages;
        $responce->records = $count;


        $i=0;
        foreach ($result as $row) {
            $responce->rows[$i]['id'] = $row['Sold'];
            $responce->rows[$i]['cell'] = array($row['Sold'],
                $row['SoldFBA'] ,
                $row['SoldTotal'],
                $row['Removed'],
                $row['PendingFBA'],
                $row['MissedOpportunities'],
                $row['Days'] ,
                $row['SoldDaily'] ,
                $row['RemovedDaily'],
                );
            $i++;
        }

        echo json_encode($responce);
    }

    function subGridData(){


        $page = isset($_GET['page']) ? $_GET['page'] : 1; // get the requested page
        $limit = isset($_GET['rows']) ? $_GET['rows'] : 10; // get how many rows we want to have into the grid
        $sidx = isset($_GET['sidx']) ? $_GET['sidx'] : 'SKU'; // get index row - i.e. user click to sort
        $sord = isset($_GET['sord']) ? $_GET['sord'] : 'desc'; // get the direction
        $days = isset($_GET['Days']) ? $_GET['Days'] : 30; // get the direction

        $sku = $this->input->get('sku');

        $select = "
                SELECT  [SKU],
                        [Name],
                        [SoldQty],
                        [SoldQtyFBA],
                        [RemovedQty],
                        [PendingFBA],
                        [MissedOpportunities],
                        [Days]
                FROM [Inventory].[dbo].fn_GetRecursiveStockMovements('{$sku}','{$days}')

        ";

  


        $query = "{$select}"; 


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
                $row['Name'] = utf8_encode($row['Name']),
                $row['SoldQty'] ,
                $row['SoldQtyFBA'],
                $row['SoldTotal'] =  $row['SoldQty'] + $row['SoldQty'],
                $row['RemovedQty'] ,
                $row['PendingFBA'] ,
                $row['MissedOpportunities'] ,
                $row['Days'],
            );
            $i++;
        }
        echo json_encode($responce);
    }
}

?>
