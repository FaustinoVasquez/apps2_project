<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Adjustmentdetails extends BP_Controller {

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
        $this->title = "MI Technologiesinc - Adjustment Details";

        $this->description = "Adjustment Details";

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
            'from' => '/Reports/adjustmentdetails/',
            'nameGrid' => 'adjdetails',
            'namePager' => 'adjdetailspager',
            'caption' => 'Adjustment Details',
            'subgrid' => 'false',
            'sort' => 'desc',
            'search' => '',
            'datefrom' => $this->MCommon->lastweek(),
            'dateto' => date("m/d/Y"),          
        );

        //Cargamos la libreria comumn
        $this->load->library('common');

        //Cargamos al arreglo los colnames
        $data['colNames'] = "['TrID','User','Movement','Qty','SKU','BinID','Reason','Comments','Date','WH','Location']";
        //Cargamos al arreglo los colmodels


        $this->common->addModel('TrID')->addWidth(50)->addAlign('center')->addNlCr()
                     ->addModel('User')->addWidth(50)->addAlign('center')->addNlCr()
                     ->addModel('Movement')->addWidth(50)->addAlign('center')->addNlCr()
                     ->addModel('Qty')->addWidth(50)->addAlign('center')->addNlCr()
                     ->addModel('SKU')->addWidth(50)->addAlign('center')->addNlCr()
                     ->addModel('BinID')->addWidth(50)->addAlign('center')->addNlCr()
                     ->addModel('Reason')->addWidth(50)->addAlign('center')->addNlCr()
                     ->addModel('Comments')->addWidth(50)->addAlign('center')->addNlCr()
                     ->addModel('Date')->addWidth(50)->addAlign('center')->addNlCr()
                     ->addModel('WH')->addWidth(50)->addAlign('center')->addNlCr()
                     ->addModel('Location')->addWidth(50)->addAlign('center')->addNlCr();
    
        $data['colModel'] = $this->common->getModel();

        //Generar el contenido....
       $this->build_content($data);
       $this->render_page();
    }

    function getData() {
        $page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1; // get the requested page
        $limit = isset($_REQUEST['rows']) ? $_REQUEST['rows'] : 10; // get how many rows we want to have into the gri
        $sidx = isset($_REQUEST['sidx']) ? $_REQUEST['sidx'] : 'BH.[ID]'; // get index row - i.e. user click to sort
        $sord = isset($_REQUEST['sord']) ? $_REQUEST['sord'] : 'desc';

       // $dataSearch = isset($_REQUEST['ds']) ? $_GET['ds'] : '';


    //Corregimos las fechas para ajustar el tiempo
   // $InitialDateFixed = $this->MCommon->fixInitialDate($InitialDate, 1);


        $search = $this->input->get('search');
        $datefrom = $this->input->get('from');
        $dateto = $this->input->get('to');
        $flow= $this->input->get('flow');

        $InitialDate = isset($from) ? $datefrom : $this->MCommon->lastweek();
        $FinalDate = isset($to) ?  $dateto  : date("m/d/Y");
    
        $FinalDateFixed = $this->MCommon->fixFinalDate($FinalDate, 1);

        $table = ' [Inventory].[dbo].[Bins_History] AS BH ';
        $where = '';
        $from = ' FROM ';
        $join = ' LEFT OUTER JOIN [Inventory].[dbo].[Users] AS US ON (BH.[User_Id] = US.[ID])
                  LEFT OUTER JOIN [Inventory].[dbo].All_List AS LS ON (BH.ScanCode = LS.listvalue)
                  LEFT OUTER JOIN [Inventory].[dbo].[Bins] AS B ON (BH.Bin_Id = B.Bin_Id) ';
        $orderby= ' ORDER BY Stamp DESC';
	
	

       // $selectCount = 'Select count(distinct ID) as rowNum ';

	
        $select = " SELECT BH.[ID] AS [TrID]
                     ,US.[FullName] AS [User]
                     ,Case WHEN BH.[Flow] = 1 Then 'Added' WHEN BH.[Flow] = 2 Then 'Removed' End AS [Movement]
                     ,BH.[Qty] AS [Qty]
                     ,BH.[Product_Catalog_ID] AS [SKU]
                     ,BH.[Bin_Id] AS [BinID]      
                     ,LS.[listdisplay] AS [Reason]
                     ,BH.[Comments] AS [Comments]
                     ,BH.[Stamp] AS [Date]
                     ,B.[WarehouseID] As [WH]
                     ,B.[Location] As [Location]
		    ";

        $selectSlice = "SELECT TrID
                        ,[User]
		                ,Movement
                        ,Qty
                        ,SKU
                        ,BinID
                        ,Reason
                        ,Comments
		                ,Date
                        ,WH
                        ,Location";

        $wherefields = array('BH.[ID]', 'US.[FullName]', 'BH.[Flow]', 'BH.[Qty]','BH.[Product_Catalog_ID]','BH.[Bin_Id]','LS.[listdisplay]','BH.[Comments]','B.[WarehouseID]','B.[Location]');

        $where .=$this->MCommon->concatAllWerefields($wherefields, $search);


        $where.= " and CONVERT(datetime,BH.Stamp,1) between '{$datefrom}' and '{$dateto} 23:59:29'";

        if ($flow){
            $where .= " and BH.[Flow] = '{$flow}' ";
        }

        $SQL = "{$select}{$from}{$table}{$join}{$where}";


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
                                FROM {$table}{$join}{$where})
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
            $responce->rows[$i]['id'] = $row['TrID'];
            $responce->rows[$i]['cell'] = array($row['TrID'],
                $row['User'],
                $row['Movement'],
                $row['Qty'],
                $row['SKU'],
                $row['BinID'],
		        $row['Reason'],
                $row['Comments'],
                $row['Date'],
                $row['WH'],
                $row['Location'],
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

    function adjustemenIDData() {
        $this->load->model('morders', '', TRUE);
        $adjustmentID = isset($_REQUEST['aid']) ? $_GET['aid'] : '';
        $page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1; // get the requested page
        $limit = isset($_REQUEST['rows']) ? $_REQUEST['rows'] : 10; // get how many rows we want to have into the gri
        $sidx = isset($_REQUEST['sidx']) ? $_REQUEST['sidx'] : 'InventoryAdjustmentsID'; // get index row - i.e. user click to sort
        $sord = isset($_REQUEST['sord']) ? $_REQUEST['sord'] : 'Desc';


        $totalrows = isset($_REQUEST['totalrows']) ? $_REQUEST['totalrows'] : false;
        if ($totalrows) {
            $limit = $totalrows;
        }

        if (!$sidx)
            $sidx = 1;


        $SQL = "SELECT ProductCatalogID,
		       ProductName,
                       Quantity,
                       UserName,
		       Comments
		      FROM [Inventory].[dbo].[vw_InventoryDetails]
			where  InventoryAdjustmentsID = {$adjustmentID} 
		    ";


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
            $responce->rows[$i]['id'] = $row['ProductCatalogID'];
            $responce->rows[$i]['cell'] = array($row['ProductCatalogID'],
             
		$row['ProductName'],
                $row['Quantity'],
                $row['UserName'] = utf8_encode($row['UserName']),
		$row['Comments'] = utf8_encode($row['Comments']),
            );
            $i++;
        }


        echo json_encode($responce);
    }

}
?>




