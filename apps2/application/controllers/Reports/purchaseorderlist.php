<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class PurchaseOrderList extends BP_Controller {

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

    function index($sku) {

        $this->title = "MI Technologiesinc - Purchase Order List";
        $this->description = "PurchaseOrderList";
        $this->css = array('form.css', 'fluid.css', 'table.css', 'jqueryui/redmond/jquery-ui-1.8.21.custom.css', 'jqgrid/ui.jqgrid.css', 'menu.css',);

        // Define custom javascript
        $this->javascript = array('jqueryui/ui/jquery-ui-1.8.21.custom.js', 'jqgrid/i18n/grid.locale-en.js', 'jqgrid/jquery.jqGrid.min.js',
        'jqgrid/jquery.jqGrid.fluid.js', 'site.js');

        $this->load->library('Layout');
        $menu = new Layout;



        $data = array(
            'menu' => $menu->show_menu($this->MUsers->getUserFullName($this->session->userdata('userid'))),
            'from' => '/Reports/purchaseorderlist',
            'caption' => 'Purchase Order List',
            'export' => 'PurchaseOrderList',
            'sort' => 'desc',
            'baseUrl' => base_url(),
            'selectData' => Array(0 => "All", 1 => "Has BackOrder", 2 => "Fully Received"),
            'sku' => $this->uri->segment(3),

//            'sq' => $_GET['q'],
//            'pon' => $_GET['p'],
//            'opc' => $_GET['t'],
        );

        $data['colNames'] = "['PONumber','PODate','SKU','Name','VendorName','ETA','QtyOrdered','QtyReceived','QtyBackOrdered','UnitCost']";

        $data['colModel'] = "[
                {name:'PONumber',index:'PONumber', width:60, align:'center', sorttype: 'int'},
                {name:'PODate',index:'PODate', width:60, align:'center', sorttype: 'date'},
                {name:'SKU',index:'SKU', width:60, align:'center', sorttype: 'int'},
                {name:'Name',index:'Name', width:250, align:'left', sorttype: 'text'},
                {name:'VendorName',index:'VendorName', width:80, align:'center', sorttype: 'text'},
                {name:'ETA',index:'ETA', width:80, align:'center', sorttype: 'text'},
                {name:'QtyOrdered',index:'QtyOrdered', width:60, align:'center', sorttype: 'int'},
                {name:'QtyReceived',index:'QtyReceived', width:60, align:'center', sorttype: 'int'},
                {name:'QtyBackOrdered',index:'QtyBackOrdered', width:60, align:'center', sorttype: 'int'},
                {name:'UnitCost',index:'UnitCost', width:60, align:'center', sorttype: 'text'},
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
        $option = $this->input->get('op');

        $sku = $this->input->get('sku');

        

        //Select utilizado para realizar el conteo del numero de registros devueltos por la consulta.
        $selectCount = 'SELECT COUNT(*) AS rowNum';

        //Select General con los campos necesarios para la vista
        $select = "SELECT A.[PONumber]
                          ,A.[PODate]
                          ,A.[SKU]
                          ,A.[Name]
                          ,A.[VendorName]
                          ,A.[ETA]
                          ,A.[QtyOrdered]
                          ,A.[QtyReceived]
                          ,A.[QtyBackOrdered]
                          ,A.[UnitCost] ";

        $selectSlice = "SELECT PONumber
                          ,PODate
                          ,SKU
                          ,Name
                          ,VendorName
                          ,ETA
                          ,QtyOrdered
                          ,QtyReceived
                          ,QtyBackOrdered
                          ,UnitCost ";
        $from = " FROM (
                        SELECT POD.PONumber
                            ,POD.PODate
                            ,POD.SKU
                            ,PC.Name
                            ,POD.VendorName
                            ,POD.ETA
                            ,CAST(ISNULL(CASE WHEN ISNULL(POD.QtyOrdered,'') <> '' THEN POD.QtyOrdered ELSE '0' END,0) AS DECIMAL(18)) AS QtyOrdered
                            ,CAST(ISNULL(CASE WHEN ISNULL(POD.QtyReceived,'') <> '' THEN POD.QtyReceived ELSE '0' END,0) AS DECIMAL(18)) AS QtyReceived
                            ,CAST(ISNULL(CASE WHEN ISNULL(POD.QtyBackOrdered,'') <> '' THEN POD.QtyBackOrdered ELSE '0' END,0) AS DECIMAL(18)) AS QtyBackOrdered
                            ,CAST(ISNULL(CASE WHEN ISNULL(POD.UnitCost,'') <> '' THEN POD.UnitCost ELSE '0' END,0) AS DECIMAL(18,5)) AS UnitCost
                        FROM [Inventory].[dbo].[PurchaseOrderData] AS POD 
                        LEFT OUTER JOIN [Inventory].[dbo].[ProductCatalog] AS PC ON (POD.[SKU] = CAST(PC.[ID] AS NVARCHAR(20)))
                    ) A ";
        $where =' ';
        $wherefields = array('A.[PONumber]','A.[SKU]','A.[VendorName]','A.[Name]');

        //Obtenemos todos los nombres de campos de la tabla productCatalog
        //$fields = $this->MCommon->getAllfields($table);
        //creamos el where concatenando todos los nombres de campos y las palabras de busqueda
        $where .= $this->MCommon->concatAllWerefields($wherefields, $search);

        $opc = $option;
        switch ($opc) 
        {
            case '0':
                $where .= " and A.[QtyBackOrdered] > 0";
            break;

            case '1':
                $where .= " and A.[QtyBackOrdered] > 0";
                if($search <= 0)
                {   $where .= " and A.[SKU] = $sku ";   }
            break;

            case '2':
                $where .= " and A.[QtyBackOrdered] = 0";
            break;
        }

        $SQL = "{$selectCount}{$from}{$where}";

    //   echo $SQL;

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
                    {$from}{$where})
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
            $responce->rows[$i]['cell'] = array($row['PONumber'], 
                $row['PODate'], 
                $row['SKU'], 
                $row['Name'] = utf8_encode($row['Name']), 
                $row['VendorName'] = utf8_encode($row['VendorName']), 
                $row['ETA'],
                $row['QtyOrdered'], 
                $row['QtyReceived'], 
                $row['QtyBackOrdered'], 
                '$ '.number_format($row['UnitCost'],2),
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
