<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Costs extends BP_Controller {

    public function __construct() {
        parent::__construct();

        $is_logged_in = $this->session->userdata('is_logged_in');

        if (!isset($is_logged_in) || $is_logged_in != true) {
            redirect(base_url(), 'refresh');
        }
  
        if ($this->MUsers->isValidUser($this->session->userdata('userid'), 881300) != 1) {
            redirect('Catalog/prodcat', 'refresh');
        }
    }

    function index() {
        // Define Meta
        $this->title = "MI Technologiesinc - Costs";

        $this->description = "Costs";

        // Define custom CSS
        $this->css = array('menu.css', 'form.css', 'fluid.css', 'table.css', 'jqueryui/redmond/jquery-ui-1.8.21.custom.css', 'jqgrid/ui.jqgrid.css');

        // Define custom javascript
        $this->javascript = array('jqueryui/ui/jquery-ui-1.8.21.custom.js', 'jqgrid/i18n/grid.locale-en.js', 'jqgrid/jquery.jqGrid.min.js', 'jqgrid/jquery.jqGrid.fluid.js', 'popup.js');

        $this->load->library('Layout');
        $menu = new Layout;

        $data = array(
            'menu' => $menu->show_menu($this->MUsers->getUserFullName($this->session->userdata('userid'))),
            //Datos para el grid
            'search' => '',
            'dateTo' => date('m/d/Y'),
            'productLines' => 0,
            'productLineOptions' => $this->MCatalog->fillProductLines(),
            'historyDays' => '30',
            'from' => '/Catalog/costs/',
            'administrator' => $this->MUsers->isadminuser($this->session->userdata('userid')),
            'caption' => 'Costs',
            'totalcosturl'=>'/Catalog/totalcost/',
            'showskudata' => '/Catalog/prodcat/showSkuData/',
        );

        $data['headers'] = "['SKU','Vendor PN','Manufacturer','Name','QOH','Target (Days)','DOIR','Excess (Days)','Unit Cost','Avg Unit Cost','Total Cost','QTY Ordered','Primary Supplier']";

        $data['body'] = "[
                {name:'ID',index:'ID', width:55, align:'left',sorttype:'int' },
                {name:'VPN',index:'VPN', width:90, align:'left'},
                {name:'Manufacturer',index:'Manufacturer', width:90, align:'left'},
                {name:'Name',index:'Name', width:200, align:'left'},
                {name:'CurrentStock',index:'CurrentStock', width:80,align:'center'},
                {name:'TargetInventory',index:'TargetInventory', width:80, align:'center', editable:true,editrules:{number:true,minValue:0,maxValue:350}, hidden:true},
                {name:'Projection',index:'Projection', width:80, align:'center',sorttype:'none' , formatter: projection},
                {name:'Excess',index:'Excess', width:80, align:'center'},
                {name:'UnitCost',index:'UnitCost', width:80, align:'center',editable:true,formatter:'currency', formatoptions:{decimalSeparator:'.', thousandsSeparator: ',', decimalPlaces: 4, prefix: '$ '}}, 
                {name:'AverageUnitCost',index:'AverageUnitCost', width:80, align:'center',editable:true,formatter:'currency', formatoptions:{decimalSeparator:'.', thousandsSeparator: ',', decimalPlaces: 4, prefix: '$ '}}, 
                {name:'TotalCost',index:'TotalCost', width:80, align:'right',formatter:'currency', formatoptions:{decimalSeparator:'.', thousandsSeparator: ',', decimalPlaces: 4, prefix: '$ '} }, 
                {name:'QtyOrdered',index:'QtyOrder', width:80, align:'center'}, 
                {name:'PrimarySupplier',index:'PrimarySupplier', width:100, align:'left'},
  	]";

        //Cargamos la libreria comumn
        $this->load->library('common');

        //Reemplazamos los campos del formularion por los que estan en el arreglo $_POST.
        $data = $this->common->fillPost('ALL', $data);

        //cadena de busequeda para el grid
        $data['gridSearch'] = 'gridDataCatalog2?search=' . $data['search'] . '&pl=' . $data['productLines'] . '&hd=' . $data['historyDays'] . '&dt=' . $data['dateTo'];


        $this->build_content($data);
        $this->render_page();
    }

    /*
     *
     *
     *
     */

    function gridDataCatalog2() {
        $this->load->model('mcatalog', '', TRUE);

        $examp = isset($_GET['q']) ? $_REQUEST['q'] : 1;  //query number
        $page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1; // get the requested page
        $limit = isset($_REQUEST['rows']) ? $_REQUEST['rows'] : 10; // get how many rows we want to have into the gri
        $sidx = isset($_REQUEST['sidx']) ? $_REQUEST['sidx'] : 'ID'; // get index row - i.e. user click to sort
        $sord = isset($_REQUEST['sord']) ? $_REQUEST['sord'] : '';

        $search = !empty($_GET['search']) ? $_REQUEST['search'] : '';
        $productLine = !empty($_REQUEST['pl']) ? $_REQUEST['pl'] : '0';
        $historyDays = !empty($_REQUEST['hd']) ? $_REQUEST['hd'] : '30';
        $finalDate = isset($_GET['dt']) ? $_GET['dt'] : date("m/d/Y");
        $initialDate = $this->MCommon->fixIDatebyDays($finalDate, $historyDays);




        //Select utilizado para realizar el conteo del numero de registros devueltos por la consulta.
        $selectCount = 'Select count(*) as rowNum';

       //Select General con los campos necesarios para la vista

        $select = 'SELECT  ID 
                ,Manufacturer
                ,Name
                ,CurrentStock 
                ,TargetInventory
                ,UnitCost
                ,AverageUnitCost
                ,QtyOrdered';

        //Se utiliza en la seccion que extrae los registros de la hoja activa
        if ($finalDate != date('m/d/Y')) {
            $selectSlice = "SELECT ID as SKU
                            ,inventory.dbo.fn_Vendor_Part_Number(ID) as VPN
                            ,Manufacturer
                            ,Name
                            ,inventory.dbo.fn_StockAtDate(ID,'{$finalDate}') as CurrentStock 
                            ,TargetInventory
                            ,inventory.dbo.fn_Inventoty_Projection(ID,'{$initialDate}','{$finalDate}') as projection
                            ,UnitCost
                            ,0 as TotalCost
                            ,AverageUnitCost
                            ,QtyOrdered
                            ,dbo.fn_Get_PrimarySupplier(ID) as PrimarySupplier";

        } else {
            $selectSlice = "SELECT ID as SKU
                            ,inventory.dbo.fn_Vendor_Part_Number(ID) as VPN
                            ,Manufacturer
                            ,Name
                            ,inventory.dbo.fn_get_Global_Stock(id) as CurrentStock 
                            ,TargetInventory
                            ,inventory.dbo.fn_Inventoty_Projection(ID,'{$initialDate}','{$finalDate}') as projection
                            ,UnitCost
                            ,AverageUnitCost
                            ,0 as TotalCost
                            ,QtyOrdered
                            ,dbo.fn_Get_PrimarySupplier(ID) as PrimarySupplier";
        }


        $from = ' from ';
        $table = '[Inventory].[dbo].ProductCatalog';
        $where = '';

        //Obtenemos todos los nombres de campos de la tabla productCatalog
        $fields = $this->MCommon->getAllfields($table);
        //creamos el were concatenando todos los nombres de campos y las palabras de busqueda
        $where .=$this->MCommon->concatAllWerefields($fields, $search);
        //Agregamos al where la busqueda por categoria
        $where .=$this->MCatalog->filterByCategory($productLine);
        //Filtramos por las fechas que se indican

        $SQL = "{$selectCount}{$from}{$table}{$where}";

        $result = $this->MCommon->getOneRecord($SQL);
        $count = $result['rowNum'];

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
                                FROM {$table}{$where})
               {$selectSlice}, RowNumber
               FROM mytable
                WHERE RowNumber BETWEEN {$start} AND {$finish}";
 
               
                
                
        $result = $this->MCommon->getSomeRecords($SQL);
        
   
          $responce = new stdClass();
          $responce->page = $page;
          $responce->total = $total_pages;
          $responce->records = $count;
          $i = 0;

          $TotInv = 0;
          foreach ($result as $row) {

          $responce->rows[$i]['id'] = $row['SKU'];
          $responce->rows[$i]['cell'] = array($row['SKU'],
          $row['VPN'] = utf8_encode($row['VPN']),
          $row['Manufacturer'] = utf8_encode($row['Manufacturer']),
          $row['Name'] = utf8_encode($row['Name']),
          $row['CurrentStock'] = utf8_encode($row['CurrentStock']),
          $row['TargetInventory'] = utf8_encode($row['TargetInventory']),
          $row['projection'] = utf8_encode($row['projection']),
          $row['excess'] = $row['projection'] - $row['TargetInventory'],
          $row['UnitCost'] = utf8_encode($row['UnitCost']),
          $row['AverageUnitCost'] = utf8_encode($row['AverageUnitCost']),     
          $row['TotalCost'] = $row['CurrentStock'] * $row['UnitCost'],
          $row['QtyOrdered'] = utf8_encode($row['QtyOrdered']),
          $row['PrimarySupplier'] = utf8_encode($row['PrimarySupplier']),
          );
          $TotInv += $row['TotalCost'];
          $i++;
          }
          // $responce->userdata['TotalCost'] = 'Totals:';
          $responce->userdata['Name'] = 'TOTALS:';
          $responce->userdata['TotalCost'] = $TotInv;

          echo json_encode($responce);
    }

    function cellDataEdit() {

        if (isset($_POST['UnitCost'])) {
            $UnitCost = $_POST['UnitCost'];
            $id = $_POST['id'];
            $SQL = "update inventory.dbo.productcatalog set UnitCost={$UnitCost} where id={$id}";
        }

        $this->MCommon->saveRecord($SQL,'InventorySave');
    }

    function show() {
        $search = $_GET['row_id'];
        $item = $this->MCatalog->showSku($search);
        $customfields = $this->MCatalog->showCustomFields($search);


        $data['row_id'] = "t_" . $_GET['row_id'];
        $data['customfields'] = $customfields;
        $data['item'] = $item;
        $data['title'] = "Show SKU |" . $search;
        $data['main'] = 'showsku';
        $this->load->vars($data);
        $this->load->view('/catalog/showtemplate');
    }

    function csvExport() {

        header('Content-type: application/vnd.ms-excel');
        header("Content-Disposition: attachment; filename=Catalog-" . date("D-M-j") . ".xls");
        header("Pragma: no-cache");

        $buffer = $_POST['csvBuffer'];

        try {
            echo $buffer;
        } catch (Exception $e) {
            
        }
    }

}
