<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Advanced extends BP_Controller {

	 public function __construct() {
        parent::__construct();

        $is_logged_in = $this->session->userdata('is_logged_in');

        if (!isset($is_logged_in) || $is_logged_in != true) {
            redirect(base_url(), 'refresh');
        }
        
    }

	function index($sku) {

		$this->title = "MI Technologiesinc - Advanced";
        $this->description = " Advanced";
        // Define custom CSS
        $this->css = array('menu.css', 'form.css', 'fluid.css', 'table.css', 'jqueryui/redmond/jquery-ui-1.8.21.custom.css', 'jqgrid/ui.jqgrid.css');
        // Define custom javascript
        $this->javascript = array('jqueryui/ui/jquery-ui-1.8.21.custom.js', 'jqgrid/i18n/grid.locale-en.js', 'jqgrid/jquery.jqGrid.min.js', 'jqgrid/jquery.jqGrid.fluid.js','jquery/jquery-ui-timepicker-addon.js');
        $this->hasNav = False;

 		//Cargamos la libreria comumn
        $this->load->library('common');

		$columns = array(
            'SKU' => array('colName' => 'SKU', 'colModel' => "{name:'ID',index:'ID', width:90, align:'center'}"),
            'DumpFBM' => array('colName' => 'DumpFBM', 'colModel' => "{name:'DumpFBM',index:'DumpFBM', width:90, align:'center', editable: false, edittype: 'checkbox', editoptions: { value: 'true:false' }, formatter: 'checkbox', formatoptions: { disabled: true} }"),
            'DumpFBMEndDate' => array('colName' => 'DumpFBMEndDate', 'colModel' => "{name:'DumpFBMEndDate',index:'DumpFBMEndDate', width:90, align:'center', editable: true,editoptions: {size: 20,maxlengh: 10}}"),
            'DumpFBA' => array('colName' => 'DumpFBA', 'colModel' => "{name:'DumpFBA',index:'DumpFBA', width:90, align:'center',editable: false, edittype: 'checkbox', editoptions: { value: 'true:false' }, formatter: 'checkbox', formatoptions: { disabled: true} }"),
            'DumpFBAEndDate' => array('colName' => 'DumpFBAEndDate', 'colModel' => "{name:'DumpFBAEndDate',index:'DumpFBAEndDate', width:90, align:'center', editable: true,editoptions: {size: 20,maxlengh: 10}}"),
            );


			$data['colNames'] = $this->common->CreateColname($columns, 'colName');
            $data['colModel'] = $this->common->CreateColmodel($columns, 'colModel');
            $data['caption'] = 'Advanced Columns';
            $data['sku'] = $sku;

		$this->build_content($data);
        $this->render_page();
	}

	function getData() {

        $page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1; // get the requested page
        $limit = isset($_REQUEST['rows']) ? $_REQUEST['rows'] : 10; // get how many rows we want to have into the gri
        $sidx = isset($_REQUEST['sidx']) ? $_REQUEST['sidx'] : 'ID'; // get index row - i.e. user click to sort
        $sord = isset($_REQUEST['sord']) ? $_REQUEST['sord'] : 'asc';
        $sku = !empty($_GET['sku']) ? $_REQUEST['sku'] : '';

        $SQL = "SELECT ID
                    ,DumpFBM
                    ,DumpFBMEndDate 
                    ,DumpFBA
                    ,DumpFBAEndDate
                from 
                    [inventory].[dbo].[ProductCatalog]
                where
                    ID = $sku";


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
                $row['DumpFBM'] = utf8_encode($row['DumpFBM']),
                $row['DumpFBMEndDate'] = utf8_encode($row['DumpFBMEndDate']),
                $row['DumpFBA'] = utf8_encode($row['DumpFBA']),
                $row['DumpFBAEndDate'] = utf8_encode($row['DumpFBAEndDate']),
            );
            $i++;
        }

        echo json_encode($responce);
    }


    function editCell(){


        if($this->input->post('DumpFBM') !== FALSE) {
            $field = 'DumpFBM';
            $value = $this->input->post('DumpFBM');
        }

        if($this->input->post('DumpFBA') !== FALSE) {
            $field = 'DumpFBA';
            $value = $this->input->post('DumpFBA');
        }

        if($this->input->post('DumpFBMEndDate') !== FALSE) {
            $field = 'DumpFBMEndDate';
            $value = $this->input->post('DumpFBMEndDate')?  $this->input->post('DumpFBMEndDate') : NULL;
            $check = $value ? ',DumpFBM = 1': ',DumpFBM = 0';
        }

        if($this->input->post('DumpFBAEndDate') !== FALSE) {
            $field = 'DumpFBAEndDate';
            $value = $this->input->post('DumpFBAEndDate')?  $this->input->post('DumpFBAEndDate') : NULL;
            $check = $value ? ',DumpFBA = 1': ',DumpFBA = 0';
        }

        $id = $this->input->post('id');

        if ($value){
            $SQL = "update inventory.dbo.productcatalog set $field ='{$value}' $check where id={$id}";
        }else{
            $SQL = "update inventory.dbo.productcatalog set $field = NULL $check where id={$id}";            
        }

       
        // echo $SQL;

        $this->db->query($SQL);
    }
}