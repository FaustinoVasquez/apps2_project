<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Fphousing extends BP_Controller {

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

        $this->title = "MI Technologiesinc - Supplier Cost";

        $this->description = "Supplier Cost";

        $this->css = array('form.css', 'fluid.css', 'table.css', 'jqueryui/redmond/jquery-ui-1.8.21.custom.css', 'jqgrid/ui.jqgrid.css', 'menu.css',);

        // Define custom javascript
        $this->javascript = array('jqueryui/ui/jquery-ui-1.8.21.custom.js', 'jqgrid/i18n/grid.locale-en.js', 'jqgrid/jquery.jqGrid.min.js', 'jqgrid/jquery.jqGrid.fluid.js', 'popup.js');

        $this->load->library('Layout');
        $menu = new Layout;

        $data = array(
            'menu' => $menu->show_menu($this->MUsers->getUserFullName($this->session->userdata('userid'))),
            'from' => '/Reports/fphousing/',
            'nameGrid' => 'fphousing',
            'namePager' => 'fphousingPager',
            'caption' => 'FP Housing ',
            'export' => 'fphousing',
            'subgrid' => 'true',
            'sort' => 'desc',
            'search' => '',
            'showskudata' => '/Catalog/prodcat/showSkuData/',
            'customerid' => $this->MUsers->getCustomerId($this->session->userdata('userid')),
        );

        $data['colNames'] = "['MITSKU','QOH','MoldGateSKU','MoldGateCost','GrandBulbSKU','GrandBulbCost','MISKU','MICost','SouthernTechSKU','SouthernTechCost','KWSKU','KWCost',
                              'LeaderSKU','LeaderCost','YitaSKU','YitaCost','FYVSKU','FYVCost','LampsChoiceSKU','LampsChoiceCost']";

        $data['colModel'] = "[
                {name:'MITSKU',index:'MITSKU', width:80, align:'center'},
                {name:'QOH',index:'QOH', width:80, align:'center'},
                {name:'MoldGateSKU',index:'MoldGateSKU', width:80, align:'center'},
                {name:'MoldGateCost',index:'MoldGateCost', width:80, align:'center'},
                {name:'GrandBulbSKU',index:'GrandBulbSKU', width:80, align:'center'},
                {name:'GrandBulbCost',index:'GrandBulbCost', width:80, align:'center'},
                {name:'MISKU',index:'MISKU', width:80, align:'center'},
                {name:'MICost',index:'MICost', width:80, align:'center'},
                {name:'SouthernTechSKU',index:'SouthernTechSKU', width:80, align:'center'},
                {name:'SouthernTechCost',index:'SouthernTechCost', width:80, align:'center'},
                {name:'KWSKU',index:'KWSKU', width:80, align:'center'},
                {name:'KWCost',index:'KWCost', width:80, align:'center'},
                {name:'LeaderSKU',index:'LeaderSKU', width:80, align:'center'},
                {name:'LeaderCost',index:'LeaderCost', width:80, align:'center'},
                {name:'YitaSKU',index:'YitaSKU', width:80, align:'center'},
                {name:'YitaCost',index:'YitaCost', width:80, align:'center'},
                {name:'FYVSKU',index:'FYVSKU', width:80, align:'center'},
                {name:'FYVCost',index:'FYVCost', width:80, align:'center'},
                {name:'LampsChoiceSKU',index:'LampsChoiceSKU', width:80, align:'center'},
                {name:'LampsChoiceCost',index:'LampsChoiceCost', width:80, align:'center'},
          	]";

        //Cargamos la libreria comumn
        $this->load->library('common');

        //Reemplazamos los campos del formulario por los que estan en el arreglo $_POST.
        $data = $this->common->fillPost('ALL', $data);

        //cadena de busqueda del grid 
        $data['gridSearch'] = 'data?ds=' . $data['search'];


        $this->build_content($data);
        $this->render_page();
    }

    function data() {

        $page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1; // get the requested page
        $limit = isset($_REQUEST['rows']) ? $_REQUEST['rows'] : 10; // get how many rows we want to have into the gri
        $sidx = isset($_REQUEST['sidx']) ? $_REQUEST['sidx'] : 'MITSKU'; // get index row - i.e. user click to sort
        $sord = isset($_REQUEST['sord']) ? $_REQUEST['sord'] : 'asc';
        $search = !empty($_GET['ds']) ? $_REQUEST['ds'] : '';

        $table = '[Inventory].[dbo].[FP-Housing-Vendor-CostInfo] ';
        $where = '';
        $from = ' FROM ';


        $select = " SELECT * ";

        $wherefields = array('MITSKU','MoldGateSKU','GrandBulbSKU','MISKU' ,'SouthernTechSKU','KWSKU','LeaderSKU','FYVSKU' );

        $where .=$this->MCommon->concatAllWerefields($wherefields, $search);


        $SQL = "{$select}{$from}{$table}{$where}";


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


        $SQL = "WITH mytable AS ($select , ROW_NUMBER() OVER (ORDER BY [$sidx] $sord) AS RowNumber
                                FROM {$table}{$where})
               {$select}, RowNumber
               FROM mytable
                WHERE RowNumber BETWEEN {$start} AND {$finish}";

        $result = $this->MCommon->getSomeRecords($SQL);

        $responce = new stdClass();
        $responce->page = $page;
        $responce->total = $total_pages;
        $responce->records = $count;

        $i = 0;
        foreach ($result as $row) {
            $responce->rows[$i]['ID'] = $row['MITSKU'];
            $responce->rows[$i]['cell'] = array($row['MITSKU'],
                $row['QOH'] = utf8_encode($row['QOH']),
                $row['MoldGateSKU'] = utf8_encode($row['MoldGateSKU']),
                $row['MoldGateCost'] = utf8_encode($row['MoldGateCost']),
                $row['GrandBulbSKU'] = utf8_encode($row['GrandBulbSKU']),
                $row['GrandBulbCost'] = utf8_encode($row['GrandBulbCost']),
               	$row['MISKU'] = utf8_encode($row['MISKU']),
                $row['MICost'] = utf8_encode($row['MICost']),
		        $row['SouthernTechSKU'] = utf8_encode($row['SouthernTechSKU']),
                $row['SouthernTechCost'] = utf8_encode($row['SouthernTechCost']),
		        $row['KWSKU'] = utf8_encode($row['KWSKU']),
                $row['KWCost'] = utf8_encode($row['KWCost']),
                $row['LeaderSKU'] = utf8_encode($row['LeaderSKU']),
                $row['LeaderCost'] = utf8_encode($row['LeaderCost']),
                $row['YitaSKU']= utf8_encode($row['YitaSKU']),
                $row['YitaCost']= utf8_encode($row['YitaCost']),
                $row['FYVSKU'] = utf8_encode($row['FYVSKU']),
                $row['FYVCost'] = utf8_encode($row['FYVCost']),
                $row['LampsChoiceSKU']= utf8_encode($row['LampsChoiceSKU']),
                $row['LampsChoiceCost']= utf8_encode($row['LampsChoiceCost']),
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
