<?php
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
class Mercadolibre extends BP_Controller {
	public function __construct() {
		parent::__construct ();
		
		$is_logged_in = $this->session->userdata ( 'is_logged_in' );
		
		if (! isset ( $is_logged_in ) || $is_logged_in != true) {
			redirect ( base_url (), 'refresh' );
		}
		
		if ($this->MUsers->isValidUser ( $this->session->userdata ( 'userid' ), 884055 ) != 1) {
			redirect ( 'Catalog/prodcat', 'refresh' );
		}
	}
	public function index() {
		// Cargamos la base de datos de OrderManager
		$this->load->model ( 'MOrders', '', TRUE );
		
		// Define Meta
		$this->title = "MI Technologiesinc - Mercado Libre";
		
		$this->description = "Mercado Libre";
		
		// Define custom CSS
		$this->css = array (
				'menu.css',
				'fluid.css',
				'jqueryui/redmond/jquery-ui-1.8.21.custom.css',
				'jqgrid/ui.jqgrid.css',
				'form.css' 
		);
		
		// Define custom javascript
		$this->javascript = array (
				'jqueryui/ui/jquery-ui-1.8.21.custom.js',
				'jqgrid/i18n/grid.locale-en.js',
				'jqgrid/jquery.jqGrid.min.js',
				'jqgrid/jquery.jqGrid.fluid.js',
				'popup.js' 
		);
		// Cargamos la libreria donde esta el menu
		$this->load->library ( 'Layout' );
		// Creamos un nuevo layout->menu
		$menu = new Layout ();
		
		$data = array (
				'menu' => $menu->show_menu ( $this->MUsers->getUserFullName ( $this->session->userdata ( 'userid' ) ) ),
				'from' => '/MercadoLibre/mercadolibre/',
				'caption' => 'Mercado Libre' 
		);
		
		// Cargamos la libreria comumn
		$this->load->library ( 'common' );
		
		// Reemplazamos los campos del formulario por los que estan en el arreglo $_POST.
		$data = $this->common->fillPost ( 'ALL', $data );
		
		$data ['colNames'] = "['ProductCatalogID','CatalogID','CountryCode','TemplateID','MLID','Title','ImageURL1','ImageURL2','ImageURL3','ImageURL4','ImageURL5','CurrentPrice','IsActive','IsLaunched','LaunchDate','CategoryId'
				              ,'InfoChanged','synchronized','Parent_MLID','IsOriginal','IsGeneric','IsBareLamp','IsHausingLamp','IsFrontProjection','IsRearProjection','IsModelBased','IsPartnumberBased','OriginalPN','OriginalModel'
				              ,'OriginalManufacturer','Stock']";
		// Cargamos al arreglo los colmodels
		$data ['colModel'] = "[
                {name:'ProductCatalogID',index:'ProductCatalogID', width:50, align:'center', frozen : true},
                {name:'CatalogID',index:'CatalogID', width:70, align:'center', frozen : true},
                {name:'CountryCode',index:'CountryCode', width:70, align:'center', frozen : true},
                {name:'TemplateID',index:'TemplateID', width:60, align:'center', frozen : true},
                {name:'MLID',index:'MLID', width:100, align:'center', frozen : true},
                {name:'Title',index:'Title', width:350, align:'left'},
				{name:'ImageURL1',index:'ImageURL1', width:65, align:'center', formatter:formatLink1},
                {name:'ImageURL2',index:'ImageURL2', width:65, align:'center', formatter:formatLink2},
                {name:'ImageURL3',index:'ImageURL3', width:65, align:'center', formatter:formatLink3},
				{name:'ImageURL4',index:'ImageURL4', width:65, align:'center', formatter:formatLink4},
                {name:'ImageURL5',index:'ImageURL5', width:65, align:'center', formatter:formatLink5},
				{name:'CurrentPrice',index:'CurrentPrice', width:60, align:'center'},
                {name:'IsActive',index:'IsActive', width:60, align:'center'},
				{name:'IsLaunched',index:'IsLaunched', width:60, align:'center'},
				{name:'LaunchDate',index:'LaunchDate', width:80, align:'center'},
                {name:'CategoryId',index:'CategoryId', width:70, align:'center'},
                {name:'InfoChanged',index:'InfoChanged', width:70, align:'center'},
                {name:'synchronized',index:'synchronized', width:70, align:'center'},
                {name:'Parent_MLID',index:'Parent_MLID', width:70, align:'center'},
                {name:'IsOriginal',index:'IsOriginal', width:60, align:'center'},
				{name:'IsGeneric',index:'IsGeneric', width:60, align:'center'},
                {name:'IsBareLamp',index:'IsBareLamp', width:60, align:'center'},
				{name:'IsHausingLamp',index:'IsHausingLamp', width:60, align:'center'},
                {name:'IsFrontProjection',index:'IsFrontProjection', width:60, align:'center'},
				{name:'IsRearProjection',index:'IsRearProjection', width:60, align:'center'},
                {name:'IsModelBased',index:'IsModelBased', width:60, align:'center'},
				{name:'IsPartnumberBased',index:'IsPartnumberBased', width:60, align:'center'},
				{name:'OriginalPN',index:'OriginalPN', width:80, align:'left'},
                {name:'OriginalModel',index:'OriginalModel', width:80, align:'left'},
                {name:'OriginalManufacturer',index:'OriginalManufacturer', width:80, align:'left'},
                {name:'Stock',index:'Stock', width:70, align:'center'},
  	]";
			
		// Generar el contenido....
		$this->build_content ( $data );
		$this->render_page ();
	}
	
	function getData() {



		$dataSearch = $this->input->get('search');

		$page  = isset ( $_REQUEST ['page'] ) ? $_REQUEST ['page'] : 1; // get the requested page
		$limit = isset ( $_REQUEST ['rows'] ) ? $_REQUEST ['rows'] : 10; // get how many rows we want to have into the gri
		$sidx  = isset ( $_REQUEST ['sidx'] ) ? $_REQUEST ['sidx'] : 'ProductCatalogID'; // get index row - i.e. user click to sort
		$sord  = isset ( $_REQUEST ['sord'] ) ? $_REQUEST ['sord'] : 'desc';
		
		$where = '';


		
		$selectCount = "SELECT count(ProductCatalogID) as rowNum ";

		$select = "SELECT 
			       [ProductCatalogID]
			      ,[CatalogID]
			      ,[CountryCode]
			      ,[TemplateID]
			      ,[MLID]
			      ,[Title]
			      ,[ImageURL1]
			      ,[ImageURL2]
			      ,[ImageURL3]
			      ,[ImageURL4]
			      ,[ImageURL5]
			      ,[CurrentPrice]
			      ,[IsActive]
			      ,[IsLaunched]
			      ,[LaunchDate]
			      ,[CategoryId]
			      ,[InfoChanged]
			      ,[synchronized]
			      ,[Parent_MLID]
			      ,[IsOriginal]
			      ,[IsGeneric]
			      ,[IsBareLamp]
			      ,[IsHausingLamp]
			      ,[IsFrontProjection]
			      ,[IsRearProjection]
			      ,[IsModelBased]
			      ,[IsPartnumberBased]
			      ,[OriginalPN]
			      ,[OriginalModel]
			      ,[OriginalManufacturer]
			      ,[Stock]";
	

		$selectSlice = "SELECT 
			       [ProductCatalogID]
			      ,[CatalogID]
			      ,[CountryCode]
			      ,[TemplateID]
			      ,[MLID]
			      ,[Title]
			      ,[ImageURL1]
			      ,[ImageURL2]
			      ,[ImageURL3]
			      ,[ImageURL4]
			      ,[ImageURL5]
			      ,[CurrentPrice]
			      ,[IsActive]
			      ,[IsLaunched]
			      ,[LaunchDate]
			      ,[CategoryId]
			      ,[InfoChanged]
			      ,[synchronized]
			      ,[Parent_MLID]
			      ,[IsOriginal]
			      ,[IsGeneric]
			      ,[IsBareLamp]
			      ,[IsHausingLamp]
			      ,[IsFrontProjection]
			      ,[IsRearProjection]
			      ,[IsModelBased]
			      ,[IsPartnumberBased]
			      ,[OriginalPN]
			      ,[OriginalModel]
			      ,[OriginalManufacturer]
			      ,[Stock]";





		$from = ' FROM ';

		$table = ' [Inventory].[dbo].[MercadoLibreListings] ';
		
		$wherefields = array ('ProductCatalogID','CatalogID','CountryCode','TemplateID','MLID','Title','ImageURL1','ImageURL2','ImageURL3','ImageURL4','ImageURL5','CurrentPrice',
			                  'IsActive','IsLaunched','LaunchDate','CategoryId','InfoChanged','synchronized','Parent_MLID','IsOriginal','IsGeneric','IsBareLamp','IsHausingLamp',
			                  'IsFrontProjection','IsRearProjection','IsModelBased','IsPartnumberBased','OriginalPN','OriginalModel','OriginalManufacturer','Stock');
		
		$where .= $this->MCommon->concatAllWerefields ( $wherefields, $dataSearch );
		
		
		$SQL = "{$selectCount}{$from}{$table}{$where}";


		$count = $this->db->query($SQL)->row()->rowNum;
		
		if ($count > 0) {
			$total_pages = ceil ( $count / $limit );
		} else {
			$total_pages = 0;
		}
		if ($page > $total_pages)
			$page = $total_pages;
		
		$start = $limit * $page - $limit; // do not put $limit*($page - 1)
		$start = ($start < 0) ? 0 : $start;
		$finish = $start + $limit;


		 $SQL = "WITH mytable AS ({$select},ROW_NUMBER() OVER (ORDER BY {$sidx} {$sord}) AS RowNumber
                 FROM {$table} {$where}) 
                      {$selectSlice},RowNumber
                       FROM mytable
                       WHERE RowNumber 
                       BETWEEN {$start} AND {$finish}";


		$query = $this->db->query($SQL);

		
		$responce = new stdClass ();
		$responce->page = $page;
		$responce->total = $total_pages;
		$responce->records = $count;
		$i = 0;
		
		foreach ( $query->result_array() as $row ) {
			$responce->rows [$i] ['id'] = $row ['RowNumber'];
			$responce->rows [$i] ['cell'] = array (
								$row ['ProductCatalogID'],
							    $row ['CatalogID'],
							    $row ['CountryCode'],
							    $row ['TemplateID'],
							    $row ['MLID'],
							    $row ['Title'],
							    $row ['ImageURL1'],
							    $row ['ImageURL2'],
							    $row ['ImageURL3'],
							    $row ['ImageURL4'],
							    $row ['ImageURL5'],
							    $row ['CurrentPrice'],
							    $row ['IsActive'],
							    $row ['IsLaunched'],
							    $row ['LaunchDate'],
							    $row ['CategoryId'],
							    $row ['InfoChanged'],
							    $row ['synchronized'],
							    $row ['Parent_MLID'],
							    $row ['IsOriginal'],
							    $row ['IsGeneric'],
							    $row ['IsBareLamp'],
							    $row ['IsHausingLamp'],
							    $row ['IsFrontProjection'],
							    $row ['IsRearProjection'],
							    $row ['IsModelBased'],
							    $row ['IsPartnumberBased'],
							    $row ['OriginalPN'],
							    $row ['OriginalModel'],
							    $row ['OriginalManufacturer'],
							    $row ['Stock'],
			);
			$i ++;
		}
		
		echo json_encode ( $responce );
	}



	
	/*
	 * CSV Export
	 */
	function csvExport($name) {
		header ( 'Content-type: application/vnd.ms-excel' );
		header ( "Content-Disposition: attachment; filename=" . $name . '_' . date ( "D-M-j" ) . ".xls" );
		header ( "Pragma: no-cache" );
		
		$buffer = $_POST ['csvBuffer'];
		
		try {
			echo $buffer;
		} catch ( Exception $e ) {
		}
	}
}
?>




