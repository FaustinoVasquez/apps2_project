<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Asinqoh extends BP_Controller {

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
        $this->title = "MI Technologiesinc - Asin QOH";

        $this->description = "Asin QOH";

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
            'from' => '/Reports/asinqoh/',
            'nameGrid' => 'asinqoh',
            'namePager' => 'asinqohPager',
            'caption' => 'Asin QOH',
            'subgrid' => 'false',
            'sort' => 'desc',
            'search' => '',
            'statusOptions' => Array(0 => "All Status", 1 => "Shipped", 2 => "Pending"),
            'status' => 0,
            'datefrom' => $this->MCommon->lastweek(),
            'dateto' => date("m/d/Y"),
            'export' => 'salesbysku',
            'cartid' => $this->MUsers->getCartName($this->session->userdata('userid')),
            'customerid' => $this->MUsers->getCustomerId($this->session->userdata('userid')),
            'productLines' => '0',
            'productLineOptions' => $this->MCatalog->fillProductLines(),
            'selectedcart' => 0,
            
        );


        //Cargamos al arreglo los colnames
        $data['colNames'] = "['Asin','MitSKU','Manufacturer','MPN','Title', 'stock']";
        //Cargamos al arreglo los colmodels
        $data['colModel'] = "[
            
                {name:'ASIN',index:'ASIN', width:60, align:'center'},
                {name:'MITSKU',index:'MITSKU', width:60, align:'center'},
                {name:'Manufacturer',index:'Manufacturer', width:60, align:'center'},
                {name:'MPN',index:'MPN', width:50, align:'center'},
                {name:'Title',index:'Title', width:150, align:'left'}, 
                {name:'STOCK',index:'STOCK', width:55, align:'right',formatter:'currency', formatoptions:{prefix:'', suffix:' ', thousandsSeparator:','}},
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
        $data['gridSearch'] = 'dataAsinqoh?ds=' . $data['search'] ;

        //datos extra para el detalle de la orden
        $data['formatLink'] = '/Orders/orderdetails/?cid=' . $data['selectedcart'];

        //Generar el contenido....
        $this->build_content($data);
        $this->render_page();
    }

    function dataAsinqoh() {
        $page = !empty($_REQUEST['page']) ? $_REQUEST['page'] : 1; // get the requested page
        $limit = !empty($_REQUEST['rows']) ? $_REQUEST['rows'] : 10; // get how many rows we want to have into the gri
        $sidx = !empty($_REQUEST['sidx']) ? $_REQUEST['sidx'] : ''; // get index row - i.e. user click to sort
        $sord = !empty($_REQUEST['sord']) ? $_REQUEST['sord'] : 'asc';
        $dataSearch = isset($_REQUEST['ds']) ? $_GET['ds'] : '';
      
        $table = " [Inventory].[dbo].[Amazon] AS AZ ";
        $where = ' ';
        $from = ' FROM ';
        

        $selectcount = " SELECT count(AZ.[ASIN]) as qty  FROM [Inventory].[dbo].[Amazon] AS AZ";
        
         $select = " SELECT AZ.[ASIN]
                    ,AZ.[ProductCatalogId] as MITSKU
                    ,AZ.[manufacturer] as Manufacturer
                    ,AZ.[manufacturerpn] as MPN
                    ,AZ.[Title]
                   ";
         
        $selectSlice = " SELECT ASIN
                        ,MITSKU
                        ,Manufacturer
                        ,MPN
                        ,Title
                        ,Inventory.dbo.fn_Get_Total_Stock(MITSKU) as STOCK
                       ";
         

        $wherefields = array('AZ.[ASIN]', 'AZ.[ProductCatalogId]', 
                             'AZ.[manufacturer]', 'AZ.[Title]');
        
        $where .=$this->MCommon->concatAllWerefields($wherefields, $dataSearch);


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

       //  echo $SQL;      

        $result = $this->MCommon->getSomeRecords($SQL);


        $responce = new stdClass();
        $responce->page = $page;
        $responce->total = $total_pages;
        $responce->records = $count;
        $i = 0;

        foreach ($result as $row) {
            $responce->rows[$i]['id'] = $row['ASIN'];
            $responce->rows[$i]['cell'] = array($row['ASIN'],
                $row['MITSKU']= utf8_encode($row['MITSKU']),
                $row['Manufacturer']= utf8_encode($row['Manufacturer']),
                $row['MPN']= utf8_encode($row['MPN']),
                $row['Title']=utf8_encode($row['Title']),
                $row['STOCK']=utf8_encode($row['STOCK']),
            );
            $i++;
        }

        echo json_encode($responce); 

}

}
