<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Invangenerator extends BP_Controller {

    public function __construct() {
        parent::__construct();

        $is_logged_in = $this->session->userdata('is_logged_in');

        if (!isset($is_logged_in) || $is_logged_in != true) {
            redirect(base_url(), 'refresh');
        }
        // if ($this->MUsers->isValidUser($this->session->userdata('userid'), 882300) != 1) {
        //     redirect('Catalog/prodcat', 'refresh');
        // }
    }

    function index() {

        // Define Meta
        $this->title = "MI Technologiesinc - Inventory Analytics Generator";

        $this->description = "Inventory Analytics Generator";

        $this->css = array('menu.css', 'form.css', 'fluid.css', 'table.css', 'jqueryui/redmondnew/jquery-ui.min.css', 'jqgrid/ui.jqgrid.css');

        // Define custom javascript
        $this->javascript = array('jqueryui/uinew/jquery-ui.min.js', 'jqgrid/i18n/grid.locale-en.js', 'jqgrid/jquery.jqGrid.min.js', 'jqgrid/jquery.jqGrid.fluid.js', 'site.js');

        //Cargamos la libreria donde esta el menu
        $this->load->library('Layout');
        //Creamos un nuevo layout->menu
        $menu = new Layout;

        $data = array(
            'menu' => $menu->show_menu($this->MUsers->getUserFullName($this->session->userdata('userid'))),
            'title' => 'MI Technologiesinc - Inventory Analytics Generator',
            'categories' => $this->MCatalog->fillCategories('All'),
            'from' => '/Reports/invangenerator/',
            'caption' => 'Inventory Analytics Generator',
            'captionConf' => 'Configure Automation',
        );

        $columns = array(
            'SKU'=>
                array('colName'  => 'SKU',
                      'colModel' => "{ name:'SKU',index:'SKU',width:70,align:'center',frozen : true}"),
            'Description'=>
                array('colName'  => 'Description',
                      'colModel' => "{ name:'Description',index:'Description',width:370,align:'left', frozen : true}"),

            '0-30DayMissedOpportunities'=>
                array('colName'  => '0-30DayMissedOpportunities',
                      'colModel' => "{ name:'0-30DayMissedOpportunities',index:'0-30DayMissedOpportunities',width:160,align:'center'}"),
            '31-60DayMissedOpportunities'=>
                array('colName'  => '31-60DayMissedOpportunities',
                      'colModel' => "{ name:'31-60DayMissedOpportunities',index:'31-60DayMissedOpportunities',width:160,align:'center'}"),
            '61-90DayMissedOpportunities'=>
                array('colName'  => '61-90DayMissedOpportunities',
                      'colModel' => "{ name:'61-90DayMissedOpportunities',index:'61-90DayMissedOpportunities',width:160,align:'center'}"),
            '0-30DaySold'=>
                array('colName'  => '0-30DaySold',
                      'colModel' => "{ name:'0-30DaySold',index:'0-30DaySold',width:100,align:'center'}"),
            '31-60DaySold'=>
                array('colName'  => '31-60DaySold',
                      'colModel' => "{ name:'[31-60DaySold',index:'[31-60DaySold',width:100, align:'center'}"),
            '61-90DaySold'=>
                array('colName'  => '61-90DaySold',
                      'colModel' => "{ name:'61-90DaySold',index:'61-90DaySold',width:100, align:'center'}"),
            '0-30DayRemoved'=>
                array('colName'  => '0-30DayRemoved',
                      'colModel' => "{ name:'0-30DayRemoved',index:'0-30DayRemoved',width:100, align:'center'}"),
            '31-60DayRemoved'=>
                array('colName'  => '31-60DayRemoved',
                      'colModel' => "{ name:'31-60DayRemoved',index:'31-60DayRemoved', width:100,align:'center'}"),
            '61-90DayRemoved'=>
                array('colName'  => '61-90DayRemoved',
                  'colModel' => "{ name:'61-90DayRemoved',index:'61-90DayRemoved', width:100,align:'center'}"),
            'QOH'=>
                array('colName'  => 'QOH',
                  'colModel' => "{ name:'QOH',index:'QOH', width:70,align:'center'}"),
            'vQOH'=>
                array('colName'  => 'vQOH',
                  'colModel' => "{ name:'vQOH',index:'vQOH', width:70,align:'center'}"),
            'tQOH'=>
                array('colName'  => 'tQOH',
                  'colModel' => "{ name:'tQOH',index:'tQOH', width:70,align:'center'}"),
            'BackOrders'=>
                array('colName'  => 'BackOrders',
                  'colModel' => "{ name:'BackOrders',index:'BackOrders', width:90,align:'center'}"),
            'PendingFBARecursive'=>
                array('colName'  => 'PendingFBARecursive',
                  'colModel' => "{ name:'PendingFBARecursive',index:'PendingFBARecursive', width:120,align:'center'}"),
            'TargetDays'=>
                array('colName'  => 'TargetDays',
                  'colModel' => "{ name:'TargetDays',index:'TargetDays', width:90,align:'center'}"),
            'HighestRunMissed'=>
                array('colName'  => 'HighestRunMissed',
                  'colModel' => "{ name:'HighestRunMissed',index:'HighestRunMissed', width:100,align:'center'}"),
            'HighestRunSold'=>
                array('colName'  => 'HighestRunSold',
                  'colModel' => "{ name:'HighestRunSold',index:'HighestRunSold', width:129,align:'center'}"),
            'SoldVariance'=>
                array('colName'  => 'SoldVariance',
                  'colModel' => "{ name:'SoldVariance',index:'SoldVariance', width:129,align:'center', formatter:'number', formatoptions:{decimalSeparator:'.',thousandsSeparator: ',', decimalPlaces: 0, suffix: '%' }}"),
            'OrderBasedOnSold'=>
                array('colName'  => 'OrderBasedOnSold',
                  'colModel' => "{ name:'OrderBasedOnSold',index:'OrderBasedOnSold', width:120,align:'center'}"),
            'HighestRunRemoved'=>
                array('colName'  => 'HighestRunRemoved',
                  'colModel' => "{ name:'HighestRunRemoved',index:'HighestRunRemoved', width:120,align:'center'}"),
            'RemovedVariance'=>
                array('colName'  => 'RemovedVariance',
                  'colModel' => "{ name:'RemovedVariance',index:'RemovedVariance', width:120,align:'center', formatter:'number', formatoptions:{decimalSeparator:'.',thousandsSeparator: ',', decimalPlaces: 0, suffix: '%' }}"),
            'OrderBasedOnRemoved'=>
                array('colName'  => 'OrderBasedOnRemoved',
                  'colModel' => "{ name:'OrderBasedOnRemoved',index:'OrderBasedOnRemoved', width:120,align:'center'}"),
            'OrderVariance'=>
                array('colName'  => 'OrderVariance',
                  'colModel' => "{ name:'OrderVariance',index:'OrderVariance', width:120,align:'center', formatter:'number', formatoptions:{decimalSeparator:'.',thousandsSeparator: ',', decimalPlaces: 0, suffix: '%' }}"),
            'WeMakeIt'=>
                array('colName'  => 'WeMakeIt?',
                  'colModel' => "{ name:'WeMakeIt',index:'WeMakeIt', width:90,align:'center'}"),
            'LowestCost'=>
                array('colName'  => 'LowestCost',
                  'colModel' => "{ name:'LowestCost',index:'LowestCost', width:90,align:'right', formatter:'number', formatoptions:{decimalSeparator:'.',thousandsSeparator: ',', decimalPlaces: 2, prefix: '$ ' }}"),
            'LowestCostSupplier'=>
                array('colName'  => 'LowestCostSupplier',
                  'colModel' => "{ name:'LowestCostSupplier',index:'LowestCostSupplier', width:120,align:'center'}"),
            'ERPUnitCost'=>
                array('colName'  => 'ERPUnitCost',
                  'colModel' => "{ name:'ERPUnitCost',index:'ERPUnitCost', width:90,align:'right', formatter:'number', formatoptions:{decimalSeparator:'.',thousandsSeparator: ',', decimalPlaces: 5, prefix: ' ' }}"),
            'DateStamp'=>
                array('colName'  => 'DateStamp',
                  'colModel' => "{ name:'DateStamp',index:'DateStamp', width:180,align:'center'}"),

        );



        $columnsConf = array(
            'ID'=>
                array('colName'  => 'ID',
                      'colModel' => "{ name:'ID',index:'ID',width:70,align:'center'}"),
            'Name'=>
                array('colName'  => 'Name',
                      'colModel' => "{ name:'Name',index:'Name',width:260,align:'left'}"),
            'RIAIsActive'=>
                array('colName'  => 'IsActive',
                      'colModel' => "{ name:'RIAIsActive',index:'RIAIsActive',width:80,align:'center',editable:true, formatter: checkboxFormatter, edittype: 'checkbox', editoptions:{value:'1:0'}}"),
            'RIATargetDays'=>
                array('colName'  => 'TargetDays',
                      'colModel' => "{ name:'RIATargetDays',index:'RIATargetDays',width:90, align:'center',editable:true}"),
            'RIAFrequency'=>
                array('colName'  => 'Frequency',
                      'colModel' => "{ name:'RIAFrequency',index:'RIAFrequency',width:90, align:'center',editable:true, edittype:'select', editoptions:{value:'3:3;5:5;7:7;10:10;15:15;20:20;25:25;30:30'}}"),
            'RIAOrderingDay'=>
                array('colName'  => 'OrderingDay',
                      'colModel' => "{ name:'RIAOrderingDay',index:'RIAOrderingDay',width:90, align:'center',editable:true, edittype:'select', editoptions:{value:'Monday:Monday;Tuesday:Tuesday;Wednesday:Wednesday;Thursday:Thursday;Friday:Friday;Saturday:Saturday;Sunday:Sunday'}}"),         
            'RIALastRunDate'=>
                array('colName'  => 'LastRunDate',
                      'colModel' => "{ name:'RIALastRunDate',index:'RIALastRunDate',width:160, align:'center'}"),
            'RIALastRunDuration'=>
                array('colName'  => 'LastRunDuration',
                      'colModel' => "{ name:'RIALastRunDuration',index:'RIALastRunDuration', width:120,align:'center'}"),
        );

        //Cargamos la libreria comumn
        $this->load->library('common');

        $data['colNamesConf'] = $this->common->CreateColname($columnsConf, 'colName');
        $data['colModelConf'] = $this->common->CreateColmodel($columnsConf, 'colModel');

        $data['colNames'] = $this->common->CreateColname($columns, 'colName');
        $data['colModel'] = $this->common->CreateColmodel($columns, 'colModel');




        $this->build_content($data);
        $this->render_page();
    }



    function getData() {
        //Variables que envia el grid, incializarlas si estan vacias
        $page = !empty($_REQUEST['page']) ? $_REQUEST['page'] : '1'; // get the requested page
        $limit = !empty($_REQUEST['rows']) ? $_REQUEST['rows'] : '10'; // get how many rows we want to have into the gri
        $sidx = !empty($_REQUEST['sidx']) ? $_REQUEST['sidx'] : 'IAR.[SKU]'; // get index row - i.e. user click to sort
        $sord = !empty($_REQUEST['sord']) ? $_REQUEST['sord'] : 'asc';
        $examp = isset($_REQUEST['q']) ? $_REQUEST['q'] : 1;  //query number
        //variables que envia el formulario inicializarlas si no existen
        $search = !empty($_GET['ds']) ? $_REQUEST['ds'] : '';
        $category = $this->input->get('cat');
     //  $category = !empty($_GET['cat']) ? $_GET['cat'] : '70';
       $targetDays= !empty($_GET['td']) ? $_GET['td'] : '90';
       // $targetDays = $this->input->get('td');


        //Select utilizado para realizar el conteo del numero de registros devueltos por la consulta.
        $selectCount = 'Select count(*) as rowNum';

        $select = "SELECT IAR.[SKU]
                              ,IAR.[Description]
                              ,IAR.[0-30DayMissedOpportunities]
                              ,IAR.[31-60DayMissedOpportunities]
                              ,IAR.[61-90DayMissedOpportunities]
                              ,IAR.[0-30DaySold]
                              ,IAR.[31-60DaySold]
                              ,IAR.[61-90DaySold]
                              ,IAR.[0-30DayRemoved]
                              ,IAR.[31-60DayRemoved]
                              ,IAR.[61-90DayRemoved]
                              ,IAR.[QOH]
                              ,IAR.[vQOH]
                              ,IAR.[tQOH]
                              ,IAR.[BackOrders]
                              ,IAR.[PendingFBARecursive]
                              ,IAR.[TargetDays]
                              ,IAR.[HighestRunMissed]
                              ,IAR.[HighestRunSold]
                              ,IAR.[SoldVariance]
                              ,IAR.[OrderBasedOnSold]
                              ,IAR.[HighestRunRemoved]
                              ,IAR.[RemovedVariance]
                              ,IAR.[OrderBasedOnRemoved]
                              ,IAR.[OrderVariance]
                              ,IAR.[WeMakeIt?]
                              ,IAR.[LowestCost]
                              ,IAR.[LowestCostSupplier]
                              ,IAR.[ERPUnitCost]
                              ,IAR.[DateStamp]
                            ";
        $selectSlice = "SELECT [SKU]
                              ,[Description]
                              ,[0-30DayMissedOpportunities]
                              ,[31-60DayMissedOpportunities]
                              ,[61-90DayMissedOpportunities]
                              ,[0-30DaySold]
                              ,[31-60DaySold]
                              ,[61-90DaySold]
                              ,[0-30DayRemoved]
                              ,[31-60DayRemoved]
                              ,[61-90DayRemoved]
                              ,[QOH]
                              ,[vQOH]
                              ,[tQOH]
                              ,[BackOrders]
                              ,[PendingFBARecursive]
                              ,[TargetDays]
                              ,[HighestRunMissed]
                              ,[HighestRunSold]
                              ,[SoldVariance]
                              ,[OrderBasedOnSold]
                              ,[HighestRunRemoved]
                              ,[RemovedVariance]
                              ,[OrderBasedOnRemoved]
                              ,[OrderVariance]
                              ,[WeMakeIt?]
                              ,[LowestCost]
                              ,[LowestCostSupplier]
                              ,[ERPUnitCost]
                              ,[DateStamp]
                         ";

        $from = ' from ';
        $table = ' [Inventory].[dbo].[InventoryAnalyticsRecursive] AS IAR ';
        $join = ' LEFT OUTER JOIN [Inventory].[dbo].[ProductCatalog] AS PC ON (IAR.[SKU] = PC.[ID])';
        $where = '';
        $wherefields = array('IAR.[SKU]','IAR.[Description]' );

        //creamos el were concatenando todos los nombres de campos y las palabras de busqueda
        $where .=$this->MCommon->concatAllWerefields($wherefields, $search);

        if ($category){
            $where .= " and (PC.[CategoryID] = '{$category}') ";
        }
        
        if($targetDays){
      //      $where .= " and (IAR.[TargetDays]  = {$targetDays}) ";
        }

        $SQL = "{$selectCount}{$from}{$table}{$join}{$where}";

        //echo $SQL;


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



      //  print_r($query->result_array());

        $responce = new stdClass();
        $responce->page = $page;
        $responce->total = $total_pages;
        $responce->records = $count;
        $i = 0;


        foreach ($query->result_array() as $row) {
            $responce->rows[$i]['id'] = $row['SKU'];
            $responce->rows[$i]['cell'] = array($row['SKU']
                ,$row['Description'] = utf8_encode($row['Description'])
                ,$row['0-30DayMissedOpportunities']=utf8_encode($row['0-30DayMissedOpportunities'])
                ,$row['31-60DayMissedOpportunities']=utf8_encode($row['31-60DayMissedOpportunities'])
                ,$row['61-90DayMissedOpportunities']=utf8_encode($row['61-90DayMissedOpportunities'])
                ,$row['0-30DaySold']=utf8_encode($row['0-30DaySold'])
                ,$row['31-60DaySold']=utf8_encode($row['31-60DaySold'])
                ,$row['61-90DaySold']=utf8_encode($row['61-90DaySold'])
                ,$row['0-30DayRemoved']=utf8_encode($row['0-30DayRemoved'])
                ,$row['31-60DayRemoved']=utf8_encode($row['31-60DayRemoved'])
                ,$row['61-90DayRemoved']=utf8_encode($row['61-90DayRemoved'])
                ,$row['QOH']=utf8_encode($row['QOH'])
                ,$row['vQOH']=utf8_encode($row['vQOH'])
                ,$row['tQOH']=utf8_encode($row['tQOH'])
                ,$row['BackOrders']=utf8_encode($row['BackOrders'])
                ,$row['PendingFBARecursive']=utf8_encode($row['PendingFBARecursive'])
                ,$row['TargetDays']=utf8_encode($row['TargetDays'])
                ,$row['HighestRunMissed']=utf8_encode($row['HighestRunMissed'])
                ,$row['HighestRunSold']=utf8_encode($row['HighestRunSold'])
                ,$row['SoldVariance']=utf8_encode($row['SoldVariance'])
                ,$row['OrderBasedOnSold']=utf8_encode($row['OrderBasedOnSold'])
                ,$row['HighestRunRemoved']=utf8_encode($row['HighestRunRemoved'])
                ,$row['RemovedVariance']=utf8_encode($row['RemovedVariance'])
                ,$row['OrderBasedOnRemoved']=utf8_encode($row['OrderBasedOnRemoved'])
                ,$row['OrderVariance']=utf8_encode($row['OrderVariance'])
                ,$row['WeMakeIt?']=utf8_encode($row['WeMakeIt?'])
                ,$row['LowestCost']=utf8_encode($row['LowestCost'])
                ,$row['LowestCostSupplier']=utf8_encode($row['LowestCostSupplier'])
                ,$row['ERPUnitCost']=utf8_encode($row['ERPUnitCost'])
                ,$row['DateStamp']=utf8_encode($row['DateStamp'])
   

            );
            $i++;
        }

        echo json_encode($responce);
    }

    public function generateReport(){

        $categoryId = $this->input->post('cId');
        $targetDays   = $this->input->post('td');
        $userId   = $this->session->userdata('userid');

        // $SQL = "EXECUTE [Inventory].[dbo].[sp_CreateInventoryAnalyticsRecursive] '{$categoryId}','{$targetDays}','{$userId}'";

        // $this->load->database('Inventory');
        // $stmt = mssql_init('sp_CreateInventoryAnalyticsRecursive');

        // mssql_bind($stmt, '@pCategory', $categoryId, SQLINT1, false,  false,  3);
        // mssql_bind($stmt, '@pTargetDays', $targetDays, SQLINT1, false,  false,  3);
        // mssql_bind($stmt, '@pUserID', $userId, SQLINT1, false,  false,  3);


        // mssql_execute($stmt);
        // mssql_free_statement($stmt);


        $scriptFolder = '/var/www/html/apps2/application/shellscript/sql.sh';
        $command = $scriptFolder." ".$categoryId." ".$targetDays." ".$userId." &>/dev/null &";
       // echo $command;
        $command = escapeshellcmd ($command);
        $output = shell_exec($command);
        echo "<pre>$command</pre>";

    }



    public function getDataConf(){

         //Variables que envia el grid, incializarlas si estan vacias
        $page = !empty($_REQUEST['page']) ? $_REQUEST['page'] : '1'; // get the requested page
        $limit = !empty($_REQUEST['rows']) ? $_REQUEST['rows'] : '10'; // get how many rows we want to have into the gri
        $sidx = !empty($_REQUEST['sidx']) ? $_REQUEST['sidx'] : 'IAR.[SKU]'; // get index row - i.e. user click to sort
        $sord = !empty($_REQUEST['sord']) ? $_REQUEST['sord'] : 'asc';
        $examp = isset($_REQUEST['q']) ? $_REQUEST['q'] : 1;  //query number
        //variables que envia el formulario inicializarlas si no existen
        $search = !empty($_GET['ds']) ? $_REQUEST['ds'] : '';

       // $targetDays = $this->input->get('td');


        //Select utilizado para realizar el conteo del numero de registros devueltos por la consulta.
        $selectCount = 'Select count(*) as rowNum';

        $select = " SELECT CAT.[ID]
                  ,CAT.[Name]
                  ,CAT.[RIAIsActive] 
                  ,CAT.[RIATargetDays] 
                  ,CAT.[RIAFrequency] 
		  ,CAT.[RIAOrderingDay]
                  ,CAT.[RIALastRunDate]
                  ,CAT.[RIALastRunDuration]
                 ";

        $selectSlice = "SELECT [ID]
                              ,[Name]
                              ,[RIAIsActive]
                              ,[RIATargetDays]
                              ,[RIAFrequency]
			      ,[RIAOrderingDay]
                              ,[RIALastRunDate]
                              ,[RIALastRunDuration]
                         ";

        $from = ' from ';
        $table = ' [Inventory].[dbo].[Categories] AS CAT ';
        $where = '';
        $wherefields = array('CAT.[ID]','CAT.[Name]' );

        //creamos el were concatenando todos los nombres de campos y las palabras de busqueda
        $where .=$this->MCommon->concatAllWerefields($wherefields, $search);

        $where .= " and CAT.[ID] IN (SELECT DISTINCT [CategoryID] FROM [Inventory].[dbo].[ProductCatalog]) ";
        

        $SQL = "{$selectCount}{$from}{$table}{$where}";

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
                                FROM {$table}{$where})
               {$selectSlice}, RowNumber
               FROM mytable
                WHERE RowNumber BETWEEN {$start} AND {$finish}";


        $query = $this->db->query($SQL);



      //  print_r($query->result_array());

        $responce = new stdClass();
        $responce->page = $page;
        $responce->total = $total_pages;
        $responce->records = $count;
        $i = 0;


        foreach ($query->result_array() as $row) {
            $responce->rows[$i]['id'] = $row['ID'];
            $responce->rows[$i]['cell'] = array($row['ID']
              ,$row['Name']= utf8_encode($row['Name'])
              ,$row['RIAIsActive']=utf8_encode($row['RIAIsActive'])
              ,$row['RIATargetDays']=utf8_encode($row['RIATargetDays'])
              ,$row['RIAFrequency']=utf8_encode($row['RIAFrequency'])
              ,$row['RIAOrderingDay']=utf8_encode($row['RIAOrderingDay'])
              ,$row['RIALastRunDate']=utf8_encode($row['RIALastRunDate'])
              ,$row['RIALastRunDuration']=utf8_encode($row['RIALastRunDuration'])
            );
            $i++;
        }

        echo json_encode($responce);

    }


    public function editData(){

        $id = $_POST['id'];
     
        if (isset($_POST['RIAFrequency'])) {
            $fieldName = 'RIAFrequency';
            $data = $_POST['RIAFrequency'];
        }

        if (isset($_POST['RIATargetDays'])) {
            $fieldName = 'RIATargetDays';
            $data = $_POST['RIATargetDays'];
        }

         if (isset($_POST['RIAOrderingDay'])) {
            $fieldName = 'RIAOrderingDay';
            $data = $_POST['RIAOrderingDay'];
        }

         if (isset($_POST['checkbox'])) {
            $fieldName = 'RIAIsActive';
            $data = $_POST['value'];
        }

        $SQL = "update [Inventory].[dbo].[Categories] set {$fieldName}='{$data}' where ID='{$id}'";

        $this->MCommon->saveRecord($SQL,'Inventory');
        
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

