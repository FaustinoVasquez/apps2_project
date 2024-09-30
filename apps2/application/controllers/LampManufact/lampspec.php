<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Lampspec extends BP_Controller {

    public function __construct() {
        parent::__construct();

        $is_logged_in = $this->session->userdata('is_logged_in');

        if (!isset($is_logged_in) || $is_logged_in != true) {
            redirect(base_url(), 'refresh');
        }

        if ($this->MUsers->isValidUser($this->session->userdata('userid'), 883500) != 1) {// 881100 prodcat Access
            redirect('Catalog/prodcat', 'refresh');
        }
    }

    function index() {  

        $this->title = "MI Technologiesinc - Lamp Specifications";

        $this->description = "Lamp Specifications";

        $this->css = array('form.css', 'fluid.css', 'table.css', 'jqueryui/redmond/jquery-ui-1.8.21.custom.css', 'jqgrid/ui.jqgrid.css', 'menu.css',);

        // Define custom javascript
        $this->javascript = array('jqueryui/ui/jquery-ui-1.8.21.custom.js', 'jqgrid/i18n/grid.locale-en.js', 'jqgrid/jquery.jqGrid.min.js', 'jqgrid/jquery.jqGrid.fluid.js','site.js');

        $this->load->library('Layout');
        $menu = new Layout;

        $data = array(
            'menu' => $menu->show_menu($this->MUsers->getUserFullName($this->session->userdata('userid'))),
            'from' => '/LampManufact/lampspec/',
            'nameGrid' => 'list',
            'namePager' => 'Pager',
            'caption' => 'Lamp Specifications',
            'export' => 'lampsepc',
            'sort' => 'desc',
            'baseUrl' => base_url(),
        );

        $data['colNames'] = "['SKU','ReflectorType','BrSpecMin','BrSpecMax','VoltageSpecMin','VoltageSpecMax','ReflectorName','ReflectorSize','Sphere','BallastWattage','BallastWattMin','BallastWattMax','DLPType','DLPTypeSlope','DLPTypeMagnification','CompensationValue','FocalPointDistance']";

        $data['colModel'] = "[
                {name:'SKU',index:'SKU', width:60, align:'center', formatter:linksku},
                {name:'ReflectorType',index:'ReflectorType', width:60, align:'center' },
                {name:'BrSpecMin',index:'BrSpecMin', width:60, align:'center'},  
                {name:'BrSpecMax',index:'BrSpecMax', width:60, align:'center'},  
                {name:'VoltageSpecMin',index:'VoltageSpecMin', width:60, align:'center'},
                {name:'VoltageSpecMax',index:'VoltageSpecMax', width:60, align:'center'}, 
                {name:'ReflectorName',index:'ReflectorName', width:60, align:'center'}, 
                {name:'ReflectorSize',index:'ReflectorSize', width:60, align:'center'}, 
                {name:'Sphere',index:'Sphere', width:60, align:'center'}, 
                {name:'BallastWattage',index:'BallastWattage', width:60, align:'center'}, 
                {name:'BallastWattMin',index:'BallastWattMin', width:60, align:'center'}, 
                {name:'BallastWattMax',index:'BallastWattMax', width:60, align:'center'}, 
                {name:'DLPType',index:'DLPType', width:60, align:'center'},  
                {name:'DLPTypeSlope',index:'DLPTypeSlope', width:60, align:'center'}, 
                {name:'DLPTypeMagnification',index:'DLPTypeMagnification', width:70, align:'center'}, 
                {name:'CompensationValue',index:'CompensationValue', width:70, align:'center'},
                {name:'FocalPointDistance',index:'FocalPointDistance', width:70, align:'center'},  
          	]";

       
        $this->build_content($data);
        $this->render_page();
    }

    function getData() {

        $page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1; // get the requested page
        $limit = isset($_REQUEST['rows']) ? $_REQUEST['rows'] : 10; // get how many rows we want to have into the gri
        $sidx = isset($_REQUEST['sidx']) ? $_REQUEST['sidx'] : 'ID'; // get index row - i.e. user click to sort
        $sord = isset($_REQUEST['sord']) ? $_REQUEST['sord'] : 'asc';
        
        $search = $this->input->get('ds');
        
        $where = '';
        $wherefields = array('[SKU]','[ReflectorType]','[BrSpecMin]','[BrSpecMax]','[ReflectorName]','[ReflectorSize]');

        $where .= $this->MCommon->concatAllWerefields($wherefields, $search);


        $SQL = "SELECT [SKU]
                      ,[ReflectorType]
                      ,[BrSpecMin]
                      ,[BrSpecMax]
                      ,[VoltageSpecMin]
                      ,[VoltageSpecMax]
                      ,[ReflectorName]
                      ,[ReflectorSize]
                      ,[Sphere]
                      ,[BallastWattage]
                      ,[BallastWattMin]
                      ,[BallastWattMax]
                      ,[DLPType]
                      ,[DLPTypeSlope]
                      ,[DLPTypeMagnification]
                      ,[CompensationValue]
                      ,[FocalPointDistance]
                  FROM [Inventory].[dbo].[LampProductionSpecs]
                  $where ";


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
            $responce->rows[$i]['ID'] = $row['SKU'];
            $responce->rows[$i]['cell'] = array($row['SKU'],
                $row['ReflectorType'],
                $row['BrSpecMin'],
                $row['BrSpecMax'],
                $row['VoltageSpecMin'],
                $row['VoltageSpecMax'],
                $row['ReflectorName'],
                $row['ReflectorSize'],
                $row['Sphere'],
                $row['BallastWattage'],
                $row['BallastWattMin'],
                $row['BallastWattMax'],
                $row['DLPType'],
                $row['DLPTypeSlope'],
                $row['DLPTypeMagnification'],
                $row['CompensationValue'],
                $row['FocalPointDistance'],
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
