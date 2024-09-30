<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Instocknotification extends BP_Controller {

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

	$this->title = "MI Technologiesinc - In-Stock Notification Global";

	$this->description = "In-Stock Notification Global";

	$this->css = array('form.css', 'fluid.css', 'table.css', 'jqueryui/redmond/jquery-ui-1.8.21.custom.css', 'jqgrid/ui.jqgrid.css', 'menu.css',);

	// Define custom javascript
	$this->javascript = array('jqueryui/ui/jquery-ui-1.8.21.custom.js', 'jqgrid/i18n/grid.locale-en.js', 'jqgrid/jquery.jqGrid.min.js', 'jqgrid/jquery.jqGrid.fluid.js', 'popup.js');


	// If the page has menu
	$this->load->library('Layout');
	$menu = new Layout;

	// If not have menu




	$data = array(
	    'menu' => $menu->show_menu($this->MUsers->getUserFullName($this->session->userdata('userid'))),
	    'from' => '/tools/instocknotification/',
	    'caption' => 'In-Stock Notification Global',
	    'export' => 'exportexcel',
	    'subgrid' => 'false', // true or false depends
	    'sort' => 'desc',
	    'search' => '',
	    'customerid' => $this->MUsers->getCustomerId($this->session->userdata('userid')),
	);


	$data['colNames'] = "['SKU','Description','QtyRequired','Backorders','Notes','User']";

	$data['colModel'] = "[
		{name:'SKU',index:'SKU', width:80 ,align:'center'},
        {name:'Description',index:'Description', width:250, align:'left'},
        {name:'QtyRequired',index:'QtyRequired', width:80, align:'center'},
        {name:'Backorders',index:'Backorders', width:80, align:'center'},
        {name:'Notes',index:'Notes', width:80, align:'center'},
        {name:'User',index:'User', width:80, align:'center'},
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
	$sidx = isset($_REQUEST['sidx']) ? $_REQUEST['sidx'] : 'ISN.[ProductCatalogID]'; // get index row - i.e. user click to sort
	$sord = isset($_REQUEST['sord']) ? $_REQUEST['sord'] : 'asc';
	$search = !empty($_GET['ds']) ? $_REQUEST['ds'] : '';


    $table = ' [Inventory].[dbo].[InStockNotification] AS ISN ';
    $where = '';
    $from = ' FROM ';
    $join = ' LEFT OUTER JOIN [Inventory].[dbo].[ProductCatalog] AS PC ON (ISN.[ProductCatalogID] = PC.[ID])
		LEFT OUTER JOIN [Inventory].[dbo].[Users] AS USR ON (ISN.[UserID] = USR.[id]) ';
    $orderby= ' ORDER BY ISN.ProductCatalogID ';

    $select = " SELECT ISN.[ProductCatalogID] AS SKU
      ,IsNull(PC.[Name],'SKU DOES NOT EXIST') AS Description
      ,ISN.[QtyRequired] AS QtyRequired
      ,IsNull((SELECT SUM(Cast(QtyBackordered AS INT)) FROM [Inventory].[dbo].[PurchaseOrderData] WHERE SKU = ISN.[ProductCatalogID]),0) AS Backorders
      ,ISN.[Notes] AS Notes
      ,USR.[fullname] AS 'User'
	";

    $selectSlice = " SELECT SKU
      ,Description
      ,QtyRequired
      ,Backorders
      ,Notes
      ,[User]
	";

    $wherefields = array('ISN.[ProductCatalogID]','PC.[Name]','ISN.[QtyRequired]','ISN.[Notes]','USR.[fullname]');

    $where .=$this->MCommon->concatAllWerefields($wherefields, $search);

    $SQL = "{$select}{$from}{$table}{$join}{$where}{$orderby}";
    
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
                    {$from}{$table}{$join}{$where})
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
	    $responce->rows[$i]['id'] = $row['SKU'];
	    $responce->rows[$i]['cell'] = array($row['SKU'],
		$row['Description'] ,
		$row['QtyRequired'],
		$row['Backorders'],
		$row['Notes'] ,
		$row['User'] ,
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
    
    function saveData(){
	
	if ($_POST['oper']== 'edit'){
	    
	    $SQL = "update MITDB.dbo.ProjectorData 
		    set 
			Brand ='{$_POST['Brand']}',
			PartNumber='{$_POST['PartNumber']}', 
			PartNumberV2='{$_POST['PartNumberV2']}', 
			PartNumberV3='{$_POST['PartNumberV3']}',
			DM_Youtube='{$_POST['DM_Youtube']}',
			DM_Vimeo='{$_POST['DM_Vimeo']}',
			DM_Facebook='{$_POST['DM_Facebook']}',
			DTVL_Youtube='{$_POST['DTVL_Youtube']}',
			DTVL_Vimeo='{$_POST['DTVL_Vimeo']}',
			DTVL_Facebook='{$_POST['DTVL_Facebook']}',
			Type='{$_POST['Type']}' 
		    where
		    ID = {$_POST['id']}";
	}

	 $this->MCommon->saveRecord($SQL,'Mitdb');
	    
    }

}
?>