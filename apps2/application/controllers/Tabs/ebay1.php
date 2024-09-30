<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Ebay1 extends BP_Controller {

    public function __construct() {
        parent::__construct();

        $is_logged_in = $this->session->userdata('is_logged_in');

        if (!isset($is_logged_in) || $is_logged_in != true) {
            redirect(base_url(), 'refresh');
        }
    }

    function index() { 

        $this->title = "MI Technologiesinc - eBay1 Details";

        $this->description = "eBay1 Details";

        $this->css = array('form.css', 'fluid.css', 'table.css', 'jqueryui/redmond/jquery-ui-1.8.21.custom.css', 'jqgrid/ui.jqgrid.css',);

        // Define custom javascript
        $this->javascript = array('jqueryui/ui/jquery-ui-1.8.21.custom.js', 'jqgrid/i18n/grid.locale-en.js', 'jqgrid/jquery.jqGrid.min.js', 'jqgrid/jquery.jqGrid.fluid.js');
        $this->hasNav = False;
        $this->load->library('Layout');
        

        $data = array(
            
            'from' => '/Tabs/ebay1/',
            'nameGrid' => 'list',
            'namePager' => 'Pager',
            'caption' => 'eBay1',
            'export' => 'ebay1_details',
            'sku' => $this->uri->segment(3),
            'sort' => 'desc',
            'baseUrl' => base_url(),
        );

        $data['colNames'] = "['FeedID','eBay1StoreName','eBay1UpsellLink','eBay1Template','eBay1Price','eBay1IsActive']";

        $data['colModel'] = "[
                {name:'FeedID',index:'FeedID', width:50, align:'center'}, 
                {name:'eBay1StoreName',index:'eBay1StoreName', width:170, align:'center'}, 
                {name:'eBay1UpsellLink',index:'eBay1UpsellLink', width:300, align:'left'}, 
                {name:'eBay1Template',index:'eBay1Template', width:300, align:'center'}, 
                {name:'eBay1Price',index:'eBay1Price', width:80, align:'center'}, 
                {name:'eBay1IsActive',index:'eBay1IsActive', width:90, align:'center'},  
            ]";

       
        $this->build_content($data);
        $this->render_page();
    }

    function getData() {

        $page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1; // get the requested page
        $limit = isset($_REQUEST['rows']) ? $_REQUEST['rows'] : 10; // get how many rows we want to have into the gri
        $sidx = isset($_REQUEST['sidx']) ? $_REQUEST['sidx'] : 'FeedID'; // get index row - i.e. user click to sort
        $sord = isset($_REQUEST['sord']) ? $_REQUEST['sord'] : 'asc';
        
        $sku = $this->input->get('sku');
        
        $SQL = "SELECT [FeedID],[eBay1StoreName],[eBay1UpsellLink],[eBay1Template],[eBay1Price],[eBay1IsActive] 
                FROM [Inventory].[dbo].[ChannelAdvisorFeed] WHERE [FeedID] = {$sku}";


        $result = $this->MCommon->getOneRecord($SQL);

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
            $responce->rows[$i]['ID'] = $row['FeedID'];
            $responce->rows[$i]['cell'] = array($row['FeedID'],
                $row['eBay1StoreName'],
                $row['eBay1UpsellLink'],
                $row['eBay1Template'],
                $row['eBay1Price'],
                $row['eBay1IsActive'],
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