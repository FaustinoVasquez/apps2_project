<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Asinmerger extends BP_Controller {

    public function __construct() {
        parent::__construct();

        $is_logged_in = $this->session->userdata('is_logged_in');

        if (!isset($is_logged_in) || $is_logged_in != true) {
            redirect(base_url(), 'refresh');
        }
        
        if ($this->MUsers->isValidUser($this->session->userdata('userid'), 884150) != 1) {
               redirect('Catalog/prodcat', 'refresh');
           }
    }

    public function index() {
        //Cargamos la base de datos de OrderManager
        $this->load->model('MOrders', '', TRUE);

        // Define Meta
        $this->title = "MI Technologiesinc - Asin Merger";

        $this->description = "Asin Merger";

        // Define custom CSS
        $this->css = array('menu.css', 'fluid.css', 'jqueryui/redmond/jquery-ui-1.8.21.custom.css', 'jqgrid/ui.jqgrid.css', 'form.css');

        // Define custom javascript
        $this->javascript = array('jqgrid/jquery.jqGrid.min.js','jqueryui/ui/jquery-ui-1.8.21.custom.js','jqgrid/i18n/grid.locale-en.js',  'jqgrid/jquery.jqGrid.fluid.js', 'popup.js');
        //Cargamos la libreria donde esta el menu
        $this->load->library('Layout');
        //Creamos un nuevo layout->menu
        $menu = new Layout;

        $data = array(
            'menu' => $menu->show_menu($this->MUsers->getUserFullName($this->session->userdata('userid'))),
            'from' => '/tools/asinmerger/',
            'nameGrid' => 'asinmerger',
            'namePager' => 'asinmergerpager',
            'caption' => 'Asin Merger',
            'subgrid' => 'true',
            'sort' => 'desc',
          
            'export' => 'AsinMerger',
            'customerid' => $this->MUsers->getCustomerId($this->session->userdata('userid')),
            'selectedcart' => 0,
            'search'=>'',
            
        );


        //Cargamos al arreglo los colnames
        $data['colNames'] = "['ASIN','Title','ProductCatalogId']";
        //Cargamos al arreglo los colmodels
        $data['colModel'] = "[
                {name:'ASIN',index:'ASIN', width:55, align:'center', formatter: formatLink},
                {name:'Title',index:'Title', width:150, align:'left'},
                {name:'ProductCatalogId',index:'ProductCatalogId', width:50, align:'center'},
  	]";


          //Cargamos la libreria comumn
        $this->load->library('common');

        //Reemplazamos los campos del formulario por los que estan en el arreglo $_POST.
        $data = $this->common->fillPost('ALL', $data);
              
        
        //cadena de busqueda del grid 
        $data['gridSearch'] = 'getData?&search=' . $data['search'];


        //Generar el contenido....
        $this->build_content($data);
        $this->render_page();
    }

    function getData() {
        $page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1; // get the requested page
        $limit = isset($_REQUEST['rows']) ? $_REQUEST['rows'] : 10; // get how many rows we want to have into the gri
        $sidx = isset($_REQUEST['sidx']) ? $_REQUEST['sidx'] : 'ProductCatalogId'; // get index row - i.e. user click to sort
        $sord = isset($_REQUEST['sord']) ? $_REQUEST['sord'] : 'desc';

        $search = !empty($_REQUEST['search']) ? $_GET['search'] : '0';
        

        $select = " SELECT [ASIN]
                          ,[Title] 
                          ,[ProductCatalogId]
                    FROM [Inventory].[dbo].[Amazon] 
                    WHERE ASIN = '{$search}'";
        
 
	
       $result = $this->MCommon->getSomeRecords($select);

       
       
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
            $responce->rows[$i]['id'] = $row['ASIN'];
            $responce->rows[$i]['cell'] = array($row['ASIN'],
                $row['Title'],
                $row['ProductCatalogId'],
            );
            $i++;
        }

        echo json_encode($responce);
    }


    function processRecord(){
        switch ($_POST['oper']) {
            case 'del':
                $SQL = " exec Inventory.dbo.sp_Delete_ASIN_Merger {$_POST['id']};";
                 $this->MCommon->executeQuery($SQL);
                break;
            default:
                break;
        }
    }
 

}
?>




