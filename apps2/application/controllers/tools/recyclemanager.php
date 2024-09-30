<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Recyclemanager extends BP_Controller {

    public function __construct() {
        parent::__construct();

        $is_logged_in = $this->session->userdata('is_logged_in');

        if (!isset($is_logged_in) || $is_logged_in != true) {
            redirect(base_url(), 'refresh');
        }

        if ($this->MUsers->isValidUser($this->session->userdata('userid'), 881100) != 1) {// 881100 prodcat Access
            redirect('Catalog/prodcat', 'refresh');
        }
    }

    public function index($sku =null) {

        // Define Meta
        $this->title = "MI Technologiesinc - Recycle Manager Report";

        $this->description = "Recycle Manager Report";

        $this->css = array('form.css', 'fluid.css', 'table.css', 'jqueryui/redmond/jquery-ui-1.8.21.custom.css', 'jqgrid/ui.jqgrid.css', 'menu.css', );

        // Define custom javascript
        $this->javascript = array('jqueryui/ui/jquery-ui-1.8.21.custom.js', 'jqgrid/i18n/grid.locale-en.js', 'jqgrid/jquery.jqGrid.min.js', 'jqgrid/jquery.jqGrid.fluid.js', 'popup.js','site.js');

        $this->load->library('Layout');
        $menu = new Layout;

        $data = array(
            'menu' => $menu->show_menu($this->MUsers->getUserFullName($this->session->userdata('userid'))),
            'caption' => 'Recycle Manager Report',
            'from' => 'Reports/recyclemanager/',
        );


        $columns = array(

            'ID'=>
                array('colName'  => 'ID',
                    'colModel' => "{ name:'ID',index:'ID',width:90,align:'center',frozen: true, hidden:true}"),
            'FirstName'=>
                array('colName'  => 'FirstName',
                    'colModel' => "{ name:'FirstName',index:'FirstName',width:80,align:'left',frozen: true}"),
            'LastName'=>
                array('colName'  => 'LastName',
                    'colModel' => "{ name:'LastName',index:'LastName',width:80, align:'left',frozen: true}"),
            'CompanyName'=>
                array('colName'  => 'CompanyName',
                    'colModel' => "{ name:'CompanyName',index:'CompanyName',width:170, align:'left',frozen: true}"),
            'AddressLine1'=>
                array('colName'  => 'AddressLine1',
                    'colModel' => "{ name:'AddressLine1',index:'AddressLine1', width:170,align:'left'}"),
            'City'=>
                array('colName'  => 'City',
                    'colModel' => "{ name:'City',index:'City',width:120,align:'left'}"),
            'State'=>
                array('colName'  => 'State',
                    'colModel' => "{ name:'State',index:'State',width:50,align:'center'}"),
            'Country'=>
                array('colName'  => 'Country',
                    'colModel' => "{ name:'Country',index:'Country',width:50,align:'center'}"),
            'ZipCode'=>
                array('colName'  => 'ZipCode',
                    'colModel' => "{ name:'ZipCode',index:'ZipCode',width:70,align:'center'}"),
            'Phone1'=>
                array('colName'  => '**Phone1',
                    'colModel' => "{ name:'Phone1',index:'Phone1',width:100,align:'left', editable:true}"),
            'Phone2'=>
                array('colName'  => '**Phone2',
                    'colModel' => "{ name:'Phone2',index:'Phone2',width:100,align:'left',editable:true}"),
            'Email'=>
                array('colName'  => 'Email',
                    'colModel' => "{ name:'Email',index:'Email',width:150,align:'left',formatter:'email'}"),
            'DateAdded'=>
                array('colName'  => 'DateAdded',
                      'colModel' => "{ name:'DateAdded',index:'DateAdded',width:90,align:'center'}"),
            'TrackingNumber'=>
                array('colName'  => 'TrackingNumber',
                      'colModel' => "{ name:'TrackingNumber',index:'TrackingNumber',width:130,align:'center'}"),
            'Comments'=>
                array('colName'  => 'Comments',
                      'colModel' => "{ name:'Comments',index:'Comments',width:150,align:'left'}"),
            'CustomerType'=>
                array('colName'  => 'CustomerType',
                      'colModel' => "{ name:'CustomerType',index:'CustomerType', width:100,align:'center'}"),
            'ProjectorMake'=>
                array('colName'  => 'ProjectorMake',
                      'colModel' => "{ name:'ProjectorMake',index:'ProjectorMake',width:100,align:'center'}"),
            'ProjectorModel'=>
                array('colName'  => 'ProjectorModel',
                      'colModel' => "{ name:'ProjectorModel',index:'ProjectorModel',width:100,align:'center'}"),
            'LampPartNumber'=>
                array('colName'  => 'LampPartNumber',
                      'colModel' => "{ name:'LampPartNumber',index:'LampPartNumber',width:100,align:'center'}"),
            'IncludesHousing'=>
                array('colName'  => '**IncludesHousing',
                      'colModel' => "{ name:'IncludesHousing',index:'IncludesHousing',editable:true, align:'center',
                                      edittype:'select',editoptions:{value:'NULL:Unspecific;0:No;1:Yes'}}"),
            'Quantity'=>
                array('colName'  => 'Quantity',
                      'colModel' => "{ name:'Quantity',index:'Quantity',width:100,align:'right'}"),
            'QuantityReceived'=>
                array('colName'  => '**QuantityReceived',
                      'colModel' => "{ name:'QuantityReceived',index:'QuantityReceived', width:100, align:'right',editable:true}"),
            'Position'=>
                array('colName'  => '**Position',
                      'colModel' => "{ name:'Position',index:'Position',width:100,align:'center',editable:true}"),
            'DateContacted'=>
                array('colName'  => '**DateContacted',
                      'colModel' => "{ name:'DateContacted',index:'DateContacted', width:100,align:'center',editable:true}"),
            'CheckAmount'=>
                array('colName'  => '**CheckAmount',
                      'colModel' => "{ name:'CheckAmount',index:'CheckAmount',width:100,align:'right',editable:true}"),
            'CheckSentDate'=>
                array('colName'  => '**CheckSentDate',
                      'colModel' => "{ name:'CheckSentDate',index:'CheckSentDate',width:100,align:'center',editable:true}"),
            'InternalComments'=>
                array('colName'  => '**InternalComments',
                      'colModel' => "{ name:'InternalComments',index:'InternalComments',width:170,align:'left',editable:true}"),


        );
        //Cargamos la libreria comumn
        $this->load->library('common');

        $data['colNames'] = $this->common->CreateColname($columns, 'colName');
        $data['colModel'] = $this->common->CreateColmodel($columns, 'colModel');

            //Generar el contenido....
        $this->build_content($data);
        $this->render_page();
    }


    function getData() {

        //Variables que envia el grid, incializarlas si estan vacias
        $page = !empty($_REQUEST['page']) ? $_REQUEST['page'] : '1'; // get the requested page
        $limit = !empty($_REQUEST['rows']) ? $_REQUEST['rows'] : '10'; // get how many rows we want to have into the gri
        $sidx = !empty($_REQUEST['sidx']) ? $_REQUEST['sidx'] : 'C.ID'; // get index row - i.e. user click to sort
        $sord = !empty($_REQUEST['sord']) ? $_REQUEST['sord'] : 'asc';
        $examp = isset($_REQUEST['q']) ? $_REQUEST['q'] : 1;  //query number
        //variables que envia el formulario inicializarlas si no existen
        $search = !empty($_GET['ds']) ? $_REQUEST['ds'] : '';


        //Select utilizado para realizar el conteo del numero de registros devueltos por la consulta.
        $selectCount = 'Select count(*) as rowNum';


        //Select General con los campos necesarios para la vista
        $select = " select C.[ID] as 'ID',
                    C.[FirstName] AS 'FirstName',
                    C.[LastName] AS 'LastName',
                    C.[CompanyName] AS 'CompanyName',
                    C.[AddressLine1] AS 'AddressLine1',
                    C.[City] AS 'City',
                    C.[State] AS 'State',
                    C.[Country] AS 'Country',
                    C.[ZipCode] AS 'ZipCode',
                    C.[Phone1] AS 'Phone1',
                    C.[Phone2] AS 'Phone2',
                    C.[Email] AS 'Email',
                    CONVERT(VARCHAR(10), C.[Stamp], 101) AS 'DateAdded',
                    C.[TrackingNumber] AS 'TrackingNumber',
                    C.[Comments] AS 'Comments',
                    IsNull(C.[CustomerType],'consumer') 'CustomerType',
                    P.[ProjectorMake] AS 'ProjectorMake',
                    P.[ProjectorModel] AS 'ProjectorModel',
                    P.[LampPartNumber] AS 'LampPartNumber',
                    (CASE WHEN P.[IncludesHousing] IS NULL THEN 'Unspecific'
                    WHEN P.[IncludesHousing] = 0 THEN 'No'
                    ELSE 'Yes' END) AS 'IncludesHousing',
                    P.[Quantity] AS 'Quantity',
                    IsNull(P.[QuantityReceived],0) AS 'QuantityReceived',
                    IsNull(C.[Position],'') AS 'Position',
                    IsNull(CONVERT(VARCHAR(10), C.[DateContacted], 101),'') AS 'DateContacted',
                    IsNull(C.[CheckAmount],'0') AS 'CheckAmount',
                    IsNull(CONVERT(VARCHAR(10), C.[CheckSentDate], 101),'') AS 'CheckSentDate',
                    IsNull(C.[InternalComments],'') AS 'InternalComments'

                   ";
        $selectSlice = "select ID,
                        FirstName,
                        LastName,
                        CompanyName,
                        AddressLine1,
                        City,
                        State,
                        Country,
                        ZipCode,
                        Phone1,
                        Phone2,
                        Email,
                        DateAdded,
                        TrackingNumber,
                        Comments,
                        CustomerType,
                        ProjectorMake,
                        ProjectorModel,
                        LampPartNumber,
                        IncludesHousing,
                        Quantity,
                        QuantityReceived,
                        Position,
                        DateContacted,
                        CheckAmount,
                        CheckSentDate,
                        InternalComments
                        ";

        $from = ' from ';
        $table = ' [RecycleAPI].[dbo].[Customer] AS C ';
        $join =  ' LEFT OUTER JOIN [RecycleAPI].[dbo].[Product] AS P ON (C.[Id] = P.[CustomerId]) ';
        $where = '';

        $wherefields = array('C.[FirstName]'
                            ,'C.[LastName]'
                            ,'C.[CompanyName]'
                            ,'C.[AddressLine1]'
                            ,'C.[City]'
                            ,'C.[State]'
                            ,'C.[Country]'
                            ,'C.[ZipCode]'
                            ,'C.[Phone1]'
                            ,'C.[Phone2]'
                            ,'C.[Email]');

        $fields = $this->MCommon->getAllfields($table);
        //creamos el were concatenando todos los nombres de campos y las palabras de busqueda
        $where .=$this->MCommon->concatAllWerefields($wherefields, $search);


        $SQL = "{$selectCount}{$from}{$table}{$join}{$where}";


        $query = $this->db->query($SQL);

        $count = $query->row('rowNum');


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


        $SQL = "WITH mytable AS ($select , ROW_NUMBER() OVER (ORDER BY {$sidx} {$sord}) AS RowNumber
                                FROM {$table}{$join}{$where})
               {$selectSlice}, RowNumber
               FROM mytable
                WHERE RowNumber BETWEEN {$start} AND {$finish}";


        $query = $this->db->query($SQL);

        $responce = new stdClass();
        $responce->page = $page;
        $responce->total = $total_pages;
        $responce->records = $count;
        $i = 0;


        foreach ($query->result_array() as $row) {
            $responce->rows[$i]['id'] = $row['ID'];
            $responce->rows[$i]['cell'] = array($row['ID'],
                $row['FirstName'],
                $row['LastName'],
                $row['CompanyName'],
                $row['AddressLine1'],
                $row['City'],
                $row['State'],
                $row['Country'],
                $row['ZipCode'],
                $row['Phone1'],
                $row['Phone2'],
                $row['Email'],
                $row['DateAdded'],
                $row['TrackingNumber'],
                $row['Comments'],
                $row['CustomerType'],
                $row['ProjectorMake'],
                $row['ProjectorModel'],
                $row['LampPartNumber'],
                $row['IncludesHousing'],
                $row['Quantity'],
                $row['QuantityReceived'],
                $row['Position'],
                $row['DateContacted'],
                $row['CheckAmount'],
                $row['CheckSentDate'],
                $row['InternalComments'],
            );
            $i++;
        }

        echo json_encode($responce);
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

    function editData(){

        $id = $_POST['id'];

        if (isset($_POST['Phone1'])) {
            $fieldName = 'Phone1';
            $data = $_POST['Phone1'];
            $SQL = "update [RecycleAPI].[dbo].[Customer] set {$fieldName}='{$data}' where ID='{$id}'";
        }
        if (isset($_POST['Phone2'])) {
            $fieldName = 'Phone2';
            $data = $_POST['Phone2'];
            $SQL = "update [RecycleAPI].[dbo].[Customer] set {$fieldName}='{$data}' where ID='{$id}'";
        }
        if (isset($_POST['QuantityReceived'])) {
            $fieldName = 'QuantityReceived';
            $data = $_POST['QuantityReceived'];
            $SQL = "update [RecycleAPI].[dbo].[Product] set {$fieldName}='{$data}' where CustomerId={$id}";
          //  $SQL = "update [RecycleAPI].[dbo].[Customer] set {$fieldName}='{$data}' where ID={$id}";
        }
        if (isset($_POST['Position'])) {
            $fieldName = 'Position';
            $data = $_POST['Position'];
            $SQL = "update [RecycleAPI].[dbo].[Customer] set {$fieldName}='{$data}' where ID='{$id}'";
        }
        if (isset($_POST['CheckAmount'])) {
            $fieldName = 'CheckAmount';
            $data = $_POST['CheckAmount'];
            $SQL = "update [RecycleAPI].[dbo].[Customer] set {$fieldName}='{$data}' where ID={$id}";
        }
        if (isset($_POST['DateContacted'])) {
            $fieldName = 'DateContacted';
            $data = $_POST['DateContacted'];
            $SQL = "update [RecycleAPI].[dbo].[Customer] set {$fieldName}='{$data}' where ID='{$id}'";
        }
        if (isset($_POST['CheckSentDate'])) {
            $fieldName = 'CheckSentDate';
            $data = $_POST['CheckSentDate'];
            $SQL = "update [RecycleAPI].[dbo].[Customer] set {$fieldName}='{$data}' where ID='{$id}'";
        }
        if (isset($_POST['InternalComments'])) {
            $fieldName = 'InternalComments';
            $data = $_POST['InternalComments'];
            $SQL = "update [RecycleAPI].[dbo].[Customer] set {$fieldName}='{$data}' where ID='{$id}'";
        }
        if (isset($_POST['IncludesHousing'])) {
            $fieldName = 'IncludesHousing';
            $data = $_POST['IncludesHousing'];
            $SQL = "update [RecycleAPI].[dbo].[Product] set {$fieldName}={$data} where CustomerId={$id}";
        }

         $this->db->query($SQL);
      //  $this->MCommon->saveRecord($SQL,'RecycleAPI');

    }

  public function crudOperation(){
     $id = $_POST['id'];

     $SQL1= "DELETE FROM [RecycleAPI].[dbo].[Product] WHERE [CustomerID] = '{$id}'";
     $SQL2 = "DELETE FROM [RecycleAPI].[dbo].[Customer] WHERE [ID] = '{$id}'";

     $this->db->query($SQL1);
     $this->db->query($SQL2);
  }

}
?>
