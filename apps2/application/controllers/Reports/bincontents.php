<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Bincontents extends BP_Controller {

    public function __construct() {
        parent::__construct();

        $is_logged_in = $this->session->userdata('is_logged_in');

        if (!isset($is_logged_in) || $is_logged_in != true) {
            redirect(base_url(), 'refresh');
        }
        
        // if ($this->MUsers->isValidUser($this->session->userdata('userid'), 883850) != 1) {
        //        redirect('Catalog/prodcat', 'refresh');
        //    }
    }

    public function getbin($search = null) {
        //Cargamos la base de datos de OrderManager
        $this->load->model('MOrders', '', TRUE);

        $mytitle = "Bin Contents";

        // Define Meta
        $this->title = "MI Technologiesinc - ".$mytitle;

        $this->description = $mytitle;

        // Define custom CSS
        $this->css = array('menu.css', 'fluid.css', 'jqueryui/redmond/jquery-ui-1.8.21.custom.css', 'jqgrid/ui.jqgrid.css', 'form.css');

        // Define custom javascript
        $this->javascript = array('jqueryui/ui/jquery-ui-1.8.21.custom.js', 'jqgrid/i18n/grid.locale-en.js', 'jqgrid/jquery.jqGrid.min.js', 'jqgrid/jquery.jqGrid.fluid.js', 'popup.js');
        //Cargamos la libreria donde esta el menu
        $this->load->library('Layout');
        //Creamos un nuevo layout->menu
        $menu = new Layout;

        $data = array(
            'menu' => $menu->show_menu($this->MUsers->getUserFullName($this->session->userdata('userid'))),
            'from' => '/Reports/bincontents/',
            'caption' => $mytitle,
            'search' => $search,
            'datefrom' => $this->MCommon->lastweek(),
            'dateto' => date("m/d/Y"),    
            'bins' => $this->MCatalog->filterBins(),
        );

      
        //Cargamos la libreria comumn
        $this->load->library('common');

        //Cargamos al arreglo los colnames
        $data['colNames'] = "['BinID','SKU', 'Qty','LastModified','LastUser']";
        //Cargamos al arreglo los colmodels


        $this->common->addModel('BinID]')->addWidth(60)->addAlign('center')->addNlCr()
                     ->addModel('SKU')->addWidth(70)->addAlign('center')->addNlCr()
                     ->addModel('Qty')->addWidth(80)->addAlign('center')->addNlCr()
                     ->addModel('LastModified')->addWidth(80)->addAlign('left')->addNlCr()
                     ->addModel('LastUser')->addWidth(80)->addAlign('center')->addNlCr();
    
        $data['colModel'] = $this->common->getModel();

        //Generar el contenido....
       $this->build_content($data);
       $this->render_page('reports/');
    }

    function getData() {
        $page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1; // get the requested page
        $limit = isset($_REQUEST['rows']) ? $_REQUEST['rows'] : 10; // get how many rows we want to have into the gri
        $sidx = isset($_REQUEST['sidx']) ? $_REQUEST['sidx'] : 'BC.[Bin_Id]'; // get index row - i.e. user click to sort
        $sord = isset($_REQUEST['sord']) ? $_REQUEST['sord'] : 'desc';


        $search = $this->input->get('search');



        $table = ' [Inventory].[dbo].[Bin_Content] as BC ';
        $where = '';
        $from = ' FROM ';
	
	

       $select = "SELECT   BC.[Bin_Id] as BinID
                          ,BC.[ProductCatalog_Id] as SKU
                          ,BC.[Counter] as 'Qty'
                          ,BC.[LastStamp] as LastModified
                          ,Inventory.dbo.fn_GetUserName(LastUser_Id)  as 'LastUser'
                  ";

	
        $selectSlice = "SELECT   BinID
                                ,SKU
                                ,Qty
                                ,LastModified
                                ,LastUser";

        $wherefields = array('BC.[Bin_Id]', 'BC.[ProductCatalog_Id]', 'Inventory.dbo.fn_GetUserName(LastUser_Id)');

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

        $SQL = "WITH mytable AS ({$select}, ROW_NUMBER() OVER (ORDER BY {$sidx} {$sord}) AS RowNumber
                                FROM {$table}{$where})
               {$selectSlice}, RowNumber
               FROM mytable
                WHERE RowNumber BETWEEN {$start} AND {$finish} ";
	

       $result = $this->MCommon->getSomeRecords($SQL);

        $responce = new stdClass();
        $responce->page = $page;
        $responce->total = $total_pages;
        $responce->records = $count;
        $i = 0;

        foreach ($result as $row) {
            $responce->rows[$i]['id'] = $row['BinID'];
            $responce->rows[$i]['cell'] = array($row['BinID'],
                $row['SKU'],
                $row['Qty'],
                $row['LastModified'],
                $row['LastUser'],
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

    function updatePos(){

        $pos = $this->MCatalog->getPos($this->MCommon->lastweek(),date("m/d/Y"));

        return pos;

    }
}
?>




