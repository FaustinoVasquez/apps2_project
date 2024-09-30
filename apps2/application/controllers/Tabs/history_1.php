<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class History extends BP_Controller {

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

        
     
        $data['dateFrom'] = isset($_POST['dateFrom']) ? $_POST['dateFrom'] : $this->MCommon->fixInitialDate(date('m/d/Y', strtotime("-1 month")), 0);
        $data['dateTo'] = isset($_POST['dateTo']) ? $_POST['dateTo'] : $this->MCommon->fixFinalDate(date("m/d/Y"), 0);
        $data['filtered'] = isset($_POST['filtered']) ? $_POST['filtered'] : 0;
        $data['remaining'] = 0;

        $data['search'] = $sku;
        $data['title'] = 'History'; //Page Title
        $data['caption'] = 'History SKU';
        $data['from'] = '/Tabs/history/';
        $data['headers'] = "['Date','flow','OriginName','DestinationName','SKU','AdjustmentType','Quantity','Remaining','UserName','Comments']";

        $data['body'] = "[{name:'Date',index:'Date',width:150, align:'left',sorttype:'date'},
                {name:'flow',index:'flow',width:10, align:'left',hidden:true, sortable:false},
              
                {name:'OriginName',index:'OriginName', width:140, align:'center', sortable:false},

                {name:'DestinationName',index:'DestinationName', width:140, align:'center', sortable:false},
                {name:'SKU',index:'SKU', width:70, align:'center', sortable:false, sortable:false},
                {name:'AdjustmentType',index:'AdjustmentType', width:100, align:'center', sortable:false},
                {name:'Quantity',index:'Quantity', width:60, align:'center', sortable:false, formatter:'number', formatoptions:{decimalSeparator:'.'thousandsSeparator: ',', decimalPlaces: 2, prefix: '' }},
                {name:'Remaining',index:'Remaining', width:70, align:'center', sortable:false, formatter:'number', formatoptions:{decimalSeparator:'.', thousandsSeparator: ',', decimalPlaces: 2, prefix: '' }},
                {name:'UserName',index:'UserName', width:90, align:'center', sortable:false},
                {name:'Comments',index:'Comments', width:140, align:'center', sortable:false},

  	]";

        $this->build_content($data);
        $this->render_page();
    }

   
    function calcInventory($sku, $date) {

        $in = " SELECT SUM( b.Quantity) as stock FROM InventoryAdjustments a,  InventoryAdjustmentDetails b
                   WHERE ( a.ID = b.InventoryAdjustmentsID )
                   AND (a.Flow = 1)
                   AND ( b.ProductCatalogID = {$sku})
                   AND (a.date <= '{$date}')";
        $resultin = $this->MCommon->getOneRecord($in);


        $out = "SELECT SUM( b.Quantity) as stock
                   FROM InventoryAdjustments a,  InventoryAdjustmentDetails b
                   WHERE ( a.ID = b.InventoryAdjustmentsID )
                   AND (a.Flow = 2)
                   AND ( b.ProductCatalogID = {$sku})
                   AND (a.date <= '{$date}')";
        $resultout = $this->MCommon->getOneRecord($out);

        $counter = $resultin['stock'] - $resultout['stock'];





        return($counter);
    }




    function gridDataHistory() {
        $search = $_GET['q'];
        $InitialDate = isset($_GET['dateFrom']) ? $_GET['dateFrom'] : date('m/d/Y', strtotime("-1 month"));
        $FinalDate = isset($_GET['dateTo']) ? $_GET['dateTo'] : date("m/d/Y");
        $Filtered = isset($_GET['filtered']) ? $_GET['filtered'] : 0;
        $FinalDate = $this->MCommon->fixFinalDate($FinalDate, 1);
        $InitialDateFixed = $this->MCommon->fixInitialDate($InitialDate, 1);


        $page = isset($_GET['page']) ? $_GET['page'] : 1; // get the requested page
        $limit = isset($_GET['rows']) ? $_GET['rows'] : 10; // get how many rows we want to have into the grid
        $sidx = isset($_GET['sidx']) ? $_GET['sidx'] : 'Date'; // get index row - i.e. user click to sort
        $sord = isset($_GET['sord']) ? $_GET['sord'] : 'desc'; // get the direction


        $selectCount = 'Select count(*) as rowNum';
        $select = "SELECT a.Date,
            a.Origin,
            a.Destination,
            b.ProductCatalogID,
            flow,
            b.Quantity,
            a.UserID,
            a.Comments ";

        $selectSlice = "SELECT Date,
            Inventory.dbo.fn_GetAccountName(Origin) as OriginName,
            Inventory.dbo.fn_GetAccountName(Destination) as DestinationName,
            ProductCatalogID as SKU,
            flow,
            case
                when flow = 1 then 'Added'
                when flow = 2 then 'Removed'
                when flow = 3 then 'Transfered'
                else 'UnknowAdjustment'
            end
            as 'AdjustmentType',
            round(Quantity,4) as Quantity,0000000000.00 as Remaining,
            cast(Inventory.dbo.fn_GetUserName(UserID) as varchar) as UserName,
            Comments";


        $from = ' from ';
        $table = "Inventory.dbo.InventoryAdjustments a, Inventory.dbo.InventoryAdjustmentDetails b";

        $where = " WHERE (a.ID = b.InventoryAdjustmentsID) and (b.ProductCatalogID ='{$search}')
                   and (a.Date between '{$InitialDate}' and '{$FinalDate}')";

        if ($Filtered != 0) {
            $where.=" and (flow = $Filtered ) ";
        }
        $SQL = "{$selectCount}{$from}{$table}{$where}";
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


        $SQL = "WITH mytable AS ({$select}, ROW_NUMBER() OVER (ORDER BY {$sidx} {$sord}) AS RowNumber
                                FROM {$table}{$where})
               {$selectSlice}, RowNumber
               FROM mytable
                WHERE RowNumber BETWEEN {$start} AND {$finish}";


        $result = $this->MCommon->getSomeRecords($SQL);
        
        $responce = new stdClass();
        $responce->page = $page;
        $responce->total = $total_pages;
        $responce->records = $count;

        if ($sord == 'asc') {
            $counter = $this->MCatalog->calcInventory($search, $InitialDate);
        } else if ($sord == 'desc') {
            $counter = $this->MCatalog->calcInventory($search, $FinalDate);
        }


        $i = 0;

        foreach ($result as $row) {
            if ($sord == 'asc') {
                switch ($row['flow']) {
                    case 1: {  //Added
                            $row['Remaining'] = $counter + $row['Quantity'];
                            $counter = $row['Remaining'];
                            break;
                        }
                    case 2: {  //Removed
                            $row['Remaining'] = $counter - $row['Quantity'];
                            $counter = $row['Remaining'];
                            break;
                        }
                    case 3: { //Tranfered
                            $row['Remaining'] = $counter;
                            $counter = $row['Remaining'];
                            break;
                        }
                };
            } else if ($sord == 'desc') {
                switch ($row['flow']) {
                    case 1: {  //Added  
                            $row['Remaining'] = $counter;
                            $counter = $row['Remaining'] - $row['Quantity'];
                            break;
                        }
                    case 2: {  //Removed
                            $row['Remaining'] = $counter;
                            $counter = $row['Remaining'] + $row['Quantity'];
                            break;
                        }
                    case 3: { //Tranfered
                            $row['Remaining'] = $counter;
                            $counter = $row['Remaining'];
                            break;
                        }
                };
            }


            $responce->rows[$i]['id'] = $row['Date'];
            $responce->rows[$i]['cell'] = array($row['Date'],
                $row['flow'] = utf8_encode($row['flow']),
                $row['OriginName'] = utf8_encode($row['OriginName']),
                $row['DestinationName'] = utf8_encode($row['DestinationName']),
                $row['SKU'] = utf8_encode($row['SKU']),
                $row['AdjustmentType'] = utf8_encode($row['AdjustmentType']),
                $row['Quantity'] = utf8_encode($row['Quantity']),
                $row['Remaining'] = utf8_encode($row['Remaining']),
                $row['UserName'] = utf8_encode($row['UserName']),
                $row['Comments'] = utf8_encode($row['Comments']),
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
