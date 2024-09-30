<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Projectordata extends BP_Controller {

    public function __construct() {
	parent::__construct();

	$is_logged_in = $this->session->userdata('is_logged_in');

	if (!isset($is_logged_in) || $is_logged_in != true) {
	    redirect(base_url(), 'refresh');
	}

	if ($this->MUsers->isValidUser($this->session->userdata('userid'), 884400) != 1) {// Access Code
	    redirect('Catalog/prodcat', 'refresh');
	}
    } 

    function index() {

	$this->title = "MI Technologiesinc - Projector Data";

	$this->description = "Projector Data";

	$this->css = array('form.css', 'fluid.css', 'table.css', 'jqueryui/redmond/jquery-ui-1.8.21.custom.css', 'jqgrid/ui.jqgrid.css', 'menu.css',);

	// Define custom javascript
	$this->javascript = array('jqueryui/ui/jquery-ui-1.8.21.custom.js', 'jqgrid/i18n/grid.locale-en.js', 'jqgrid/jquery.jqGrid.min.js', 'jqgrid/jquery.jqGrid.fluid.js', 'popup.js');


	// If the page has menu
	$this->load->library('Layout');
	$menu = new Layout;

	// If not have menu




	$data = array(
	    'menu' => $menu->show_menu($this->MUsers->getUserFullName($this->session->userdata('userid'))),
	    'from' => '/tools/projectordata/',
	    'nameGrid' => 'projectordata',
	    'namePager' => 'projectordataPager',
	    'caption' => 'Projector Data',
	    'export' => 'exportexcel',
	    'subgrid' => 'false', // true or false depends
	    'sort' => 'desc',
	    'search' => '',
	    'customerid' => $this->MUsers->getCustomerId($this->session->userdata('userid')),
	);


	$data['colNames'] = "['ID'
	,'CatalogID'
	,'Type'
	,'Brand'
	,'PartNumber'
	,'PartNumberV2'
	,'PartNumberV3'
	,'OriginalPN'
	,'BareSKU'
	,'EncSKU'
	,'BareSKUPH'
	,'EncSKUPH'
	,'BareSKUOS'
	,'EncSKUOS'
 	,'BareSKUNL'
    ,'EncSKUNL'
    ,'BareSKUPX'
    ,'EncSKUPX'
    ,'BareSKUUSH'
    ,'EncSKUUSH'
    ,'BareSKUOEM' 
    ,'EncSKUOEM']";

	$data['colModel'] = "[
						{name:'ID',index:'ID', width:50, align:'center',
							hidden:true,
							editable:false,
							editoptions:{size:30, maxlength: 50},
						},
						{name:'CatalogID',index:'CatalogID', width:80, align:'center',
							editable:false,
							editoptions:{size:30, maxlength: 50},
							editrules: { edithidden: false },
						},
				       	{name:'Type',index:'Type', width:80, align:'center',
				       		editable:true,
				       		editoptions:{size:30, maxlength: 50},
				       		formoptions: {rowpos: 1, colpos: 1}
				       	},
				        {name:'Brand',index:'Brand', width:80, align:'center',
				        	editable:true,
				        	editoptions:{size:30, maxlength: 50},
				        	formoptions: {rowpos: 1, colpos: 2}
				    	},
				        {name:'PartNumber',index:'PartNumber', width:100, align:'center',
				        	editable:true,
				        	editoptions:{size:30, maxlength: 50},
				        	formoptions: {rowpos: 2, colpos: 1}
				    	},
				        {name:'PartNumberV2',index:'PartNumberV2', width:80, align:'center',
				        	editable:true,
				        	editoptions:{size:30, maxlength: 50},
				        	formoptions: {rowpos: 2, colpos: 2}
				    	},
				        {name:'PartNumberV3',index:'PartNumberV3', width:80, align:'center',
				        	editable:true,
				        	editoptions:{size:30, maxlength: 50},
				        	formoptions: {rowpos: 3, colpos: 1}
				    	},		
						{name:'OriginalPN',index:'OriginalPN', width:120, align:'center',
							editable:true,
							editoptions:{size:30, maxlength: 250},
							formoptions: {rowpos: 3, colpos: 2}
						},

						{name:'BareSKU',index:'BareSKU', width:80, align:'center',
							editable:true,
							editoptions:{size:30, maxlength: 250},
							formoptions: {rowpos: 4, colpos: 1},
							edittype: 'select', editoptions: { 
       						dataUrl: 'combobox/BareSKU'                      
    					 }},

				        {name:'EncSKU',index:'EncSKU', width:80, align:'center',
				        	editable:true,
				        	editoptions:{size:30, maxlength: 250},
				        	formoptions: {rowpos: 4, colpos: 2},
				        	edittype: 'select', editoptions: { 
       						dataUrl: 'combobox/EncSKU'                      
    					 }},

						{name:'BareSKUPH',index:'BareSKUPH', width:80, align:'center',
							editable:true,
							editoptions:{size:30, maxlength: 250},
							formoptions: {rowpos: 5, colpos: 1},
							edittype: 'select', editoptions: { 
       						dataUrl: 'combobox/BareSKUPH'                      
    					 }},		    	
						{name:'EncSKUPH',index:'EncSKUPH', width:80, align:'center',
							editable:true,
							editoptions:{size:30, maxlength: 250},
							formoptions: {rowpos: 5, colpos: 2},
							edittype: 'select', editoptions: { 
       						dataUrl: 'combobox/EncSKUPH'                      
    					 }},	
						{name:'BareSKUOS',index:'BareSKUOS', width:80, align:'center',
							editable:true,
							editoptions:{size:30, maxlength: 250},
							formoptions: {rowpos: 6, colpos: 1},
							edittype: 'select', editoptions: { 
       						dataUrl: 'combobox/BareSKUOS'                      
    					 }},	
				        {name:'EncSKUOS',index:'EncSKUOS', width:80, align:'center',
				        	editable:true,
				        	editoptions:{size:30, maxlength: 250},
				        	formoptions: {rowpos: 6, colpos: 2},
				        	edittype: 'select', editoptions: { 
       						dataUrl: 'combobox/EncSKUOS'                      
    					 }},	

						{name:'BareSKUNL',index:'BareSKUNL', width:80, align:'center',
							editable:true,
							editoptions:{size:30, maxlength: 250},
							formoptions: {rowpos: 7, colpos: 1},
							edittype: 'select', editoptions: { 
       						dataUrl: 'combobox/BareSKUNL'                      
    					 }},	
						{name:'EncSKUNL',index:'EncSKUNL', width:80, align:'center',
							editable:true,
							editoptions:{size:30, maxlength: 250},
							formoptions: {rowpos: 7, colpos: 2},
							edittype: 'select', editoptions: { 
       						dataUrl: 'combobox/EncSKUNL'                      
    					 }},

						{name:'BareSKUPX',index:'BareSKUPX', width:80, align:'center',
							editable:true,
							editoptions:{size:30, maxlength: 250},
							formoptions: {rowpos: 8, colpos: 1},
							edittype: 'select', editoptions: { 
       						dataUrl: 'combobox/BareSKUPX'                      
    					 }},
						{name:'EncSKUPX',index:'EncSKUPX', width:80, align:'center',
							editable:true,
							editoptions:{size:30, maxlength: 250},
							formoptions: {rowpos: 8, colpos: 2},
							edittype: 'select', editoptions: { 
       						dataUrl: 'combobox/EncSKUPX'                      
    					 }},
						{name:'BareSKUUSH',index:'BareSKUUSH', width:80, align:'center',
							editable:true,
							editoptions:{size:30, maxlength: 250},
							formoptions: {rowpos: 9, colpos: 1},
							edittype: 'select', editoptions: { 
       						dataUrl: 'combobox/BareSKUUSH'                      
    					 }},
						{name:'EncSKUUSH',index:'EncSKUUSH', width:80, align:'center',
							editable:true,
							editoptions:{size:30, maxlength: 250},
							formoptions: {rowpos: 9, colpos: 2},
							edittype: 'select', editoptions: { 
       						dataUrl: 'combobox/EncSKUUSH'                      
    					 }},

						{name:'BareSKUOEM',index:'BareSKUOEM', width:80, align:'center',
							editable:true,
							editoptions:{size:30, maxlength: 250},
							formoptions: {rowpos: 10, colpos: 1},
							edittype: 'select', editoptions: { 
       						dataUrl: 'combobox/BareSKUOEM'                      
    					 }},
						{name:'EncSKUOEM',index:'EncSKUOEM', width:80, align:'center',
							editable:true,
							editoptions:{size:30, maxlength: 250},
							formoptions: {rowpos: 10, colpos: 2},
							edittype: 'select', editoptions: { 
       						dataUrl: 'combobox/EncSKUOEM'                      
    					 }},
		               ]";

	//Cargamos la libreria comumn
	$this->load->library('common');

	//Reemplazamos los campos del formulario por los que estan en el arreglo $_POST.
	$data = $this->common->fillPost('ALL', $data);

	//cadena de busqueda del grid 
	$data['gridSearch'] = 'Data?ds=' . $data['search'];


	$this->build_content($data);
	$this->render_page();
    }

    function Data() {

	$page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1; // get the requested page
	$limit = isset($_REQUEST['rows']) ? $_REQUEST['rows'] : 10; // get how many rows we want to have into the gri
	$sidx = isset($_REQUEST['sidx']) ? $_REQUEST['sidx'] : 'Brand'; // get index row - i.e. user click to sort
	$sord = isset($_REQUEST['sord']) ? $_REQUEST['sord'] : 'asc';
	$search = !empty($_GET['ds']) ? $_REQUEST['ds'] : '';


	$table = 'MITDB.dbo.ProjectorData ';

	$where = '';
	$from = ' FROM ';


	$select = "SELECT
		    		[ID]
			      ,[CatalogID]
			      ,[Type]
			      ,[Brand]
			      ,[PartNumber]
			      ,[PartNumberV2]
			      ,[PartNumberV3]
			      ,[OriginalPN]
			      ,[BareSKU]
			      ,[EncSKU]
			      ,[BareSKUPH]
			      ,[EncSKUPH]
			      ,[BareSKUOS]
			      ,[EncSKUOS]
			      ,[BareSKUNL]
			      ,[EncSKUNL]
			      ,[BareSKUPX]
			      ,[EncSKUPX]
			      ,[BareSKUUSH]
			      ,[EncSKUUSH]
			      ,[BareSKUOEM]
			      ,[EncSKUOEM]
			         ";


	$wherefields = array('ID','CatalogID','Type','Brand','PartNumber','PartNumberV2','PartNumberV3','OriginalPN','BareSKU','EncSKU'
			        ,'BareSKUPH','EncSKUPH','BareSKUOS','EncSKUOS','BareSKUNL','EncSKUNL','BareSKUPX','EncSKUPX','BareSKUUSH','EncSKUUSH','BareSKUOEM','EncSKUOEM');

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


	$SQL =" WITH mytable AS (
                Select *, ROW_NUMBER() OVER (ORDER BY {$sidx} {$sord}) AS RowNumber
                FROM {$table}{$where}
		)
		Select * from mytable WHERE RowNumber BETWEEN {$start} AND {$finish}";
	


	$result = $this->MCommon->getSomeRecords($SQL);


	$responce = new stdClass();
	$responce->page = $page;
	$responce->total = $total_pages;
	$responce->records = $count;


	$i = 0;
	foreach ($result as $row) {
	    $responce->rows[$i]['id'] = $row['ID'];
	    $responce->rows[$i]['cell'] = array($row['ID'],
	    $row['CatalogID'] 			= utf8_encode($row['CatalogID']),
		$row['Type'] 				= utf8_encode($row['Type']),
		$row['Brand'] 				= utf8_encode($row['Brand']),
		$row['PartNumber'] 			= utf8_encode($row['PartNumber']),
		$row['PartNumberV2'] 		= utf8_encode($row['PartNumberV2']),
		$row['PartNumberV3'] 		= utf8_encode($row['PartNumberV3']),
		$row['OriginalPN']			= utf8_encode($row['OriginalPN']),
		$row['BareSKU']				= utf8_encode($row['BareSKU']),
		$row['EncSKU']				= utf8_encode($row['EncSKU']),
		$row['BareSKUPH']			= utf8_encode($row['BareSKUPH']),
		$row['EncSKUPH']			= utf8_encode($row['EncSKUPH']),
		$row['BareSKUOS']			= utf8_encode($row['BareSKUOS']),
		$row['EncSKUOS']			= utf8_encode($row['EncSKUOS']),
		$row['BareSKUNL']			= utf8_encode($row['BareSKUNL']),
  		$row['EncSKUNL']			= utf8_encode($row['EncSKUNL']),
    	$row['BareSKUPX']			= utf8_encode($row['BareSKUPX']),
    	$row['EncSKUPX']			= utf8_encode($row['EncSKUPX']),
    	$row['BareSKUUSH']			= utf8_encode($row['BareSKUUSH']),
    	$row['EncSKUUSH']			= utf8_encode($row['EncSKUUSH']),
    	$row['BareSKUOEM']			= utf8_encode($row['BareSKUOEM']),
   		$row['EncSKUOEM']			= utf8_encode($row['EncSKUOEM']),
	    );
	    $i++;
	}

	echo json_encode($responce);
    }


	function getTabs(){

        $Id = $this->input->get('id');



        $tabs = '<div class="tabs">
                    <ul>
                        <li><a href="#tabs-1">DM</a></li>
                        <li><a href="#tabs-2">DTVL</a></li>
                        <li><a href="#tabs-3">Pricing</a></li>
                        <li><a href="#tabs-4">UPC</a></li>
                        <li><a href="#tabs-5">Specs</a></li>
                        <li><a href="#tabs-6">Notes</a></li>
                    </ul>
                    <div id="tabs-1">
                        <center>
                           <table id="grid1'.$Id.'"></table>
                           <div id="pager1'.$Id.'"></div>
                        </center>
                    </div>
                    <div id="tabs-2">
                         <center>
                            <table id="grid2'.$Id.'"></table>
                            <div id="pager2'.$Id.'"></div>
                        </center>
                    </div>
                    <div id="tabs-3">
                         <center>
                           <table id="grid3'.$Id.'"></table>
                           <div id="pager3'.$Id.'"></div>
                       </center>
                    </div>
                    <div id="tabs-4">
                         <center>
                            <table id="grid4'.$Id.'"></table>
                            <div id="pager4'.$Id.'"></div>
                        </center>
                    </div>
                    <div id="tabs-5">
                         <center>
                            <table id="grid5'.$Id.'"></table>
                            <div id="pager5'.$Id.'"></div>
                        </center>
                    </div>
                    <div id="tabs-6">
                         <center>
                            <table id="grid6'.$Id.'"></table>
                            <div id="pager6'.$Id.'"></div>
                        </center>
                    </div>
                </div>';
        echo $tabs;
    }

function tab($param) {

        $page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1; // get the requested page
        $limit = isset($_REQUEST['rows']) ? $_REQUEST['rows'] : 10; // get how many rows we want to have into the gri
        $sidx = isset($_REQUEST['sidx']) ? $_REQUEST['sidx'] : '[ID]'; // get index row - i.e. user click to sort
        $sord = isset($_REQUEST['sord']) ? $_REQUEST['sord'] : 'asc';
        
        $Id = $this->input->get('ds');

switch ($param) {
    case 1:
        $f1='DM_Youtube';
        $f2='DM_Vimeo';
        $f3='DM_Facebook';
        break;
    case 2:
        $f1='DTVL_Youtube';
        $f2='DTVL_Vimeo';
        $f3='DTVL_Facebook';
        break;
    case 3:
        $f1='PriceEncSKU';
        $f2='PriceGeneric';
        $f3='PricePremium';
        break;
    case 4:
        $f1='UPCEconomy';
        $f2='UPCPremium';
        $f3='UPCPhilips';
        break;
    case 5:
        $f1='Watt';
        $f2='OriginalLampType';
        break;
    case 6:
        $f1='Notes';
        $f2='CompatibiltiesNotes';
        break;
}


        //Select General con los campos necesarios para la vista
        $select = 'SELECT ID ';
        switch ($param) {
        	case 1:
                $select.= ',['.$f1.'] 
		                   ,['.$f2.'] 
		                   ,['.$f3.'] 
		                   FROM  [MITDB].[dbo].[ProjectorData]
		                   WHERE [ID] ='.$Id;
           	break;
           	case 2:
                $select.= ',['.$f1.'] 
		                   ,['.$f2.'] 
		                   ,['.$f3.'] 
		                   FROM  [MITDB].[dbo].[ProjectorData]
		                   WHERE [ID] ='.$Id;
           	break;
            case 3:
                $select.= ',['.$f1.'] 
		                   ,['.$f2.'] 
		                   ,['.$f3.'] 
		                   FROM  [MITDB].[dbo].[ProjectorData]
		                   WHERE [ID] ='.$Id;
           	break;

           	case 4:
                $select.= ',['.$f1.'] 
		                   ,['.$f2.'] 
		                   ,['.$f3.'] 
		                   FROM  [MITDB].[dbo].[ProjectorData]
		                   WHERE [ID] ='.$Id;
           	break;

           	case 5:
                $select.= ',['.$f1.'] 
		                   ,['.$f2.']
		                   FROM  [MITDB].[dbo].[ProjectorData]
		                   WHERE [ID] ='.$Id;
           	break;

            case 6:
                $select.= ',['.$f1.'] 
		                   ,['.$f2.'] 
		                   FROM  [MITDB].[dbo].[ProjectorData]
		                   WHERE [ID] ='.$Id;
           	break;


        }


        $result = $this->MCommon->getSomeRecords($select); 

        $count = 1;

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

        switch ($param) {
            case 1:
                foreach ($result as $row) { 
                    $responce->rows[$i]['id'] = $row['ID'];
                    $responce->rows[$i]['cell'] = array($row['ID'],
                        $row[$f1] = utf8_encode($row[$f1]),
                        $row[$f2] = utf8_encode($row[$f2]),
                        $row[$f3] = utf8_encode($row[$f3]),
                    );
                    $i++;
                }
           	break;

           	case 2:
                foreach ($result as $row) { 
                    $responce->rows[$i]['id'] = $row['ID'];
                    $responce->rows[$i]['cell'] = array($row['ID'],
                        $row[$f1] = utf8_encode($row[$f1]),
                        $row[$f2] = utf8_encode($row[$f2]),
                        $row[$f3] = utf8_encode($row[$f3]),
                    );
                    $i++;
                }
           	break;

           	case 3:
                foreach ($result as $row) { 
                    $responce->rows[$i]['id'] = $row['ID'];
                    $responce->rows[$i]['cell'] = array($row['ID'],
                        $row[$f1] = utf8_encode($row[$f1]),
                        $row[$f2] = utf8_encode($row[$f2]),
                        $row[$f3] = utf8_encode($row[$f3]),
                    );
                    $i++;
                }
           	break;

           	case 4:
                foreach ($result as $row) { 
                    $responce->rows[$i]['id'] = $row['ID'];
                    $responce->rows[$i]['cell'] = array($row['ID'],
                        $row[$f1] = utf8_encode($row[$f1]),
                        $row[$f2] = utf8_encode($row[$f2]),
                        $row[$f3] = utf8_encode($row[$f3]),
                    );
                    $i++;
                }
           	break;

           	case 5:
                foreach ($result as $row) { 
                    $responce->rows[$i]['id'] = $row['ID'];
                    $responce->rows[$i]['cell'] = array($row['ID'],
                        $row[$f1] = utf8_encode($row[$f1]),
                        $row[$f2] = utf8_encode($row[$f2]),
                    );
                    $i++;
                }
           	break;

           	case 6:
                foreach ($result as $row) { 
                    $responce->rows[$i]['id'] = $row['ID'];
                    $responce->rows[$i]['cell'] = array($row['ID'],
                        $row[$f1] = utf8_encode($row[$f1]),
                        $row[$f2] = utf8_encode($row[$f2]),
                    );
                    $i++;
                }
           	break;
        }

        echo json_encode($responce);
    }


    /*
     * CSV Export
     */

function saveMainData(){


	$fields = array('Type','Brand','PartNumber','PartNumberV2','PartNumberV3','OriginalPN','BareSKU','EncSKU','BareSKUPH','EncSKUPH','BareSKUOS','EncSKUOS','BareSKUNL',
				    'EncSKUNL','BareSKUPX','EncSKUPX','BareSKUUSH','EncSKUUSH','BareSKUOEM','EncSKUOEM');

	$ID =$this->input->post('id');

	
	foreach ($fields as  $field) {

		if (isset($_POST[$field])) {
			$myfield = $field;
			$mydata = $this->input->post($field);
		}
	}

	$SQL = "update MITDB.dbo.ProjectorData set $myfield = '$mydata' where ID =  $ID";




	$this->MCommon->saveRecord($SQL,'Mitdb');

}

	function saveTab($grid){
		
		$ID =$this->input->post('id');
		
		switch ($grid) {
			case '1':
				$fields = array('DM_Youtube','DM_Vimeo','DM_Facebook');
				break;
			case '2':
				$fields = array('DTVL_Youtube','DTVL_Vimeo','DTVL_Facebook');
				break;
			case '3':
				$fields = array('PriceEncSKU','PriceGeneric','PricePremium');
				break;
			case '4':
				$fields = array('UPCEconomy','UPCPremium','UPCPhilips');
				break;
			case '5':
				$fields = array('Watt','OriginalLampType');
				break;
			case '6':
				$fields = array('Notes','CompatibiltiesNotes');
				break;

		}

		

		foreach ($fields as  $field) {

			if (isset($_POST[$field])) {
				$myfield = $field;
				$mydata = $this->input->post($field);
			}
		}

		if(($grid==3) || ($grid==4)){

			if ($mydata){
				$SQL = "update MITDB.dbo.ProjectorData set $myfield = $mydata where ID =  $ID";
			}
			else
			{
				$SQL = "update MITDB.dbo.ProjectorData set $myfield = NULL where ID =  $ID";
			}
		}
		else
		{
			$SQL = "update MITDB.dbo.ProjectorData set $myfield = '$mydata' where ID =  $ID";

		}


		$this->MCommon->saveRecord($SQL,'Mitdb');
	}



  function saveData(){

	//	$ID 				= $this->input->post('ID') ? $this->input->post('ID') : '';;
		$Type				= $this->input->post('Type') ? $this->input->post('Type') : '';;
	  	$Brand				= $this->input->post('Brand') ? $this->input->post('Brand') : ' ';;
	  	$PartNumber			= $this->input->post('PartNumber') ? $this->input->post('PartNumber') : '-';
	  	$PartNumberV2		= $this->input->post('PartNumberV2')? $this->input->post('PartNumberV2') : '-';;
	  	$PartNumberV3		= $this->input->post('PartNumberV3')? $this->input->post('PartNumberV3') : '-';;
	  	$OriginalPN			= $this->input->post('OriginalPN')? $this->input->post('OriginalPN') : ' ';;
	  	$BareSKU			= $this->input->post('BareSKU');
	  	$EncSKU				= $this->input->post('EncSKU');
	  	$BareSKUPH			= $this->input->post('BareSKUPH');
	  	$EncSKUPH			= $this->input->post('EncSKUPH');
	  	$BareSKUOS			= $this->input->post('BareSKUOS');
	  	$EncSKUOS			= $this->input->post('EncSKUOS');
	  	$BareSKUNL			= $this->input->post('BareSKUNL');
	  	$EncSKUNL			= $this->input->post('EncSKUNL');   
	  	$BareSKUPX 			= $this->input->post('BareSKUPX');   
	  	$EncSKUPX			= $this->input->post('EncSKUPX');   
	  	$BareSKUUSH			= $this->input->post('BareSKUUSH');   
	  	$EncSKUUSH			= $this->input->post('EncSKUUSH');   
	  	$BareSKUOEM			= $this->input->post('BareSKUOEM');   
	  	$EncSKUOEM			= $this->input->post('EncSKUOEM');   


		$SQL ="INSERT INTO MITDB.[dbo].[ProjectorData] 
		    (Type
		 	,Brand
		 	,PartNumber
		 	,PartNumberV2
		 	,PartNumberV3
		 	,OriginalPN
		 	,BareSKU
		 	,EncSKU
		 	,BareSKUPH
		 	,EncSKUPH
		 	,BareSKUOS
		 	,EncSKUOS
		 	,BareSKUNL
		 	,EncSKUNL
		 	,BareSKUPX
		 	,EncSKUPX
		 	,BareSKUUSH
		 	,EncSKUUSH
		 	,BareSKUOEM
		 	,EncSKUOEM
		 	)
		VALUES		  
			('$Type'
			,'$Brand'
			,'$PartNumber'
			,'$PartNumberV2'
			,'$PartNumberV3'
			,'$OriginalPN'
			,'$BareSKU'
			,'$EncSKU'
			,'$BareSKUPH'
			,'$EncSKUPH'
			,'$BareSKUOS'
			,'$EncSKUOS'
			,'$BareSKUNL'
			,'$EncSKUNL'
			,'$BareSKUPX'
			,'$EncSKUPX'
			,'$BareSKUUSH'
	  		,'$EncSKUUSH'
	  		,'$BareSKUOEM'
	  		,'$EncSKUOEM'
	  		)";
	

	$return = $this->db->query($SQL);

    }

    function DeleteRow(){
		$ID = $this->input->post('id');

    	$SQL = "Delete from MITDB.[dbo].[ProjectorData] where ID= $ID";

    	$return = $this->db->query($SQL);
    }


    function combobox($skutype){

    	switch ($skutype) {
    		case 'BareSKU':
    			$SQL="SELECT Cast([ID] AS NVARCHAR(15)) as SKU FROM [Inventory].[dbo].[ProductCatalog] WHERE [CategoryID] IN ('9','19')";
    			break;
    		case 'EncSKU':
    			$SQL="SELECT Cast([ID] AS NVARCHAR(15)) as SKU FROM [Inventory].[dbo].[ProductCatalog] WHERE [CategoryID] IN ('14','24')";
    			break;
    		case 'BareSKUPH':
    			$SQL="SELECT Cast([ID] AS NVARCHAR(15)) as SKU FROM [Inventory].[dbo].[ProductCatalog] WHERE [CategoryID] IN ('5','15')";
    			break;
    		case 'EncSKUPH':
    			$SQL="SELECT Cast([ID] AS NVARCHAR(15)) as SKU FROM [Inventory].[dbo].[ProductCatalog] WHERE [CategoryID] IN ('10','20')";
    			break;
    		case 'BareSKUOS':
    			$SQL="SELECT Cast([ID] AS NVARCHAR(15)) as SKU FROM [Inventory].[dbo].[ProductCatalog] WHERE [CategoryID] IN ('6','16') AND [Name] NOT LIKE '%NEOLUX%'";
    			break;
    		case 'EncSKUOS':
    			$SQL= "SELECT Cast([ID] AS NVARCHAR(15)) as SKU FROM [Inventory].[dbo].[ProductCatalog] WHERE [CategoryID] IN ('11','21') AND [Name] NOT LIKE '%NEOLUX%'";
    			break;
    		case 'BareSKUNL':
    			$SQL= "SELECT Cast([ID] AS NVARCHAR(15)) as SKU FROM [Inventory].[dbo].[ProductCatalog] WHERE [CategoryID] IN ('6','16') AND [Name] LIKE '%NEOLUX%'";
    			break;
    		case 'EncSKUNL':
    			$SQL= "SELECT Cast([ID] AS NVARCHAR(15)) as SKU FROM [Inventory].[dbo].[ProductCatalog] WHERE [CategoryID] IN ('11','21') AND [Name] LIKE '%NEOLUX%'";
    			break;
    		case 'BareSKUPX':
    			$SQL="SELECT Cast([ID] AS NVARCHAR(15)) as SKU FROM [Inventory].[dbo].[ProductCatalog] WHERE [CategoryID] IN ('7','17')";
    			break;
    		case 'EncSKUPX':
    			$SQL="SELECT Cast([ID] AS NVARCHAR(15)) as SKU FROM [Inventory].[dbo].[ProductCatalog] WHERE [CategoryID] IN ('12','22')";
    			break;
    		case 'BareSKUUSH':
    			$SQL="SELECT Cast([ID] AS NVARCHAR(15)) as SKU FROM [Inventory].[dbo].[ProductCatalog] WHERE [CategoryID] IN ('8','18')";
    			break;
    		case 'EncSKUUSH':
    			$SQL= "SELECT Cast([ID] AS NVARCHAR(15)) as SKU FROM [Inventory].[dbo].[ProductCatalog] WHERE [CategoryID] IN ('13','23')";
    			break;
    		case 'BareSKUOEM':
    			$SQL="SELECT Cast([ID] AS NVARCHAR(15)) as SKU FROM [Inventory].[dbo].[ProductCatalog] WHERE [CategoryID] IN ('61','63')";
    			break;
    		case 'EncSKUOEM':
    			$SQL="SELECT Cast([ID] AS NVARCHAR(15)) as SKU FROM [Inventory].[dbo].[ProductCatalog] WHERE [CategoryID] IN ('62','64')";
    			break;
    	}

    

    	 $options = $this->MCommon->fillDropDown2('-','-',$SQL,'SKU','SKU' );

    
    	echo form_dropdown('BareSKU', $options, '');

    }

}
?>