<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Shippingcostbysku extends BP_Controller {

    public function __construct() {
        parent::__construct();

        $is_logged_in = $this->session->userdata('is_logged_in');

        if (!isset($is_logged_in) || $is_logged_in != true) {
            redirect(base_url(), 'refresh');
        }
        
        // if ($this->MUsers->isValidUser($this->session->userdata('userid'), 883300) != 1) {
        //        redirect('Catalog/prodcat', 'refresh');
        //    }
    }

    public function index() {
        //Cargamos la base de datos de OrderManager
        $this->load->model('MOrders', '', TRUE);

        // Define Meta
        $this->title = "MI Technologiesinc - Shipping cost by SKU";

        $this->description = "Shipping cost by SKU";

        // Define custom CSS
        $this->css = array('menu.css', 'fluid.css', 'jqueryui/redmond/jquery-ui-1.8.21.custom.css', 'jqgrid/ui.jqgrid.css', 'form.css', 'ventanas-modales.css');

        // Define custom javascript
        $this->javascript = array('jqueryui/ui/jquery-ui-1.8.21.custom.js', 'jqgrid/i18n/grid.locale-en.js', 'jqgrid/jquery.jqGrid.min.js', 'jqgrid/jquery.jqGrid.fluid.js', 'popup.js');
        //Cargamos la libreria donde esta el menu
        $this->load->library('Layout');
        //Creamos un nuevo layout->menu
        $menu = new Layout;

        
        
        $data = array(
            'menu' => $menu->show_menu($this->MUsers->getUserFullName($this->session->userdata('userid'))),
            'from' => '/Reports/shippingcostbysku/',
            'nameGrid' => 'shipcostbysku',
            'namePager' => 'ashipcostbyskuPager',
            'caption' => 'Shipping cost by SKU',
            'subgrid' => 'false',
            'sort' => 'desc',
            'search' => '',
            'statusOptions' => Array(0 => "All Status", 1 => "Shipped", 2 => "Pending"),
            'status' => 0,
            'datefrom' => $this->MCommon->lastweek(),
            'dateto' => date("m/d/Y"),
            'export' => 'salesbysku',
            'categoriesOptions' => $this->MCatalog->fillCategories(),
	    'categories' => '0',
            'cartid' => $this->MUsers->getCartName($this->session->userdata('userid')),
            'customerid' => $this->MUsers->getCustomerId($this->session->userdata('userid')),
            'productLines' => '0',
            'productLineOptions' => $this->MCatalog->fillProductLines(),
            'selectedcart' => 0,
            
        );


        //Cargamos al arreglo los colnames
        $data['colNames'] = "['SKU','Avg1DayShipCost','Avg2DayShipCost','AvgStandardShipCost','AvgIntPriorityShipCost', 'AvgIntEconomyShipCost','AvgWeightPounds']";
        //Cargamos al arreglo los colmodels
        $data['colModel'] = "[
                {name:'SKU',index:'SKU', width:60, align:'center'},
                {name:'Avg1DayShipCost',index:'Avg1DayShipCost', width:60, align:'center'},
                {name:'Avg2DayShipCost',index:'Avg2DayShipCost', width:60, align:'center'},
                {name:'AvgStandardShipCost',index:'AvgStandardShipCost', width:60, align:'center'},
                {name:'AvgIntPriorityShipCost',index:'AvgIntPriorityShipCost', width:50, align:'center'},
                {name:'AvgIntEconomyShipCost',index:'AvgIntEconomyShipCost', width:50, align:'center'}, 
                {name:'AvgWeightPounds',index:'AvgWeightPounds', width:55, align:'center'},
              	]";

          //Cargamos la libreria comumn
        $this->load->library('common');

        //Reemplazamos los campos del formulario por los que estan en el arreglo $_POST.
        $data = $this->common->fillPost('ALL', $data);
        
        
        
        // Verificamos si el usuario activo no es un partner
        if ($data['customerid'] !=0) {
             //Obtenemos solo el nombre de un carro para usuario y enviamos el Id del usuario activo
            $data['cartOptions'] = $this->MOrders->getCartName($data['cartid']);
         //   $data['customerId'] = $this->MUsers->getCustomerId($this->session->userdata('userid'));

        } else {
            $data['cartOptions'] = $this->MOrders->getCartNames();
        }

        //cadena de busqueda del grid 
        $data['gridSearch'] = 'dataShippingcostbysku?ds=' . $data['search'].'&ct='.$data['categories'] ;

        //datos extra para el detalle de la orden
        $data['formatLink'] = '/Orders/orderdetails/?cid=' . $data['selectedcart'];

        //Generar el contenido....
        $this->build_content($data);
        $this->render_page();
    }

    function dataShippingcostbysku() {
        $page = !empty($_REQUEST['page']) ? $_REQUEST['page'] : 1; // get the requested page
        $limit = !empty($_REQUEST['rows']) ? $_REQUEST['rows'] : 10; // get how many rows we want to have into the gri
        $sidx = !empty($_REQUEST['sidx']) ? $_REQUEST['sidx'] : ''; // get index row - i.e. user click to sort
        $sord = !empty($_REQUEST['sord']) ? $_REQUEST['sord'] : 'asc';
        $dataSearch = isset($_REQUEST['ds']) ? $_GET['ds'] : '';
        $categories = !empty($_GET['ct']) ? $_REQUEST['ct'] : 0;
      
        $table = " Inventory.dbo.ProductCatalog AS CAT ";
        $where = ' ';
        $from = ' FROM ';
        

        $selectcount = " SELECT count(CAT.id) as qty  FROM Inventory.dbo.ProductCatalog AS CAT";
       
        
        $select = " SELECT CAT.id
                    ,CAT.CategoryID
                    ,CAT.AvgStandardShipCost
                    ,CAT.Avg1DayShipCost
                    ,CAT.Avg2DayShipCost
                    ,CAT.AvgIntEconomyShipCost
                    ,CAT.AvgIntPriorityShipCost
                    ,CAT.AvgWeightPounds
                   ";
         
        $selectSlice = " SELECT id
                        ,CategoryID
                        ,AvgStandardShipCost
                        ,Avg1DayShipCost
                        ,Avg2DayShipCost
                        ,AvgIntEconomyShipCost
                        ,AvgIntPriorityShipCost
                        ,AvgWeightPounds
                       ";
         

        $wherefields = array('CAT.[id]');
        
        $where .=$this->MCommon->concatAllWerefields($wherefields, $dataSearch);

         if ($categories != 0){
	    $where .= " AND (CAT.CategoryID = {$categories})";
	}

        $SQL = "{$selectcount}{$where}";
   
     
        $result = $this->MCommon->getOneRecord($SQL);
        

        $count = $result['qty'];

        //echo $count;

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
        

        $SQL = "WITH mytable AS ({$select},ROW_NUMBER() OVER (ORDER BY {$sidx} {$sord}) AS RowNumber
	FROM {$table} {$where}) 
               {$selectSlice},RowNumber
               FROM mytable
               WHERE RowNumber BETWEEN {$start} AND {$finish}";

        // echo $SQL;      

        $result = $this->MCommon->getSomeRecords($SQL);


        $responce = new stdClass();
        $responce->page = $page;
        $responce->total = $total_pages;
        $responce->records = $count;
        $i = 0;

        
        foreach ($result as $row) {
            $responce->rows[$i]['id'] = $row['id'];
            $responce->rows[$i]['cell'] = array($row['id'],
                $row['Avg1DayShipCost']= utf8_encode($row['Avg1DayShipCost']),
                $row['Avg2DayShipCost']= utf8_encode($row['Avg2DayShipCost']),
                $row['AvgStandardShipCost']= utf8_encode($row['AvgStandardShipCost']),
                $row['AvgIntPriorityShipCost']=utf8_encode($row['AvgIntPriorityShipCost']),
                $row['AvgIntEconomyShipCost']=utf8_encode($row['AvgIntEconomyShipCost']),
                $row['AvgWeightPounds']=utf8_encode($row['AvgWeightPounds']),
            );
            $i++;
        }

        echo json_encode($responce); 

}

}
