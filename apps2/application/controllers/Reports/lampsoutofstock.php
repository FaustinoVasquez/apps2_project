<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Lampsoutofstock extends BP_Controller {

    public function __construct() {
        parent::__construct();

        $is_logged_in = $this->session->userdata('is_logged_in');

        if (!isset($is_logged_in) || $is_logged_in != true) {
            redirect(base_url(), 'refresh');
        }

        // if ($this->MUsers->isValidUser($this->session->userdata('userid'), 883900) != 1) {
        //     redirect('Catalog/prodcat', 'refresh');
        // }
    }

    public function index() {
        //Cargamos la base de datos de OrderManager
        $this->load->model('MOrders', '', TRUE);

        // Define Meta
        $this->title = "MI Technologiesinc - Lamp out of stock";

        $this->description = "Lamp out of stock";

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
            'from' => '/Reports/lampsoutofstock/',
            'nameGrid' => 'lampsoutofstock',
            'namePager' => 'lampsoutofstockPager',
            'caption' => 'Lamps out of stock',
            'subgrid' => 'false',
	    'sortname' => 'Remaining',
            'sortorder' => 'Asc',
            'search' => '',
            'status' => 0,
            'datefrom' => date("m/d/Y", strtotime("-10 days")),
            'dateto' => date("m/d/Y"),
            'export' => 'lampsoutofstock',
            'customerid' => $this->MUsers->getCustomerId($this->session->userdata('userid'))
        );


        //Cargamos al arreglo los colnames
        $data['colNames'] = "['SKU','Name','CurrentStock','PendingShipment','Remaining','BackOrders','AvgSalePrice','Stats']";
        //Cargamos al arreglo los colmodels
        $data['colModel'] = "[
                {name:'SKU',index:'SKU', align:'center'},
                {name:'Name',index:'Name', width:550, align:'left'},
                {name:'CurrentStock',index:'CurrentStock', align:'center'},
                {name:'PendingShipment',index:'PendingShipment', align:'center'},
                {name:'Remaining',index:'Remaining', align:'center'},
                {name:'BackOrders',index:'BackOrders', align:'center',formatter:formatLink},
        		{name:'AvgSalePrice',index:'AvgSalePrice', align:'right'},
        		{name:'Stats',index:'Stats', align:'center',formatter: statistics}
  	]";


        //Cargamos la libreria comumn
        $this->load->library('common');

        //Reemplazamos los campos del formulario por los que estan en el arreglo $_POST.
        $data = $this->common->fillPost('ALL', $data);


        //cadena de busqueda del grid 
        $data['gridSearch'] = 'getData?&ds=' . $data['search'] .'&df=' . $data['datefrom'] . '&dt=' . $data['dateto'];

        //Generar el contenido....
        $this->build_content($data);
        $this->render_page();
    }

    function getData() {
        $page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1; // get the requested page
        $limit = isset($_REQUEST['rows']) ? $_REQUEST['rows'] : 10; // get how many rows we want to have into the gri
        $sidx = isset($_REQUEST['sidx']) ? $_REQUEST['sidx'] : 'Remaining'; // get index row - i.e. user click to sort
        $sord = isset($_REQUEST['sord']) ? $_REQUEST['sord'] : 'desc';

		//Dataos pasados al metodo desde el view
	$search = !empty($_GET['ds']) ? $_REQUEST['ds'] : '';
	$InitialDate = isset($_REQUEST['df']) ? $_GET['df'] : date("m/d/Y", strtotime("-10 days"));
        $FinalDate = isset($_REQUEST['dt']) ? $_GET['dt'] : date("m/d/Y");
	
	//Corregimos las fechas para ajustar el tiempo
	$InitialDateFixed = $this->MCommon->fixInitialDate($InitialDate, 1);
	$FinalDateFixed = $this->MCommon->fixFinalDate($FinalDate, 1);
	

        $table = ' [OrderManager].[dbo].[Order Details] AS OD ';
	$join = ' JOIN [Inventory].[dbo].[Global_Stocks] AS GS ON (Cast(OD.SKU as VARCHAR(MAX)) = Cast(GS.ProductCatalogId as VARCHAR(MAX)))
		  LEFT OUTER JOIN [Inventory].[dbo].[ProductCatalog] AS PC ON (OD.SKU = PC.ID)
		  LEFT OUTER JOIN [OrderManager].[dbo].[Orders] AS O ON (OD.OrderNumber = O.OrderNumber)
		  LEFT OUTER JOIN [Inventory].[dbo].[QBPurchaseOrderBackorders] AS BO ON (Cast(OD.SKU as VARCHAR(MAX)) = Cast(BO.SKU as VARCHAR(MAX)))';
        $where = ' ';
        $from = ' FROM ';
	
	$groupby = ' Group By OD.SKU, GS.TotalStock, PC.Name, BO.Backorder';
	$orderby = " Order by {$sidx} {$sord}";

 
        $selectSlice = "SELECT  OD.[SKU] AS SKU
				,PC.Name AS Name
				,Cast(GS.[TotalStock] as int) AS CurrentStock
				,Sum(OD.[QuantityOrdered]) AS PendingShipment
				,Cast(GS.[TotalStock] - Sum(OD.[QuantityOrdered]) as int) AS Remaining
				,Cast(BO.Backorder as int) AS BackOrders
				,(Cast(Sum(OD.BilledSubtotal) / Sum(OD.[QuantityOrdered]) AS money)) AS AvgSalePrice
                    ";

        $wherefields = array('OD.[SKU]', 'PC.Name');
        $where .=$this->MCommon->concatAllWerefields($wherefields, $search);

        $where .=" and  (OD.SKU LIKE '133%' OR OD.SKU LIKE '135%')
		   AND PC.CategoryID = '24'
		   AND O.Cancelled = '0'
		   AND O.OrderDate > '{$InitialDateFixed}'
		   AND O.OrderStatus != 'Order Canceled'
		   AND O.OrderStatus != 'Shipped'
		   AND O.OrderStatus != 'Item Returned'
		   AND O.OrderStatus != 'Item Exchanged'
		   AND O.OrderStatus != 'Credit Issued'
		   AND O.Shipping != 'Customer Pickup'";



        $SQL = "{$selectSlice}{$from}{$table}{$join}{$where}{$groupby}{$orderby}";

	


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


    /*    $SQL = "WITH mytable AS ({$select},ROW_NUMBER() OVER (ORDER BY {$sidx} {$sord}) AS RowNumber
	FROM {$table} {$where}) 
               {$selectSlice},RowNumber
               FROM mytable
               WHERE RowNumber BETWEEN {$start} AND {$finish}";


        $result = $this->MCommon->getSomeRecords($SQL);
*/


        $responce = new stdClass();
        $responce->page = $page;
        $responce->total = $total_pages;
        $responce->records = $count;
        $i = 0;


        foreach ($result as $row) {
            $responce->rows[$i]['id'] = $row['SKU'];
            $responce->rows[$i]['cell'] = array($row['SKU'],
                $row['Name'],
                $row['CurrentStock'],
                $row['PendingShipment'],
                $row['Remaining'],
                $row['BackOrders'], 
		$row['AvgSalePrice'],
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





