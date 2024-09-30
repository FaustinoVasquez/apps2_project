<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Schoolextraction extends BP_Controller {

    public function __construct() {
        parent::__construct();

        $is_logged_in = $this->session->userdata('is_logged_in');

        if (!isset($is_logged_in) || $is_logged_in != true) {
            redirect(base_url(), 'refresh');
        }

//        if ($this->MUsers->isValidUser($this->session->userdata('userid'), 884060) != 1) {
//            redirect('Catalog/prodcat', 'refresh');
//        }
    }

    public function index() {

        //Cargamos la base de datos de OrderManager
     //   $this->load->model('MOrders', '', TRUE);

        // Define Meta
        $this->title = "MI Technologiesinc - User Management";

        $this->description = "School Extraction";

        // Define custom CSS
        $this->css = array('menu.css', 'fluid.css', 'jqueryui/redmond/jquery-ui-1.8.21.custom.css', 'jqgrid/ui.jqgrid.css', 'form.css');

        // Define custom javascript
        $this->javascript = array('jqueryui/ui/jquery-ui-1.8.21.custom.js', 'jqgrid/i18n/grid.locale-en.js', 'jqgrid/jquery.jqGrid.min.js', 'jqgrid/jquery.jqGrid.fluid.js', 'popup.js','site.js');
        //Cargamos la libreria donde esta el menu
        $this->load->library('Layout');
        //Creamos un nuevo layout->menu
        $menu = new Layout;

        $data = array(
            'menu' => $menu->show_menu($this->MUsers->getUserFullName($this->session->userdata('userid'))),
            'from' => '/Reports/schoolextraction/',
            'caption' => 'School Extraction',
            'sort' => 'asc',
          //  'categoriesOptions' => $this->MCatalog->fillCategories(),
          //  'fullNameOptions' => $this->MUsers->CustIdFullName(),
            'baseUrl' => base_url(),
        );


        $data['colNames'] = "['rowid',
                              'CustomerID',
                              'Email',
                              'ShipName',
                              'ShipCompany',
                              'ShipAddress',
                              'ShipAddress2',
                              'ShipCity',
                              'ShipState',
                              'ShipZip',
                              'ShipCountry',
                              'ShipPhone',
                              ]";

        $data['colModel'] = "[
                {name:'rowid',index:'rowid', width:70, align:'center'},
                {name:'CustomerID',index:'CustomerID', width:70, align:'center'},
                {name:'Email',index:'Email', width:100, align:'left',editable:true},
                {name:'ShipName',index:'ShipName', width:200, align:'left',editable:true},
                {name:'ShipCompany',index:'ShipCompany', width:150, align:'left',editable:true},
                {name:'ShipAddress',index:'ShipAddress', width:200, align:'left',editable:true},
                {name:'ShipAddress2',index:'ShipAddress2', width:200, align:'left',editable:true},
                {name:'ShipCity',index:'ShipCity', width:100, align:'left',editable:true},
                {name:'ShipState',index:'ShipState', width:60, align:'center',editable:true},
                {name:'ShipZip',index:'ShipZip', width:90, align:'center',editable:true},
                {name:'ShipCountry',index:'ShipCountry', width:90, align:'center',editable:true},
                {name:'ShipPhone',index:'ShipPhone', width:90, align:'center',editable:true},
   	         ]";

        //Generar el contenido....
        $this->build_content($data);
        $this->render_page();
    }

    function GridData() {

        $page   = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1; // get the requested page
        $limit  = isset($_REQUEST['rows']) ? $_REQUEST['rows'] : 10; // get how many rows we want to have into the gri
        $sidx = $_GET['sidx']; // get index row - i.e. user click to sort
        $sord = $_GET['sord'];

        $datasearch = $_GET['ds'];

        $where = '';


        //Select utilizado para realizar el conteo del numero de registros devueltos por la consulta.
        $selectCount = 'SELECT COUNT(*) AS rowNum';


        //Se utiliza en la seccion que extrae los registros de la hoja activa
        $select = " SELECT  [rowid],
                            [CustomerID],
                            [Email],
                            [ShipName],
                            [ShipCompany],
                            [ShipAddress],
                            [ShipAddress2],
                            [ShipCity],
                            [ShipState],
                            [ShipZip],
                            [ShipCountry],
                            [ShipPhone] ";

      
        $selectSlice = " SELECT [rowid],
                           [CustomerID],
                           [Email],
                           [ShipName],
                           [ShipCompany],
                           [ShipAddress],
                           [ShipAddress2],
                           [ShipCity],
                           [ShipState],
                           [ShipZip],
                           [ShipCountry],
                           [ShipPhone]";

        $from = " FROM  [Inventory].[dbo].[SchoolExtraction]";


        $wherefields = array('[CustomerID]','[Email]','[ShipName]','[ShipCompany]','[ShipAddress]','[ShipAddress2]','[ShipCity]',
                              '[ShipState]','[ShipZip]','[ShipCountry]','[ShipPhone]');
        $where .=$this->MCommon->concatAllWerefields($wherefields, $datasearch);

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
            $responce->rows[$i]['id'] = $row['rowid'];
            $responce->rows[$i]['cell'] = array($row['rowid'],
                $row['CustomerID'],
                $row['Email'],
                $row['ShipName'],
                $row['ShipCompany'],
                $row['ShipAddress'],
                $row['ShipAddress2'],
                $row['ShipCity'],
                $row['ShipState'],
                $row['ShipZip'],
                $row['ShipCountry'],
                $row['ShipPhone'],
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


    function editSKU()
    {
        $rowId = $this->input->post('id');

        if (isset($_POST['Email'])) {
                $Email = $_POST['Email'];

                $SQL = "UPDATE [Inventory].[dbo].[SchoolExtraction] SET Email='{$Email}' WHERE rowid={$rowId}";
        }

        if (isset($_POST['ShipName'])) {
                $ShipName = $_POST['ShipName'];
                $SQL = "UPDATE [Inventory].[dbo].[SchoolExtraction] SET ShipName='{$ShipName}' WHERE rowid={$rowId}";
        }

        if (isset($_POST['ShipCompany'])) {
                $ShipCompany = $_POST['ShipCompany'];
                $SQL = "UPDATE [Inventory].[dbo].[SchoolExtraction] SET ShipCompany='{$ShipCompany}' WHERE rowid={$rowId}";
        }

        if (isset($_POST['ShipAddress'])) {
                $ShipAddress = $_POST['ShipAddress'];
                $SQL = "UPDATE [Inventory].[dbo].[SchoolExtraction] SET ShipAddress='{$ShipAddress}' WHERE rowid={$rowId}";
        }

        if (isset($_POST['ShipAddress2'])) {
                $ShipAddress2 = $_POST['ShipAddress2'];
                $SQL = "UPDATE [Inventory].[dbo].[SchoolExtraction] SET ShipAddress2='{$ShipAddress2}' WHERE rowid={$rowId}";
        }
        if (isset($_POST['ShipCity'])) {
             $ShipCity = $_POST['ShipCity'];
             $SQL = "UPDATE [Inventory].[dbo].[SchoolExtraction] SET ShipCity='{$ShipCity}' WHERE rowid={$rowId}";
         }

        if (isset($_POST['ShipState'])) {
             $ShipState = $_POST['ShipState'];
             $SQL = "UPDATE [Inventory].[dbo].[SchoolExtraction] SET ShipState='{$ShipState}' WHERE rowid={$rowId}";
        }

        if (isset($_POST['ShipZip'])) {
            $ShipZip = $_POST['ShipZip'];
            $SQL = "UPDATE [Inventory].[dbo].[SchoolExtraction] SET ShipZip='{$ShipZip}' WHERE rowid={$rowId}";
        }

        if (isset($_POST['ShipCountry'])) {
            $ShipCountry = $_POST['ShipCountry'];
            $SQL = "UPDATE [Inventory].[dbo].[SchoolExtraction] SET ShipCountry='{$ShipCountry}' WHERE rowid={$rowId}";
        }

        if (isset($_POST['ShipPhone'])) {
            $ShipPhone = $_POST['ShipPhone'];
            $SQL = "UPDATE [Inventory].[dbo].[SchoolExtraction] SET ShipPhone='{$ShipPhone}' WHERE rowid={$rowId}";
        }

      //  print_r($SQL);
        $this->MCommon->saveRecord($SQL,'Inventory');
    }

    function editData()
    {
        $oper = $_POST['oper'];

        if($oper == 'del'){
            $rowid = $_POST['id'];
            $SQL1 ="insert into [Inventory].[dbo].[SchoolExtractionDeleted]
                    select [CustomerID],
                            [Email],
                            [ShipName],
                            [ShipCompany],
                            [ShipAddress],
                            [ShipAddress2],
                            [ShipCity],
                            [ShipState],
                            [ShipZip],
                            [ShipCountry],
                            [ShipPhone] 
                    from [Inventory].[dbo].[SchoolExtraction] where rowid={$rowid}";

            $this->MCommon->saveRecord($SQL1,'Inventory');

            $SQL = " DELETE FROM [Inventory].[dbo].[SchoolExtraction] WHERE rowid={$rowid}";

            $this->MCommon->saveRecord($SQL,'Inventory');
        }
        return true;
    }

}
?>
