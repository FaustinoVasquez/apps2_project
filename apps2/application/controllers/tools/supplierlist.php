<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class SupplierList extends BP_Controller {

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

        $this->title = "MI Technologiesinc - Supplier List";
        $this->description = "Supplier List";
        $this->css = array('form.css', 'fluid.css', 'table.css', 'jqueryui/redmond/jquery-ui-1.8.21.custom.css', 'jqgrid/ui.jqgrid.css', 'menu.css',);

        // Define custom javascript
        $this->javascript = array('jqueryui/ui/jquery-ui-1.8.21.custom.js', 'jqgrid/i18n/grid.locale-en.js', 'jqgrid/jquery.jqGrid.min.js', 'jqgrid/jquery.jqGrid.fluid.js', 'site.js');

        $this->load->library('Layout');
        $menu = new Layout;

        $data = array( 
            'menu' => $menu->show_menu($this->MUsers->getUserFullName($this->session->userdata('userid'))),
            'from' => '/tools/supplierlist',
            'caption' => 'Supplier List',
            'export' => 'SupplierList',
            'sort' => 'asc',
            'baseUrl' => base_url(),
        );

        $data['colNames'] = "['SupplierID','SupplierName','ContactName','Email','Phone','Address','City','State','Country','CreditLimit']";

        $data['colModel'] = "[
                {name:'SupplierID',index:'SupplierID', width:50, align:'center', sorttype: 'int'},
                {name:'SupplierName',index:'SupplierName', width:80, align:'left', editable:true},
                {name:'ContactName',index:'ContactName', width:80, align:'center', editable:true},
                {name:'Email',index:'Email', width:60, align:'center', editable:true},  
                {name:'Phone',index:'Phone', width:60, align:'center', editable:true}, 
                {name:'Address',index:'Address', width:60, align:'center', editable:true}, 
                {name:'City',index:'City', width:60, align:'center', editable:true}, 
                {name:'State',index:'State', width:60, align:'center', editable:true}, 
                {name:'Country',index:'Country', width:50, align:'center', editable:true}, 
                {name:'CreditLimit',index:'CreditLimit', width:50, align:'center', editable:true,editrules:{number:true}, sorttype: 'int'}, 
            ]";
            
        $this->build_content($data);
        $this->render_page();
    }

    function getData() {

        $page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1; // get the requested page
        $limit = isset($_REQUEST['rows']) ? $_REQUEST['rows'] : 10; // get how many rows we want to have into the gri
        $sidx = $_GET['sidx']; // get index row - i.e. user click to sort
        $sord = $_GET['sord'];
        //if(!$sidx) $sidx =1;
        
        $search = $this->input->get('ds');

        //Select utilizado para realizar el conteo del numero de registros devueltos por la consulta.
        $selectCount = 'SELECT COUNT(*) AS rowNum';

        //Select General con los campos necesarios para la vista
        $select = " SELECT [SupplierId] AS 'SupplierID'
                            ,[SupplierName]
                            ,[ContactName]
                            ,[Email]
                            ,[Phone]
                            ,[Address]
                            ,[City]
                            ,[State]
                            ,[Country]
                            ,[CreditLimit]";

        $from =  ' FROM';
        $table = ' [Inventory].[dbo].[SupplierInfo] ';

        $where ='';
        $wherefields = array('[SupplierId]','[SupplierName]','[City]','[State]','[Country]');

        //Obtenemos todos los nombres de campos de la tabla productCatalog
      //  $fields = $this->MCommon->getAllfields($table);
        //creamos el where concatenando todos los nombres de campos y las palabras de busqueda
        $where .= $this->MCommon->concatAllWerefields($wherefields, $search);

        $orderby = " ORDER BY $sidx $sord ";

        $SQL = "{$select}{$from}{$table}{$where}{$orderby}";

        //print_r($SQL);

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
            $responce->rows[$i]['id'] = $row['SupplierID'];
            $responce->rows[$i]['cell'] = array($row['SupplierID'],
                $row['SupplierName'] = utf8_encode($row['SupplierName']),
                $row['ContactName'] = utf8_encode($row['ContactName']),
                $row['Email'] = utf8_encode($row['Email']),
                $row['Phone'] = utf8_encode($row['Phone']),
                $row['Address'] = utf8_encode($row['Address']),
                $row['City'] = utf8_encode($row['City']),
                $row['State'] = utf8_encode($row['State']),
                $row['Country'] = utf8_encode($row['Country']),
                $row['CreditLimit'] = utf8_encode($row['CreditLimit']),
            );
            $i++;
        }
        echo json_encode($responce);
    }

    /*
     * CSV Export
     */


    function editSupplier()
    {
        $sup = $this->input->post('id');

        if (isset($_POST['SupplierName'])) {
                $SupplierName = $_POST['SupplierName'];
                $SQL = "UPDATE [Inventory].[dbo].[SupplierInfo] SET SupplierName={$SupplierName} WHERE SupplierId={$sup}";
        }

        if (isset($_POST['ContactName'])) {
                $ContactName = $_POST['ContactName'];
                $SQL = "UPDATE [Inventory].[dbo].[SupplierInfo] SET ContactName='{$ContactName}' WHERE SupplierId={$sup}";
        }

        if (isset($_POST['Email'])) {
            $Email = $_POST['Email'];
            $SQL = "UPDATE [Inventory].[dbo].[SupplierInfo] SET Email={$Email} WHERE SupplierId = {$sup}";
        }

        if (isset($_POST['Phone'])) {
                $Phone = $_POST['Phone'];
                $SQL = "UPDATE [Inventory].[dbo].[SupplierInfo] SET Phone='{$Phone}' WHERE SupplierId={$sup}";
        }

        if (isset($_POST['Address'])) {
                $Address = $_POST['Address'];
                $SQL = "UPDATE [Inventory].[dbo].[SupplierInfo] SET Address={$Address} WHERE SupplierId={$sup}";
        }

        if (isset($_POST['City'])) {
                $City = $_POST['City'];
                $SQL = "UPDATE [Inventory].[dbo].[SupplierInfo] SET City='{$City}' WHERE SupplierId={$sup}";
        }

        if (isset($_POST['State'])) {
                $State = $_POST['State'];
                $SQL = "UPDATE [Inventory].[dbo].[SupplierInfo] SET State={$State} WHERE SupplierId={$sup}";
        }

        if (isset($_POST['Country'])) {
                $Country = $_POST['Country'];
                $SQL = "UPDATE [Inventory].[dbo].[SupplierInfo] SET Country='{$Country}' WHERE SupplierId={$sup}";
        }

        if (isset($_POST['CreditLimit'])) {
                $CreditLimit = $_POST['CreditLimit'];
                $SQL = "UPDATE [Inventory].[dbo].[SupplierInfo] SET CreditLimit={$CreditLimit} WHERE SupplierId={$sup}";
        }

        $this->MCommon->saveRecord($SQL,'Inventory');
    }

    function SaveSupplier() {


        $SQL = " INSERT INTO Inventory.[dbo].[SupplierInfo] (SupplierName,ContactName,Email,Phone,Address,City,State,Country,
            CreditLimit) 
            VALUES ('{$_POST['SupplierName']}',
                '{$_POST['ContactName']}',
                '{$_POST['Email']}',
                '{$_POST['Phone']}',
                '{$_POST['Address']}',
                '{$_POST['City']}',
                '{$_POST['State']}',
                '{$_POST['Country']}',
                '{$_POST['CreditLimit']}'); ";

        $this->MCommon->saveRecord($SQL,'InventorySave');

        return 'true';

    }

    
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