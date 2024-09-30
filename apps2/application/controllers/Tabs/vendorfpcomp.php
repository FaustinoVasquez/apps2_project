<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Vendorfpcomp extends BP_Controller {

    public function __construct() {
        parent::__construct();

        $is_logged_in = $this->session->userdata('is_logged_in');

        if (!isset($is_logged_in) || $is_logged_in != true) {
            redirect(base_url(), 'refresh');
        }
         if ($this->MUsers->isValidUser($this->session->userdata('userid'), 881120) != 1) {
               redirect('Catalog/prodcat', 'refresh');
           }
    }

    function index($sku) {

        $this->title = "MI Technologiesinc - Vendor FP Compatiblility";
        $this->description = " Vendor FP Compatiblility";
        // Define custom CSS
        $this->css = array('menu.css', 'form.css', 'fluid.css', 'table.css', 'jqueryui/redmond/jquery-ui-1.8.21.custom.css', 'jqgrid/ui.jqgrid.css');
        // Define custom javascript
        $this->javascript = array('jqueryui/ui/jquery-ui-1.8.21.custom.js', 'jqgrid/i18n/grid.locale-en.js', 'jqgrid/jquery.jqGrid.min.js', 'jqgrid/jquery.jqGrid.fluid.js');
        $this->hasNav = False;

        $data['search'] = $sku;
        $data['title'] = 'History'; //Page Title
        $data['caption'] = 'Suppliers Info';
        $data['from'] = '/Tabs/vendorfpcomp/';
        
        $data['headers'] = "['SupplierID','Supplier SKU','Name','UnitCost']";

        $data['body'] = "[
                {name:'SupplierID',index:'SupplierID',width:70, align:'center'},
                {name:'supplierSKU',index:'supplierSKU',width:80, align:'left'},
                {name:'Name',index:'Name', width:200, align:'Left'},
                {name:'unitCost',index:'unitCost', width:60, align:'center', sortable:false, formatter:'number', formatoptions:{decimalSeparator:'.', thousandsSeparator: ',', decimalPlaces: 2, prefix: '' }}
  	]";

        $this->build_content($data);
        $this->render_page();
    }

  




    function DataVendorfpcomp() {
        $search = $_GET['q'];
        $page = isset($_GET['page']) ? $_GET['page'] : 1; // get the requested page
        $limit = isset($_GET['rows']) ? $_GET['rows'] : 10; // get how many rows we want to have into the grid
        $sidx = isset($_GET['sidx']) ? $_GET['sidx'] : 'SupplierID'; // get index row - i.e. user click to sort
        $sord = isset($_GET['sord']) ? $_GET['sord'] : 'desc'; // get the direction


        $selectCount = 'Select count(*) as rowNum';
        $select = "SELECT SupplierID, supplierSKU, inventory.dbo.fn_get_SupplierName(SupplierID) as Name, unitCost ";

        $selectSlice = "SELECT SupplierID, supplierSKU,Name, unitCost ";

        $from = ' from ';
        $table = "suppliers";

        $where = " WHERE ProductCatalogID = {$search}";
        
      

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


   /*     $SQL = "WITH mytable AS ({$select}, ROW_NUMBER() OVER (ORDER BY {$sidx} {$sord}) AS RowNumber
                                FROM {$table}{$where})
               {$selectSlice}, RowNumber
               FROM mytable
                WHERE RowNumber BETWEEN {$start} AND {$finish}";

   
       $result = $this->MCommon->getSomeRecords($SQL);*/
        
        $responce = new stdClass();
        $responce->page = $page;
        $responce->total = $total_pages;
        $responce->records = $count;

        $i = 0;

        foreach ($result as $row) {           
            $responce->rows[$i]['id'] = $row['SupplierID'];
            $responce->rows[$i]['cell'] = array($row['SupplierID'],
                $row['supplierSKU'] = $row['supplierSKU'],
                $row['Name'] = $row['Name'],
                $row['unitCost'] = $row['unitCost']
            );
            $i++;
        }

        echo json_encode($responce); 
    }

    function csvExportHistory($name) {

        header('Content-type: application/vnd.ms-excel');
        header("Content-Disposition: attachment; filename= history_" . $name . ".xls");
        header("Pragma: no-cache");

        $buffer = $_POST['csvBuffer'];

        try {
            echo $buffer;
        } catch (Exception $e) {
            
        }
    }
    
    
}

?>
