<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class RptvModuleSafetyStock extends BP_Controller {

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

        $this->title = "MI Technologiesinc - RPTV Module Safety Stock";
        $this->description = "RPTV Module Safety Stock";
        $this->css = array('form.css', 'fluid.css', 'table.css', 'jqueryui/redmond/jquery-ui-1.8.21.custom.css', 'jqgrid/ui.jqgrid.css', 'menu.css',);

        // Define custom javascript
        $this->javascript = array('jqueryui/ui/jquery-ui-1.8.21.custom.js', 'jqgrid/i18n/grid.locale-en.js', 'jqgrid/jquery.jqGrid.min.js', 'jqgrid/jquery.jqGrid.fluid.js', 'site.js');

        $this->load->library('Layout');
        $menu = new Layout;

        $data = array( 
            'menu' => $menu->show_menu($this->MUsers->getUserFullName($this->session->userdata('userid'))),
            'from' => '/Reports/rptvmodulesafetystock',
            'caption' => 'RPTV Module Safety Stock',
            'export' => 'RPTVModuleSafetyStock',
            'sort' => 'desc',
            'baseUrl' => base_url(),
        );

        $data['colNames'] = "['SKUSold','Name','Last30DaysSold','Last60DaysSold','Last90DaysSold','QOH','vQOH','tQOH','FBAQty',
        'FBAInboundQty','PendingFBANeeded','SalesPerDay','Build(Safety10)','Disassemble']";

        $data['colModel'] = "[
                {name:'SKUSold',index:'SKUSold', width:80, align:'center', sorttype: 'int'},  
                {name:'Name',index:'Name', width:450, align:'left'}, 
                {name:'Last30DaysSold',index:'Last30DaysSold', width:80, align:'center', sorttype: 'int'}, 
                {name:'Last60DaysSold',index:'Last60DaysSold', width:80, align:'center', sorttype: 'int'}, 
                {name:'Last90DaysSold',index:'Last90DaysSold', width:80, align:'center', sorttype: 'int'},
                {name:'QOH',index:'QOH', width:80, align:'center', sorttype: 'int'},  
                {name:'vQOH',index:'vQOH', width:80, align:'center', sorttype: 'int'}, 
                {name:'tQOH',index:'tQOH', width:80, align:'center', sorttype: 'int'}, 
                {name:'FBAQty',index:'FBAQty', width:80, align:'center', sorttype: 'int'}, 
                {name:'FBAInboundQty',index:'FBAInboundQty', width:80, align:'center', sorttype: 'int'},
                {name:'PendingFBANeeded',index:'PendingFBANeeded', width:80, align:'center', sorttype: 'int'},  
                {name:'SalesPerDay',index:'SalesPerDay', width:80, align:'center', sorttype: 'int'},  
                {name:'Build(Safety10)',index:'Build(Safety10)', width:80, align:'center', sorttype: 'int'}, 
                {name:'Disassemble',index:'Disassemble', width:80, align:'center', sorttype: 'int'}, 
                
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
        $select = 'SELECT [SKUSold]
                            ,[Name]
                            ,[Last30DaysSold]
                            ,[Last60DaysSold]
                            ,[Last90DaysSold]
                            ,[QOH]
                            ,[vQOH]
                            ,[tQOH]
                            ,[FBAQty]
                            ,[FBAInboundQty]
                            ,[PendingFBANeeded]
                            ,[SalesPerDay]
                            ,[Build(Safety10)]
                            ,[Disassemble]';

        $selectSlice = 'SELECT [SKUSold]
                            ,[Name]
                            ,[Last30DaysSold]
                            ,[Last60DaysSold]
                            ,[Last90DaysSold]
                            ,[QOH]
                            ,[vQOH]
                            ,[tQOH]
                            ,[FBAQty]
                            ,[FBAInboundQty]
                            ,[PendingFBANeeded]
                            ,[SalesPerDay]
                            ,[Build(Safety10)]
                            ,[Disassemble]';
        $from = ' FROM';
        $table = ' [Inventory].[dbo].[RPTV-Generic-Module-Safety] ';
        $where ='';
        $wherefields = array('SKUSold','Name');


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

        //print_r($SQL);

        $result = $this->MCommon->getSomeRecords($SQL);

        $responce = new stdClass();
        $responce->page = $page;
        $responce->total = $total_pages;
        $responce->records = $count;

        $i = 0;
        foreach ($result as $row) {
            
            $responce->rows[$i]['id'] = $row['RowNumber'];
            $responce->rows[$i]['cell'] = array($row['SKUSold'],
                $row['Name'] = utf8_encode($row['Name']),
                $row['Last30DaysSold'],
                $row['Last60DaysSold'], 
                $row['Last90DaysSold'],
                $row['QOH'],
                $row['vQOH'],
                $row['tQOH'], 
                $row['FBAQty'],
                $row['FBAInboundQty'],
                $row['PendingFBANeeded'],
                $row['SalesPerDay'],
                $row['Build(Safety10)'],
                $row['Disassemble'],
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

