<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Housinglampinsideinv extends BP_Controller {

    public function __construct() {
        parent::__construct();

        $is_logged_in = $this->session->userdata('is_logged_in');

        if (!isset($is_logged_in) || $is_logged_in != true) {
            redirect(base_url(), 'refresh');
        }
        
        // if ($this->MUsers->isValidUser($this->session->userdata('userid'), 883210) != 1) {
        //        redirect('Catalog/prodcat', 'refresh');
        //    }
    }

    public function index() {
        //Cargamos la base de datos de OrderManager
        $this->load->model('MOrders', '', TRUE);

        // Define Meta
        $this->title = "MI Technologiesinc - Housing + Lamp Inside inventory";

        $this->description = "Housing + Lamp Inside inventory";

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
            'from' => '/Reports/housinglampinsideinv/',
            'nameGrid' => 'housinglampinsideinv',
            'namePager' => 'housinglampinsideinvPager',
            'caption' => 'Housing + Lamp Inside inventory',
            'subgrid' => 'false',
            'sort' => 'desc',
            'search' => '',
            'statusOptions' => Array(0 => "All Status", 1 => "Shipped", 2 => "Pending"),
            'status' => 0,
            'datefrom' => $this->MCommon->lastweek(),
            'dateto' => date("m/d/Y"),
            'export' => 'Housing_Plus_Lamp_Inside_inventory',
            'cartid' => $this->MUsers->getCartName($this->session->userdata('userid')),
            'customerid' => $this->MUsers->getCustomerId($this->session->userdata('userid')),
            'productLines' => '0',
            'productLineOptions' => $this->MCatalog->fillProductLines(),
            'selectedcart' => 0,
            
        );


        //Cargamos al arreglo los colnames
        $data['colNames'] = "['ID','Manufacturer','ManufacturerPN','Name','Lamp + Housing QOH','Lamp Inside SKU', 'Lamp Inside QOH']";
        //Cargamos al arreglo los colmodels
        $data['colModel'] = "[
               {name:'ID',index:'ID', width:60, align:'center'},
                {name:'Manufacturer',index:'Manufacturer', width:60, align:'center'},
                {name:'ManufacturerPN',index:'ManufacturerPN', width:60, align:'center'},
                {name:'Name',index:'Name', width:200, align:'left'},
                {name:'Lamp + Housing QOH',index:'Lamp + Housing QOH', width:60, align:'center'},
                {name:'Lamp Inside SKU',index:'Lamp Inside SKU', width:50, align:'center'},
                {name:'Lamp Inside QOH',index:'Lamp Inside QOH', width:50, align:'center'}, 
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
        $data['gridSearch'] = 'data?ds=' . $data['search'] ;

        //datos extra para el detalle de la orden
        $data['formatLink'] = '/Orders/orderdetails/?cid=' . $data['selectedcart'];

        //Generar el contenido....
        $this->build_content($data);
        $this->render_page();
    }

    function data() {
        $page = !empty($_REQUEST['page']) ? $_REQUEST['page'] : 1; // get the requested page
        $limit = !empty($_REQUEST['rows']) ? $_REQUEST['rows'] : 10; // get how many rows we want to have into the gri
        $sidx = !empty($_REQUEST['sidx']) ? $_REQUEST['sidx'] : ''; // get index row - i.e. user click to sort
        $sord = !empty($_REQUEST['sord']) ? $_REQUEST['sord'] : 'asc';
        $dataSearch = isset($_REQUEST['ds']) ? $_GET['ds'] : '';
      
        $where = '';
        
        
        $myselect = "SELECT PC.[ID]
                        ,PC.[Manufacturer]
                        ,PC.[ManufacturerPN]
                        ,PC.[Name]
                        ,ASSY.SubSKU AS 'Lamp Inside SKU'
                        ,CEILING([Inventory].[dbo].fn_Get_Global_Stock(PC.[ID]))  AS 'Lamp + Housing QOH'
                        ,CEILING([Inventory].[dbo].fn_Get_Global_Stock(ASSY.SubSKU)) AS 'Lamp Inside QOH'
                    FROM [Inventory].[dbo].[ProductCatalog] as PC
                    LEFT OUTER JOIN [Inventory].[dbo].[AssemblyDetails] as ASSY ON (PC.ID = ASSY.ProductCatalogID)
                    WHERE (pc.CategoryID in (10, 14))
                    AND ([Inventory].[dbo].fn_GetCategoryId(ASSY.SubSKU) in (5,9,15,19))
                  ";
            if ($dataSearch){
            $myselect.= " and (
                                   (pc.ID like '%{$dataSearch}%')
                                OR (pc.Manufacturer like '%{$dataSearch}%')
                                OR (pc.ManufacturerPN like '%{$dataSearch}%')
                                OR (pc.Name like '%{$dataSearch}%')
                                OR (ASSY.SubSKU like '%{$dataSearch}%')
                                )
                               ";
             }
       

        $SQL = "{$myselect}";
        
        
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
            $responce->rows[$i]['id'] = $row['ID'];
            $responce->rows[$i]['cell'] = array($row['ID'],
                $row['Manufacturer']= utf8_encode($row['Manufacturer']),
                $row['ManufacturerPN']= utf8_encode($row['ManufacturerPN']),
                $row['Name']= utf8_encode($row['Name']),
                $row['Lamp + Housing QOH']=utf8_encode($row['Lamp + Housing QOH']),
                $row['Lamp Inside SKU']=utf8_encode($row['Lamp Inside SKU']),
                $row['Lamp Inside QOH']=utf8_encode($row['Lamp Inside QOH']),
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

}