<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Itemsbysupplier extends BP_Controller {

    public function __construct() {
        parent::__construct();

        $is_logged_in = $this->session->userdata('is_logged_in');

        if (!isset($is_logged_in) || $is_logged_in != true) {
            redirect(base_url(), 'refresh');
        }

        // if ($this->MUsers->isValidUser($this->session->userdata('userid'), 883530) != 1) {
        //     redirect('Catalog/prodcat', 'refresh');
        // }
    }

    public function index() {
        //Cargamos la base de datos de OrderManager
        $this->load->model('MOrders', '', TRUE);

        // Define Meta
        $this->title = "MI Technologiesinc - Items by supplier";

        $this->description = "Items by supplier";

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
            'from' => '/Reports/itemsbysupplier/',
            'nameGrid' => 'itemsbysupplier',
            'namePager' => 'itemsbysupplierPager',
            'caption' => 'Items by supplier',
            'subgrid' => 'false',
            'sort' => 'desc',
            'search' => '',
            'datefrom' => $this->MCommon->lastweek(),
            'dateto' => date("m/d/Y"),
            'export' => 'projectorlampleads',      
            'supplierOptions' => $this->MSuppliers->getSuppliers(),
            'selectedsupplier'=>0,
        );


        //Cargamos al arreglo los colnames
        $data['colNames'] = "['Serial Number','Destination','Inbound Date','Flow','Expiration Date','Lot','SKU','Scan Date']";
        //Cargamos al arreglo los colmodels
        $data['colModel'] = "[
                {name:'SerialNumber',index:'SerialNumber', width:100, align:'center'}, 
                {name:'Destination',index:'Destination', width:100, align:'center', hidden:true},
                {name:'Date',index:'Date', width:80, align:'center'},
                {name:'Flow',index:'Flow', width:50, align:'center',hidden:true},
                {name:'Expiration_Date',index:'Expiration_Date', width:60, align:'center'},
                {name:'Lot',index:'Lot', width:80, align:'center'},
                {name:'ProductCatalogID',index:'ProductCatalogID', width:60, align:'center'},
                {name:'ScanDate',index:'ScanDate', width:60, align:'center'},

  	]";


        
        //Cargamos la libreria comumn
        $this->load->library('common');

        //Reemplazamos los campos del formulario por los que estan en el arreglo $_POST.
        $data = $this->common->fillPost('ALL', $data);

        //  $data['selectedqtyordered']=$qtyOrdered;
        //cadena de busqueda del grid 
        $data['gridSearch'] = 'gridData?ds=' . $data['search'] . '&suid=' . $data['selectedsupplier'] . '&df=' . $data['datefrom'] . '&dt=' . $data['dateto'];


        //Generar el contenido....
        $this->build_content($data);
        $this->render_page();
    }

    function gridData() {
        $page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1; // get the requested page
        $limit = isset($_REQUEST['rows']) ? $_REQUEST['rows'] : 10; // get how many rows we want to have into the gri
        $sidx = isset($_REQUEST['sidx']) ? $_REQUEST['sidx'] : ''; // get index row - i.e. user click to sort
        $sord = isset($_REQUEST['sord']) ? $_REQUEST['sord'] : 'desc';

      //  $this->load->model('MOrders', '', TRUE);

        $dataSearch = isset($_REQUEST['ds']) ? $_GET['ds'] : '';
        $supplierid = isset($_REQUEST['suid']) ? $_GET['suid'] : 0;
        $InitialDate = isset($_REQUEST['df']) ? $_GET['df'] : $this->MCommon->lastweek();
        $FinalDate = isset($_REQUEST['dt']) ? $_GET['dt'] : date("m/d/Y");
      

        if (!$sidx)
            $sidx = 1;
        
        
        $table = ' Inventory.dbo.InventoryAdjustments LEFT OUTER JOIN
                   Inventory.dbo.SerialNumbers ON InventoryAdjustments.ID = SerialNumbers.InventoryAdjustmentID ';
        $where = ' ';
        $from = ' FROM ';

        $select = " SELECT
                    SerialNumbers.SerialNumber,
                    InventoryAdjustments.Destination, 
                    InventoryAdjustments.Date, 
                    InventoryAdjustments.Flow, 
                    InventoryAdjustments.expiration_date, 
                    InventoryAdjustments.lot, 
                    SerialNumbers.ProductCatalogID, 
                    SerialNumbers.[timestamp] as ScanDate
                   ";



        $wherefields = array('InventoryAdjustments.Origin', 'InventoryAdjustments.Date', 'InventoryAdjustments.lot', 'SerialNumbers.ProductCatalogID', 'SerialNumbers.SerialNumber', 'SerialNumbers.[timestamp]');
        $where .=$this->MCommon->concatAllWerefields($wherefields, $dataSearch);

        $where .=" and (InventoryAdjustments.Date between '{$InitialDate}' and '{$FinalDate}')
                   AND (InventoryAdjustments.Flow = 1)";

        if ($supplierid){
             $where.= " AND (InventoryAdjustments.Destination = {$supplierid})"; 
        }

        
        $SQL = "{$select}{$from}{$table}{$where}";
        
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


        $responce = new stdClass();
        $responce->page = $page;
        $responce->total = $total_pages;
        $responce->records = $count;
        $i = 0;
        
        
        foreach ($result as $row) {
            $responce->rows[$i]['id'] = $row['SerialNumber'];
            $responce->rows[$i]['cell'] = array($row['SerialNumber'],
                $row['Destination'],
                $row['Date'],
                $row['Flow'],
                $row['expiration_date'],
                $row['lot'],
                $row['ProductCatalogID'],
                $row['ScanDate']
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





