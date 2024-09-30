<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Suppliercost extends BP_Controller {

    public function __construct() {
        parent::__construct();

        $is_logged_in = $this->session->userdata('is_logged_in');

        if (!isset($is_logged_in) || $is_logged_in != true) {
            redirect(base_url(), 'refresh');
        }

        // if ($this->MUsers->isValidUser($this->session->userdata('userid'), 883500) != 1) {// 881100 prodcat Access
        //     redirect('Catalog/prodcat', 'refresh');
        // }
    }

    function index() {

        $this->title = "MI Technologiesinc - Supplier Cost";

        $this->description = "Supplier Cost";

        $this->css = array('form.css', 'fluid.css', 'table.css', 'jqueryui/redmond/jquery-ui-1.8.21.custom.css', 'jqgrid/ui.jqgrid.css', 'menu.css',);

        // Define custom javascript
        $this->javascript = array('jqueryui/ui/jquery-ui-1.8.21.custom.js', 'jqgrid/i18n/grid.locale-en.js', 'jqgrid/jquery.jqGrid.min.js', 'jqgrid/jquery.jqGrid.fluid.js', 'popup.js');

        $this->load->library('Layout');
        $menu = new Layout;

        $data = array(
            'menu' => $menu->show_menu($this->MUsers->getUserFullName($this->session->userdata('userid'))),
            'from' => '/Reports/suppliercost/',
            'nameGrid' => 'suppliercost',
            'namePager' => 'suppliercostPager',
            'caption' => 'Supplier Cost',
            'export' => 'suppliercost',
            'subgrid' => 'true',
            'sort' => 'desc',
            'search' => '',
            'showskudata' => '/Catalog/prodcat/showSkuData/',
            'customerid' => $this->MUsers->getCustomerId($this->session->userdata('userid')),
        );

        $data['colNames'] = "['ID','Name','QoH','ArcliteSKU','ArcLiteUnitCost','GlorySKU','GloryUnitCost','ClpSKU','ClpUnitCost','GrandBulbsSKU','GrandBulbsCost','BackOrders']";

        $data['colModel'] = "[
                {name:'ID',index:'ID', width:70, align:'left',align:'center'},
                {name:'Name',index:'Name', width:390, align:'left'},
                {name:'CurrentStock',index:'CurrentStock', width:60, align:'center'},
                {name:'ArcliteSKU',index:'ArcliteSKU', width:80, align:'center'},
                {name:'ArcLiteUnitCost',index:'ArcLiteUnitCost', width:70, align:'center',formatter:'currency', formatoptions:{decimalSeparator:'.', thousandsSeparator: ',', decimalPlaces: 2, prefix: '$ '}},
                {name:'GlorySKU',index:'GlorySKU', width:80, align:'center'},
                {name:'GloryUnitCost',index:'GloryUnitCost', width:70, align:'center',formatter:'currency', formatoptions:{decimalSeparator:'.', thousandsSeparator: ',', decimalPlaces: 2, prefix: '$ '}},
                {name:'ClpSKU',index:'ClpSKU', width:80,align:'center'},
                {name:'ClpUnitCost',index:'ClpUnitCost', width:70, align:'center',formatter:'currency', formatoptions:{decimalSeparator:'.', thousandsSeparator: ',', decimalPlaces: 2, prefix: '$ '}},
                {name:'GrandBulbsSKU',index:'GrandBulbsSKU', width:80, align:'center'},
                {name:'GrandBulbsCost',index:'GrandBulbsCost', width:70,  align:'center', formatter:'currency', formatoptions:{decimalSeparator:'.', thousandsSeparator: ',', decimalPlaces: 2, prefix: '$ '}},
		{name:'BackOrders',index:'BackOrders', width:80,align:'center', formatter:'number', formatoptions:{decimalSeparator:'.',thousandsSeparator: ',', decimalPlaces: 2, prefix: '' }},
  	]";

        //Cargamos la libreria comumn
        $this->load->library('common');

        //Reemplazamos los campos del formulario por los que estan en el arreglo $_POST.
        $data = $this->common->fillPost('ALL', $data);

        //cadena de busqueda del grid 
        $data['gridSearch'] = 'datasuppliercost?ds=' . $data['search'];


        $this->build_content($data);
        $this->render_page();
    }

    function datasuppliercost() {

        $page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1; // get the requested page
        $limit = isset($_REQUEST['rows']) ? $_REQUEST['rows'] : 10; // get how many rows we want to have into the gri
        $sidx = isset($_REQUEST['sidx']) ? $_REQUEST['sidx'] : 'ID'; // get index row - i.e. user click to sort
        $sord = isset($_REQUEST['sord']) ? $_REQUEST['sord'] : 'asc';
        $search = !empty($_GET['ds']) ? $_REQUEST['ds'] : '';


        $table = 'Inventory.dbo.[FP-Generic-Vendor-CostInfo] ';
        //   $table1 ='[Inventory].[dbo].[ProductCatalog] b ';
        $where = '';
        $from = ' FROM ';


        $select = "SELECT  ID,
                   Name,
                   ArcliteSKU,
                   ArcLiteUnitCost,
                   GlorySKU,
                   GloryUnitCost,
                   ClpSKU,
                   ClpUnitCost,
                   GrandBulbsSKU,
                   GrandBulbsCost,
		   BackOrders
                   ";

        $selectSlice = "SELECT  ID,
                        Name,
                        inventory.dbo.fn_get_Global_Stock(ID) as CurrentStock,
                        ArcliteSKU,
                        ArcLiteUnitCost,
                        GlorySKU,
                        GloryUnitCost,
                        ClpSKU,
                        ClpUnitCost,
                        GrandBulbsSKU,
                        GrandBulbsCost,
			BackOrders
                        ";

        $wherefields = array('ID', 'Name', 'ArcliteSKU', 'ArcLiteUnitCost', 'GlorySKU', 'GloryUnitCost', 'ClpSKU', 'ClpUnitCost', 'GrandBulbsSKU', 'GrandBulbsCost');

        $where .=$this->MCommon->concatAllWerefields($wherefields, $search);


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


        $SQL = "WITH mytable AS ({$select},ROW_NUMBER() OVER (ORDER BY {$sidx} {$sord} ) AS RowNumber
	        FROM {$table}{$where}) 
               {$selectSlice},RowNumber
               FROM mytable
               WHERE RowNumber BETWEEN {$start} AND {$finish}";


        $result = $this->MCommon->getSomeRecords($SQL);


        $responce = new stdClass();
        $responce->page = $page;
        $responce->total = $total_pages;
        $responce->records = $count;

        $i = 0;
        foreach ($result as $row) {
            $responce->rows[$i]['ID'] = $row['ID'];
            $responce->rows[$i]['cell'] = array($row['ID'],
                $row['Name'] = utf8_encode($row['Name']),
                $row['CurrentStock'] = utf8_encode($row['CurrentStock']),
                $row['ArcliteSKU'] = utf8_encode($row['ArcliteSKU']),
                $row['ArcLiteUnitCost'] = utf8_encode($row['ArcLiteUnitCost']),
                $row['GlorySKU'] = utf8_encode($row['GlorySKU']),
                $row['GloryUnitCost'] = utf8_encode($row['GloryUnitCost']),
                $row['ClpSKU'] = utf8_encode($row['ClpSKU']),
                $row['ClpUnitCost'] = utf8_encode($row['ClpUnitCost']),
                $row['GrandBulbsSKU'] = utf8_encode($row['GrandBulbsSKU']),
                $row['GrandBulbsCost'] = utf8_encode($row['GrandBulbsCost']),
		$row['BackOrders'] = utf8_encode($row['BackOrders']),
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
