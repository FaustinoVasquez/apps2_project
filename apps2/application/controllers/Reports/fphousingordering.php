<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class FpHousingOrdering extends BP_Controller {

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

        $this->title = "MI Technologiesinc - FP Housing Ordering";
        $this->description = "FP Housing Ordering";
        $this->css = array('form.css', 'fluid.css', 'table.css', 'jqueryui/redmond/jquery-ui-1.8.21.custom.css', 'jqgrid/ui.jqgrid.css', 'menu.css',);

        // Define custom javascript
        $this->javascript = array('jqueryui/ui/jquery-ui-1.8.21.custom.js', 'jqgrid/i18n/grid.locale-en.js', 'jqgrid/jquery.jqGrid.min.js', 'jqgrid/jquery.jqGrid.fluid.js', 'site.js');

        $this->load->library('Layout');
        $menu = new Layout;

        $data = array( 
            'menu' => $menu->show_menu($this->MUsers->getUserFullName($this->session->userdata('userid'))),
            'from' => '/Reports/fphousingordering',
            'caption' => 'FP Housing Ordering',
            'export' => 'FPHousingOrdering',
            'sort' => 'desc',
            'baseUrl' => base_url(),
        );

        $data['colNames'] = "['KitSKU','Name','SKUSold','Sold30Days','Sold60Days','Sold90Days','Kit-QOH','Kit-vQOH','Kit-tQOH','Backordered','60DayShortOrOver',
        'SuggestOrder','WeMakeIt?','LowestCost','LowestCostSupplier']";

        $data['colModel'] = "[
                {name:'KitSKU',index:'KitSKU', width:80, align:'center', sorttype: 'int'},
                {name:'Name',index:'Name', width:350, align:'left'},  
                {name:'SKUSold',index:'SKUSold', width:80, align:'center', sorttype: 'int'}, 
                {name:'Sold30Days',index:'Sold30Days', width:80, align:'center', sorttype: 'int'}, 
                {name:'Sold60Days',index:'Sold60Days', width:80, align:'center', sorttype: 'int'}, 
                {name:'Sold90Days',index:'Sold90Days', width:80, align:'center', sorttype: 'int'},
                {name:'Kit-QOH',index:'Kit-QOH', width:80, align:'center', sorttype: 'int'},  
                {name:'Kit-vQOH',index:'Kit-vQOH', width:80, align:'center', sorttype: 'int'}, 
                {name:'Kit-tQOH',index:'Kit-tQOH', width:80, align:'center', sorttype: 'int'}, 
                {name:'Backordered',index:'Backordered', width:80, align:'center',formatter:formatLink, sorttype: 'int'}, 
                {name:'60DayShortOrOver',index:'60DayShortOrOver', width:80, align:'center', sorttype: 'int'},
                {name:'SuggestOrder',index:'SuggestOrder', width:80, align:'center', sorttype: 'int'},  
                {name:'WeMakeIt',index:'WeMakeIt', width:80, align:'center'},  
                {name:'LowestCost',index:'LowestCost', width:60, align:'center', sorttype: 'float'},  
                {name:'LowestCostSupplier',index:'LowestCostSupplier', width:105, align:'center'},
            ]";
            
        $this->build_content($data);
        $this->render_page();
    }

    function getData() {

        $page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1; // get the requested page
        $limit = isset($_REQUEST['rows']) ? $_REQUEST['rows'] : 50; // get how many rows we want to have into the gri
        $sidx = $_GET['sidx']; // get index row - i.e. user click to sort
        $sord = $_GET['sord'];
        
        $search = $this->input->get('ds');

        //Select utilizado para realizar el conteo del numero de registros devueltos por la consulta.
        $selectCount = 'SELECT COUNT(*) AS rowNum';

        //Select General con los campos necesarios para la vista
        $select = 'SELECT [KitSKU]
                          ,[Name]
                          ,[SKUSold]
                          ,[Sold30Days]
                          ,[Sold60Days]
                          ,[Sold90Days]
                          ,[Kit-QOH]
                          ,[Kit-vQOH]
                          ,[Kit-tQOH]
                          ,[Backordered]
                          ,[60DayShortOrOver]
                          ,[SuggestOrder]
                          ,[WeMakeIt]
                          ,[LowestCost]
                          ,[LowestCostSupplier]';

        $from = ' FROM';
        $table = ' [Inventory].[dbo].[FP-Generic-Housing-Planning] ';
        $where ='';
        $wherefields = array('KitSKU','Name','SKUSold');

        //Obtenemos todos los nombres de campos de la tabla productCatalog
        //$fields = $this->MCommon->getAllfields($table);
        //creamos el where concatenando todos los nombres de campos y las palabras de busqueda
        $where .= $this->MCommon->concatAllWerefields($wherefields, $search);

        $where .= " and (KitSKU != 0) AND (KitSKU IS NOT NULL)";

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



        $SQL = "WITH mytable AS (
                    $select , ROW_NUMBER() OVER (ORDER BY [{$sidx}] {$sord}) AS RowNumber
                    FROM {$table}{$where}
                )
               Select * from mytable WHERE RowNumber BETWEEN {$start} AND {$finish}";


        $result = $this->MCommon->getSomeRecords($SQL);
        $responce = new stdClass();
        $responce->page = $page;
        $responce->total = $total_pages;
        $responce->records = $count;

        $i = 0;
        foreach ($result as $row) {
            
            $responce->rows[$i]['id'] = $row['RowNumber'];
            $responce->rows[$i]['cell'] = array($row['KitSKU'],
                $row['Name'] = utf8_encode($row['Name']),
                $row['SKUSold'],
                $row['Sold30Days'], 
                $row['Sold60Days'],
                $row['Sold90Days'],
                $row['Kit-QOH'],
                $row['Kit-vQOH'],
                $row['Kit-tQOH'], 
                $row['Backordered'], 
                $row['60DayShortOrOver'],
                $row['SuggestOrder'],
                $row['WeMakeIt'],
                '$ '.number_format($row['LowestCost'],2),
                $row['LowestCostSupplier']
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

