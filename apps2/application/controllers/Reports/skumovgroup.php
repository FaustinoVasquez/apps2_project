<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Skumovgroup extends BP_Controller {

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

    public function index() {
        //Cargamos la base de datos de OrderManager
        $this->load->model('MOrders', '', TRUE);

        // Define Meta
        $this->title = "MI Technologiesinc - Bin Quantity By Group";

        $this->description = "Bin Quantity By Group";

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
            'from' => '/Reports/skumovgroup/',
            'caption' => 'Bin Quantity',
            'search' => '',
            'datefrom' => $this->MCommon->lastweek(),
            'dateto' => date("m/d/Y"),          
        );

        //Cargamos la libreria comumn
        $this->load->library('common');

        //Cargamos al arreglo los colnames
        $data['colNames'] = "['SKU','Name','Qty']";
        //Cargamos al arreglo los colmodels
 

        $obj = new Common();
        $data['colModel'] = $obj->addModel('SKU')->addWidth(100)->addAlign('center')->addNlCr()
                     ->addModel('Name')->addWidth(200)->addAlign('left')->addNlCr()
                     ->addModel('Qty')->addWidth(100)->addAlign('center')->addNlCr()
                     ->getModel();


        $data['subGridColNames'] = "['Stamp','Bin_Id','Location','Qty','FlowCaption','ScanCode','UserName']";

        $obj1 = new Common();
        $data['subGridColModel']= $obj1->addModel('Stamp')->addWidth(90)->addAlign('center')->addNlCr()
                     ->addModel('Bin_Id')->addWidth(80)->addAlign('center')->addNlCr()
                     ->addModel('Location')->addWidth(80)->addAlign('center')->addNlCr()
                     ->addModel('Qty')->addWidth(50)->addAlign('center')->addNlCr()
                     ->addModel('FlowCaption')->addWidth(50)->addAlign('center')->addNlCr()
                     ->addModel('ScanCode')->addWidth(80)->addAlign('center')->addNlCr()
                     ->addModel('UserName')->addWidth(100)->addAlign('center')->addNlCr()
                     ->getModel();


        //Generar el contenido....
       $this->build_content($data);
       $this->render_page();
    }

    function getData() {
        $page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1; // get the requested page
        $limit = isset($_REQUEST['rows']) ? $_REQUEST['rows'] : 10; // get how many rows we want to have into the gri
        $sidx = isset($_REQUEST['sidx']) ? $_REQUEST['sidx'] : 'Product_Catalog_ID'; // get index row - i.e. user click to sort
        $sord = isset($_REQUEST['sord']) ? $_REQUEST['sord'] : 'asc';



        $search = $this->input->get('search');
        $datefrom = $this->input->get('from');
        $dateto = $this->input->get('to');


    

        $table = ' Inventory.dbo.Bins_History a ';
        $where = ' WHERE 1=1 ';
        $from = ' FROM ';
        $groupby= ' Group by Product_Catalog_ID ';
	
	    $select = "SELECT  Product_Catalog_ID
                         ,Inventory.dbo.fn_GetProductName(Product_Catalog_ID) as Name
                        ,sum(Qty) as Quantity ";


        $selectSlice = "SELECT Product_Catalog_ID
                               ,Name
                               ,Quantity";

        $where.= " and (Stamp between '{$datefrom} 00:00:00' AND '{$dateto} 23:59:59')";

        if($search){
            $where .= " and ( Product_Catalog_ID like '%{$search}%')";
        }

        $SQL = "{$select}{$from}{$table}{$where}{$groupby}";

   


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
                                FROM {$table}{$where}{$groupby})
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
            $responce->rows[$i]['id'] = $row['Product_Catalog_ID'];
            $responce->rows[$i]['cell'] = array($row['Product_Catalog_ID'],
                $row['Name'],
                $row['Quantity']
            );
            $i++;
        }

        echo json_encode($responce);
    }


    function getDataSubgrid(){

        $page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1; // get the requested page
        $limit = isset($_REQUEST['rows']) ? $_REQUEST['rows'] : 10; // get how many rows we want to have into the gri
        $sidx = isset($_REQUEST['sidx']) ? $_REQUEST['sidx'] : 'Stamp'; // get index row - i.e. user click to sort
        $sord = isset($_REQUEST['sord']) ? $_REQUEST['sord'] : 'asc';



        $datefrom= $this->input->get('from');
        $dateto = $this->input->get('to');

  

        $table = ' Inventory.dbo.Bins_History a ';
        $where = ' WHERE 1=1 ';
        $from = ' FROM ';
        $orderby= ' Order By SKU Asc';
    
    

       $select = "SELECT Stamp
                        ,Bin_Id
                        ,inventory.dbo.fn_Bins_BinLocation(Bin_Id) as Location
                        ,Qty
                        ,CASE When Flow = 1 and ScanCode not like 'TR%' then 'IN' WHEN Flow = 2 AND ScanCode not like 'TR%' THEN 'OUT' 
                              When Flow = 1 and ScanCode like 'TR%' Then 'TRN IN' When Flow = 2 and ScanCode like 'TR%' Then 'TRN OUT' END as FlowCaption
                        ,ScanCode
                        ,Inventory.dbo.fn_GetUserName(User_Id) as UserName
                  ";

    
        $selectSlice = "SELECT Stamp
                              ,Bin_Id
                              ,Location
                              ,Qty
                              ,FlowCaption
                              ,ScanCode
                              ,UserName ";

        $where.= " and (Stamp between '{$datefrom} 00:00:00' AND '{$dateto} 23:59:59')";


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
            $responce->rows[$i]['id'] = $row['Stamp'];
            $responce->rows[$i]['cell'] = array($row['Stamp'],
                $row['Bin_Id'],
                $row['Location'],
                $row['Qty'],
                $row['FlowCaption'],
                $row['ScanCode'],
                $row['UserName'],
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




