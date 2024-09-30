<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Philipsfpbulbstock extends BP_Controller {

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

        $this->title = "MI Technologiesinc - Philips FP Bulb Stock";

        $this->description = "Philips FP Bulb Stock";

        $this->css = array('form.css', 'fluid.css', 'table.css', 'jqueryui/redmond/jquery-ui-1.8.21.custom.css', 'jqgrid/ui.jqgrid.css', 'menu.css',);

        // Define custom javascript
        $this->javascript = array('jqueryui/ui/jquery-ui-1.8.21.custom.js', 'jqgrid/i18n/grid.locale-en.js', 'jqgrid/jquery.jqGrid.min.js', 'jqgrid/jquery.jqGrid.fluid.js', 'popup.js');

        $this->load->library('Layout');
        $menu = new Layout;

        $data = array(
            'menu' => $menu->show_menu($this->MUsers->getUserFullName($this->session->userdata('userid'))),
            'from' => '/Reports/philipsfpbulbstock/',
            'nameGrid' => 'list',
            'namePager' => 'Pager',
            'caption' => 'Philips FP Bulb Stock',
            'export' => 'philipsfpbulbstock',
            'subgrid' => 'true',
            'sort' => 'desc',
        );

        $data['colNames'] = "['BulbSKU','BulbQOH','Cost']";

        $data['colModel'] = "[
                {name:'BulbSKU',index:'BulbSKU', width:80, align:'center' , sorttype:'int'},
                {name:'BulbQOH',index:'BulbQOH', width:80, align:'center' , sorttype:'int'},
                {name:'Cost',index:'Cost', width:80, align:'center' , sorttype:'int'},  
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

        $select = "SELECT A.BulbSKU
                          ,A.BulbQOH
                          ,A.Cost ";

        $selectSlice = "SELECT BulbSKU
                          ,BulbQOH
                          ,Cost ";

        $from = " FROM (
                        SELECT DISTINCT [BulbSKU]
                        ,ISNULL(BulbQOH,0) AS BulbQOH
                        ,ISNULL(PC.UnitCost,0) AS Cost

                        FROM [Inventory].[dbo].[FP-Philips-SKU-NEW-Step2]
                        LEFT OUTER JOIN [Inventory].[dbo].[ProductCatalog] AS PC on [BulbSKU] = PC.ID
                        GROUP BY [BulbSKU], ISNULL(BulbQOH,0), ISNULL(PC.UnitCost,0)
                    ) A ";

        $where = " ";
        $wherefields = array('A.BulbSKU','A.BulbQOH','A.Cost');

        $where .=$this->MCommon->concatAllWerefields($wherefields, $search);

        $SQL = "{$selectCount}{$from}{$where}";
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

        //print_r($SQL);
        $result = $this->MCommon->getSomeRecords($SQL);
        $responce = new stdClass();
        $responce->page = $page;
        $responce->total = $total_pages;
        $responce->records = $count;

        $i = 0;
        foreach ($result as $row) {
            $responce->rows[$i]['ID'] = $row['BulbSKU'];
            $responce->rows[$i]['cell'] = array($row['BulbSKU'],
                $row['BulbQOH'] = utf8_encode($row['BulbQOH']),
                '$ '.number_format($row['Cost'],2),
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
