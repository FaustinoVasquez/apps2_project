<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Prodcat extends BP_Controller {

    public function __construct() {
        parent::__construct();

        $is_logged_in = $this->session->userdata('is_logged_in');

        if (!isset($is_logged_in) || $is_logged_in != true) {
            redirect(base_url(), 'refresh');
        }

        if ($this->MUsers->isValidUser($this->session->userdata('userid'), 881100) != 1) {// 881100 prodcat Access
            redirect('Catalog/prodcat', 'refresh');
        }
    }

    public function index($sku =null) {

        // Define Meta
        $this->title = "MI Technologiesinc - Product Catalog";

        $this->description = "Product Catalog";

        // Define custom CSS
        $this->css = array( 'form.css', 'fluid.css', 'table.css', 'jqueryui/redmond/jquery-ui-1.8.21.custom.css', 'jqgrid/ui.jqgrid.css','menu.css',);

        // Define custom javascript
        $this->javascript = array('jqueryui/ui/jquery-ui-1.8.21.custom.js', 'jqgrid/i18n/grid.locale-en.js', 'jqgrid/jquery.jqGrid.min.js', 'jqgrid/jquery.jqGrid.fluid.js', 'popup.js');

        $this->load->library('Layout');
        $menu = new Layout;

        $data = array(
            'menu' => $menu->show_menu($this->MUsers->getUserFullName($this->session->userdata('userid'))),
            //Datos para el grid
            'caption' => 'Product Catalog',
            'from' => 'Catalog/prodcat/',
            'nameGrid' => 'cat',
            'namePager' => 'catPager',
            'subgrid' => 'true',
            'subgridprocesor' => 'showSkuData',
            'sort' => 'asc',
            'celledit' => 'true',
            'cellediturl' => 'Catalog/prodcat/cellDataEdit',
            //Campos del formulario
            'search' => $sku,
            'categories' => '0',
            'qtyOrdered' => '',
            'categoriesOptions' => $this->MCatalog->fillCategories(),
            'doir' => '0',
            'historyDays' => '30',
	    'backorders' => '/Catalog/prodcat/backorders',
	    'backordersButton' => 0
        );
	
	
	//solo usuarios con permiso 881150 pueden ver el boton backorders
	if ($this->MUsers->isValidUser($this->session->userdata('userid'), 881150) == 1) {// Access Code
	    $data['backordersButton'] = 1;
	}
	     
        //Cargamos la libreria comumn
        $this->load->library('common');

        //Reemplazamos los campos del formularion por los que estan en el arreglo $_POST.
        $data = $this->common->fillPost('ALL', $data);


        $columns = array(
            'SKU' => array('colName' => 'SKU', 'colModel' => "{name:'ID',index:'ID', width:55, align:'left',sorttype:'int'}"),
            'VPN' => array('colName' => 'Vendor PN', 'colModel' => "{name:'VPN',index:'VPN', width:90, align:'left'}"),
            'Manufacturer' => array('colName' => 'Manufacturer', 'colModel' => "{name:'Manufacturer',index:'Manufacturer', width:90, align:'left'}"),
            'Name' => array('colName' => 'Name', 'colModel' => "{name:'Name',index:'Name', width:200, align:'left'}"),
            'CurrentStock' => array('colName' => 'QOH', 'colModel' => "{name:'CurrentStock',index:'CurrentStock', width:80,align:'center', formatter:'number', formatoptions:{decimalSeparator:'.',thousandsSeparator: ',', decimalPlaces: 2, prefix: '' }}"),
            'VirtualStock' => array('colName' => 'vQOH', 'colModel' => "{name:'VirtualStock',index:'VirtualStock', width:80, align:'center', formatter:'number', formatoptions:{decimalSeparator:'.',thousandsSeparator: ',', decimalPlaces: 2, prefix: '' }}"),
            'TotalStock' => array('colName' => 'tQOH', 'colModel' => "{name:'TotalStock',index:'TotalStock', width:80, align:'center', formatter:'number', formatoptions:{decimalSeparator:'.',thousandsSeparator: ',', decimalPlaces: 2, prefix: '' }}"),

	    'LowestSupplierCost'=> array('colName' => 'LSCost', 'colModel' => "{name:'LowestSupplierCost',index:'LowestSupplierCost', width:80, align:'center', formatter:'number', formatoptions:{decimalSeparator:'.',thousandsSeparator: ',', decimalPlaces: 2, prefix: '$ ' }}"),
            'PriceFloor'=> array('colName' => 'PriceFloor', 'colModel' => "{name:'PriceFloor',index:'PriceFloor', width:80, align:'center', formatter:'number', formatoptions:{decimalSeparator:'.',thousandsSeparator: ',', decimalPlaces: 2, prefix: '$ ' }}"),
            'PriceCeiling' =>array('colName' => 'PriceCeiling', 'colModel' => "{name:'PriceCeiling',index:'PriceCeiling', width:80, align:'center', formatter:'number', formatoptions:{decimalSeparator:'.',thousandsSeparator: ',', decimalPlaces: 2, prefix: '$ ' }}"),
            'PriceFloorFBA'=> array('colName' => 'PriceFloorFBA', 'colModel' => "{name:'PriceFloorFBA',index:'PriceFloorFBA', width:80, align:'center', formatter:'number', formatoptions:{decimalSeparator:'.',thousandsSeparator: ',', decimalPlaces: 2, prefix: '$ ' }}"),
            'PriceCeilingFBA' =>array('colName' => 'PriceCeilingFBA', 'colModel' => "{name:'PriceCeilingFBA',index:'PriceCeilingFBA', width:90, align:'center', formatter:'number', formatoptions:{decimalSeparator:'.',thousandsSeparator: ',', decimalPlaces: 2, prefix: '$ ' }}"),
            'PriceFloorMFP' =>array('colName' => 'PriceFloorMFP', 'colModel' => "{name:'PriceFloorMFP',index:'PriceFloorMFP', width:90, align:'center', formatter:'number', formatoptions:{decimalSeparator:'.',thousandsSeparator: ',', decimalPlaces: 2, prefix: '$ ' }}"),
            'PriceCeilingMFP' =>array('colName' => 'PriceCeilingMFP', 'colModel' => "{name:'PriceCeilingMFP',index:'PriceCeilingMFP', width:90, align:'center', formatter:'number', formatoptions:{decimalSeparator:'.',thousandsSeparator: ',', decimalPlaces: 2, prefix: '$ ' }}"),
            'TargetInventory' => array('colName' => 'Target (Days)', 'colModel' => "{name:'TargetInventory',index:'TargetInventory', width:80, align:'center', editable:true,editrules:{number:true,minValue:0,maxValue:350}}"),
            'Projection' => array('colName' => 'DOIR', 'colModel' => "{name:'Projection',index:'Projection', width:80, align:'center',sorttype:'none', formatter: projection}"),
            'Excess' => array('colName' => 'Excess (Days)', 'colModel' => "{name:'Excess',index:'Excess', width:80, align:'center'}"),
            'BackOrders' => array('colName' => 'BackOrders', 'colModel' => "{name:'BackOrders',index:'BackOrders', width:100, align:'center',formatter:backorders}"),

	    'OnVessel' => array('colName' => 'OnVessel', 'colModel' => "{name:'OnVessel',index:'OnVessel', width:100, align:'center'}"),

            'ProductAlert' => array('colName' => 'ProductAlert', 'colModel' => "{name:'ProductAlert',index:'ProductAlert', width:80, align:'center',hidden:true, editable:true, edittype:'textarea',editoptions:{rows:'3',cols:'20',style:'width:85%'},editrules:{required:false, edithidden:true}} "),
           // 'categoryID' => array('colName' => 'OrdByCat', 'colModel' => "{name:'categoryID',index:'categoryID', width:200, align:'center',sorttype: 'number'}"),

            );
           // 'stats' => array('colName' => 'Stats', 'colModel' => "{name:'stats',index:'stats', width:50, align:'center',formatter: statistics}"));



        if ($this->MUsers->isValidUser($this->session->userdata('userid'), 881110) != 1) { //Usuarios sin statistics
            if (!$data['doir']) {
                //Acceso a usuarios no administradores sin DOIR
                unset($columns['Projection']);
                unset($columns['Excess']);
		        unset($columns['categoryID']);
                $data['colNames'] = $this->common->CreateColname($columns, 'colName');
                $data['colModel'] = $this->common->CreateColmodel($columns, 'colModel');
            } else {
                //Acceso a usuarios no administradores con DOIR
               // unset($columns['stats']);
                unset($columns['categoryID']);
                $data['colNames'] = $this->common->CreateColname($columns, 'colName');
                $data['colModel'] = $this->common->CreateColmodel($columns, 'colModel');
            }
        } else {
            if (!$data['doir']) {
                //Acceso a usuarios administradores sin DOIR pero con estadisticas

                unset($columns['Projection']);
                unset($columns['Excess']);
                // if ($this->session->userdata('userid') != 47) {
                //     unset($columns['categoryID']);
                // }

                $data['colNames'] = $this->common->CreateColname($columns, 'colName');
                $data['colModel'] = $this->common->CreateColmodel($columns, 'colModel');
            } else {

                //Acceso a usuarios administradores con DOIR y estadisticas

                unset($columns['categoryID']);

                $data['colNames'] = $this->common->CreateColname($columns, 'colName');
                $data['colModel'] = $this->common->CreateColmodel($columns, 'colModel');
            }
        }



        //cadena de busequeda para el grid
        $data['gridSearch'] = 'DataProdcat?search=' . $data['search'] . '&pl=' . $data['categories'] . '&qo=' . $data['qtyOrdered'] . '&do=' . $data['doir'] . '&hd=' . $data['historyDays'];

        //cadena de busequeda para el exportar a CSV
        $data['export'] = $data['categoriesOptions'][$data['categories']];


        //Generar el contenido....
        $this->build_content($data);
        $this->render_page();
    }


    function DataProdcat() {

        //Variables que envia el grid, incializarlas si estan vacias
        $page = !empty($_REQUEST['page']) ? $_REQUEST['page'] : '1'; // get the requested page
        $limit = !empty($_REQUEST['rows']) ? $_REQUEST['rows'] : '10'; // get how many rows we want to have into the gri
        $sidx = !empty($_REQUEST['sidx']) ? $_REQUEST['sidx'] : 'PC.ID'; // get index row - i.e. user click to sort
        $sord = !empty($_REQUEST['sord']) ? $_REQUEST['sord'] : 'asc';
        $examp = isset($_REQUEST['q']) ? $_REQUEST['q'] : 1;  //query number
        //variables que envia el formulario inicializarlas si no existen
        $search = !empty($_GET['search']) ? $_REQUEST['search'] : '';
        $category = !empty($_REQUEST['pl']) ? $_REQUEST['pl'] : '0';
        $historyDays = !empty($_REQUEST['hd']) ? $_REQUEST['hd'] : '30';
        $qtyOrdered = !empty($_REQUEST['qo']) ? $_REQUEST['qo'] : '0';
        $doir = !empty($_REQUEST['do']) ? $_REQUEST['do'] : '0';


        //Select utilizado para realizar el conteo del numero de registros devueltos por la consulta.
        $selectCount = 'Select count(*) as rowNum';

        //Select General con los campos necesarios para la vista
        $select = 'SELECT PC.[ID]
                ,PC.[Manufacturer]
                ,PC.[Name]
                ,GS.[GlobalStock]
                ,GS.[VirtualStock]
                ,GS.[TotalStock]
		,PC.[LowestSupplierCost]
                ,PC.[PriceFloor]
                ,PC.[PriceCeiling]
                ,PC.[PriceFloorFBA]
                ,PC.[PriceCeilingFBA]
                ,PC.[PriceFloorMFP]
                ,PC.[PriceCeilingMFP]
                ,TargetInventory
                ,ETA
                ,categoryID
                ,PC.[ProductAlert]';

        //Se utiliza en la seccion que extrae los registros de la hoja activa
        $selectSlice = 'SELECT ID
                ,[inventory].[dbo].fn_Vendor_Part_Number(ID) as VPN
                ,Manufacturer
                ,Name
                ,[inventory].[dbo].fn_get_Global_Stock(ID) as CurrentStock
                ,VirtualStock
                ,TotalStock
		,LowestSupplierCost
                ,PriceFloor
                ,PriceCeiling
                ,PriceFloorFBA
                ,PriceCeilingFBA
                ,PriceFloorMFP
                ,PriceCeilingMFP
                ,TargetInventory
                ,IsNull((SELECT SUM(Cast(CEILING(QtyBackordered) AS INT)) FROM [Inventory].[dbo].PurchaseOrderData WHERE SKU = ID),0) as BackOrders
		
		,([Inventory].[dbo].[fn_GetInTransitQty](ID)) as OnVessel

                ,ProductAlert
               --,inventory.dbo.fn_QB_ExpectedDate(ID) as ExpectedDate
               -- ,inventory.dbo.fn_GetCategoryName(categoryID) as categoryID
                ';


        $from = ' from ';
        $table = ' [inventory].[dbo].ProductCatalog AS PC';
        $join =  ' LEFT OUTER JOIN [inventory].[dbo].global_stocks AS GS ON (PC.ID = GS.ProductCatalogID)';
        $where = '';


        //Obtenemos todos los nombres de campos de la tabla productCatalog
        //
        $wherefields = array('PC.ID','PC.Manufacturer','PC.Name','GS.GlobalStock','GS.VirtualStock','GS.TotalStock');

        $fields = $this->MCommon->getAllfields($table);
        //creamos el were concatenando todos los nombres de campos y las palabras de busqueda
        $where .=$this->MCommon->concatAllWerefields($wherefields, $search);
        //Agregamos al where la busqueda por categoria
        if ($category) {
            $where .= ' and (PC.categoryID =' . $category . ')' ;
        }


        if ($qtyOrdered) {
            $where .=$this->MCatalog->filterByQtyOrdered($where);
        }

        $SQL = "{$selectCount}{$from}{$table}{$join}{$where}";



 
        $result = $this->MCommon->getOneRecord($SQL);

        $count = $result['rowNum'];

        if ($doir) {
            $selectSlice = $this->MCatalog->filterByDOIR($historyDays);
        }

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


        $SQL = "WITH mytable AS ($select , ROW_NUMBER() OVER (ORDER BY PC.ID {$sord}) AS RowNumber
                                FROM {$table}{$join}{$where})
               {$selectSlice}, RowNumber
               FROM mytable
                WHERE RowNumber BETWEEN {$start} AND {$finish}";

       // echo $SQL;
 
        $result = $this->MCommon->getSomeRecords($SQL);

    //    print_r($result);


        $responce = new stdClass();
        $responce->page = $page;
        $responce->total = $total_pages;
        $responce->records = $count;
        $i = 0;


        if ($doir) {
            foreach ($result as $row) {
                $responce->rows[$i]['id'] = $row['ID'];
                $responce->rows[$i]['cell'] = array($row['ID'],
                    $row['VPN'] ,
                    $row['Manufacturer'],
                    $row['Name'] = utf8_encode($row['Name']),
                    $row['CurrentStock'],
                    $row['VirtualStock'],
                    $row['TotalStock'],
		    $row['LowestSupplierCost'],
                    $row['PriceFloor'],
                    $row['PriceCeiling'],
                    $row['PriceFloorFBA'],
                    $row['PriceCeilingFBA'],
                    $row['PriceFloorMFP'],
                    $row['PriceCeilingMFP'],
                    $row['TargetInventory'],
                    $row['projection'],
                    $row['excess'] = $row['projection'] - $row['TargetInventory'],
                    $row['BackOrders'],

		    $row['OnVessel'],

                  //  $row['ExpectedDate'] = utf8_encode($row['ExpectedDate']),
                    $row['categoryID'],
                   
                );
                $i++;
            }
        } else {
            foreach ($result as $row) {
                $responce->rows[$i]['id'] = $row['ID'];
                $responce->rows[$i]['cell'] = array($row['ID'],
                    $row['VPN'],
                    $row['Manufacturer'],
                    $row['Name']= utf8_encode($row['Name']), 
                    $row['CurrentStock'],
                    $row['VirtualStock'],
                    $row['TotalStock'],
		    $row['LowestSupplierCost'],
                    $row['PriceFloor'],
                    $row['PriceCeiling'],
                    $row['PriceFloorFBA'],
                    $row['PriceCeilingFBA'],
                    $row['PriceFloorMFP'],
                    $row['PriceCeilingMFP'],
                    $row['TargetInventory'],
                    $row['BackOrders'],

                    $row['OnVessel'],

                    $row['ProductAlert'] = utf8_encode($row['ProductAlert']),
                    //$row['ExpectedDate'] = utf8_encode($row['ExpectedDate']),
                );
                $i++;
            }
        }
        echo json_encode($responce);
    }

    /*
     * 
     * 
     * 
     *
     */

    function showSkuData($row_id) {

        // Define custom CSS
        $this->css = array('table.css', 'fluid.css');

        $this->javascript = array('jqueryui/ui/jquery.ui.widget.js', 'jqueryui/ui/jquery.ui.tabs.js', 'jqgrid/jquery.jqGrid.fluid.js');

        $this->hasNav = false;
        $this->hasFooter = false;


        $data = array(
            'row_id' => "t_" . $row_id,
            'customfields' => $this->MCatalog->showCustomFields($row_id),
            'skuData' => $this->MCatalog->showSku($row_id),
            'skuNumber' => $row_id,
            'title' => "Show SKU |" . $row_id,
            'from' => 'Catalog/prodcat/',
            'color' => 'Green'
        );
        

       $this->load->view("pages/showsku", $data);
    }

    
    function skuinf($row_id) {

        // Define custom CSS
      
        $this->hasNav = false;
        $this->hasFooter = false;


        $skuData = $this->MCatalog->showSku($row_id);

        

        $customfields = $this->MCatalog->showCustomFields($row_id);

        $skuNumber = $skuData['SKU'];

        $data = array(
             'css' => array( 'jqueryui/redmond/jquery-ui-1.8.21.custom.css', 'table.css','style.css' ),
            'javascript' => array('jqueryui/ui/jquery-ui-1.8.21.custom.js',),
            'sku'=> $row_id,
            'row_id' => "t_" . $row_id,
            'customfields' => $customfields,
            'skuData' => $skuData,
            'skuNumber' => $skuNumber,
            'title' => "Show SKU |" . $row_id,
            'from' => 'Catalog/prodcat/',
            'color' => 'Green'
        );

        $this->load->view("pages/skuinf", $data);
    }
    
    
    /*
     * 
     * 
     * 
     * 
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

    /*
     * 
     * 
     * 
     * 
     */

    function cellDataEdit() {

        if (isset($_POST['TargetInventory'])) {
            $TargetInventory = $_POST['TargetInventory'];
            $id = $_POST['id'];
            $SQL = "update inventory.dbo.productcatalog set TargetInventory={$TargetInventory} where id={$id}";
        }
        if (isset($_POST['QtyOrdered'])) {
            $QtyOrdered = $_POST['QtyOrdered'];
            $id = $_POST['id'];
            $SQL = "update inventory.dbo.productcatalog set QtyOrdered='{$QtyOrdered}' where id={$id}";
           
            
        }
        if (isset($_POST['ETA'])) {
            $ETA = $_POST['ETA'];
            $id = $_POST['id'];
            $SQL = "update inventory.dbo.productcatalog set ETA='{$ETA}' where id={$id}";
        }

        $this->MCommon->saveRecord($SQL,'InventorySave');
    }


    public function dataEdit(){

        $oper = $this->input->post('oper');
        $id = $this->input->post('id');
        $productAlert = $this->input->post('ProductAlert');

        if ($oper='edit'){
            $SQL = "update inventory.dbo.productcatalog set ProductAlert='{$productAlert}' where ID={$id}";
        }


        $this->MCommon->saveRecord($SQL,'InventorySave');

    }
    

}
?>
