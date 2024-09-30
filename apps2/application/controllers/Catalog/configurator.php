<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Configurator extends BP_Controller {

    public function __construct() {
        parent::__construct();

        $is_logged_in = $this->session->userdata('is_logged_in');

        if (!isset($is_logged_in) || $is_logged_in != true) {
            redirect(base_url(), 'refresh');
        }
                
         if ($this->MUsers->isValidUser($this->session->userdata('userid'), 881200) != 1) {
               redirect('Catalog/prodcat', 'refresh');
           }
    }

    function index() {
        // Define Meta
        $this->title = "MI Technologiesinc - Configurator";

        $this->description = "Configurator";

        // Define custom CSS
        $this->css = array('menu.css', 'form.css', 'fluid.css', 'table.css', 'jqueryui/redmond/jquery-ui-1.8.21.custom.css', 'jqgrid/ui.jqgrid.css');

        // Define custom javascript
        $this->javascript = array('jqueryui/ui/jquery-ui-1.8.21.custom.js', 'jqgrid/i18n/grid.locale-en.js', 'jqgrid/jquery.jqGrid.min.js', 'jqgrid/jquery.jqGrid.fluid.js', 'popup.js');

        $this->load->library('Layout');
        $menu = new Layout;


        $userid = $this->session->userdata('userid');

        $data = array(
            'menu' => $menu->show_menu($this->MUsers->getUserFullName($this->session->userdata('userid'))),
            //Datos para el grid
            'caption' => 'Configurator',
            'from' => '/Catalog/configurator/',
            'search' => '',
            'showskudata' => '/Catalog/prodcat/showSkuData/',
        );

        $data['headers'] = "['SKU','ProductLine', 'Original Manf','Name','ManufacturerPN','Alt MF','Alt PN','Model','Alt Model','InventoryMin','InventoryMax','QOH','vQOH']";
        $administrator = $this->MUsers->isadminuser($userid);
        $data['administrator'] = $administrator;
        $data['body'] = "[
                {name:'SKU',index:'SKU', width:50, align:'left',sorttype:'int' },
                {name:'ProductLineID',index:'ProductLineID', width:200, align:'left'},
                {name:'Manufacturer',index:'Manufacturer', width:80, align:'left'},
                {name:'Name',index:'Name', width:240, align:'left'},
                {name:'ManufacturerPN',index:'ManufacturerPN', width:100, align:'left'},
                {name:'AltManufacturer',index:'AltManufacturer', width:70, align:'center'},
                {name:'AltPartNumber',index:'AltPartNumber', width:80, align:'center'},
                {name:'Model',index:'Model', width:80, align:'center'},
                {name:'AltModel',index:'AltModel', width:70, align:'center'},
                {name:'InventoryMin',index:'InventoryMin', width:60, align:'center',hidden:true},
                {name:'InventoryMax',index:'InventoryMax', width:60, align:'center',hidden:true},               
                {name:'CurrentStock',index:'CurrentStock', width:60, align:'center' , formatter: fvirtualstock},
                {name:'VirtualStock',index:'VirtualStock', width:60, align:'center' , formatter:'number', formatoptions:{decimalSeparator:'.',thousandsSeparator: ',', decimalPlaces: 2, prefix: '' }}
                
  	]";

        //Cargamos la libreria comumn
        $this->load->library('common');

        //Reemplazamos los campos del formularion por los que estan en el arreglo $_POST.
        $data = $this->common->fillPost('ALL', $data);


        if (($data['search']) != '') {

            $data['mydata'] = $this->gridDataConfigurator($data['search']);
        } else {
            $data['mydata'] = '[]';
        }


        $this->build_content($data);
        $this->render_page();
    }

    function explode_words($ls) {
        $word = array();
        $words = explode(" ", $ls);
        foreach ($words as $row)
            if ($row <> "") {
                $word[] = $row;
            }
        return $word;
    }

    function My_Where_Merged($ltable, $ls, $lWhereFields) {

        $where = '';

        $word = $this->explode_words($ls);

        //Obtener numero de campos
        if ($lWhereFields == '') {
            $SQL = mssql_query("select top(1) * from {$ltable}");
        } else {
            $SQL = mssql_query("select {$lWhereFields} from {$ltable}");
        }
        for ($j = 0; $j < count($word); $j++) {
            if ($j == 0)
                $where .= "(";
            else
                $where .= " and (";

            for ($i = 0; $i < mssql_num_fields($SQL) - 1; ++$i) {
                $where .= "CONVERT(varchar(250)," . mssql_field_name($SQL, $i) . ",0) + ";
            }
            $where .= "CONVERT(varchar(250)," . mssql_field_name($SQL, $i) . ",0) like '%{$word[$j]}%') ";
        }
        return $where;
    }

    function Insert_alt_models($pquery, $data) {
        $result = $this->MCommon->getSomeRecords($pquery);

        foreach ($result as $value) {
            $data = $this->search_and_insert_AltPartnumbers($value['model'], $value['altmodel'], $value['AltManufacturer'], $data);
        }

        return $data;
    }

    function search_and_insert_AltPartnumbers($pmodel, $paltmodel, $paltmanufacturer, $data) {

        $laltpartnumber = $this->Get_Partnumber4Model($pmodel);

        $SQL = " SELECT productCatalogID FROM Inventory.dbo.Compatibility WHERE PartNumber = '{$laltpartnumber}'";
       
    //   print_r($SQL);

	$result = $this->MCommon->getSomeRecords($SQL);
	//print_r($result);

        $j = count($data);

        for ($i = 0; $i < count($result); $i++) {
	//echo("<script>console.log('".$j."-".$result[$i]['productCatalogID']."')</script>");
            $data[$j]['SKU'] = $result[$i]['productCatalogID'];
            $data[$j]['Manufacturer'] = '';
            $data[$j]['ManufacturerPN'] = '';
            $data[$j]['AltManufacturer'] = $paltmanufacturer;
            $data[$j]['AltPartNumber'] = $laltpartnumber;
            $data[$j]['Model'] = utf8_encode($pmodel);
            $data[$j]['AltModel'] = $paltmodel;
            $j++;
        }
       //print_r($data);
        return $data;
    }

    function Get_Partnumber4Model($pmodel) {
        $SQL = "SELECT PartNumber FROM Inventory.dbo.CompatibilityDetails WHERE model = '{$pmodel}'";
	//print_r($SQL);       
        $result = $this->MCommon->getOneRecord($SQL);
        $data = $result['PartNumber'];
	//echo("<script>console.log('".$result['PartNumber']."')</script>");

        return $data;
    }

    function insert_models($pquery, $data) {
        $result = $this->MCommon->getSomeRecords($pquery);
        //print_r($result);

        if ($result != '') {
            foreach ($result as $value) {
                $data = $this->search_and_insert_AltPartnumbers($value['model'], '', $value['AltManufacturer'], $data);
            }
        }
        //print_r($data);
        return $data;
    }

    function insert_alt_alt_PartNumber($pquery, $data) {
        $result = $this->MCommon->getSomeRecords($pquery);

        foreach ($result as $value) {
            $data = $this->search_and_insert_compatibilities($value['AltPartNumber'], $value['AltManufacturer'], $data);
        }

        return $data;
    }

    function search_and_insert_compatibilities($pAltPArtnumber, $pAltmanufacturer, $data) {

        $SQL = "SELECT ProductCatalogID ,PartNumber, Manufacturer  FROM Inventory.dbo.Compatibility 
                where (PartNumber = '{$pAltPArtnumber}') and (Manufacturer = '{$pAltmanufacturer}') order by productCatalogID";
        //print_r($SQL);
	$result = $this->MCommon->getSomeRecords($SQL);
      
        $j = count($data);

        for ($i = 0; $i < count($result); $i++) {
	//echo("<script>console.log('".$j."-".$result[$i]['ProductCatalogID']."-".$pAltPArtnumber."-".$pAltmanufacturer."')</script>");
        //echo("<script>console.log('".$result[$i]['ProductCatalogID']."')</script>");
            $data[$j]['SKU'] = $result[$i]['ProductCatalogID'];
            $data[$j]['Manufacturer'] = $result[$i]['Manufacturer'];
            $data[$j]['ManufacturerPN'] = '';
            $data[$j]['AltManufacturer'] = $pAltmanufacturer;
            $data[$j]['AltPartNumber'] = $pAltPArtnumber;
            $data[$j]['Model'] = '';
            $data[$j]['AltModel'] = '';
            $j++;
        }

        return $data;
    }

    function insert_compatibilities($pquery, $data) {
        $result = $this->MCommon->getSomeRecords($pquery);
        foreach ($result as $value) {
            $data = $this->search_and_insert_compatibilities($value['AltPartNumber'], $value['AltManufacturer'], $data);
        }
        return $data;
    }

    function insert_SKUS($pquery, $data) {
        //echo($pquery);

        $result = $this->MCommon->getSomeRecords($pquery);

        $j = count($data);

        //echo("<script>console.log('".$j.$pquery."')</script>");

        for ($i = 0; $i < count($result); $i++) {
            $data[$j]['SKU'] = $result[$i]['SKU'];
            //echo("<script>console.log('".$result[$i]['SKU']."')</script>");
            $data[$j]['Manufacturer'] = $result[$i]['Manufacturer'];
            $data[$j]['ManufacturerPN'] = $result[$i]['ManufacturerPN'];
            $data[$j]['AltManufacturer'] = $result[$i]['AltManufacturer'];
            $data[$j]['AltPartNumber'] = $result[$i]['AltPartNumber'];
            //$data[$i]['Model'] = '';
            //$data[$i]['AltModel'] = '';
            $j++;
        }

        return $data;
    }

    function ordenar_array() {
        $n_parametros = func_num_args(); // Obenemos el número de parámetros 
        if ($n_parametros < 3 || $n_parametros % 2 != 1) { // Si tenemos el número de parametro mal... 
            return false;
        } else { // Hasta aquí todo correcto...veamos si los parámetros tienen lo que debe ser... 
            $arg_list = func_get_args();

            if (!(is_array($arg_list[0]) && is_array(current($arg_list[0])))) {
                return false; // Si el primero no es un array...MALO! 
            }
            for ($i = 1; $i < $n_parametros; $i++) { // Miramos que el resto de parámetros tb estén bien... 
                if ($i % 2 != 0) {// Parámetro impar...tiene que ser un campo del array... 
                    if (!array_key_exists($arg_list[$i], current($arg_list[0]))) {
                        return false;
                    }
                } else { // Par, no falla...si no es SORT_ASC o SORT_DESC...a la calle! 
                    if ($arg_list[$i] != SORT_ASC && $arg_list[$i] != SORT_DESC) {
                        return false;
                    }
                }
            }
            $array_salida = $arg_list[0];

            // Una vez los parámetros se que están bien, procederé a ordenar... 
            $a_evaluar = "foreach (\$array_salida as \$fila){\n";
            for ($i = 1; $i < $n_parametros; $i+=2) { // Ahora por cada columna... 
                $a_evaluar .= "  \$campo{$i}[] = \$fila['$arg_list[$i]'];\n";
            }
            $a_evaluar .= "}\n";
            $a_evaluar .= "array_multisort(\n";
            for ($i = 1; $i < $n_parametros; $i+=2) { // Ahora por cada elemento... 
                $a_evaluar .= "  \$campo{$i}, SORT_REGULAR, \$arg_list[" . ($i + 1) . "],\n";
            }
            $a_evaluar .= "  \$array_salida);";
            // La verdad es que es más complicado de lo que creía en principio... :) 

            eval($a_evaluar);
            return $array_salida;
        }
    }

    //function gridDataConfigurator() {

    function gridDataConfigurator($searchfield) {

        $examp = isset($_GET['q']) ? $_GET['q'] : 1;  //query number
        $page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1; // get the requested page
        $limit = isset($_REQUEST['rows']) ? $_REQUEST['rows'] : 10; // get how many rows we want to have into the gri
        $sidx = isset($_REQUEST['sidx']) ? $_REQUEST['sidx'] : 'ID'; // get index row - i.e. user click to sort
        $sord = isset($_REQUEST['sord']) ? $_REQUEST['sord'] : '';
        //  $searchfield = isset($_GET['sf']) ? $_GET['sf'] : '';


        $totalrows = isset($_REQUEST['totalrows']) ? $_REQUEST['totalrows'] : false;
        if ($totalrows) {
            $limit = $totalrows;
        }

        if (!$sidx)
            $sidx = 1;


        $ltable1 = "[Inventory].[dbo].ProductCatalog";
        $ltable2 = "[Inventory].[dbo].Compatibility";
        $ltable3 = "[Inventory].[dbo].CompatibilityDetails";
        $ltable4 = "[Inventory].[dbo].CompatibilityAlternativePN";
        $lTable5 = "[Inventory].[dbo].CompatibilityAlternativeModels";


        $lSelectfields1 = "ID as SKU, Manufacturer , ManufacturerPN, '' as AltManufacturer, '' as AltPartNumber, '' as model, '' as altmodel";
        $lSelectfields2 = "ProductCatalogID as SKU,'' as Manufacturer, '' as ManufacturerPN, Manufacturer as AltManufacturer, PartNumber as AltPartNumber, '' as model, '' as altmodel";
        $lSelectfields3 = "0 as SKU, '' as Manufacturer, '' as ManufacturerPN, OriginalManufacturer as AltManufacturer, PartNumber as AltPartNumber, model, '' as altmodel ";
        $lSelectfields4 = "0 as SKU, '' as Manufacturer, '' as ManufacturerPN, OriginalMAnufacturer as AltManufacturer, PartNumber as AltPartNumber, '' as model, '' as altmodel";
        $lSelectfields5 = "0 as SKU, '' as Manufacturer, '' as ManufacturerPN, OriginalMAnufacturer as AltManufacturer, '' as AltPartNumber, OriginalModel as model, AlternativeModel as altmodel";


        $lWhereFields1 = "";
        $lWhereFields2 = "Partnumber, manufacturer";
        $lWhereFields3 = "Model";
        $lWhereFields4 = "AlternativePN";
        $lWhereFields5 = "AlternativeModel";



        $lwhere1 = $this->My_Where_Merged($ltable1, $searchfield, $lWhereFields1);
        $lwhere2 = $this->My_Where_Merged($ltable2, $searchfield, $lWhereFields2);
        $lwhere3 = $this->My_Where_Merged($ltable3, $searchfield, $lWhereFields3);
        $lwhere4 = $this->My_Where_Merged($ltable4, $searchfield, $lWhereFields4);
        $lwhere5 = $this->My_Where_Merged($lTable5, $searchfield, $lWhereFields5);


        $lquery1 = "SELECT " . $lSelectfields1 . " FROM " . $ltable1 . " WHERE " . $lwhere1;
        $lquery2 = "SELECT " . $lSelectfields2 . " FROM " . $ltable2 . " WHERE " . $lwhere2;
        $lquery3 = "SELECT " . $lSelectfields3 . " FROM " . $ltable3 . " WHERE " . $lwhere3;
        $lquery4 = "SELECT " . $lSelectfields4 . " FROM " . $ltable4 . " WHERE " . $lwhere4;
        $lquery5 = "SELECT " . $lSelectfields5 . " FROM " . $lTable5 . " WHERE " . $lwhere5;

       // print_r($lquery1);
	//print $lqury2;
	//echo("console.log(".$lquery5.");");
	
        $data = array();

        // Obtener los arreglos
        $data1 = $this->Insert_alt_models($lquery5, $data);
        $data2 = $this->insert_models($lquery3, $data1);
        $data3 = $this->insert_alt_alt_PartNumber($lquery4, $data2);
        $data4 = $this->insert_compatibilities($lquery2, $data3);
        $data5 = $this->insert_SKUS($lquery1, $data4);

	//print_r($data4);

        //eliminar arreglos repetidos y reindexar main arreglo
        for ($j = 0; $j < count($data5); $j++) {
            for ($i = $j + 1; $i < count($data5); $i++) {
                if ($data5[$j]['SKU'] == $data5[$i]['SKU']) {
                    $data5[$i]['SKU'] = '00000';
                }
            }
        }



        $data6 = '';

        for ($j = 0; $j < count($data5); $j++) {
            if (($data5[$j]['SKU']) != '00000') {
                $data6[] = $data5[$j];
            }
        }

	//print_r($data6);

        if ($data6 != '') {

            $data7 = '';
            for ($i = 0; $i < count($data6); $i++) {

                $SQL = "SELECT Inventory.dbo.fn_GetProductLineName(ProductLineID) as ProductLineID,Manufacturer,Name,ManufacturerPN,InventoryMin,InventoryMax,inventory.dbo.fn_get_Global_Stock(id) as CurrentStock,VirtualStock  FROM Inventory.dbo.ProductCatalog WHERE (ID = '{$data6[$i]['SKU']}')";
                //print_r($SQL);
               

                $query = $this->MCommon->getOneRecord($SQL);
               
                $data7[$i]['SKU'] = isset($data6[$i]['SKU']) ? $data6[$i]['SKU'] : '';
                $data7[$i]['ProductLineID'] = $query['ProductLineID'];

                $data7[$i]['Manufacturer'] = isset($query['Manufacturer']) ? $query['Manufacturer'] : '';
                $data7[$i]['Name'] = isset($query['Name']) ? $query['Name'] : '';
                $data7[$i]['ManufacturerPN'] = isset($data6[$i]['ManufacturerPN']) ? $query['ManufacturerPN'] : '';
                $data7[$i]['AltManufacturer'] = isset($data6[$i]['AltManufacturer']) ? $data6[$i]['AltManufacturer'] : '';
                $data7[$i]['AltPartNumber'] = isset($data6[$i]['AltPartNumber']) ? $data6[$i]['AltPartNumber'] : '';
                $data7[$i]['Model'] = isset($data6[$i]['Model']) ? $data6[$i]['Model'] : '';
                $data7[$i]['AltModel'] = isset($data6[$i]['AltModel']) ? $data6[$i]['AltModel'] : '';
                $data7[$i]['InventoryMin'] = isset($query['InventoryMin']) ? $query['InventoryMin'] : '';
                $data7[$i]['InventoryMax'] = isset($query['InventoryMax']) ? $query['InventoryMax'] : '';
                $data7[$i]['CurrentStock'] = isset($query['CurrentStock']) ? $query['CurrentStock'] : '';
                $data7[$i]['VirtualStock'] = isset($query['VirtualStock']) ? $query['VirtualStock'] : '';
            }

		//print_r($data7);

            $data7 = $this->ordenar_array($data7, 'ProductLineID', SORT_ASC, 'SKU', SORT_ASC) or die('<br>ERROR!<br>');



            $start = '';
            $finish = '';
            
            
            
            $arreglo = '';
            for ($i = 0; $i < count($data7); $i++) {
                if ($i == 0) {
                    $start = "[{";
                } else {
                    $start = "{";
                }
                if ($i == (count($data7) - 1)) {
                    $finish = "}]";
                } else {
                    $finish = "},";
                }
                $arreglo .= "{$start}
             SKU:'{$data7[$i]['SKU']}',   
            ProductLineID:'{$data7[$i]['ProductLineID']}',
            
            Manufacturer:'{$data7[$i]['Manufacturer']}',
            Name:'{$data7[$i]['Name']}',
            ManufacturerPN:'{$data7[$i]['ManufacturerPN']}',
            AltManufacturer:'{$data7[$i]['AltManufacturer']}',
            AltPartNumber:'{$data7[$i]['AltPartNumber']}',
            Model:'{$data7[$i]['Model']}',
            AltModel:'{$data7[$i]['AltModel']}',
            InventoryMin:'{$data7[$i]['InventoryMin']}',
            InventoryMax:'{$data7[$i]['InventoryMax']}',
            CurrentStock:'{$data7[$i]['CurrentStock']}',
            VirtualStock:'{$data7[$i]['VirtualStock']}'{$finish}";
            }
        } else {
            $arreglo = '[]';
        }


        return $arreglo;
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
        header("Content-Disposition: attachment; filename=Configurator-" . date("D-M-j") . ".xls");
        header("Pragma: no-cache");

        $buffer = $_POST['csvBuffer'];

        try {
            echo $buffer;
        } catch (Exception $e) {
            
        }
    }

}
?>

