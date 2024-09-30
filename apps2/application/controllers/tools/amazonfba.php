<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Amazonfba extends BP_Controller {

    public function __construct() {
        parent::__construct();

        $is_logged_in = $this->session->userdata('is_logged_in');

        if (!isset($is_logged_in) || $is_logged_in != true) {
            redirect(base_url(), 'refresh');
        }

        if (($this->MUsers->isValidUser($this->session->userdata('userid'), 884100) != 1) and
                ($this->MUsers->isValidUser($this->session->userdata('userid'), 884200) != 1)) {
            redirect('Catalog/prodcat', 'refresh');
        }
    }

    public function index() {
        $user = 0;
        if ($this->MUsers->isValidUser($this->session->userdata('userid'), 884100) == 1) {
            $user = TRUE;
        }



        // Define Meta
        $this->title = "MI Technologiesinc - Amazon FBA";

        $this->description = "Amazon FBA";


        // Define custom CSS
        $this->css = array('menu.css', 'form.css', 'fluid.css', 'jqueryui/redmond/jquery-ui-1.8.21.custom.css', 'jqgrid/ui.jqgrid.css', 'ventanas-modales.css');

        // Define custom javascript
        $this->javascript = array('jqueryui/ui/jquery-ui-1.8.21.custom.js', 'jqgrid/i18n/grid.locale-en.js', 'jqgrid/jquery.jqGrid.min.js', 'jqgrid/jquery.jqGrid.fluid.js', 'jqgrid/grid.base.js', 'jqgrid/grid.common.js', 'jqgrid/grid.formedit.js', 'libs/jquery-barcode-2.0.2.min.js');
        //Cargamos la libreria donde esta el menu
        $this->load->library('Layout');
        //Creamos un nuevo layout->menu
        $menu = new Layout;

        $data = array(
            'menu' => $menu->show_menu($this->MUsers->getUserFullName($this->session->userdata('userid'))),
            'from' => '/tools/amazonfba/',
            'nameGrid' => 'amazonfba',
            'namePager' => 'amazonfbaPager',
            'caption' => 'Amazon FBA',
            'user' => $user,
            'subgrid' => 'true',
            'sortorder' => 'asc',
            'sortname' => 'mitsku',
            'search' => '',
            'orderOptions' => Array(0 => "Products", 1 => "Pending Orders", 2 => "Complete Orders"),
            'selectedorder' => 0,
            'multiselect' => 'false',
            'datefrom' => $this->MCommon->lastweek(),
            'dateto' => date("m/d/Y"),
            'export' => 'exportfba',
            'customerid' => $this->MUsers->getCustomerId($this->session->userdata('userid')),
            'channels' => $this->MCatalog->fillAmazonFBAChannelName(),
            'selectechannel' => '',
        );

        //Cargamos la libreria comumn
        $this->load->library('common');

        //Reemplazamos los campos del formulario por los que estan en el arreglo $_POST.
        $data = $this->common->fillPost('ALL', $data);

        if ($data['selectedorder']==1){
            $data['multiselect'] = 'true';
        }
        
        
        //Products
        //Channel MerchantSKU ASIN MITSKU BulbSKU FNSKU 
        //Pending Orders
        //OrderID Channel MITSKU MerchantSKU ASIN FNSKU OrderQTY OrderNotes
        //Complete Orders
        // OrderID Channel MITSKU ASIN MerchantSKU FNSKU OrderQTY OrderNotes OrderDate CompletedDate


        switch ($data['selectedorder']) {
            case 0:
                //Cargamos al arreglo los colnames
                $data['colNames'] = "['Id','Channel','MerchantSKU','ASIN','MITSKU','BulbSKU', 'FNSKU','Title']";

                //Cargamos al arreglo los colmodels
                $data['colModel'] = "[
                {name:'Id',index:'Id', width:70, align:'left', hidden:true},          
                {name:'Channel',index:'Channel', width:70, align:'left'},     
                {name:'MerchantSKU',index:'MerchantSKU', width:55, align:'center'},
                {name:'ASIN',index:'ASIN', width:55, align:'center',sortable:true},
                {name:'MITSKU',index:'MITSKU', width:70, align:'left', editable:true}, 
                {name:'BulbSKU',index:'BulbSKU', width:70},
                {name:'FNSKU',index:'FNSKU', width:70, align:'left'},
                {name:'Title',index:'Title', width:300, align:'left'}]";


                //cadena de busqueda del grid 
                $data['gridSearch'] = 'dataProductsFBA?ds=' . $data['search']  . '&df=' . $data['datefrom'] . '&dt=' . $data['dateto'];
                $data['edit'] = 'editAsin';
                break;

            case 1:
                //OrderID Channel MITSKU MerchantSKU ASIN FNSKU QOH VQOH OrderQTY OrderNotes
                 
                $data['sortname'] = 'orderdate'; 
                $data['sortorder'] = 'desc';
                $data['colNames'] = "['OrderID','Channel','MITSKU','BulbSKU','MerchantSKU','ASIN','BrandMentioned','FNSKU','QOH','VQOH','OrderQTY','OrderNotes','OrderDate', 'Action']";
                //Cargamos al arreglo los colmodels
                $data['colModel'] = "[
                {name:'OrderID',index:'OrderID', width:60, align:'center'},
                {name:'Channel',index:'Channel', width:60, align:'left'},
                {name:'MITSKU',index:'MITSKU', width:60, align:'center'},
                {name:'BulbSKU',index:'BulbSKU', width:70, align:'center'},
                {name:'MerchantSKU',index:'MerchantSKU', width:80, align:'center'}, 
                {name:'ASIN',index:'ASIN', width:60, align:'center'},
                {name:'BrandMentioned',index:'BrandMentioned', width:70, align:'center'},
                {name:'FNSKU',index:'FNSKU', width:60, align:'center'},
                {name:'GlobalStock',index:'GlobalStock', width:60, align:'center', formatter:'number', formatoptions:{decimalSeparator:'.',thousandsSeparator: ',', decimalPlaces: 2, prefix: '' }},
                {name:'VirtualStock',index:'VirtualStock', width:60, align:'center', formatter:'number', formatoptions:{decimalSeparator:'.',thousandsSeparator: ',', decimalPlaces: 2, prefix: '' }},
                {name:'OrderQTY',index:'OrderQTY', width:70, align:'center', editable:true},
                {name:'OrderNotes',index:'OrderNotes', width:200, align:'center', editable:true},
                {name:'OrderDate',index:'OrderDate', width:50, align:'center', formatter:'date',formatoptions: {newformat:'M j, Y'}},
                {name:'Action',index:'Action', width:50, align:'center',formatter: printFnSku}]";

                //cadena de busqueda del grid 
                $data['gridSearch'] = 'dataPendingOrderFBA?ds=' . $data['search']  . '&df=' . $data['datefrom'] . '&dt=' . $data['dateto'];
                $data['edit'] = 'editOrder';
                break;
            case 2:
                //Cargamos al arreglo los colnames
                $data['sortname'] = 'OrderDate';
                $data['sortorder'] = 'desc';
                //Cargamos al arreglo los colnames
                $data['colNames'] = "['OrderID','Channel','MITSKU','BulbSKU','MerchantSKU','ASIN','BrandMentioned','FNSKU','OrderQTY','OrderNotes', 'OrderDate','CompletedDate','TrackingNumber','Completed','Action']";
                //Cargamos al arreglo los colmodels
                $data['colModel'] = "[
                {name:'OrderID',index:'OrderID', width:60, align:'center'},
                {name:'Channel',index:'Channel', width:60, align:'center'},
                {name:'MITSKU',index:'MITSKU', width:60, align:'center'},
                {name:'BulbSKU',index:'BulbSKU', width:70, align:'center'},
                {name:'MerchantSKU',index:'MerchantSKU', width:70, align:'left'}, 
                {name:'ASIN',index:'ASIN', width:60, align:'left'},
                {name:'BrandMentioned',index:'BrandMentioned', width:70, align:'center'},
                {name:'FNSKU',index:'FNSKU', width:60, align:'left'},
                {name:'OrderQTY',index:'OrderQTY', width:60, align:'center'},
                {name:'OrderNotes',index:'OrderNotes', width:80, align:'center'},
                {name:'OrderDate',index:'OrderDate', width:80, align:'center',formatter:'date',formatoptions: {newformat:'M j, Y'}}, 
                {name:'CompletedDate',index:'CompletedDate', width:80, align:'center',formatter:'date',formatoptions: {newformat:'M j, Y'}},
                {name:'TrackingNumber',index:'TrackingNumber', width:80, align:'left'},
                {name:'Completed',index:'Completed', width:50, align:'center'},
                {name:'Action',index:'Action', width:50, align:'center',formatter: printticket}]";

                //cadena de busqueda del grid 
                $data['gridSearch'] = 'dataCompleteOrderFBA?ds=' . $data['search'] . '&df=' . $data['datefrom'] . '&dt=' . $data['dateto'];
                $data['edit'] = 'editOrder';
                break;
        }




        //Generar el contenido....
        $this->build_content($data);
        $this->render_page();
    }

    function dataProductsFBA() {
        $page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1; // get the requested page
        $limit = isset($_REQUEST['rows']) ? $_REQUEST['rows'] : 10; // get how many rows we want to have into the gri
        $sidx = isset($_REQUEST['sidx']) ? $_REQUEST['sidx'] : 'a.mitsku'; // get index row - i.e. user click to sort
        $sord = isset($_REQUEST['sord']) ? $_REQUEST['sord'] : 'asc';

        $dataSearch = isset($_REQUEST['ds']) ? $_GET['ds'] : '';
        $status = '2';
        $cartid = isset($_REQUEST['caid']) ? $_GET['caid'] : '0';
        $customerid = isset($_GET['cuid']) ? $_GET['cuid'] : '';
        $InitialDate = isset($_REQUEST['df']) ? $_GET['df'] : $this->MCommon->lastweek();
        $FinalDate = isset($_REQUEST['dt']) ? $_GET['dt'] : date("m/d/Y");
	
	   


        if (!$sidx)
            $sidx = 1;

        $table = '[Inventory].[dbo].AmazonFBA a';
        $table1 = '[Inventory].[dbo].ProductCatalog b';

        $where = '';
        $from = ' FROM ';
        $join = ' LEFT JOIN ' . $table1 . ' ON a.MITSKU=b.ID  ';

 
        
        $select = "SELECT a.[Id],a.[Channel], a.[MerchantSKU], a.[ASIN], a.[MITSKU], b.[CustomField04] AS BulbSKU, a.[FNSKU], a.[Title] ";


        $selectSlice = "SELECT Id,
                               Channel,
                               MerchantSKU,
                               ASIN,
                               MITSKU,
                               BulbSKU,
                               FNSKU,
                               Title";

        $wherefields = array('a.Channel', 'a.MerchantSKU','a.ASIN', 'b.CustomField04', 'a.FNSKU', 'a.Title');

        $where .=$this->MCommon->concatAllWerefields($wherefields, $dataSearch);
	

        $SQL = "{$select}{$from}{$table}{$join}{$where}";

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

        $SQL = "WITH mytable AS ({$select}, ROW_NUMBER() OVER (ORDER BY {$sidx} {$sord}) AS RowNumber
                FROM {$table}{$join}{$where} )
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
            $responce->rows[$i]['id'] = $row['Id'];
            $responce->rows[$i]['cell'] = array($row['Id'],
                $row['Channel'] =utf8_encode($row['Channel']),
                $row['MerchantSKU']= utf8_encode($row['MerchantSKU']),
                $row['ASIN']= utf8_encode($row['ASIN']),
                $row['MITSKU']= utf8_encode($row['MITSKU']),
                $row['BulbSKU']= utf8_encode($row['BulbSKU']),
                $row['FNSKU']= utf8_encode($row['FNSKU']),
                $row['Title']= utf8_encode($row['Title']),
            );
            $i++;
        }



        echo json_encode($responce);
    }

    function dataPendingOrderFBA() {
        $page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1; // get the requested page
        $limit = isset($_REQUEST['rows']) ? $_REQUEST['rows'] : 10; // get how many rows we want to have into the gri
        $sidx = isset($_REQUEST['sidx']) ? $_REQUEST['sidx'] : 'orderdate'; // get index row - i.e. user click to sort
        $sord = isset($_REQUEST['sord']) ? $_REQUEST['sord'] : 'asc';

        $dataSearch = isset($_REQUEST['ds']) ? $_GET['ds'] : '';
        $status = '2';
        $cartid = isset($_REQUEST['caid']) ? $_GET['caid'] : '0';
        $customerid = isset($_GET['cuid']) ? $_GET['cuid'] : '';
        $InitialDate = isset($_REQUEST['df']) ? $_GET['df'] : $this->MCommon->lastweek();
        $FinalDate = isset($_REQUEST['dt']) ? $_GET['dt'] : date("m/d/Y");


	$InitialDateFixed = $this->MCommon->fixInitialDate($InitialDate, 1);
	$FinalDateFixed = $this->MCommon->fixFinalDate($FinalDate, 1);
      

        if (!$sidx)
            $sidx = 1;


        $table1 = 'Inventory.dbo.AmazonFBAOrders amfo';
        $table2 = 'Inventory.dbo.AmazonFBA amf ';
        $table3 = 'Inventory.dbo.Amazon am ';
        $table4 = 'Inventory.dbo.ProductCatalog pc ';
        $table5 = 'Inventory.dbo.Global_Stocks gs';
        $from   = ' FROM '.$table1;
        $join   = ' LEFT JOIN Inventory.dbo.AmazonFBA amf ON amfo.MerchantSKU = amf.MerchantSKU
                    LEFT JOIN Inventory.dbo.ProductCatalog pc ON amf.MITSKU = pc.ID
                    LEFT JOIN Inventory.dbo.Global_Stocks gs ON pc.ID = gs.ProductCatalogId
                    LEFT JOIN Inventory.dbo.Amazon am ON amfo.ASIN = am.ASIN AND am.manufacturer = pc.Manufacturer';
        $where  = '';

        $select = 'SELECT amfo.[OrderID],
                          amf.[Channel],
                          amf.[MITSKU],
                          CONVERT(NVARCHAR,pc.[CustomField04]) AS BulbSKU,
                          amfo.[MerchantSKU],
                          amf.[ASIN],
                          am.[BrandMentioned],
                          amf.[FNSKU],
                          gs.[GlobalStock],
                          gs.[VirtualStock],
                          amfo.[OrderQty],
                          amfo.[OrderNotes],
                          Cast(amfo.[OrderDate] as date) as OrderDate';

        $selectSlice = 'SELECT  OrderID,
                                Channel,
                                MITSKU,
                                BulbSKU,
                                MerchantSKU,
                                ASIN,
                                BrandMentioned,
                                FNSKU,
                                GlobalStock,
                                VirtualStock,
                                OrderQty,
                                OrderNotes,
                                OrderDate';

        $wherefields = array('amfo.[OrderID]','amf.[Channel]','amf.[MITSKU]','CONVERT(NVARCHAR,pc.[CustomField04])', 'amfo.[MerchantSKU]','amf.[ASIN]','am.[BrandMentioned]','amf.[FNSKU]','gs.[GlobalStock]','gs.[VirtualStock]','amfo.[OrderQty]','amfo.[OrderNotes]','amfo.[OrderDate]');

        $where .=$this->MCommon->concatAllWerefields($wherefields, $dataSearch);

        $where .= " and (amfo.Completed='No') ";
	
        $SQL = "{$select}{$from}{$join}{$where}";

        $GroupBy = " GROUP BY  amfo.[OrderID],
                          amf.[Channel],
                          amf.[MITSKU],
                          CONVERT(NVARCHAR,pc.[CustomField04]),
                          amfo.[MerchantSKU],
                          amf.[ASIN],
                          am.[BrandMentioned],
                          amf.[FNSKU],
                          gs.[GlobalStock],
                          gs.[VirtualStock],
                          amfo.[OrderQty],
                          amfo.[OrderNotes],
                          amfo.[OrderDate] ";
       
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
                {$from}{$join}{$where} {$GroupBy}) 
                    {$selectSlice}, RowNumber
               FROM mytable
                WHERE RowNumber BETWEEN {$start} AND {$finish}";


        
        $result = $this->MCommon->getSomeRecords($SQL);
        //print_r($SQL);
       
        $responce = new stdClass();
        $responce->page = $page;
        $responce->total = $total_pages;
        $responce->records = $count;
        $i = 0;
        
        
        foreach ($result as $row) {
            $responce->rows[$i]['id'] = $row['OrderID'];
            $responce->rows[$i]['cell'] = array($row['OrderID']= utf8_encode($row['OrderID']),
                $row['Channel']= utf8_encode($row['Channel']),
                $row['MITSKU']= utf8_encode($row['MITSKU']),
                $row['BulbSKU']= utf8_encode($row['BulbSKU']),
                $row['MerchantSKU']= utf8_encode($row['MerchantSKU']),
                $row['ASIN']= utf8_encode($row['ASIN']),
                $row['BrandMentioned']= utf8_encode($row['BrandMentioned']),
                $row['FNSKU']= utf8_encode($row['FNSKU']),
                $row['GlobalStock']= utf8_encode($row['GlobalStock']),
                $row['VirtualStock']= utf8_encode($row['VirtualStock']),
                $row['OrderQty']= utf8_encode($row['OrderQty']),
                $row['OrderNotes']= utf8_encode($row['OrderNotes']),
                $row['OrderDate']= utf8_encode($row['OrderDate']),
            );
            $i++;
        }

        echo json_encode($responce);
    }

    function dataCompleteOrderFBA() {
        $page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1; // get the requested page
        $limit = isset($_REQUEST['rows']) ? $_REQUEST['rows'] : 10; // get how many rows we want to have into the gri
        $sidx = isset($_REQUEST['sidx']) ? $_REQUEST['sidx'] : 'orderdate'; // get index row - i.e. user click to sort
        $sord = isset($_REQUEST['sord']) ? $_REQUEST['sord'] : 'asc';

        $dataSearch = isset($_REQUEST['ds']) ? $_GET['ds'] : '';
        $status = '2';
        $cartid = isset($_REQUEST['caid']) ? $_GET['caid'] : '0';
        $customerid = isset($_GET['cuid']) ? $_GET['cuid'] : '';
        $InitialDate = isset($_REQUEST['df']) ? $_GET['df'] : $this->MCommon->lastweek();
        $FinalDate = isset($_REQUEST['dt']) ? $_GET['dt'] : date("m/d/Y");


		//Corregimos las fechas para ajustar el tiempo
	$InitialDateFixed = $this->MCommon->fixInitialDate($InitialDate, 1);
	$FinalDateFixed = $this->MCommon->fixFinalDate($FinalDate, 1);
	

        if (!$sidx)
            $sidx = 1;

         $table1 = 'Inventory.dbo.AmazonFBAOrders amfo';
        $table2 = 'Inventory.dbo.AmazonFBA amf ';
        $table3 = 'Inventory.dbo.Amazon am ';
        $table4 = 'Inventory.dbo.ProductCatalog pc ';
        $table5 = 'Inventory.dbo.Global_Stocks gs';
        $from   = ' FROM '.$table1;
        $join   = ' LEFT JOIN Inventory.dbo.AmazonFBA amf ON amfo.MerchantSKU = amf.MerchantSKU
                    LEFT JOIN Inventory.dbo.ProductCatalog pc ON amf.MITSKU = pc.ID
                    LEFT JOIN Inventory.dbo.Global_Stocks gs ON pc.ID = gs.ProductCatalogId
                    LEFT JOIN Inventory.dbo.Amazon am ON amfo.ASIN = am.ASIN AND am.manufacturer = pc.Manufacturer';
        $where  = '';

        $select = 'SELECT amfo.[OrderID],
                          amf.[Channel],
                          amf.[MITSKU],
                          CONVERT(NVARCHAR,pc.[CustomField04]) AS BulbSKU,
                          amfo.[MerchantSKU],
                          amf.[ASIN],
                          am.[BrandMentioned],
                          amf.[FNSKU],
                          amfo.[OrderQty],
                          amfo.[OrderNotes],
                          Cast(amfo.[OrderDate] as date) as OrderDate,
                          Cast(amfo.[CompletedDate] as date) as CompletedDate,
                          amfo.[TrackingNumber],
                          amfo.[Completed] ';

        $selectSlice = 'SELECT  OrderID,
                                Channel,
                                MITSKU,
                                BulbSKU,
                                MerchantSKU,
                                ASIN,
                                BrandMentioned,
                                FNSKU,
                                OrderQty,
                                OrderNotes,
                                OrderDate,
                                CompletedDate,
                                TrackingNumber,
                                Completed ';

        $wherefields = array('amfo.[OrderID]','amf.[Channel]','amf.[MITSKU]','CONVERT(NVARCHAR,pc.[CustomField04])', 'amfo.[MerchantSKU]','amf.[ASIN]','am.[BrandMentioned]','amf.[FNSKU]','gs.[GlobalStock]','gs.[VirtualStock]','amfo.[OrderQty]','amfo.[OrderNotes]','amfo.[OrderDate]');

        $where .=$this->MCommon->concatAllWerefields($wherefields, $dataSearch);

        $where.= " and (amfo.CompletedDate between '{$InitialDateFixed}' and '{$FinalDateFixed}')";
        $where .= " and ((amfo.Completed)='Yes') ";

	
        $SQL = "{$select}{$from}{$join}{$where}";

        $GroupBy = " GROUP BY  amfo.[OrderID],
                          amf.[Channel],
                          amf.[MITSKU],
                          CONVERT(NVARCHAR,pc.[CustomField04]),
                          amfo.[MerchantSKU],
                          amf.[ASIN],
                          am.[BrandMentioned],
                          amf.[FNSKU],
                          amfo.[OrderQty],
                          amfo.[OrderNotes],
                          amfo.[OrderDate],
                          amfo.[CompletedDate],
                          amfo.[TrackingNumber],
                          amfo.[Completed] ";
       
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
                {$from}{$join}{$where} {$GroupBy}) 
                    {$selectSlice}, RowNumber
               FROM mytable
                WHERE RowNumber BETWEEN {$start} AND {$finish}";


        
        $result = $this->MCommon->getSomeRecords($SQL);
        //print_r($SQL);
       
        $responce = new stdClass();
        $responce->page = $page;
        $responce->total = $total_pages;
        $responce->records = $count;
        $i = 0;
        
        
        foreach ($result as $row) {
            $responce->rows[$i]['id'] = $row['OrderID'];
            $responce->rows[$i]['cell'] = array($row['OrderID'],
                $row['Channel'],
                $row['MITSKU'],
                $row['BulbSKU'],
                $row['MerchantSKU'],
                $row['ASIN'],
                $row['BrandMentioned'],
                $row['FNSKU'],
                $row['OrderQty'],
                $row['OrderNotes'],
                $row['OrderDate'],
                $row['CompletedDate'],
                $row['TrackingNumber'],
                $row['Completed'],
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

    function showTicket() {

        // Define custom CSS
        $this->css = array('table.css', 'fluid.css');

        $this->javascript = array('jqueryui/ui/jquery.ui.widget.js', 'jqueryui/ui/jquery.ui.tabs.js', 'jqgrid/jquery.jqGrid.fluid.js');

        $this->hasNav = false;
        $this->hasFooter = false;

        $bs = isset($_REQUEST['bs']) ? $_REQUEST['bs'] : 'No BuilSKU'; // get the requested page
        $as = isset($_REQUEST['as']) ? $_REQUEST['as'] : 'NO ASIN'; // get how many rows we want to have into the gri
        $fn = isset($_REQUEST['fn']) ? $_REQUEST['fn'] : 'No FNSKU'; // get index row - i.e. user click to sort
        $des = isset($_REQUEST['des']) ? $_REQUEST['des'] : 'No description';


        $data = array(
            'buildsku' => $bs,
            'asin' => $as,
            'snsku' => $fn,
            'description' => $des,
            'title' => "Print Ticket",
            'from' => 'tools/amazonfba/',
            'printTicket' => 'printTicket?bs=' . $bs . '&as=' . $as . '&fn=' . $fn . '&des=' . $des,
        );

        $this->load->view("tickets/showticket", $data);
    }



    function showFnSku() {

        // Define custom CSS
        $this->css = array('table.css', 'fluid.css');

        $this->javascript = array('jqueryui/ui/jquery.ui.widget.js', 'jqueryui/ui/jquery.ui.tabs.js', 'jqgrid/jquery.jqGrid.fluid.js');

        $this->hasNav = false;
        $this->hasFooter = false;
        $fn = isset($_REQUEST['fn']) ? $_REQUEST['fn'] : 'No FNSKU'; // get index row - i.e. user click to sort
       
        $data = array(
            'fnsku' => $fn,
            'title' => "Print Ticket",
            'from' => 'tools/amazonfba/',
            'printTicket' => 'printTicket?bs='. '&fn=' . $fn ,
        );

        $this->load->view("tickets/showfnsku", $data);
    }


    function partnumber() {
        $q = strtolower($_GET["q"]);
        if (!$q)
            return;

        $sql = 'select distinct compatibility.partnumber as value from [Inventory].[dbo].compatibility';

        $result = mssql_query($sql); //query the database for entries containing the term

        while ($row = mssql_fetch_array($result, MYSQL_ASSOC)) {//loop through the retrieved values
            $row['value'] = htmlentities(stripslashes($row['value']));
            $items[$row['value']] = $row['value']; //build an array
        }


        foreach ($items as $key => $value) {
            if (strpos(strtolower($key), $q) !== false) {
                echo "$key|$value\n";
            }
        }
    }

    function description() {
        $q = strtolower($_GET["q"]);
        if (!$q)
            return;

        $sql = "select AmazonFBA.Title from [Inventory].[dbo].AmazonFBA where (AmazonFBA.ASIN like '%{$asin}%'))";

        $result = mssql_query($sql); //query the database for entries containing the term

        while ($row = mssql_fetch_array($result, MYSQL_ASSOC)) {//loop through the retrieved values
            $row['value'] = htmlentities(stripslashes($row['value']));
            $items[$row['value']] = $row['value']; //build an array
        }


        foreach ($items as $key => $value) {
            if (strpos(strtolower($key), $q) !== false) {
                echo "$key|$value\n";
            }
        }
    }

    function FillPartNumber() {
        $SQL = "SELECT distinct PartNumber FROM [Inventory].[dbo].[Compatibility] where PartNumber LIKE '%{$_GET['term']}%'";
        echo $SQL;
        echo json_encode($this->MCommon->fillAutoComplete($SQL, 'PartNumber'));
    }

    function getSKU() {

        $SQL = "SELECT distinct ProductCatalogId From [Inventory].[dbo].Compatibility WHERE Partnumber = '{$_POST['pn']}'";
        //  echo $SQL;
        echo $this->MCommon->fillCombo($SQL, 'ProductCatalogId');
    }

    function getDescription() {
        $SQL = "SELECT title from [Inventory].[dbo].AmazonFBA WHERE ASIN like '%{$_POST['pa']}%'";
        $return = $this->MCommon->getOneRecord($SQL);
        echo ($return['title']);
    }
    
    function validateAsin() {
        $SQL = "SELECT COUNT(ASIN) as qty FROM [Inventory].[dbo].AmazonFBA WHERE ASIN = '{$_POST['av']}' AND Channel = '{$_POST['cs']}' ";
        //print_r($SQL);
        $return = $this->MCommon->getOneRecord($SQL);
        echo $return['qty'];
    }

    function deleteAsin() {
        $SQL = "DELETE FROM [Inventory].[dbo].AmazonFBA WHERE ASIN = '{$_POST['da']}'";

        $this->MCommon->executeQuery($SQL,'InventorySave');
    }

    function saveAsin() {

        $SQL = "insert into [Inventory].[dbo].AmazonFBA (MITSKU, Channel, MerchantSKU,ASIN,FNSKU,Title) 
            Values 
            ({$_POST['skusend']},'{$_POST['channel']}','{$_POST['MerchanSKU']}','{$_POST['Asin']}','{$_POST['FNSKU']}','{$_POST['Description']}')";

        $this->MCommon->saveRecord($SQL,'InventorySave');
    }
    
    

    function saveOrder() {
        $SQL = "insert into [Inventory].[dbo].AmazonFBAOrders (MerchantSKU,ASIN, OrderQty,OrderNotes,OrderDate,Completed) 
            Values 
            ('{$_POST['OAMS']}','{$_POST['OAASIN']}','{$_POST['OQTY']}','{$_POST['ON']}','{$_POST['OD']}','{$_POST['Completed']}')";

        $this->MCommon->saveRecord($SQL,'InventorySave');
    }

    function editOrder(){
        $order = $this->input->post('id');
        $OrderQty= $this->input->post('OrderQTY');
        $OrderNotes= $this->input->post('OrderNotes');

        $SQL = "update [Inventory].[dbo].AmazonFBAOrders set 
                OrderQty = $OrderQty,
                OrderNotes = '{$OrderNotes}'
                where orderid = $order";

       $this->MCommon->executeQuery($SQL,'InventorySave');
    }

    function editAsin(){

        $mitsku = $this->input->post('MITSKU');
        $id = $this->input->post('id');
        
        $SQL = "update [Inventory].[dbo].AmazonFBA set 
                MITSKU = $mitsku
                where id = $id";

        $this->db->query($SQL);
    }



    function deleteOrder() {
        $SQL = "DELETE FROM [Inventory].[dbo].AmazonFBAOrders WHERE orderid = '{$_POST['order']}'";

        $this->MCommon->executeQuery($SQL,'InventorySave');
    }

    function deletePendingOrder() {

        $orders = $this->input->post('orders');

        foreach ($orders as $order) {
            $SQL = "DELETE FROM [Inventory].[dbo].AmazonFBAOrders WHERE orderid = {$order}";
            $this->MCommon->executeQuery($SQL,'InventorySave');
        }
    }


    function completeOrder() {
        $today = date("m/d/Y H:i:s");
        $orders = $this->input->post('orders');

        foreach ($orders as $order) {
           $SQL = "update [Inventory].[dbo].AmazonFBAOrders set completeddate='{$today}',completed='Yes' where orderid={$order}";
           $this->MCommon->executeQuery($SQL,'InventorySave');
        }

    }

     function returnOrder() {
        $today = date("m/d/Y H:i:s");
        $SQL = "update [Inventory].[dbo].AmazonFBAOrders set completeddate='{$today}',completed='No' where orderid={$_POST['order']}";

        $this->MCommon->executeQuery($SQL,'InventorySave');
    }

    function getStore($store) {

        switch ($store) {
            case 'LMD':
                $where = ' and ((a.asin LIKE "LMD%") or (a.asin LIKE "LDB%"))';
                break;
            case 'BW':
                $where = ' and ((a.asin LIKE "BW%") or (a.asin LIKE "BWR%"))';
                break;
            case 'MFB':
                $where = ' and ((a.asin LIKE "MFB%") or (a.asin LIKE "MTT%") or (a.asin LIKE "MTB%"))';
                break;
            default:
                $where = ' and (a.asin Like  "' . $store . '%") ';
                break;
        }
        return $where;
    }

    function printticket() {
        echo '<script>  
            $(document).ready(function() {
                $("#barcode").click();
                $("#PrintTicket").click(function (){
                    $("#ticket").jqprint();
                })
            });
            
        </script> ';
        echo '<div id="ticket" class="layer">';
        echo '   <p>';
        echo '       <b class="hide">Build SKU:</b> <span id="bulidsku" class="hide">' . $_POST['mitsku'] . '</span></br>';
        echo '   <hr class="hide">';
        echo '   <b>Amazon ASIN:</b>' . $_POST['asin'] . '</br>';
        echo '   <div id="bcTarget"></div></br> ';
        echo '  ' . $_POST['title'] . '</br>';
        echo '</p>';
        echo '</div>';
        echo '<input id="barcode" type="hidden" onclick=$("#bcTarget").barcode("' . $_POST['fnsku'] . '", "code128",{barWidth:2, barHeight:30}); value="Test"/>';
    }


    function printFnkuTicket() {
        echo '<script>  
            $(document).ready(function() {
                $("#barcode").click();
                $("#PrintTicket").click(function (){
                    $("#ticket").jqprint();
                })
            });
            
        </script> ';
        echo '<div id="ticket" class="layer">';
        echo '   <p>';
        echo '       <b class="hide">Build SKU:</b> <span id="bulidsku" class="hide">' . $_POST['mitsku'] . '</span></br>';
        echo '   <hr class="hide">';
        echo '   <b>Amazon ASIN:</b>' . $_POST['asin'] . '</br>';
        echo '   <div id="bcTarget"></div></br> ';
        echo '  ' . $_POST['title'] . '</br>';
        echo '</p>';
        echo '</div>';
        echo '<input id="barcode" type="hidden" onclick=$("#bcTarget").barcode("' . $_POST['fnsku'] . '", "code128",{barWidth:2, barHeight:30}); value="Test"/>';
    }


}
?>
