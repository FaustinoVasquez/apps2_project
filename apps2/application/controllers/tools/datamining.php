<?php

if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

class Datamining extends BP_Controller {

	public function __construct() {
		parent::__construct();

		$is_logged_in = $this->session->userdata('is_logged_in');

		if (!isset($is_logged_in) || $is_logged_in != true) {
			redirect(base_url(), 'refresh');
		}

		if ($this->MUsers->isValidUser($this->session->userdata('userid'), 884300) != 1) {// Access Code
			redirect('Catalog/prodcat', 'refresh');
		}
	}

	function index() {

		$this->title       = "MI Technologiesinc - Channel Data Mining";
		$this->description = "Channel Data Mining";
		$this->css         = array('form.css', 'fluid.css', 'table.css', 'jqueryui/redmond/jquery-ui-1.8.21.custom.css', 'jqgrid/ui.jqgrid.css', 'menu.css', );

		// Define custom javascript
		$this->javascript = array('jqueryui/ui/jquery-ui-1.8.21.custom.js', 'jqgrid/i18n/grid.locale-en.js', 'jqgrid/jquery.jqGrid.min.js', 'jqgrid/jquery.jqGrid.fluid.js', 'site.js');

		// If the page has menu
		$this->load->library('Layout');
		$menu = new Layout;

		$data = array(
			'menu'      => $menu->show_menu($this->MUsers->getUserFullName($this->session->userdata('userid'))),
			'from'      => '/tools/datamining/',
			'caption'   => 'Data Mining',
			'export'    => 'data_mining',
			'sort'      => 'desc',
			'channel'   => $this->MCatalog->fillChannelNameAmazon(),
			'categoria' => $this->MCatalog->fillCategoryAmazon(),
			'country'   => $this->MCatalog->fillCountryCode(),
			'baseUrl'   => base_url(),
		);

		$data['colNames'] = "['ID','ChannelName','ASIN','CountryCode','IsActive','Category','Title','SKU','TQOH','DartFBMSKU','DartFBASKU','ELEMENTARYFBMSKU','ELEMENTARYFBASKU','ManufacturerPN','Manufacturer','Enclosure','BrandMentioned','BrandSell','NeedToSend','NeedToRequestBack','PendingOrders','Stamp','Comments', ' Create User', 'Create Date', 'Change User', 'Change Date','FLXActive','FLXStamp']";

		$data['colModel'] = "[
                {name:'id',index:'id', width:60, align:'center',hidden:true, editable:true,frozen:true},
                {name:'ChannelName',index:'ChannelName', width:70, align:'center',frozen:true},
                {name:'ASIN',index:'ASIN', width:80, align:'center',editable:true ,frozen:true},
                {name:'CountryCode',index:'CountryCode', width:45, align:'center',editable:true,edittype:'select',editoptions:{value:'US:US;CA:CA;DE:DE;FR:FR;ID:ID;IT:IT;SP:SP;UK:UK;MX:MX'},frozen:true},
                {name:'IsActive',index:'IsActive', width:40, align:'center',editable:true,edittype:'checkbox',editoptions:{value:'1:0'},frozen:true},
                {name:'Category',index:'Category', width:70, align:'center',editable:true,edittype:'select',editoptions:{value:'FP LAMP:FP LAMP;RPTV LAMP:RPTV LAMP;REMOTE:REMOTE;SPEAKER:SPEAKER;TABLET:TABLET;TOY:TOY;TV PART:TV PART'},frozen:true},
                {name:'Title',index:'Title', width:300, align:'left'},
                {name:'ProductCatalogId',index:'ProductCatalogId', width:50, align:'center',editable:true,editrules:{number:true}, sorttype: 'int'},
                {name:'TotalStock',index:'TotalStock', width:50, align:'center', formatter:'number', formatoptions:{decimalSeparator:'.',thousandsSeparator: ',', decimalPlaces: 2, prefix: '' }},
                {name:'DartFBMSKU',index:'DartFBMSKU', width:85, align:'center',editable:true},
                {name:'DartFBASKU',index:'DartFBASKU', width:85, align:'center',editable:true},
                {name:'ELEMENTARYFBMSKU',index:'ELEMENTARYFBMSKU', width:100, align:'center',editable:true},
                {name:'ELEMENTARYFBASKU',index:'ELEMENTARYFBASKU', width:100, align:'center',editable:true},
                {name:'manufacturerpn',index:'manufacturerpn', width:80, align:'center',editable:true},
                {name:'manufacturer',index:'manufacturer', width:85, align:'center',editable:true},
                {name:'LampType',index:'LampType', width:50, align:'center',editable:true,edittype:'checkbox',editoptions:{value:'1:0'}},
                {name:'BrandMentioned',index:'BrandMentioned', width:90, align:'center',editable:true},
                {name:'BrandSell',index:'BrandSell', width:80, align:'center',editable:true,edittype:'select',editoptions:{value:'PHILIPS:PHILIPS;OSRAM:OSRAM;PHOENIX:PHOENIX;OEM:OEM;NEOLUX:NEOLUX;USHIO:USHIO;COMPATIBLE:COMPATIBLE;LUTEMA:LUTEMA;AURABEAM:AURABEAM'}},
                {name:'NeedToSend',index:'NeedToSend', width:70, align:'center', sorttype: 'int'},
                {name:'NeedToRequestBack',index:'NeedToRequestBack', width:75, align:'center', sorttype: 'int'},
                {name:'PendingOrders',index:'PendingOrders', width:75, align:'center', sorttype: 'int'},
                {name:'Stamp',index:'Stamp', width:90, align:'center'},
                {name:'Comments',index:'Comments', width:100, align:'center'},
                {name:'create_user',index:'create_user', width:80, align:'center'},
                {name:'create_date',index:'create_date', width:80, align:'center'},
                {name:'change_user',index:'change_user', width:80, align:'center'},
                {name:'change_date',index:'change_date', width:100, align:'center'},
                {name:'FLXActive',index:'FLXActive', width:100, align:'center',editable:true,edittype:'checkbox',editoptions:{value:'1:0'},frozen:true},
                {name:'FLXStamp',index:'FLXStamp', width:100,  align:'center'}
            ]";

		$this->build_content($data);
		$this->render_page();
	}

	function getData() {

		$page  = isset($_REQUEST['page'])?$_REQUEST['page']:1;// get the requested page
		$limit = isset($_REQUEST['rows'])?$_REQUEST['rows']:10;// get how many rows we want to have into the gri
		$sidx  = $_GET['sidx'];// get index row - i.e. user click to sort
		$sord  = $_GET['sord'];

		$search    = $this->input->get('ds');
		$channel   = $this->input->get('ch');
		$categoria = $this->input->get('ca');
		$country   = $this->input->get('co');

		//Select utilizado para realizar el conteo del numero de registros devueltos por la consulta.
		$selectCount = 'SELECT COUNT(*) AS rowNum';

		//Select General con los campos necesarios para la vista
		$select = "SELECT D.[id]
                    ,D.[ChannelName]
                    ,D.[ASIN]
                    ,D.[CountryCode]
                    ,D.[IsActive]
                    ,D.[Category]
                    ,D.[Title]
                    ,D.[ProductCatalogId]
                    ,D.[TotalStock]
                    ,D.[DARTFBMSKU]
                    ,D.[DARTFBASKU]
                    ,D.[ELEMENTARYVISIONFBMSKU]
                    ,D.[ELEMENTARYVISIONFBASKU]
                    ,D.[manufacturerpn]
                    ,D.[manufacturer]
                    ,D.[LampType]
                    ,D.[BrandMentioned]
                    ,D.[BrandSell]
                    ,D.[NeedToSend]
                    ,D.[NeedToRequestBack]
                    ,D.[PendingOrders]
                    ,D.[Stamp]
                    ,D.[Comments]
                    ,D.[create_user]
                    ,D.[create_date]
                    ,D.[change_user]
                    ,D.[change_date]
                    ,D.[FLXActive]
                    ,D.[FLXStamp]";

		$selectSlice = "SELECT id,
                          ChannelName,
                          ASIN,
                          CountryCode,
                          IsActive,
                          Category,
                          Title,
                          ProductCatalogId,
                          TotalStock,
                          DartFBMSKU,
                          DartFBASKU,
                          ELEMENTARYVISIONFBMSKU,
                          ELEMENTARYVISIONFBASKU,
                          manufacturerpn,
                          manufacturer,
                          LampType,
                          BrandMentioned,
                          BrandSell,
                          NeedToSend,
                          NeedToRequestBack,
                          PendingOrders,
                          Stamp,
                          Comments,
                          create_user,
                          create_date,
                          change_user,
                          change_date,
                          FLXActive,
                          FLXStamp";
		$from = " FROM (
                      SELECT a.[id],
                          a.[ChannelName],
                          a.[ASIN],
                          a.[CountryCode],
                          a.[IsActive],
                          a.[Category],
                          a.[Title],
                          a.[ProductCatalogId],
                          d.[TotalStock],
                          b.[DartFBMSKU],
                          b.[DartFBASKU],
                          b.[ELEMENTARYVISIONFBMSKU],
                          b.[ELEMENTARYVISIONFBASKU],
                          a.[manufacturerpn],
                          a.[manufacturer],
                          a.[LampType],
                          a.[BrandMentioned],
                          a.[BrandSell],
                          c.[NeedToSend],
                          c.[NeedToRequestBack],
                          (SELECT SUM(OrderQty) FROM [Inventory].[dbo].[AmazonFBAOrders]
                            WHERE ASIN = a.[ASIN] and Completed = 'No' group by ASIN)
                          AS PendingOrders,
                          a.[Stamp],
                          a.[Comments],
                          [Inventory].[dbo].[fn_GetUserName](a.[create_user]) AS [create_user],
                          a.[create_date],
                          [Inventory].[dbo].[fn_GetUserName](a.[change_user]) AS [change_user],
                          a.[change_date],
                          a.[FLXActive],
                          a.[FLXStamp]

                        FROM  [Inventory].[dbo].[Amazon] as a
                        LEFT OUTER JOIN [Inventory].[dbo].[AmazonMerchantSKU] AS b
                        ON a.[ASIN] = b.[ASIN]
                        LEFT OUTER JOIN [Inventory].[dbo].[AmazonAnalysisTable] AS c
                        ON c.[ASIN] = a.[ASIN] and c.[SKU] = a.[ProductCatalogId]
                        LEFT OUTER JOIN [Inventory].[dbo].[Global_Stocks] AS d
						ON d.ProductCatalogId = a.[ProductCatalogId]
                  ) D ";

		$where       = " ";
		$wherefields = array('D.[ASIN]', 'D.[ProductCatalogId]', 'D.[manufacturer]', 'D.[manufacturerpn]', 'D.[BrandMentioned]', 'D.[BrandSell]', 'D.[DartFBMSKU]', 'D.[DartFBASKU]', 'D.[ELEMENTARYVISIONFBMSKU]', 'D.[ELEMENTARYVISIONFBASKU]', 'D.[create_user]');
		$where .= $this->MCommon->concatAllWerefields($wherefields, $search);

		if ($channel) {$where .= " and (D.[ChannelName] = '{$channel}') ";}

		if ($categoria) {$where .= " and (D.[Category] = '{$categoria}') ";}

		if ($country) {$where .= " and (D.[CountryCode] = '{$country}') ";}

		//Obtenemos todos los nombres de campos de la tabla productCatalog
		//$fields = $this->MCommon->getAllfields($table);
		//creamos el where concatenando todos los nombres de campos y las palabras de busqueda

		$SQL    = "{$selectCount}{$from}{$where}";
              
		$result = $this->MCommon->getOneRecord($SQL);

		$count = $result['rowNum'];

		if ($count > 0) {
			$total_pages = ceil($count/$limit);
		} else {
			$total_pages = 0;
		}
		if ($page > $total_pages) {
			$page = $total_pages;
		}

		$start  = $limit*$page-$limit;// do not put $limit*($page - 1)
		$start  = ($start < 0)?0:$start;
		$finish = $start+$limit;

		$SQL = "WITH mytable AS ($select , ROW_NUMBER() OVER (ORDER BY $sidx $sord) AS RowNumber
				{$from}{$where})
				{$selectSlice}, RowNumber
               FROM mytable
                WHERE RowNumber BETWEEN {$start} AND {$finish}";

		//echo($SQL);
		$result = $this->MCommon->getSomeRecords($SQL);

		$responce          = new stdClass();
		$responce->page    = $page;
		$responce->total   = $total_pages;
		$responce->records = $count;

		$i = 0;
		foreach ($result as $row) {
			$responce->rows[$i]['id']   = $row['id'];
			$responce->rows[$i]['cell'] = array($row['id'] = utf8_encode($row['id']),
				$row['ChannelName'] = utf8_encode($row['ChannelName']),
				$row['ASIN'] = utf8_encode($row['ASIN']),
				$row['CountryCode'] = utf8_encode($row['CountryCode']),
				$row['IsActive'] = utf8_encode($row['IsActive']),
				$row['Category'] = utf8_encode($row['Category']),
				$row['Title'] = utf8_encode($row['Title']),
				$row['ProductCatalogId'] = utf8_encode($row['ProductCatalogId']),
				$row['TotalStock'] = utf8_encode($row['TotalStock']),
				$row['DartFBMSKU'] = utf8_encode($row['DartFBMSKU']),
				$row['DartFBASKU'] = utf8_encode($row['DartFBASKU']),
				$row['ELEMENTARYVISIONFBMSKU'] = utf8_encode($row['ELEMENTARYVISIONFBMSKU']),
				$row['ELEMENTARYVISIONFBASKU'] = utf8_encode($row['ELEMENTARYVISIONFBASKU']),
				$row['manufacturerpn'] = utf8_encode($row['manufacturerpn']),
				$row['manufacturer'] = utf8_encode($row['manufacturer']),
				$row['LampType'] = utf8_encode($row['LampType']),
				$row['BrandMentioned'] = utf8_encode($row['BrandMentioned']),
				$row['BrandSell'] = utf8_encode($row['BrandSell']),
				$row['NeedToSend'] = utf8_encode($row['NeedToSend']),
				$row['NeedToRequestBack'] = utf8_encode($row['NeedToRequestBack']),
				$row['PendingOrders'] = utf8_encode($row['PendingOrders']),
				$row['Stamp'] = utf8_encode($row['Stamp']),
				$row['Comments'] = utf8_encode($row['Comments']),
				$row['create_user'] = utf8_encode($row['create_user']),
				$row['create_date'] = utf8_encode($row['create_date']),
				$row['change_user'] = utf8_encode($row['change_user']),
				$row['change_date'] = utf8_encode($row['change_date']),
				$row['FLXActive'] = utf8_encode($row['FLXActive']),
				$row['FLXStamp'] = utf8_encode($row['FLXStamp'])
			);
			$i++;
		}
		echo json_encode($responce);
	}

	function countryCodes() {
		$SQL = "SELECT countryCode FROM inventory.dbo.Amazon GROUP BY countryCode";

		echo $this->MCommon->fillCombo($SQL, 'countryCode');
	}

	function validateAsin() {
		$SQL    = "select count(ASIN) as qty from inventory.dbo.Amazon where ASIN = '{$_POST['av']}'";
		$return = $this->MCommon->getOneRecord($SQL);
		echo $return['qty'];
	}

	function fillManufacturerPN() {
		$SQL = "select distinct PartNumber From mitdb.dbo.ProjectorData where PartNumber LIKE '%{$_GET['term']}%'";
		echo json_encode($this->MCommon->fillAutoComplete($SQL, 'PartNumber'));
	}

	function fillBrandSell() {
		$SQL = "SELECT BrandSell FROM Inventory.dbo.Amazon WHERE BrandSell LIKE '%{$_GET['term']}%' GROUP BY BrandSell";
		echo json_encode($this->MCommon->fillAutoComplete($SQL, 'BrandSell'));
	}

	function getManufacturer() {
		$SQL    = "SELECT DISTINCT Brand FROM mitdb.dbo.ProjectorData WHERE PartNumber = '{$_POST['q']}'";
		$return = $this->MCommon->getOneRecord($SQL);
		echo ($return['Brand']);
	}

	function getMITSKU() {
		$SQL = "SELECT TOP 1 PC.ID FROM inventory.dbo.ProductCatalog AS PC
                WHERE ((CONVERT(varchar(250),PC.ID,0)) + (CONVERT(varchar(250),PC.Manufacturer,0)) + (CONVERT(varchar(250),PC.Name,0)) like '%{$_POST['q']}%' ) and PC.Name LIKE '%{$_POST['r']}%'";
		$return = $this->MCommon->getOneRecord($SQL);
		echo ($return['ID']);
	}

	function getBrandMentioned() {
		$SQL = "SELECT DISTINCT BrandMentioned FROM inventory.dbo.Amazon WHERE BrandMentioned LIKE '%{$_GET['term']}%'";
		echo json_encode($this->MCommon->fillAutoComplete($SQL, 'BrandMentioned'));
	}

	function saveRecord() {

		$Title = $_POST['Title'];
		$Title = addslashes($Title);

		$Comments = $_POST['Comments'];
		$Comments = addslashes($Comments);

		$user = $this->session->userdata('userid');

		$SQL = "INSERT INTO [inventory].[dbo].[Amazon]
                            (ChannelName,
                            ASIN,
                            CountryCode, IsActive, Category, Title,
                            ProductCatalogId,manufacturerpn,
                            manufacturer,
                            LampType,
                            BrandMentioned,
                            BrandSell,
                            Comments,
                            user_ID,
                            FLXActive)
                Values
                ('{$_POST['ChannelName']}',
                 '{$_POST['Asin']}',
                 '{$_POST['CountryCode']}',
                 '{$_POST['IsActive']}',
                 '{$_POST['Category']}',
                 '{$Title}',
                 '{$_POST['ProductCatalogId']}',
                 '{$_POST['manufacturerpn']}',
                 '{$_POST['manufacturer']}',
                 '{$_POST['LampType']}',
                 '{$_POST['BrandMentioned']}',
                 '{$_POST['BrandSell']}',
                 '{$Comments}',
                 '{$user}',
                 '{$_POST['FLXActive']}')";

		//$SQL = stripslashes($SQL);
		$this->MCommon->saveRecord($SQL, 'InventorySave');
	}

	function saveDefault() {

		$id = $this->input->post('id');

		$ASIN             = $_POST['ASIN'];
		$CountryCode      = $_POST['CountryCode'];
		$IsActive         = $_POST['IsActive'];
		$Category         = $_POST['Category'];
		$ProductCatalogId = $_POST['ProductCatalogId'];
		$manufacturerpn   = $_POST['manufacturerpn'];
		$manufacturer     = $_POST['manufacturer'];
		$LampType         = $_POST['LampType'];
		$BrandMentioned   = $_POST['BrandMentioned'];
		$BrandSell        = $_POST['BrandSell'];
		$FLXActive        = $_POST['FLXActive'];
		
		$user = $this->session->userdata('userid');
		echo $FLXActive;

		if($FLXActive=="1"){
			$test = 'GETDATE()';
		}
		else{
			$test = 'NULL';
		}
		

		$SQL = "UPDATE [Inventory].[dbo].[Amazon]
      SET ASIN = '{$ASIN}', CountryCode = '{$CountryCode}', IsActive = {$IsActive}, Category = '{$Category}', ProductCatalogId = {$ProductCatalogId}, manufacturerpn = '{$manufacturerpn}',
      manufacturer = '{$manufacturer}', LampType = {$LampType}, BrandMentioned = '{$BrandMentioned}', BrandSell = '{$BrandSell}',FLXActive = '{$FLXActive }' ,user_ID = '{$user}',
      FLXStamp = {$test}   WHERE id = {$id}";

		$this->MCommon->saveRecord($SQL, 'Inventory');
	}

	function saveOrder() {

		$oams   = $this->input->post('oams');
		$oaasin = $this->input->post('oaasin');
		$oqty   = $this->input->post('oqty');

		$SQL = "INSERT INTO [Inventory].[dbo].[AmazonFBAOrders] (MerchantSKU,ASIN, OrderQty,OrderNotes,OrderDate,Completed)
              VALUES ({$oams},{$oaasin},{$oqty},'',GETDATE(),'No')";

		$this->MCommon->saveRecord($SQL, 'Inventory');
		//print_r($SQL);
	}

	function deleteAsin() {

		$asin = $this->input->post('oaasin');

		$SQL = "DELETE FROM [Inventory].[dbo].[Amazon] WHERE ASIN = {$asin}";
		$this->MCommon->executeQuery($SQL, 'InventorySave');

		//print_r($SQL);
	}

	function csvExport($name) {

		header('Content-type: application/vnd.ms-excel');
		header("Content-Disposition: attachment; filename=".$name.'_'.date("D-M-j").".xls");
		header("Pragma: no-cache");

		$buffer = $_POST['csvBuffer'];

		try {
			echo $buffer;
		} catch (Exception $e) {

		}
	}

}
?>
