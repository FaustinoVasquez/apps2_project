<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Globalinventory extends BP_Controller {

    public function __construct() {
        parent::__construct();

        $is_logged_in = $this->session->userdata('is_logged_in');

        if (!isset($is_logged_in) || $is_logged_in != true) {
            redirect(base_url(), 'refresh');
        }
        
        // if ($this->MUsers->isValidUser($this->session->userdata('userid'), 883960) != 1) {
        //        redirect('Catalog/prodcat', 'refresh');
        //    }
    }

    public function index() {
        //Cargamos la base de datos de OrderManager
        $this->load->model('MOrders', '', TRUE);

        // Define Meta
        $this->title = "MI Technologiesinc - Global Inventory";

        $this->description = "Global Inventory";

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
            'from' => '/Reports/globalinventory/',
            'nameGrid' => 'globalinventory',
            'namePager' => 'globalinventorypager',
            'caption' => 'Global Inventory',
            'subgrid' => 'true',
            'sort' => 'desc',
            'search' => '',
            'categoriesOptions' => $this->MCatalog->fillCategories(),
	        'categories' => '0',
            'export' => 'GlobalInventory',
            'customerid' => $this->MUsers->getCustomerId($this->session->userdata('userid')),
            'selectedcart' => 0,
            'breakdown'=>''
            
        );

        
        //Cargamos la libreria comumn
        $this->load->library('common');

        //Reemplazamos los campos del formulario por los que estan en el arreglo $_POST.
        $data = $this->common->fillPost('ALL', $data);
        

    if ($data['breakdown']){
        
        $data['colNames'] = "['SKU','Product','LocalQTY','FBAQty','FBAInRoute','TotalQty','BackOrders','Channel','Category']";
        //Cargamos al arreglo los colmodels
        $data['colModel'] = "[
                {name:'SKU',index:'SKU', width:50, align:'center'},
                {name:'Product',index:'Product', width:200, align:'left'},
                {name:'LocalQTY',index:'LocalQTY', width:60, align:'center'},
                {name:'FBAQty',index:'FBAQty', width:60, align:'center'},
                {name:'FBAInRoute',index:'FBAInRoute', width:60, align:'center'},
		        {name:'TotalQty',index:'TotalQty', width:80, align:'center'},
                {name:'BackOrders',index:'BackOrders', width:80, align:'center',formatter:formatLink},
                {name:'Channel',index:'Channel', width:80, align:'center'},
		        {name:'Category',index:'Category', width:120, align:'center'} 
  	]";
 
        //cadena de busqueda del grid 
        $data['gridSearch'] = 'getDataBreakdown?ds=' . $data['search'].'&ct='.$data['categories'].'&brk='.$data['breakdown'];
        
    }else{
        
        //Cargamos al arreglo los colnames
        $data['colNames'] = "['SKU','Product','LocalQTY','FBAQty','FBAInRoute','TotalQty','BackOrders','Category']";
        //Cargamos al arreglo los colmodels
        $data['colModel'] = "[
                {name:'SKU',index:'SKU', width:50, align:'center'},
                {name:'Product',index:'Product', width:200, align:'left'},
                {name:'LocalQTY',index:'LocalQTY', width:60, align:'center'},
                {name:'FBAQty',index:'FBAQty', width:60, align:'center'},
                {name:'FBAInRoute',index:'FBAInRoute', width:60, align:'center'},
		        {name:'TotalQty',index:'TotalQty', width:80, align:'center'},
                {name:'BackOrders',index:'BackOrders', width:80, align:'center',formatter:formatLink},
		        {name:'Category',index:'Category', width:120, align:'center'} 
  	]";

     
        //cadena de busqueda del grid 
        $data['gridSearch'] = 'getData?ds=' . $data['search'].'&ct='.$data['categories'].'&brk='.$data['breakdown'];
      }   

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
        $categories = !empty($_GET['ct']) ? $_REQUEST['ct'] : 0;
	
       $where = '';
       
	
        $select = " SELECT AZFBA.[MITSKU] AS [SKU]
                    ,PC.Name AS [Product]
                    ,Cast(GS.GlobalStock AS Int) AS [LocalQTY]
                    ,IsNull(Sum(AZFBA.FBAQty), 0) AS [FBAQty]
                    ,IsNull(Sum(AZFBA.FBAInboundQty), 0) AS [FBAInRoute]
                    ,Cast(IsNull((Sum(AZFBA.FBAQty) + Sum(AZFBA.FBAInboundQty) + GS.GlobalStock), 0) AS Int) AS [TotalQty]
                    ,IsNull(Cast(BO.Backorder AS Int), 0) AS BackOrders
                    ,CAT.Name AS [Category]
                    FROM [Inventory].[dbo].[AmazonFBA] AZFBA WITH(NOLOCK)
                LEFT OUTER JOIN [Inventory].[dbo].[ProductCatalog] AS PC WITH(NOLOCK) ON (AZFBA.MITSKU = PC.ID)
                LEFT OUTER JOIN [Inventory].[dbo].[Categories] AS CAT WITH(NOLOCK) ON (PC.CategoryID = CAT.ID)
                LEFT OUTER JOIN [Inventory].[dbo].[Global_Stocks] AS GS WITH(NOLOCK) ON (AZFBA.MITSKU = GS.ProductCatalogId)
                LEFT OUTER JOIN [Inventory].[dbo].[QBPurchaseOrderBackorders] AS BO WITH(NOLOCK) ON (Cast(AZFBA.MITSKU AS NVARCHAR(MAX)) = Cast(BO.SKU AS NVARCHAR(MAX))) ";       

        $wherefields = array('PC.Name','CAT.Name');

        $where .=$this->MCommon->concatAllWerefields($wherefields, $dataSearch);
        
        if ($categories != 0){
	    $where .= " AND (PC.CategoryID = {$categories})";
	}
        
        $group = " GROUP BY AZFBA.[MITSKU], PC.Name, CAT.Name, GS.GlobalStock, BO.Backorder
                    HAVING GS.GlobalStock > 0
                    AND Sum(AZFBA.FBAInboundQty) > 0
                    AND Sum(AZFBA.FBAQty) > 0";

        $SQL = "{$select}{$where}{$group}";
      
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
                $row['Product'],
                $row['LocalQTY'],
                $row['FBAQty'],
                $row['FBAInRoute'],
                $row['TotalQty'],
                $row['BackOrders'],
		$row['Category'],

            );
            $i++;
        }

        echo json_encode($responce);
    }
    
    
    
    
    function getDataBreakdown(){
         $page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1; // get the requested page
        $limit = isset($_REQUEST['rows']) ? $_REQUEST['rows'] : 10; // get how many rows we want to have into the gri
        $sidx = isset($_REQUEST['sidx']) ? $_REQUEST['sidx'] : 'ID'; // get index row - i.e. user click to sort
        $sord = isset($_REQUEST['sord']) ? $_REQUEST['sord'] : 'desc';

        $dataSearch = isset($_REQUEST['ds']) ? $_GET['ds'] : '';
        $categories = !empty($_GET['ct']) ? $_REQUEST['ct'] : 0;
	
       $where = '';
       
	
        $select = " SELECT AZFBA.[MITSKU] AS [SKU]
                    ,PC.Name AS [Product]
                    ,Cast(GS.GlobalStock AS Int) AS [LocalQTY]
                    ,IsNull(Sum(AZFBA.FBAQty), 0) AS [FBAQty]
                    ,IsNull(Sum(AZFBA.FBAInboundQty), 0) AS [FBAInRoute]
                    ,Cast(IsNull((Sum(AZFBA.FBAQty) + Sum(AZFBA.FBAInboundQty) + GS.GlobalStock), 0) AS Int) AS [TotalQty]
                    ,IsNull(Cast(BO.Backorder AS Int), 0) AS BackOrders
                    ,AZFBA.Channel AS [Channel]
                    ,CAT.Name AS [Category]
                    FROM [Inventory].[dbo].[AmazonFBA] AZFBA WITH(NOLOCK)
                LEFT OUTER JOIN [Inventory].[dbo].[ProductCatalog] AS PC WITH(NOLOCK) ON (AZFBA.MITSKU = PC.ID)
                LEFT OUTER JOIN [Inventory].[dbo].[Categories] AS CAT WITH(NOLOCK) ON (PC.CategoryID = CAT.ID)
                LEFT OUTER JOIN [Inventory].[dbo].[Global_Stocks] AS GS WITH(NOLOCK) ON (AZFBA.MITSKU = GS.ProductCatalogId)
                LEFT OUTER JOIN [Inventory].[dbo].[QBPurchaseOrderBackorders] AS BO WITH(NOLOCK) ON (Cast(AZFBA.MITSKU AS NVARCHAR(MAX)) = Cast(BO.SKU AS NVARCHAR(MAX))) ";       

        $wherefields = array('PC.Name','CAT.Name');

        $where .=$this->MCommon->concatAllWerefields($wherefields, $dataSearch);
        
        if ($categories != 0){
	    $where .= " AND (PC.CategoryID = {$categories})";
	}
        
        $group = " GROUP BY AZFBA.Channel, AZFBA.[MITSKU], PC.Name, CAT.Name, GS.GlobalStock, BO.Backorder
                    HAVING GS.GlobalStock > 0
                    AND Sum(AZFBA.FBAInboundQty) > 0
                    AND Sum(AZFBA.FBAQty) > 0";

        $SQL = "{$select}{$where}{$group}";
      
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
                $row['Product'],
                $row['LocalQTY'],
                $row['FBAQty'],
                $row['FBAInRoute'],
                $row['TotalQty'],
                $row['BackOrders'],
                $row['Channel'],
		$row['Category'],

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




