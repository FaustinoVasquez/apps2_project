<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class PhilipsFPBunByPN extends BP_Controller {

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

        $this->title = "MI Technologiesinc - Philips FP Buildability by PN";

        $this->description = "Philips FP Buildability by PN";

        $this->css = array('form.css', 'fluid.css', 'table.css', 'jqueryui/redmond/jquery-ui-1.8.21.custom.css', 'jqgrid/ui.jqgrid.css', 'menu.css',);

        // Define custom javascript
        $this->javascript = array('jqueryui/ui/jquery-ui-1.8.21.custom.js', 'jqgrid/i18n/grid.locale-en.js', 'jqgrid/jquery.jqGrid.min.js', 'jqgrid/jquery.jqGrid.fluid.js', 'popup.js');

        $this->load->library('Layout');
        $menu = new Layout;

        $data = array(
            'menu' => $menu->show_menu($this->MUsers->getUserFullName($this->session->userdata('userid'))),
            'from' => '/Reports/philipsfpbunbypn/',
            'nameGrid' => 'list',
            'namePager' => 'Pager',
            'caption' => 'Philips FP Buildability by PN',
            'export' => 'philipsfpbunbypn',
            'subgrid' => 'true',
            'sort' => 'desc',
        );

        $data['colNames'] = "['Brand','PartNumber','PartNumberV2','PartNumberV3','ModuleSKU','ModulePrebuilt','ModuleBuildability','BulbSKU','BulbQOH','KitSKU','KitQOH']";

        $data['colModel'] = "[
                {name:'Brand',index:'Brand', width:80, align:'center'},
                {name:'PartNumber',index:'PartNumber', width:80, align:'center'},
                {name:'PartNumberV2',index:'PartNumberV2', width:80, align:'center'},
                {name:'PartNumberV3',index:'PartNumberV3', width:80, align:'center'},
                {name:'ModuleSKU',index:'ModuleSKU', width:80, align:'center' , sorttype:'int'},
                {name:'ModulePrebuilt',index:'ModulePrebuilt', width:80, align:'center' , sorttype:'int'},
                {name:'ModuleBuildability',index:'ModuleBuildability', width:80, align:'center' , sorttype:'int'},
                {name:'BulbSKU',index:'BulbSKU', width:80, align:'center' , sorttype:'int'},
                {name:'BulbQOH',index:'BulbQOH', width:80, align:'center' , sorttype:'int'},
                {name:'KitSKU',index:'KitSKU', width:80, align:'center' , sorttype:'int'},
                {name:'KitQOH',index:'KitQOH', width:80, align:'center' , sorttype:'int'},
    
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
        $select = "SELECT Brand
                          ,PartNumber
                          ,PartNumberV2
                          ,PartNumberV3
                          ,ModuleSKU
                          ,ModulePrebuilt
                          ,ModuleBuildability
                          ,BulbSKU
                          ,BulbQOH
                          ,KitSKU
                          ,KitQOH ";

        $selectSlice = "SELECT Brand
                          ,PartNumber
                          ,PartNumberV2
                          ,PartNumberV3
                          ,ModuleSKU
                          ,ModulePrebuilt
                          ,ModuleBuildability
                          ,BulbSKU
                          ,BulbQOH
                          ,KitSKU
                          ,KitQOH";
        $from = " FROM (
                        SELECT Brand
                              ,PartNumber
                              ,PartNumberV2
                              ,PartNumberV3
                              ,ModuleSKU
                              ,ISNULL(ModulePrebuilt,0) AS ModulePrebuilt
                              ,ISNULL(ModuleBuildability,0) AS ModuleBuildability 
                              ,BulbSKU
                              ,ISNULL(BulbQOH,0) AS BulbQOH
                              ,KitSKU
                              ,ISNULL(KitQOH,0) AS KitQOH
                        FROM [Inventory].[dbo].[FP-Philips-SKU-NEW-Step2]
                    ) A ";

        $where = '';
        $wherefields = array('Brand','PartNumber','PartNumberV2','PartNumberV3','ModuleSKU','ModulePrebuilt','ModuleBuildability','BulbSKU','BulbQOH','KitSKU','KitQOH');

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
            $responce->rows[$i]['ID'] = $row['Brand'];
            $responce->rows[$i]['cell'] = array($row['Brand'],
                $row['PartNumber'] = utf8_encode($row['PartNumber']),
                $row['PartNumberV2'] = utf8_encode($row['PartNumberV2']),
                $row['PartNumberV3'] = utf8_encode($row['PartNumberV3']),
                $row['ModuleSKU'] = utf8_encode($row['ModuleSKU']),
                $row['ModulePrebuilt'] = utf8_encode($row['ModulePrebuilt']),
               	$row['ModuleBuildability'] = utf8_encode($row['ModuleBuildability']),
                $row['BulbSKU'] = utf8_encode($row['BulbSKU']),
		        $row['BulbQOH'] = utf8_encode($row['BulbQOH']),
                $row['KitSKU'] = utf8_encode($row['KitSKU']),
		        $row['KitQOH'] = utf8_encode($row['KitQOH']),
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
