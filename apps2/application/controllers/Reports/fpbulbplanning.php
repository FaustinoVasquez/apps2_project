<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class FpBulbPlanning extends BP_Controller {

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

        $this->title = "MI Technologiesinc - FP Bulb Planning";
        $this->description = "FP Bulb Planning";
        $this->css = array('form.css', 'fluid.css', 'table.css', 'jqueryui/redmond/jquery-ui-1.8.21.custom.css', 'jqgrid/ui.jqgrid.css', 'menu.css',);

        // Define custom javascript
        $this->javascript = array('jqueryui/ui/jquery-ui-1.8.21.custom.js', 'jqgrid/i18n/grid.locale-en.js', 'jqgrid/jquery.jqGrid.min.js', 'jqgrid/jquery.jqGrid.fluid.js', 'site.js');

        $this->load->library('Layout');
        $menu = new Layout;

        $data = array( 
            'menu' => $menu->show_menu($this->MUsers->getUserFullName($this->session->userdata('userid'))),
            'from' => '/Reports/fpbulbplanning',
            'caption' => 'FP Bulb Planning',
            'export' => 'FPBulbPlanning',
            'sort' => 'desc',
            'baseUrl' => base_url(),
        );

        $data['colNames'] = "['SKU','Item','30DaysSold','60DaysSold','90DaysSold','30PerDay','60PerDay','90PerDay','QOH','vQOH','tQOH',
        'ShortOrOver','ProduceThisWeek?']";

        $data['colModel'] = "[
                {name:'SKU',index:'SKU', width:80, align:'center', sorttype: 'int'},
                {name:'Item',index:'Item', width:300, align:'left'},  
                {name:'30DaysSold',index:'30DaysSold', width:80, align:'center', sorttype: 'int'}, 
                {name:'60DaysSold',index:'60DaysSold', width:80, align:'center', sorttype: 'int'}, 
                {name:'90DaysSold',index:'90DaysSold', width:80, align:'center', sorttype: 'int'}, 
                {name:'30PerDay',index:'30PerDay', width:80, align:'center', sorttype: 'int'},
                {name:'60PerDay',index:'60PerDay', width:80, align:'center', sorttype: 'int'},  
                {name:'90PerDay',index:'90PerDay', width:80, align:'center', sorttype: 'int'}, 
                {name:'QOH',index:'QOH', width:80, align:'center', sorttype: 'int'}, 
                {name:'vQOH',index:'vQOH', width:80, align:'center', sorttype: 'int'}, 
                {name:'tQOH',index:'tQOH', width:80, align:'center', sorttype: 'int'},
                {name:'ShortOrOver',index:'ShortOrOver', width:80, align:'center', sorttype: 'int'},  
                {name:'ProduceThisWeek',index:'ProduceThisWeek', width:80, align:'center', sorttype: 'int'},  
                
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

        //Select General con los campos necesarios para la vista
        $select = 'SELECT GB.[SKU]
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
                            ,GB.[ProduceThisWeek]';

        $selectSlice = 'SELECT [SKU]
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
                            ,[ProduceThisWeek]';
        $from = ' FROM';
        $table = ' [Inventory].[dbo].[FP-Generic-Bulb-Planning] AS GB';
        $where ='';
        $wherefields = array('GB.[SKU]','GB.[Item]');


        //Obtenemos todos los nombres de campos de la tabla productCatalog
        $fields = $this->MCommon->getAllfields($table);
        //creamos el where concatenando todos los nombres de campos y las palabras de busqueda
        $where .= $this->MCommon->concatAllWerefields($wherefields, $search);

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

        $SQL = "WITH mytable AS ($select , ROW_NUMBER() OVER (ORDER BY [$sidx] $sord) AS RowNumber
                                FROM {$table}{$where})
               {$selectSlice}, RowNumber
               FROM mytable
                WHERE RowNumber BETWEEN {$start} AND {$finish}";

        $result = $this->MCommon->getSomeRecords($SQL);

        $responce = new stdClass();
        $responce->page = $page;
        $responce->total = $total_pages;
        $responce->records = $count;

        $i = 0;
        foreach ($result as $row) {
            
            $responce->rows[$i]['id'] = $row['RowNumber'];
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

