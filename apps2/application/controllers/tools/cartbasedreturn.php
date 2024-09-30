<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Cartbasedreturn extends BP_Controller {

    public function __construct() {
        parent::__construct();

        $is_logged_in = $this->session->userdata('is_logged_in');

        if (!isset($is_logged_in) || $is_logged_in != true) {
            redirect(base_url(), 'refresh');
        }
        
        if ($this->MUsers->isValidUser($this->session->userdata('userid'), 884055) != 1) {
               redirect('Catalog/prodcat', 'refresh');
           }
    }

    public function index() {
        //Cargamos la base de datos de OrderManager
        $this->load->model('MOrders', '', TRUE);

        // Define Meta
        $this->title = "MI Technologiesinc - Price Management";

        $this->description = "Price Management";

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
            'from' => '/tools/cartbasedreturn/',
            'nameGrid' => 'cartbasedreturn',
            'namePager' => 'cartbasedreturnpager',
            'caption' => 'Cart based return',
            'sort' => 'desc',
            'search' => '',
            'export' => 'cartbasedreturn',
            'breakdown'=>''
            
        );

        
        //Cargamos la libreria comumn
        $this->load->library('common');

        //Reemplazamos los campos del formulario por los que estan en el arreglo $_POST.
        $data = $this->common->fillPost('ALL', $data);
        
        
        $data['colNames'] = "['CartID','Return Contact','Return Company','Return Address1','Return Address2','Return City','Return State','Return Postal','Return Country']";
        //Cargamos al arreglo los colmodels
        $data['colModel'] = "[
                {name:'CartID',index:'CartID', width:60, align:'center'},
                {name:'ReturnContact',index:'Return Contact', width:70, align:'left',editable:true},
                {name:'ReturnCompany',index:'Return Company', width:100, align:'left',editable:true},
                {name:'ReturnAddress1',index:'Return Address1', width:130, align:'left',editable:true},
                {name:'ReturnAddress2',index:'Return Address2', width:130, align:'center',editable:true},
                {name:'ReturnCity',index:'Return City', width:70, align:'center',editable:true},
                {name:'ReturnState',index:'Return State', width:50, align:'center',editable:true},
		        {name:'ReturnPostal',index:'Return Postal', width:70, align:'center',editable:true},
		        {name:'ReturnCountry',index:'Return Country', width:60, align:'center',editable:true},
  	]";

        //cadena de busqueda del grid 
     //   $data['gridSearch'] = 'getData?ds=' . $data['search'];
        

        //Generar el contenido....
        $this->build_content($data);
        $this->render_page();
    }

    function getData() {
        $page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1; // get the requested page
        $limit = isset($_REQUEST['rows']) ? $_REQUEST['rows'] : 10; // get how many rows we want to have into the gri
        $sidx = isset($_REQUEST['sidx']) ? $_REQUEST['sidx'] : 'CartID'; // get index row - i.e. user click to sort
        $sord = isset($_REQUEST['sord']) ? $_REQUEST['sord'] : 'desc';

        $dataSearch = !empty($_GET['ds']) ? $_REQUEST['ds'] : '';

     //   $dataSearch = isset($_REQUEST['ds']) ? $_GET['ds'] : '';
        $where = '';
        $select = 'SELECT [CartID]
                          ,[Return Contact]
                          ,[Return Company]
                          ,[Return Address1]
                          ,[Return Address2]
                          ,[Return City]
                          ,[Return State]
                          ,[Return Postal]
                          ,[Return Country]';
        $selectSlice = 'SELECT [CartID]
                          ,[Return Contact]
                          ,[Return Company]
                          ,[Return Address1]
                          ,[Return Address2]
                          ,[Return City]
                          ,[Return State]
                          ,[Return Postal]
                          ,[Return Country]';
        $from = ' from ';
        $table='  [Inventory].[dbo].[ADSICartInfo] ';

        $wherefields = array('CartID','[Return Contact]','[Return Company]','[Return Address1]','[Return Address2]','[Return City]','[Return State]','[Return Postal]','[Return Country]');

        $where .=$this->MCommon->concatAllWerefields($wherefields, $dataSearch);

        $SQL = "{$select}{$from}{$table}{$where}";


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

        $SQL =" WITH mytable AS ($select , ROW_NUMBER() OVER (ORDER BY {$sidx} {$sord}) AS RowNumber
                    {$from}{$table}{$where})
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
            $responce->rows[$i]['id'] = $row['CartID'];
            $responce->rows[$i]['cell'] = array($row['CartID'],
               $row['Return Contact'],
               $row['Return Company'],
               $row['Return Address1'],
               $row['Return Address2'],
               $row['Return City'],
               $row['Return State'],
               $row['Return Postal'],
               $row['Return Country'],
            );
            $i++;
        }

        echo json_encode($responce);
    }
    
    
     function saveData() {
          
          $CartID = $_POST['id'];
          
          if (isset($_POST['ReturnContact'])) {
               $Return_Contact = $_POST['ReturnContact'];
               $SQL = "update ADSICartInfo set [Return Contact]='{$Return_Contact}' where CartID={$CartID}";
          }

          if (isset($_POST['ReturnCompany'])) {
               $Return_Company = $_POST['ReturnCompany'];
               $SQL = "update ADSICartInfo set [Return Company]='{$Return_Company}' where CartID={$CartID}";
          }

          if (isset($_POST['ReturnAddress1'])) {
               $Return_Address1 = $_POST['ReturnAddress1'];
               $SQL = "update ADSICartInfo set [Return Address1]='{$Return_Address1}' where CartID={$CartID}";
          }
          
          if (isset($_POST['ReturnAddress2'])) {
               $Return_Address2 = $_POST['ReturnAddress2'];
               $SQL = "update ADSICartInfo set [Return Address2]='{$Return_Address2}' where CartID={$CartID}";
          }

	      if (isset($_POST['ReturnCity'])) {
            	$Return_City = $_POST['ReturnCity'];
               	$SQL = "update ADSICartInfo set [Return City]='{$Return_City}' where CartID={$CartID}";
          }

          if (isset($_POST['ReturnState'])) {
              $Return_State = $_POST['ReturnState'];
               $SQL = "update ADSICartInfo set [Return State]='{$Return_State}' where CartID={$CartID}";
          }
	
          if (isset($_POST['ReturnPostal'])) {
               $Return_Postal = $_POST['ReturnPostal'];
               $SQL = "update ADSICartInfo set [Return Postal]='{$Return_Postal}' where CartID={$CartID}";
          }

         if (isset($_POST['ReturnCountry'])) {
             $Return_Country = $_POST['ReturnCountry'];
             $SQL = "update ADSICartInfo set [Return Country]='{$Return_Country}' where CartID={$CartID}";
         }


          $this->MCommon->saveRecord($SQL,'Inventory');
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




