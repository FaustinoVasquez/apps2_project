<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class FpReflectorGlassOrdering extends BP_Controller {

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

        $this->title = "MI Technologiesinc - FP ReflectorGlass Ordering";
        $this->description = "FP ReflectorGlass Ordering";
        $this->css = array('form.css', 'fluid.css', 'table.css', 'jqueryui/redmond/jquery-ui-1.8.21.custom.css', 'jqgrid/ui.jqgrid.css', 'menu.css',);

        // Define custom javascript
        $this->javascript = array('jqueryui/ui/jquery-ui-1.8.21.custom.js', 'jqgrid/i18n/grid.locale-en.js', 'jqgrid/jquery.jqGrid.min.js', 'jqgrid/jquery.jqGrid.fluid.js', 'site.js');

        $this->load->library('Layout');
        $menu = new Layout;

        $data = array( 
            'menu' => $menu->show_menu($this->MUsers->getUserFullName($this->session->userdata('userid'))),
            'from' => '/Reports/fpreflectorglassordering',
            'caption' => 'FP ReflectorGlass Ordering',
            'export' => 'FPReflectorGlassOrdering',
            'sort' => 'desc',
            'baseUrl' => base_url(),
        );

        $data['colNames'] = "['ReflectorGlass','Item','QOH','30DaysSoldRFQ','60DaysSoldRFQ','90DaysSoldRFQ','60DayShortOver','BackOrders','SuggestOrder']";

        $data['colModel'] = "[
                {name:'ReflectorGlass',index:'ReflectorGlass', width:60, align:'center'},
                {name:'Item',index:'Item', width:300, align:'left'},
                {name:'QOH',index:'QOH', width:60, align:'center'},
                {name:'30DaysSoldRFQ',index:'30DaysSoldRFQ', width:60, align:'center'},  
                {name:'60DaysSoldRFQ',index:'60DaysSoldRFQ', width:60, align:'center'}, 
                {name:'90DaysSoldRFQ',index:'90DaysSoldRFQ', width:60, align:'center'}, 
                {name:'60DayShortOver',index:'60DayShortOver', width:60, align:'center'}, 
                {name:'BackOrders',index:'BackOrders', width:60, align:'center',formatter:formatLink}, 
                {name:'SuggestOrder',index:'SuggestOrder', width:60, align:'center'}, 
            ]";
            
        $this->build_content($data);
        $this->render_page();
    }

    function getData() {

        $page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1; // get the requested page
        $limit = isset($_REQUEST['rows']) ? $_REQUEST['rows'] : 10; // get how many rows we want to have into the gri
        $sidx = isset($_REQUEST['sidx']) ? $_REQUEST['sidx'] : 'ReflectorGlass'; // get index row - i.e. user click to sort
        $sord = isset($_REQUEST['sord']) ? $_REQUEST['sord'] : 'asc';
        
        $search = $this->input->get('ds');

        //Select utilizado para realizar el conteo del numero de registros devueltos por la consulta.
        $selectCount = 'SELECT COUNT(*) AS rowNum';

        //Select General con los campos necesarios para la vista
        $select = " SELECT DISTINCT FPGLCSR.[ReflectorGlass]
                    ,PC.[Name] AS 'Item'
                    ,IsNull(Cast(GS.[GlobalStock] as int),0) AS 'QOH'
                    ,(SELECT SUM([30DaysSold]) FROM [Inventory].[dbo].[FP-Generic-LampComponent-Sales-Report] AS FPGLCSR2 WHERE FPGLCSR2.[ReflectorGlass] = FPGLCSR.[ReflectorGlass]) AS '30DaysSoldRFQ'
                    ,(SELECT SUM([60DaysSold]) FROM [Inventory].[dbo].[FP-Generic-LampComponent-Sales-Report] AS FPGLCSR2 WHERE FPGLCSR2.[ReflectorGlass] = FPGLCSR.[ReflectorGlass]) AS '60DaysSoldRFQ'
                    ,(SELECT SUM([90DaysSold]) FROM [Inventory].[dbo].[FP-Generic-LampComponent-Sales-Report] AS FPGLCSR2 WHERE FPGLCSR2.[ReflectorGlass] = FPGLCSR.[ReflectorGlass]) AS '90DaysSoldRFQ'
                    ,(IsNull(Cast(GS.GlobalStock as int),0)-(SELECT SUM([60DaysSold]) FROM [Inventory].[dbo].[FP-Generic-LampComponent-Sales-Report] AS FPGLCSR2 WHERE FPGLCSR2.[ReflectorGlass] = FPGLCSR.[ReflectorGlass])) AS '60DayShortOver'
                    ,IsNull((SELECT SUM(Cast(POD.[QtyBackOrdered] AS INT)) FROM [Inventory].[dbo].[PurchaseOrderData] AS POD WHERE Cast(POD.SKU AS int) = FPGLCSR.[ReflectorGlass]),0) AS 'BackOrders'
                    ,(CASE WHEN IsNull((SELECT SUM(Cast(POD.[QtyBackOrdered] AS INT)) FROM [Inventory].[dbo].[PurchaseOrderData] AS POD WHERE Cast(POD.SKU AS int) = FPGLCSR.[ReflectorGlass]),0)+(IsNull(Cast(GS.GlobalStock as int),0)-(SELECT SUM([60DaysSold]) FROM [Inventory].[dbo].[FP-Generic-LampComponent-Sales-Report] AS FPGLCSR2 WHERE FPGLCSR2.[ReflectorGlass] = FPGLCSR.[ReflectorGlass])) < 0 THEN
                        IsNull((SELECT SUM(Cast(POD.[QtyBackOrdered] AS INT)) FROM [Inventory].[dbo].[PurchaseOrderData] AS POD WHERE Cast(POD.SKU AS int) = FPGLCSR.[ReflectorGlass]),0)+(IsNull(Cast(GS.GlobalStock as int),0)-(SELECT SUM([60DaysSold]) FROM [Inventory].[dbo].[FP-Generic-LampComponent-Sales-Report] AS FPGLCSR2 WHERE FPGLCSR2.[ReflectorGlass] = FPGLCSR.[ReflectorGlass]))
                        ELSE '0' END) AS 'SuggestOrder'";
    
        $from =  ' FROM';
        $table = ' [Inventory].[dbo].[FP-Generic-LampComponent-Sales-Report] AS FPGLCSR ';
        $join =  '  LEFT OUTER JOIN [Inventory].[dbo].[Global_Stocks] AS GS ON (FPGLCSR.[ReflectorGlass] = GS.ProductCatalogId)
                    LEFT OUTER JOIN [Inventory].[dbo].[ProductCatalog] AS PC ON (FPGLCSR.[ReflectorGlass] = PC.[ID])';
        $where ='';
        $wherefields = array('FPGLCSR.[ReflectorGlass]','PC.[Name]');

        //Obtenemos todos los nombres de campos de la tabla productCatalog
      //  $fields = $this->MCommon->getAllfields($table);
        //creamos el where concatenando todos los nombres de campos y las palabras de busqueda
        $where .= $this->MCommon->concatAllWerefields($wherefields, $search);

        $where .= " and FPGLCSR.[ReflectorGlass] IS NOT NULL AND FPGLCSR.[ReflectorGlass] != '0'";

        $SQL = "{$select}{$from}{$table}{$join}{$where}";

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
        $finish = $start + $limit;

        $responce = new stdClass();
        $responce->page = $page;
        $responce->total = $total_pages;
        $responce->records = $count;

        $i = 0;
        foreach ($result as $row) {
            $responce->rows[$i]['id'] = $row['ReflectorGlass'];
            $responce->rows[$i]['cell'] = array($row['ReflectorGlass'],
                $row['Item'] = utf8_encode($row['Item']),
                $row['QOH'],
                $row['30DaysSoldRFQ'],
                $row['60DaysSoldRFQ'],
                $row['90DaysSoldRFQ'],
                $row['60DayShortOver'],
                $row['BackOrders'],
                $row['SuggestOrder'],
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