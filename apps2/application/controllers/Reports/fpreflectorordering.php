<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class FpReflectorOrdering extends BP_Controller {

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

        $this->title = "MI Technologiesinc - FP Reflector Ordering";
        $this->description = "FP Reflector Ordering";
        $this->css = array('form.css', 'fluid.css', 'table.css', 'jqueryui/redmond/jquery-ui-1.8.21.custom.css', 'jqgrid/ui.jqgrid.css', 'menu.css',);

        // Define custom javascript
        $this->javascript = array('jqueryui/ui/jquery-ui-1.8.21.custom.js', 'jqgrid/i18n/grid.locale-en.js', 'jqgrid/jquery.jqGrid.min.js', 'jqgrid/jquery.jqGrid.fluid.js', 'site.js');

        $this->load->library('Layout');
        $menu = new Layout;

        $data = array( 
            'menu' => $menu->show_menu($this->MUsers->getUserFullName($this->session->userdata('userid'))),
            'from' => '/Reports/fpreflectorordering',
            'caption' => 'FP Reflector Ordering',
            'export' => 'FPReflectorOrdering',
            'sort' => 'desc',
            'baseUrl' => base_url(),
        );

        $data['colNames'] = "['Reflector','Item','QOH','30DaysSoldRFQ','60DaysSoldRFQ','90DaysSoldRFQ','60DayShortOver','BackOrders','SuggestOrder']";

        $data['colModel'] = "[
                {name:'Reflector',index:'Reflector', width:60, align:'center', sorttype: 'int'},
                {name:'Item',index:'Item', width:350, align:'left'},
                {name:'QOH',index:'QOH', width:60, align:'center', sorttype: 'int'},
                {name:'30DaysSoldRFQ',index:'30DaysSoldRFQ', width:60, align:'center', sorttype: 'int'},  
                {name:'60DaysSoldRFQ',index:'60DaysSoldRFQ', width:60, align:'center', sorttype: 'int'}, 
                {name:'90DaysSoldRFQ',index:'90DaysSoldRFQ', width:60, align:'center', sorttype: 'int'}, 
                {name:'60DayShortOver',index:'60DayShortOver', width:60, align:'center', sorttype: 'int'}, 
                {name:'BackOrders',index:'BackOrders', width:60, align:'center',formatter:formatLink, sorttype: 'int'}, 
                {name:'SuggestOrder',index:'SuggestOrder', width:60, align:'center', sorttype: 'int'}, 
            ]";
            
        $this->build_content($data);
        $this->render_page();
    }

    function getData() {

        $page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1; // get the requested page
        $limit = isset($_REQUEST['rows']) ? $_REQUEST['rows'] : 10; // get how many rows we want to have into the gri
        $sidx = $_GET['sidx']; // get index row - i.e. user click to sort
        $sord = $_GET['sord'];
        //if(!$sidx) $sidx =1;
        
        $search = $this->input->get('ds');

        //Select utilizado para realizar el conteo del numero de registros devueltos por la consulta.
        $selectCount = 'SELECT COUNT(*) AS rowNum';

        //Select General con los campos necesarios para la vista
        $select = " SELECT C.Reflector, C.Item, C.QOH, C.[30DaysSoldRFQ], C.[60DaysSoldRFQ], C.[90DaysSoldRFQ], C.[60DayShortOver],
                    C.BackOrders, C.SuggestOrder ";

        $from =  "FROM (
                    SELECT B.Reflector, B.Item, B.QOH, B.[30DaysSoldRFQ], B.[60DaysSoldRFQ], B.[90DaysSoldRFQ], B.[60DayShortOver], 
                        ISNULL(SUM(Cast(POD.[QtyBackOrdered] AS INT)),0) AS 'BackOrders'
                        ,(CASE WHEN ISNULL(SUM(Cast(POD.[QtyBackOrdered] AS INT)),0)+(IsNull(Cast(B.QOH as int),0)-(B.[60DaysSold])) < 0 THEN
                            ISNULL(SUM(Cast(POD.[QtyBackOrdered] AS INT)),0)+(IsNull(Cast(B.QOH as int),0)-(B.[60DaysSold])) ELSE 0 END) AS 'SuggestOrder'      
                    FROM (
                        SELECT A.[Reflector], A.Item, A.QOH, SUM(FPGLCSR2.[30DaysSold]) AS '30DaysSoldRFQ', SUM(FPGLCSR2.[60DaysSold]) AS '60DaysSoldRFQ', SUM(FPGLCSR2.[90DaysSold]) AS '90DaysSoldRFQ',
                            (IsNull(Cast(A.QOH as int),0)-(SUM(FPGLCSR2.[60DaysSold]))) AS '60DayShortOver', SUM(FPGLCSR2.[60DaysSold]) AS [60DaysSold]
                        FROM (
                            SELECT FPGLCSR.[Reflector], PC.[Name] AS 'Item', IsNull(Cast(GS.[GlobalStock] as int),0) AS 'QOH'
                            FROM [Inventory].[dbo].[FP-Generic-LampComponent-Sales-Report] FPGLCSR
                            LEFT OUTER JOIN [Inventory].[dbo].[Global_Stocks] GS 
                            ON FPGLCSR.[Reflector] = GS.ProductCatalogId
                            LEFT OUTER JOIN [Inventory].[dbo].[ProductCatalog] PC 
                            ON FPGLCSR.[Reflector] = PC.[ID]
                            WHERE 1=1 and FPGLCSR.[Reflector] IS NOT NULL AND FPGLCSR.[Reflector] != '0' 
                            GROUP BY FPGLCSR.[Reflector], PC.[Name], IsNull(Cast(GS.[GlobalStock] as int),0)
                        ) A
                        LEFT OUTER JOIN [Inventory].[dbo].[FP-Generic-LampComponent-Sales-Report] FPGLCSR2 
                        ON FPGLCSR2.[Reflector] = A.[Reflector]
                        GROUP BY A.[Reflector], A.Item, A.QOH
                    ) B
                    LEFT OUTER JOIN [Inventory].[dbo].[PurchaseOrderData] POD 
                    ON Cast(POD.SKU AS int) = B.[Reflector]
                    WHERE B.[Reflector] IS NOT NULL AND B.[Reflector] != '0'
                    GROUP BY B.Reflector, B.Item, B.QOH, B.[30DaysSoldRFQ], B.[60DaysSoldRFQ], B.[90DaysSoldRFQ], B.[60DayShortOver], B.[60DaysSold]
                ) C";
        
        $where ='';
        $wherefields = array('C.[Reflector]','C.[Item]');

        //Obtenemos todos los nombres de campos de la tabla productCatalog
      //  $fields = $this->MCommon->getAllfields($table);
        //creamos el where concatenando todos los nombres de campos y las palabras de busqueda
        $where .= $this->MCommon->concatAllWerefields($wherefields, $search);

        $orderby = " ORDER BY $sidx $sord ";

        $SQL = "{$select}{$from}{$where}{$orderby}";

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
            $responce->rows[$i]['id'] = $row['Reflector'];
            $responce->rows[$i]['cell'] = array($row['Reflector'],
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