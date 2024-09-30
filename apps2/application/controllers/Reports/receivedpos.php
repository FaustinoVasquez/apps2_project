<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Receivedpos extends BP_Controller {

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

        $mytitle = "Received POs";

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
            'from' => '/Reports/binqty/',
            'caption' => $mytitle,
            'datefrom' => $this->MCommon->lastweek(),
            'dateto' => date("m/d/Y"),    
            'pos' => $this->MCatalog->getPos($this->MCommon->lastweek(),date("m/d/Y")),
        );

      
        //Cargamos la libreria comumn
        $this->load->library('common');

        //Cargamos al arreglo los colnames
        $data['colNames'] = "['PO#','SKU', 'Qty','Movement','BinID','User','Date','TransactionID']";
        //Cargamos al arreglo los colmodels


        $this->common->addModel('PO#]')->addWidth(60)->addAlign('center')->addNlCr()
                     ->addModel('SKU')->addWidth(70)->addAlign('center')->addNlCr()
                     ->addModel('Qty')->addWidth(80)->addAlign('center')->addNlCr()
                     ->addModel('Movement')->addWidth(80)->addAlign('left')->addNlCr()
                     ->addModel('BinID')->addWidth(80)->addAlign('center')->addNlCr()
                     ->addModel('UserName')->addWidth(150)->addAlign('left')->addNlCr()
                     ->addModel('Date')->addWidth(90)->addAlign('center')->addNlCr()
                     ->addModel('TransactionID')->addWidth(80)->addAlign('center')->addNlCr();
    
        $data['colModel'] = $this->common->getModel();

        //Generar el contenido....
       $this->build_content($data);
       $this->render_page('reports/');
    }

    function getData() {
        $page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1; // get the requested page
        $limit = isset($_REQUEST['rows']) ? $_REQUEST['rows'] : 10; // get how many rows we want to have into the gri
        $sidx = isset($_REQUEST['sidx']) ? $_REQUEST['sidx'] : 'BH.[Comments]'; // get index row - i.e. user click to sort
        $sord = isset($_REQUEST['sord']) ? $_REQUEST['sord'] : 'desc';


        $search = $this->input->get('search');
        $datefrom = $this->input->get('from');
        $dateto = $this->input->get('to');
        $scanCode = $this->input->get('scanCode');
        $po =  $this->input->get('po');

        if ($po){
            $search .= $po;
        }


        $table = ' [Inventory].[dbo].[Bins_History] AS BH ';
        $where = '';
        $from = ' FROM ';
        $leftjoin = '  LEFT OUTER JOIN [Inventory].[dbo].[Users] AS US ON (BH.[User_Id] = US.ID) ';
        $orderby= ' ORDER BY Comments, [Product_Catalog_ID] ';
	
	

       $select = "SELECT   BH.[Comments] as PO#
                          ,BH.[Product_Catalog_ID] as SKU
                          ,BH.[Qty] as Qty
                          ,Case When BH.[Flow] = 1 Then 'Added' When BH.[Flow] = 2 Then 'Adjusted (Removed)' End AS Movement
                          ,BH.[Bin_Id] as BinID
                          ,Inventory.dbo.fn_GetUserName(User_Id) as UserName
                          ,BH.[Stamp] as Date
                          ,BH.[ID] as TransactionID
                  ";

	
        $selectSlice = "SELECT   PO#
                                ,SKU
                                ,Qty
                                ,Movement
                                ,BinID
                                ,UserName
                                ,Date
                                ,TransactionID";

        $wherefields = array('BH.[Comments]', 'BH.[Product_Catalog_ID]', 'BH.[Bin_Id]');

        $where .=$this->MCommon->concatAllWerefields($wherefields, $search);

        $where.= " and (BH.[Stamp] between '{$datefrom} 00:00:00' AND '{$dateto} 23:59:59')";

        $where.= " and Scancode = 'IN55' OR Scancode = 'OUT55' AND Comments LIKE '%%'";


        $SQL = "{$select}{$from}{$table}{$where}{$orderby}";

       // echo $SQL;

    
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
            $responce->rows[$i]['id'] = $row['PO#'];
            $responce->rows[$i]['cell'] = array($row['PO#'],
                $row['SKU'],
                $row['Qty'],
                $row['Movement'],
                $row['BinID'],
                $row['UserName'],
                $row['Date'],
		        $row['TransactionID'],

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

        $datefrom = $this->input->get('myfrom');
        $dateto = $this->input->get('myto');

        $poAttrib = 'style="font-size:11px" id="po"';
        $pos = $this->MCatalog->getPos( $datefrom,$dateto);

        foreach ($pos as $key => $value) {
            if ($key==0){
                echo "<option value='{$key}' selected='selected'>{$value}</option>";
            }else{
                echo "<option value='{$key}'>{$value}</option>";
            }
        }
       // echo form_dropdown('po', $pos, '0',$poAttrib);
    }
}
?>




