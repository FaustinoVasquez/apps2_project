<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class BackorderReport extends BP_Controller {

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

        $this->title = "MI Technologiesinc - Backorder Report";
        $this->description = "Backorder Report";
        $this->css = array('form.css', 'fluid.css', 'table.css', 'jqueryui/redmond/jquery-ui-1.8.21.custom.css', 'jqgrid/ui.jqgrid.css', 'menu.css',);

        // Define custom javascript
        $this->javascript = array('jqueryui/ui/jquery-ui-1.8.21.custom.js', 'jqgrid/i18n/grid.locale-en.js', 'jqgrid/jquery.jqGrid.min.js', 'jqgrid/jquery.jqGrid.fluid.js', 'site.js');

        $this->load->library('Layout');
        $menu = new Layout;

        $data = array( 
            'menu' => $menu->show_menu($this->MUsers->getUserFullName($this->session->userdata('userid'))),
            'from' => '/Reports/backorderreport',
            'caption' => 'Backorder Report',
            'export' => 'BackorderReport',
            'sort' => 'desc',
            'sq' => $_GET['q'],
            'baseUrl' => base_url(),
        );

        $data['colNames'] = "['PONumber','PODate','SKU','Name','VendorName','QtyOrdered','QtyReceived','QtyBackOrdered','UnitCost']";

        $data['colModel'] = "[
                {name:'PONumber',index:'PONumber', width:60, align:'center'},
                {name:'PODate',index:'PODate', width:60, align:'center'},  
                {name:'SKU',index:'SKU', width:60, align:'center'}, 
                {name:'Name',index:'Name', width:250, align:'left'}, 
                {name:'VendorName',index:'VendorName', width:80, align:'center'}, 
                {name:'QtyOrdered',index:'QtyOrdered', width:60, align:'center'},
                {name:'QtyReceived',index:'QtyReceived', width:60, align:'center'},  
                {name:'QtyBackOrdered',index:'QtyBackOrdered', width:60, align:'center'}, 
                {name:'UnitCost',index:'UnitCost', width:60, align:'center'},  
            ]";
            
        $this->build_content($data);
        $this->render_page();
    }

    function getData() {

        $page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1; // get the requested page
        $limit = isset($_REQUEST['rows']) ? $_REQUEST['rows'] : 10; // get how many rows we want to have into the gri
        $sidx = isset($_REQUEST['sidx']) ? $_REQUEST['sidx'] : 'PONumber'; // get index row - i.e. user click to sort
        $sord = isset($_REQUEST['sord']) ? $_REQUEST['sord'] : 'asc';
        
        $search = $this->input->get('ds');
        $sq = $this->input->get('sq');

        //Select utilizado para realizar el conteo del numero de registros devueltos por la consulta.
        $selectCount = 'SELECT COUNT(*) AS rowNum';

        //Select General con los campos necesarios para la vista
        $select = 'SELECT POD.[PONumber]
                          ,POD.[PODate]
                          ,POD.[SKU]
                          ,PC.[Name]
                          ,POD.[VendorName]
                          ,POD.[QtyOrdered]
                          ,POD.[QtyReceived]
                          ,POD.[QtyBackOrdered]
                          ,POD.[UnitCost]';

        $selectSlice = 'SELECT [PONumber]
                          ,[PODate]
                          ,[SKU]
                          ,[Name]
                          ,[VendorName]
                          ,[QtyOrdered]
                          ,[QtyReceived]
                          ,[QtyBackOrdered]
                          ,[UnitCost]';
        $from = ' FROM ';
        $table = ' [Inventory].[dbo].[PurchaseOrderData] AS POD ';
        $join = ' LEFT OUTER JOIN [Inventory].[dbo].[ProductCatalog] AS PC ON (POD.[SKU] = CAST(PC.[ID] AS NVARCHAR(20))) ';
        $where ='';
        $wherefields = array('POD.[PONumber]','POD.[SKU]','POD.[VendorName]','PC.[Name]');

        //Obtenemos todos los nombres de campos de la tabla productCatalog
        //$fields = $this->MCommon->getAllfields($table);
        //creamos el where concatenando todos los nombres de campos y las palabras de busqueda
        $where .= $this->MCommon->concatAllWerefields($wherefields, $search);

        switch ($option) 
        {
            case '1':
                $where .= " and POD.[QtyBackOrdered] > 0";
                break;
            case '2':
                $where .= " and POD.[QtyBackOrdered] = 0";
                break;
        }

        $SQL = "{$selectCount}{$from}{$table}{$join}{$where}";
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

        $SQL = "WITH mytable AS ($select , ROW_NUMBER() OVER (ORDER BY [PONumber] {$sord}) AS RowNumber
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
            $responce->rows[$i]['cell'] = array($row['PONumber'],
                $row['PODate'],
                $row['SKU'], 
                $row['VendorName'] = utf8_encode($row['VendorName']),
                $row['DeliveryDate'],
                $row['QtyOrdered'],
                $row['QtyReceived'],
                $row['QtyBackOrdered'], 
                $row['UnitCost'], 
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