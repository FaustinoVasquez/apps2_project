<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Receivedinventory extends BP_Controller {

    public function __construct() {
        parent::__construct();

        $is_logged_in = $this->session->userdata('is_logged_in');

        if (!isset($is_logged_in) || $is_logged_in != true) {
            redirect(base_url(), 'refresh');
        }
        
        // if ($this->MUsers->isValidUser($this->session->userdata('userid'), 883950) != 1) {
        //        redirect('Catalog/prodcat', 'refresh');
        //    }
    }

    public function index() {
        //Cargamos la base de datos de OrderManager
        $this->load->model('MOrders', '', TRUE);

        // Define Meta
        $this->title = "MI Technologiesinc - Adjustment Details";

        $this->description = "Received Inventory";

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
            'from' => '/Reports/receivedinventory/',
            'nameGrid' => 'receivedinventory',
            'namePager' => 'receivedinventorypager',
            'caption' => 'Received Inventory',
            'subgrid' => 'true',
            'sort' => 'desc',
            'search' => '',
            'datefrom' => $this->MCommon->lastweek(),
            'dateto' => date("m/d/Y"),
            'export' => 'Received',
            'customerid' => $this->MUsers->getCustomerId($this->session->userdata('userid')),
            'selectedcart' => 0,
            
        );


        //Cargamos al arreglo los colnames
        $data['colNames'] = "['SKU','PO#','Description','QTYReceived','ReceiverID','ReceivedBy','DateRcvd','AdjustmentID']";
        //Cargamos al arreglo los colmodels
        $data['colModel'] = "[
                {name:'SKU',index:'SKU', width:50, align:'center'},
                {name:'PO#',index:'PO#', width:55, align:'left'},
                {name:'Description',index:'Description', width:150, align:'left'},
                {name:'QTYReceived',index:'QTYReceived', width:60, align:'center'},
                {name:'ReceiverID',index:'ReceiverID', width:60, align:'center'},
		{name:'ReceivedBy',index:'ReceivedBy', width:80, align:'center'},
		{name:'DateRcvd',index:'DateRcvd', width:100, align:'center'},
                {name:'AdjustmentID',index:'AdjustmentID', width:60, align:'center'}   
  	]";

          //Cargamos la libreria comumn
        $this->load->library('common');

        //Reemplazamos los campos del formulario por los que estan en el arreglo $_POST.
        $data = $this->common->fillPost('ALL', $data);
        
      
        //cadena de busqueda del grid 
        $data['gridSearch'] = 'getData?ds=' . $data['search'] . '&df=' . $data['datefrom'] . '&dt=' . $data['dateto'];


        //Generar el contenido....
        $this->build_content($data);
        $this->render_page();
    }

    function getData() {
        $page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1; // get the requested page
        $limit = isset($_REQUEST['rows']) ? $_REQUEST['rows'] : 10; // get how many rows we want to have into the gri
        $sidx = isset($_REQUEST['sidx']) ? $_REQUEST['sidx'] : 'ID'; // get index row - i.e. user click to sort
        $sord = isset($_REQUEST['sord']) ? $_REQUEST['sord'] : 'desc';

        $dataSearch = isset($_REQUEST['ds']) ? $_GET['ds'] : '';

        $InitialDate = isset($_REQUEST['df']) ? $_GET['df'] : $this->MCommon->lastweek();
        $FinalDate = isset($_REQUEST['dt']) ? $_GET['dt'] : date("m/d/Y");

	//Corregimos las fechas para ajustar el tiempo
	$InitialDateFixed = $this->MCommon->fixInitialDate($InitialDate, 1);
	$FinalDateFixed = $this->MCommon->fixFinalDate($FinalDate, 1);
	
       $where = '';
       
	
        $select = " SELECT InvDetails.ProductCatalogID as SKU
                        ,InvAdj.po AS [PO#]
                        ,inventory.dbo.fn_GetSKUname(InvDetails.ProductCatalogID) AS Description
                        ,Cast(InvDetails.quantity as int) AS QTYReceived
                        ,InvAdj.UserID AS ReceiverID
                        ,Inventory.dbo.fn_GetUserName(InvAdj.UserID ) AS ReceivedBy
                        ,InvAdj.[Date] AS DateRcvd
                        ,InvAdj.id  AS AdjustmentID
                    FROM Inventory.dbo.InventoryAdjustments AS InvAdj
                        left join Inventory.dbo.InventoryAdjustmentDetails AS InvDetails on InvAdj.id  = InvDetails.InventoryAdjustmentsID ";
        

        $wherefields = array('InvDetails.ProductCatalogID','InvAdj.po','inventory.dbo.fn_GetSKUname(InvDetails.ProductCatalogID)','Cast(InvDetails.quantity as int)','InvAdj.UserID','Inventory.dbo.fn_GetUserName(InvAdj.UserID )','InvAdj.[Date]','InvAdj.id');

        $where .=$this->MCommon->concatAllWerefields($wherefields, $dataSearch);


        $where.= " and (Cast(InvAdj.[Date] as date) BETWEEN '{$InitialDateFixed}' and '{$FinalDateFixed}')
                        and (InvAdj.Flow = 1)
                        and InvDetails.ProductCatalogID is not null
                        and (InvAdj.UserID = '76' OR InvAdj.UserID = '145')
                    ORDER BY InvDetails.ProductCatalogID";
        
        

        $SQL = "{$select}{$where}";
	
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
            $responce->rows[$i]['id'] = $row['SKU'];
            $responce->rows[$i]['cell'] = array($row['SKU'],
               
                $row['PO#'],
                $row['Description'],
                $row['QTYReceived'],
                $row['ReceiverID'],
                $row['ReceivedBy'],
		$row['DateRcvd'],
                $row['AdjustmentID'],
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




