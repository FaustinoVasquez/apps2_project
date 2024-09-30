<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class History extends BP_Controller {

    public function __construct() {
        parent::__construct();

        $is_logged_in = $this->session->userdata('is_logged_in');

        if (!isset($is_logged_in) || $is_logged_in != true) {
            redirect(base_url(), 'refresh');
        }
    }

    function index($sku) {

        $this->title = "MI Technologiesinc - History";
        $this->description = " History";
        // Define custom CSS
        $this->css = array('menu.css', 'form.css', 'fluid.css', 'table.css', 'jqueryui/redmond/jquery-ui-1.8.21.custom.css', 'jqgrid/ui.jqgrid.css');
        // Define custom javascript
        $this->javascript = array('jqueryui/ui/jquery-ui-1.8.21.custom.js', 'jqgrid/i18n/grid.locale-en.js', 'jqgrid/jquery.jqGrid.min.js', 'jqgrid/jquery.jqGrid.fluid.js');
        $this->hasNav = False;

        
     
        $data['dateFrom'] = isset($_POST['dateFrom']) ? $_POST['dateFrom'] : $this->MCommon->fixInitialDate(date('m/d/Y', strtotime("-1 month")), 0);
        $data['dateTo'] = isset($_POST['dateTo']) ? $_POST['dateTo'] : $this->MCommon->fixFinalDate(date("m/d/Y"), 0);
        $data['filtered'] = isset($_POST['filtered']) ? $_POST['filtered'] : 0;
        $data['remaining'] = 0;
	$data['warehouseOptions'] =  $this->MCatalog->fillWarehouses();
	
	$data['warehouses']=0;
	
        $data['search'] = $sku;
        $data['title'] = 'History'; //Page Title
        $data['caption'] = 'History SKU';
        $data['from'] = '/Tabs/history/';
        $data['headers'] = "['id','Adjustment','Date','Origin','Destination','Input','Output','Qty','Comments','UserName','Audit','PO']";
        
        
        // Permiso para editar solo a los usuarion 137 y 155
        $data['edit'] = ($this->session->userdata('userid') == 137) || ($this->session->userdata('userid') == 155) ? 'true' : 'false';
	
        
	 //Cargamos la libreria comumn
        $this->load->library('common');

        //Reemplazamos los campos del formularion por los que estan en el arreglo $_POST.
        $data = $this->common->fillPost('ALL', $data);
	
		
        $data['body'] = "[ {name:'id',index:'id',width:60, align:'center'},
	        {name:'Adjustment_Id',index:'Adjustment_Id',width:80, align:'center', editable:true, editoptions: {readonly: 'readonly'},editrules: {edithidden: true}},
                {name:'Adjustment_Date',index:'Adjustment_Date', width:80, align:'center',sortable:false, formatter:'date', formatoptions: { srcformat:'Y/m/d', newformat:'m/d/Y'}},
                {name:'Origin',index:'Origin', width:150, align:'left', sortable:false},
                {name:'Destination',index:'Destination', width:150, align:'left', sortable:false, sortable:false},
                {name:'inputs',index:'inputs', width:60, align:'center', sortable:false,formatter:'number', formatoptions:{decimalSeparator:'.',thousandsSeparator: ',', decimalPlaces: 2, prefix: '' }},
                {name:'outputs',index:'outputs', width:60, align:'center', sortable:false, formatter:'number', formatoptions:{decimalSeparator:'.',thousandsSeparator: ',', decimalPlaces: 2, prefix: '' }},
                {name:'current_qty',index:'current_qty', width:70, align:'center', sortable:false, formatter:'number', formatoptions:{decimalSeparator:'.',thousandsSeparator: ',', decimalPlaces: 2, prefix: '' }},
                {name:'comments',index:'comments', width:140, align:'left', sortable:false},
                {name:'username',index:'username', width:140, align:'left', sortable:false},
		{name:'Audit',index:'Audit', width:140, align:'left', editable:true,editoptions: {size:50, maxlength: 250}},
		{name:'po',index:'po', width:70, align:'left', sortable:false},
  	]";

        $this->build_content($data);
        $this->render_page();
    }


    function gridDataHistory() {
        $search = $_GET['q'];
        $InitialDate = isset($_GET['dateFrom']) ? $_GET['dateFrom'] : date('m/d/Y', strtotime("-1 month"));
        $FinalDate = isset($_GET['dateTo']) ? $_GET['dateTo'] : date("m/d/Y");
        $Filtered = isset($_GET['filtered']) ? $_GET['filtered'] : 0;
        $FinalDateFixed = $this->MCommon->fixFinalDate($FinalDate, 1);
        $InitialDateFixed = $this->MCommon->fixInitialDate($InitialDate, 1);


        $page = isset($_GET['page']) ? $_GET['page'] : 1; // get the requested page
        $limit = isset($_GET['rows']) ? $_GET['rows'] : 10; // get how many rows we want to have into the grid
        $sidx = isset($_GET['sidx']) ? $_GET['sidx'] : 'Adjustment_Id'; // get index row - i.e. user click to sort
        $sord = isset($_GET['sord']) ? $_GET['sord'] : 'desc'; // get the direction

	$fdate = str_replace('/','-',$FinalDateFixed);
	$idate = str_replace('/','-',$InitialDateFixed);
	
	
	
	

	/* Param1 = SKU
	 * Param2= Almacen
	 * Param3 = inicio del rango, -1 para razones de conteo
	 * Param3 = Fin del rango, 0 para razones de conteo
	 */
	$select = "exec inventory.dbo.sp_Analitic_History {$search},{$Filtered},1,'{$idate}','{$fdate}'";
	

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
        $start = ($start <= 0) ? 1 : $start ;
        $finish = $start + $limit;

	//$select = "exec inventory.dbo.sp_Analitic_History {$search},{$Filtered},{$start},{$finish},'{$idate}','{$fdate}'";
	

      //$result = $this->MCommon->getSomeRecords($select);
	

        $responce = new stdClass();
        $responce->page = $page;
        $responce->total = $total_pages;
        $responce->records = $count;

        $i=0;
	foreach ($result as $row) {
            $responce->rows[$i]['id'] = $row['id'];
            $responce->rows[$i]['cell'] = array($row['id'],
                $row['Adjustment_Id'] = utf8_encode($row['Adjustment_Id']),
                $row['Adjustment_Date'] = utf8_encode($row['Adjustment_Date']),
                $row['Origin'] = utf8_encode($row['Origin']),
                $row['Destination'] = utf8_encode($row['Destination']),
                $row['inputs'] = utf8_encode($row['inputs']),
                $row['outputs'] = utf8_encode($row['outputs']),
                $row['current_qty'] = utf8_encode($row['current_qty']),
                $row['comments'] = utf8_encode($row['comments']),
                $row['username'] = utf8_encode($row['username']),
		$row['Audit'] = utf8_encode($row['Audit']), 
		$row['po'] = utf8_encode($row['po']), 
            );
            $i++;
        }

        echo json_encode($responce);
    }

    
    function saveData(){
	
	$SQL="exec sp_save_audit_comment {$_POST['Adjustment_Id']},'{$_POST['Audit']}'";

	$this->MCommon->saveRecord($SQL,'InventorySave');

    }
    
    function csvExportHistory($name) {

        header('Content-type: application/vnd.ms-excel');
        header("Content-Disposition: attachment; filename= history_" . $name . ".xls");
        header("Pragma: no-cache");

        $buffer = $_POST['csvBuffer'];

        try {
            echo $buffer;
        } catch (Exception $e) {
            
        }
    }
    
    
}

?>
