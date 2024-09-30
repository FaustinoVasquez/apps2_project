<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Buyonline extends BP_Controller {

    public function __construct() {
        parent::__construct();

        $is_logged_in = $this->session->userdata('is_logged_in');

        if (!isset($is_logged_in) || $is_logged_in != true) {
            redirect(base_url(), 'refresh');
        }
        
        if ($this->MUsers->isValidUser($this->session->userdata('userid'), 844600) != 1) {
               redirect('Catalog/prodcat', 'refresh');
           }
    }

    public function index($extSku='') {
        //Cargamos la base de datos de OrderManager
        $this->load->model('MOrders', '', TRUE);

        // Define Meta
        $this->title = "MI Technologiesinc - Adjustment Details";

        $this->description = "BuyOnLine";

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
            'from' => '/tools/buyonline/',
            'nameGrid' => 'buyonline',
            'namePager' => 'buyonlinepager',
            'caption' => 'BuyOnline',
            'subgrid' => 'true',
            'sort' => 'desc',
          
            'export' => 'BuyOnline',
            'customerid' => $this->MUsers->getCustomerId($this->session->userdata('userid')),
            'selectedcart' => 0,
            'myselect'=> $extSku,
            
        );


        //Cargamos al arreglo los colnames
        $data['colNames'] = "['ProductCatalogId','ASIN','Title','LampType','price','CountryCode','FulfillmentType','Marketplace','LastChecked']";
        //Cargamos al arreglo los colmodels
        $data['colModel'] = "[
                {name:'ProductCatalogId',index:'ProductCatalogId', width:50, align:'center'},
                {name:'ASIN',index:'ASIN', width:55, align:'left', formatter: formatLink},
                {name:'Title',index:'Title', width:150, align:'left'},
                {name:'LampType',index:'LampType', width:60, align:'center'},
                {name:'price',index:'price', width:60, align:'right',formatter:'currency', formatoptions:{prefix:'$', suffix:' ', thousandsSeparator:','}},
		        {name:'CountryCode',index:'CountryCode', width:50, align:'center'},
		        {name:'FulfillmentType',index:'FulfillmentType', width:60, align:'center'},
                {name:'Marketplace',index:'Marketplace', width:60, align:'center'},
                {name:'LastChecked',index:'LastChecked', width:90, align:'center'} 
  	]";


          //Cargamos la libreria comumn
        $this->load->library('common');

        //Reemplazamos los campos del formulario por los que estan en el arreglo $_POST.
        $data = $this->common->fillPost('ALL', $data);
              
        //cadena de busqueda del grid 
        $data['gridSearch'] = 'getData?&sku=' . $data['myselect'];


        //Generar el contenido....
        $this->build_content($data);
        $this->render_page();
    }

    function getData() {
        $page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1; // get the requested page
        $limit = isset($_REQUEST['rows']) ? $_REQUEST['rows'] : 10; // get how many rows we want to have into the gri
        $sidx = isset($_REQUEST['sidx']) ? $_REQUEST['sidx'] : 'ProductCatalogId'; // get index row - i.e. user click to sort
        $sord = isset($_REQUEST['sord']) ? $_REQUEST['sord'] : 'desc';

        $sku = !empty($_REQUEST['sku']) ? $_GET['sku'] : '101001';
        

        $select = " SELECT [ProductCatalogId]
                        ,[ASIN]
                        ,[Title]
                        ,[LampType]
                        ,[price]
                        ,[CountryCode]
                        ,[FulfillmentChannel] AS FulfillmentType
                        ,[ChannelName] As Marketplace
                        ,[EnteredTime] AS LastChecked
                    FROM [Inventory].[dbo].[Amazon] 
                    WHERE price > 0  and CountryCode = 'US' 
                   and ChannelName = 'Amazon' and [ProductCatalogId] = '{$sku}'
                   order by FulfillmentChannel asc, price asc";
        
       

       
         
	
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
            $responce->rows[$i]['id'] = $row['ProductCatalogId'];
            $responce->rows[$i]['cell'] = array($row['ProductCatalogId'],
               
                $row['ASIN'],
                $row['Title'],
                $row['LampType'],
                $row['price'],
                $row['CountryCode'],
		$row['FulfillmentType'],
                $row['Marketplace'],
                $row['LastChecked'],
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
    
    
    function fillselect(){
        $SQL = "SELECT top 10 ProductCatalogID FROM [Inventory].[dbo].[vw_InventoryDetails] where ProductCatalogID  LIKE '{$_GET['term']}%' group by ProductCatalogID order by ProductCatalogID asc ";
        echo json_encode($this->MCommon->fillAutoComplete($SQL, 'ProductCatalogID'));
    }
    
 

}
?>




