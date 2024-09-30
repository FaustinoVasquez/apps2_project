<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class RptvBulbPlanning extends BP_Controller {

    public function __construct() {
        parent::__construct();

        $is_logged_in = $this->session->userdata('is_logged_in');

        if (!isset($is_logged_in) || $is_logged_in != true) {
            redirect(base_url(), 'refresh');
        }

        // if ($this->MUsers->isValidUser($this->session->userdata('userid'), 881215) != 1) {// 881100 prodcat Access
        //     redirect('Catalog/prodcat', 'refresh');
        // }
    }

    function index() { 

        $this->title = "MI Technologiesinc - RPTV Bulb Planning";
        $this->description = "RPTV Bulb Planning";
        $this->css = array('form.css', 'fluid.css', 'table.css', 'jqueryui/redmond/jquery-ui-1.8.21.custom.css', 'jqgrid/ui.jqgrid.css', 'menu.css',);

        // Define custom javascript
        $this->javascript = array('jqueryui/ui/jquery-ui-1.8.21.custom.js', 'jqgrid/i18n/grid.locale-en.js', 'jqgrid/jquery.jqGrid.min.js', 'jqgrid/jquery.jqGrid.fluid.js', 'site.js');

        $this->load->library('Layout');
        $menu = new Layout;

        $data = array( 
            'menu' => $menu->show_menu($this->MUsers->getUserFullName($this->session->userdata('userid'))),
            'from' => '/Reports/rptvbulbplanning',
            'caption' => 'RPTV Bulb Planning',
            'export' => 'RPTVBulbPlanning',
            'sort' => 'desc',
            'baseUrl' => base_url(),
        );

        $data['colNames'] = "['SKU','Item','30DaysSold','60DaysSold','90DaysSold','30PerDay','60PerDay','90PerDay','QOH','vQOH','tQOH',
        'ShortOrOver','BackOrders','ProduceThisWeek']";

        $data['colModel'] = "[
                {name:'SKU',index:'SKU', align:'center', sorttype: 'int'},
                {name:'Item',index:'Item', align:'center'},
                {name:'30DaysSold',index:'30DaysSold', align:'center', sorttype: 'int'},
                {name:'60DaysSold',index:'60DaysSold', align:'center', sorttype: 'int'},
                {name:'90DaysSold',index:'90DaysSold', align:'center', sorttype: 'int'},
                {name:'30PerDay',index:'30PerDay', align:'center', sorttype: 'int'},
                {name:'60PerDay',index:'60PerDay', align:'center', sorttype: 'int'},
                {name:'90PerDay',index:'90PerDay', align:'center', sorttype: 'int'},
                {name:'QOH',index:'QOH', align:'center', sorttype: 'int'},
                {name:'vQOH',index:'vQOH', align:'center', sorttype: 'int'},
                {name:'tQOH',index:'tQOH', align:'center', sorttype: 'int'},
                {name:'ShortOrOver',index:'ShortOrOver', align:'center', sorttype: 'int'},
                {name:'BackOrders',index:'BackOrders', align:'center',formatter:formatLink, sorttype: 'int'},
                {name:'ProduceThisWeek',index:'ProduceThisWeek', align:'center', sorttype: 'int'},
                
            ]";
            
        $this->build_content($data);
        $this->render_page();
    }

    function getData() {

        $page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1; // get the requested page
        $limit = isset($_REQUEST['rows']) ? $_REQUEST['rows'] : 10; // get how many rows we want to have into the gri
        $sidx = $_GET['sidx']; // get index row - i.e. user click to sort
        $sord = $_GET['sord'];
        
        $search = $this->input->get('ds');

        //Select utilizado para realizar el conteo del numero de registros devueltos por la consulta.
        $selectCount = 'SELECT COUNT(*) AS rowNum';

        $select = " SELECT GB.[SKU]
                    ,GB.[Item]
                    ,GB.[30DaysSold]
                    ,GB.[60DaysSold]
                    ,GB.[90DaysSold]
                    ,GB.[30PerDay]
                    ,GB.[60PerDay]
                    ,GB.[90PerDay]
                    ,GB.[QOH]
                    ,GB.[vQOH]
                    ,GB.[tQOH]
                    ,GB.[ShortOrOver]
                    ,GB.[BackOrders]
                    ,GB.[ProduceThisWeek]";

        $selectSlice = " SELECT [SKU]
                        ,[Item]
                        ,[30DaysSold]
                        ,[60DaysSold]
                        ,[90DaysSold]
                        ,[30PerDay]
                        ,[60PerDay]
                        ,[90PerDay]
                        ,[QOH]
                        ,[vQOH]
                        ,[tQOH]
                        ,[ShortOrOver]
                        ,[BackOrders]
                        ,[ProduceThisWeek]";

        $from = " FROM [Inventory].[dbo].[RPTV-Generic-Bulb-Planning] AS GB";

        
        $where = '';
        $wherefields = array('GB.[Item]','GB.[SKU]');

        $where .= $this->MCommon->concatAllWerefields($wherefields, $search);

        $SQL = "{$selectCount}{$from}{$where}";
        $result = $this->MCommon->getOneRecord($SQL);
        //print_r($SQL);

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
            
            $responce->rows[$i]['id'] = $row['SKU'];
            $responce->rows[$i]['cell'] = array($row['SKU'],
                $row['Item'] = utf8_encode($row['Item']),
                $row['30DaysSold'],
                $row['60DaysSold'], 
                $row['90DaysSold'],
                $row['30PerDay'],
                $row['60PerDay'],
                $row['90PerDay'], 
                $row['QOH'],
                $row['vQOH'],
                $row['tQOH'], 
                $row['ShortOrOver'],
                $row['BackOrders'],
                $row['ProduceThisWeek'],
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


