<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class PartInventory extends BP_Controller {

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

        $this->title = "MI Technologiesinc - Part Inventory";
        $this->description = "Part Inventory";
        $this->css = array('form.css', 'fluid.css', 'table.css', 'jqueryui/redmond/jquery-ui-1.8.21.custom.css', 'jqgrid/ui.jqgrid.css', 'menu.css',);

        // Define custom javascript
        $this->javascript = array('jqueryui/ui/jquery-ui-1.8.21.custom.js', 'jqgrid/i18n/grid.locale-en.js', 'jqgrid/jquery.jqGrid.min.js', 'jqgrid/jquery.jqGrid.fluid.js', 'site.js');

        $this->load->library('Layout');
        $menu = new Layout;

        $data = array( 
            'menu' => $menu->show_menu($this->MUsers->getUserFullName($this->session->userdata('userid'))),
            'from' => '/Catalog/partinventory',
            'caption' => 'Part Inventory',
            'subgrid' => 'true',
            'export' => 'PartInventory',
            'sort' => 'desc',
            'category' => $this->MCatalog->fillCategoryParts(),
            'baseUrl' => base_url(),
        );

        $data['colNames'] = "['ITEMID','BOXID','MAKE','MODEL','SIZE','CHASSIS','SERIAL','PDPMODULE','SUBCATEGORY1'
                             ,'PARTDESC','REFERENCE1','REFERENCE2','REFERENCE3','REFERENCE4','REFERENCE5']";

        $data['colModel'] = "[
                {name:'ITEMID',index:'ITEMID', width:60, align:'center'},
                {name:'BOXID',index:'BOXID', width:80, align:'center'},  
                {name:'MAKE',index:'MAKE', width:90, align:'center'}, 
                {name:'MODEL',index:'MODEL', width:80, align:'center'}, 
                {name:'SIZE',index:'SIZE', width:50, align:'center'}, 
                {name:'CHASSIS',index:'CHASSIS', width:70, align:'center'}, 
                {name:'SERIAL',index:'SERIAL', width:85, align:'center'}, 
                {name:'PDPMODULE',index:'PDPMODULE', width:85, align:'center'}, 
                {name:'SUBCATEGORY1',index:'SUBCATEGORY1', width:110, align:'center'}, 
                {name:'PARTDESC',index:'PARTDESC', width:200, align:'center'}, 
                {name:'REFERENCE1',index:'REFERENCE1', width:180, align:'center'}, 
                {name:'REFERENCE2',index:'REFERENCE2', width:180, align:'center'}, 
                {name:'REFERENCE3',index:'REFERENCE3', width:180, align:'center'}, 
                {name:'REFERENCE4',index:'REFERENCE4', width:180, align:'center'}, 
                {name:'REFERENCE5',index:'REFERENCE5', width:180, align:'center'},  

            ]";
            
        $this->build_content($data);
        $this->render_page();
    }

    function getData() {

        $page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1; // get the requested page
        $limit = isset($_REQUEST['rows']) ? $_REQUEST['rows'] : 10; // get how many rows we want to have into the gri
        $sidx = isset($_REQUEST['sidx']) ? $_REQUEST['sidx'] : 'ITEMID'; // get index row - i.e. user click to sort
        $sord = isset($_REQUEST['sord']) ? $_REQUEST['sord'] : 'asc';
        
        $search = $this->input->get('ds');
        $category = $this->input->get('ca');

        //Select utilizado para realizar el conteo del numero de registros devueltos por la consulta.
        $selectCount = 'SELECT COUNT(*) AS rowNum';

        //Select General con los campos necesarios para la vista
        $select = 'SELECT [ITEMID]
                         ,[BOXID]
                         ,[MAKE]
                         ,[MODEL]
                         ,[SIZE]
                         ,[CHASSIS]
                         ,[SERIAL]
                         ,[PDPMODULE]
                         ,[SUBCATEGORY1]
                         ,[PARTDESC]
                         ,[REFERENCE1]
                         ,[REFERENCE2]
                         ,[REFERENCE3]
                         ,[REFERENCE4]
                         ,[REFERENCE5]';

        $selectSlice = 'SELECT ITEMID
                         ,BOXID
                         ,MAKE
                         ,MODEL
                         ,SIZE
                         ,CHASSIS
                         ,SERIAL
                         ,PDPMODULE
                         ,SUBCATEGORY1
                         ,PARTDESC
                         ,REFERENCE1
                         ,REFERENCE2
                         ,REFERENCE3
                         ,REFERENCE4
                         ,REFERENCE5';


        $from = ' from';
        $table = ' [PartsData].[dbo].[PARTSINVENTORY] ';
        $where ='';
        $wherefields = array('ITEMID','BOXID','MAKE','MODEL','SIZE','CHASSIS','SERIAL','PDPMODULE'
                            ,'SUBCATEGORY1','PARTDESC','REFERENCE1','REFERENCE2','REFERENCE3','REFERENCE4','REFERENCE5');


        //Obtenemos todos los nombres de campos de la tabla productCatalog
        $fields = $this->MCommon->getAllfields($table);
        //creamos el where concatenando todos los nombres de campos y las palabras de busqueda
        $where .= $this->MCommon->concatAllWerefields($wherefields, $search);

        if ($category){
            $where .= " and ([CATEGORY] = '{$category}') ";
        }

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

        $SQL = "WITH mytable AS ($select , ROW_NUMBER() OVER (ORDER BY [ITEMID] {$sord}) AS RowNumber
                                FROM {$table}{$where})
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
            
            $responce->rows[$i]['ID'] = $row['ITEMID'];
            $responce->rows[$i]['cell'] = array($row['ITEMID'],
                $row['BOXID'],
                $row['MAKE'],
                $row['MODEL'],
                $row['SIZE'],
                $row['CHASSIS'],
                $row['SERIAL'],
                $row['PDPMODULE'],
                $row['SUBCATEGORY1'],
                $row['PARTDESC'],
                $row['REFERENCE1'],
                $row['REFERENCE2'],
                $row['REFERENCE3'],
                $row['REFERENCE4'],
                $row['REFERENCE5'],
            );
            $i++;
        }
        echo json_encode($responce);
    }


    function categoryData() {

        $feedId = isset($_REQUEST['on']) ? $_GET['on'] : '';
        $page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1; // get the requested page
        $limit = isset($_REQUEST['rows']) ? $_REQUEST['rows'] : 10; // get how many rows we want to have into the gri
        $sidx = isset($_REQUEST['sidx']) ? $_REQUEST['sidx'] : 'ID'; // get index row - i.e. user click to sort
        $sord = isset($_REQUEST['sord']) ? $_REQUEST['sord'] : 'Desc';


        $totalrows = isset($_REQUEST['totalrows']) ? $_REQUEST['totalrows'] : false;
        if ($totalrows) {
            $limit = $totalrows;
        }

        if (!$sidx)
            $sidx = 1;


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

        $responce = new stdClass();
        $responce->page = $page;
        $responce->total = $total_pages;
        $responce->records = $count;


        $i = 0;

        foreach ($result as $row) {
            $responce->rows[$i]['id'] = $row['Compatiblity'];
            $responce->rows[$i]['cell'] = array($row['Compatiblity'],
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

