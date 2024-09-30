<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Recconvfbmtofba extends BP_Controller {

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

        $this->title = "MI Technologiesinc - Recommended Conversion FBM to FBA";

        $this->description = "Recommended Conversion FBM to FBA";

        $this->css = array('form.css', 'fluid.css', 'table.css', 'jqueryui/redmond/jquery-ui-1.8.21.custom.css', 'jqgrid/ui.jqgrid.css', 'menu.css',);

        // Define custom javascript
        $this->javascript = array('jqueryui/ui/jquery-ui-1.8.21.custom.js', 'jqgrid/i18n/grid.locale-en.js', 'jqgrid/jquery.jqGrid.min.js', 'jqgrid/jquery.jqGrid.fluid.js', 'popup.js');

        $this->load->library('Layout');
        $menu = new Layout;

        $data = array(
            'menu' => $menu->show_menu($this->MUsers->getUserFullName($this->session->userdata('userid'))),
            'from' => '/Reports/amazon/recconvfbmtofba/',
            'nameGrid' => 'list',
            'namePager' => 'Pager',
            'caption' => 'Recommended Conversion FBM to FBA',
            'export' => 'recconvfbmtofba',
            'subgrid' => 'true',
            'sort' => 'desc',

        );

        $data['colNames'] = "['ASIN','DARTFBMSKU','Sold30DaysFBM','ASP30DayFBM','Sold90DaysFBA','ASP30DayFBA','Sold30DaysMPRIME','ASP30DayMPRIME','DARTMFPACTIVE']";

        $data['colModel'] = "[
                {name:'ASIN',index:'ASIN', width:80, align:'center' , sorttype:'int'},
                {name:'DARTFBMSKU',index:'DARTFBMSKU', width:80, align:'center',sorttype:'int'},
                {name:'Sold30DaysFBM',index:'Sold30DaysFBM', width:80, align:'center' , sorttype:'int'},
                {name:'ASP30DayFBM',index:'ASP30DayFBM', width:80, align:'center'},
                {name:'Sold90DaysFBA',index:'Sold90DaysFBA', width:80, align:'left', sorttype:'int'},
                {name:'ASP30DayFBA',index:'ASP30DayFBA', width:80, align:'center' , sorttype:'int'},
                {name:'Sold30DaysMPRIME',index:'Sold30DaysMPRIME', width:80, align:'center' , sorttype:'int'},   
                {name:'ASP30DayMPRIME',index:'ASP30DayMPRIME', width:80, align:'center' , sorttype:'int'}, 
                {name:'DARTMFPACTIVE',index:'DARTMFPACTIVE', width:80, align:'center' , sorttype:'int'}, 
 
          	]";

       
        $this->build_content($data);
        $this->render_page();
    }

    function getData() {

        $page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1; // get the requested page
        $limit = isset($_REQUEST['rows']) ? $_REQUEST['rows'] : 10; // get how many rows we want to have into the gri
        $sidx = isset($_REQUEST['sidx']) ? $_REQUEST['sidx'] : 'ID'; // get index row - i.e. user click to sort
        $sord = isset($_REQUEST['sord']) ? $_REQUEST['sord'] : 'asc';
        $search = !empty($_GET['ds']) ? $_REQUEST['ds'] : '';
        // $to = !empty($_GET['dt']) ? $_REQUEST['dt'] : date("m/d/Y");
        // $nsfba= $_GET['nsfba'];
        $where ='';


        $select = "SELECT DISTINCT AZMSKU.[ASIN]
              ,AZMSKU.[DARTFBMSKU]
              ,(SELECT SUM(OD.[QuantityOrdered]) 
                FROM [OrderManager].[dbo].[Order Details] AS OD 
                WHERE OD.[WebSKU] = AZMSKU.[DARTFBMSKU] AND OD.[DetailDate] BETWEEN GETDATE()-30 AND GETDATE()) AS 'Sold30DaysFBM'
              ,CAST((SELECT SUM(OD.[PricePerUnit]*OD.[QuantityOrdered])/SUM(OD.[QuantityOrdered]) 
                FROM [OrderManager].[dbo].[Order Details] AS OD 
                WHERE OD.[WebSKU] = AZMSKU.[DARTFBMSKU] AND OD.[DetailDate] BETWEEN GETDATE()-30 AND GETDATE()) AS Decimal(10,2)) AS 'ASP30DayFBM'
              ,AZMSKU.[DARTFBASKU]
              ,(SELECT SUM(OD.[QuantityOrdered]) 
                FROM [OrderManager].[dbo].[Order Details] AS OD 
                WHERE OD.[WebSKU] = AZMSKU.[DARTFBASKU] AND OD.[DetailDate] BETWEEN GETDATE()-30 AND GETDATE()) AS 'Sold90DaysFBA'
              ,CAST((SELECT SUM(OD.[PricePerUnit]*OD.[QuantityOrdered])/SUM(OD.[QuantityOrdered]) 
                FROM [OrderManager].[dbo].[Order Details] AS OD 
                WHERE OD.[WebSKU] = AZMSKU.[DARTFBASKU] AND OD.[DetailDate] BETWEEN GETDATE()-90 AND GETDATE()) AS Decimal(10,2)) AS 'ASP30DayFBA'
              ,AZMSKU.[DARTMFPSKU]
              ,(SELECT SUM(OD.[QuantityOrdered]) 
                FROM [OrderManager].[dbo].[Order Details] AS OD 
                WHERE OD.[WebSKU] = AZMSKU.[DARTMFPSKU] AND OD.[DetailDate] BETWEEN GETDATE()-30 AND GETDATE()) AS 'Sold30DaysMPRIME'
              ,CAST((SELECT SUM(OD.[PricePerUnit]*OD.[QuantityOrdered])/SUM(OD.[QuantityOrdered]) 
                FROM [OrderManager].[dbo].[Order Details] AS OD 
                WHERE OD.[WebSKU] = AZMSKU.[DARTMFPSKU] AND OD.[DetailDate] BETWEEN GETDATE()-30 AND GETDATE()) AS Decimal(10,2)) AS 'ASP30DayMPRIME'
              ,AZMSKU.[DARTMFPACTIVE]
                  
              FROM [Inventory].[dbo].[AmazonMerchantSKU] AS AZMSKU
              LEFT OUTER JOIN [OrderManager].[dbo].[Order Details] AS ODMAIN ON (AZMSKU.[DARTFBMSKU] = ODMAIN.[WebSKU]) ";


        $wherefields = array('AZMSKU.[ASIN]');

        $where .=$this->MCommon->concatAllWerefields($wherefields, $search);

        $where .=" and (ODMAIN.[DetailDate] BETWEEN GETDATE()-30 AND GETDATE()) AND ODMAIN.[Product] IS NOT NULL
                  AND (SELECT SUM(OD.[QuantityOrdered]) FROM [OrderManager].[dbo].[Order Details] AS OD 
                           WHERE OD.[WebSKU] = AZMSKU.[DARTFBMSKU] AND OD.[DetailDate] BETWEEN GETDATE()-30 AND GETDATE()) > 10
                  AND (SELECT SUM(OD.[QuantityOrdered]) FROM [OrderManager].[dbo].[Order Details] AS OD 
                           WHERE OD.[WebSKU] = AZMSKU.[DARTFBASKU] AND OD.[DetailDate] BETWEEN GETDATE()-90 AND GETDATE()) IS NULL ORDER BY 'Sold30DaysFBM' DESC";

        $SQL = "{$select}{$where}";

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
            $responce->rows[$i]['ID'] = $row['ASIN'];
            $responce->rows[$i]['cell'] = array($row['ASIN'],
                $row['DARTFBMSKU'],
                $row['Sold30DaysFBM'],
                $row['ASP30DayFBM'],
                $row['Sold90DaysFBA'],
                $row['ASP30DayFBA'],
               	$row['Sold30DaysMPRIME'],
                $row['ASP30DayMPRIME'],
                $row['DARTMFPACTIVE'],
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
