<?php

if (!defined('BASEPATH')) {
	exit('No direct script access allowed');
}

class Costinfo extends BP_Controller {

	public function __construct() {
		parent::__construct();

		$is_logged_in = $this->session->userdata('is_logged_in');

		if (!isset($is_logged_in) || $is_logged_in != true) {
			redirect(base_url(), 'refresh');
		}
	}

	function index() {

		$this->title       = "MI Technologiesinc - Cost Info";
		$this->description = " Cost Info";
		// Define custom CSS
		$this->css = array('menu.css', 'form.css', 'fluid.css', 'table.css', 'jqueryui/redmond/jquery-ui-1.8.21.custom.css', 'jqgrid/ui.jqgrid.css');
		// Define custom javascript
		$this->javascript = array('jqueryui/ui/jquery-ui-1.8.21.custom.js', 'jqgrid/i18n/grid.locale-en.js', 'jqgrid/jquery.jqGrid.min.js', 'jqgrid/jquery.jqGrid.fluid.js', 'site.js');
		$this->hasNav     = False;

		$data['sku']     = $this->uri->segment(3);
		//$data['cat']     = $this->MCatalog->getCategoryId($data['sku']);
		$data['title']   = 'Cost Info';//Page Title
		$data['caption'] = 'Cost Info';
		$data['from']    = '/Tabs/costinfo/';

		//Cargamos la libreria comumn
		$this->load->library('common');

		//--IF CATEGORY = '60' - "Lamp Housing Kits - Front Projection" USE THIS QUERY
//		switch ($data['cat']) {
//			case '60':
//				$columns = array(
//					'LowestCost'      => array('colName'      => 'LowestCost', 'colModel' => "{name:'LowestCost',index:'LowestCost', width:60, align:'center' }"),
//					'AvgPOCost'       => array('colName'       => 'AvgPOCost', 'colModel' => "{name:'AvgPOCost',index:'AvgPOCost', width:60, align:'center'}"),
//					'MITCost'         => array('colName'         => 'MITCost', 'colModel' => "{name:'MITCost',index:'MITCost', width:60, align:'center'}"),
//					'MgtCost'         => array('colName'         => 'MgtCost', 'colModel' => "{name:'MgtCost',index:'MgtCost', width:60, align:'center'}"),
//					'GBCost'          => array('colName'          => 'GBCost', 'colModel' => "{name:'GBCost',index:'GBCost', width:60, align:'center'}"),
//					'STCCost'         => array('colName'         => 'STCCost', 'colModel' => "{name:'STCCost',index:'STCCost', width:60, align:'center'}"),
//					'KWCost'          => array('colName'          => 'KWCost', 'colModel' => "{name:'KWCost',index:'KWCost', width:60, align:'center'}"),
//					'LeaderCost'      => array('colName'      => 'LeaderCost', 'colModel' => "{name:'LeaderCost',index:'LeaderCost', width:60, align:'center'}"),
//					'YitaCost'        => array('colName'        => 'YitaCost', 'colModel' => "{name:'YitaCost',index:'YitaCost', width:60, align:'center'}"),
//					'FYVCost'         => array('colName'         => 'FYVCost', 'colModel' => "{name:'FYVCost',index:'FYVCost', width:60, align:'center'}"),
//					'LampsChoiceCost' => array('colName' => 'LampsChoiceCost', 'colModel' => "{name:'LampsChoiceCost',index:'LampsChoiceCost', width:60, align:'center'}"),
//					'OnlyLampCost'	  => array('colName'    => 'OnlyLampCost', 'colModel' => "{name:'OnlyLampCost',index:'OnlyLampCost', width:60, align:'center'}"),
//				);
//				break;
//			case '24':
//				//--IF CATEGORY = '24' - "GENERIC FP LAMPS WITH HOUSING" USE THIS QUERY
//				$columns = array(
//					'LowestCost'    => array('colName'    => 'LowestCost', 'colModel' => "{name:'LowestCost',index:'LowestCost', width:60, align:'center' }"),
//					'AvgPOCost'     => array('colName'     => 'AvgPOCost', 'colModel' => "{name:'AvgPOCost',index:'AvgPOCost', width:60, align:'center'}"),
//					'MITCost'       => array('colName'       => 'MITCost', 'colModel' => "{name:'MITCost',index:'MITCost', width:60, align:'center'}"),
//					'ArcliteCost'   => array('colName'   => 'ArcliteCost', 'colModel' => "{name:'ArcliteCost',index:'ArcliteCost', width:60, align:'center'}"),
//					'GloryCost'     => array('colName'     => 'GloryCost', 'colModel' => "{name:'GloryCost',index:'GloryCost', width:60, align:'center'}"),
//					'CLPCost'       => array('colName'       => 'CLPCost', 'colModel' => "{name:'CLPCost',index:'CLPCost', width:60, align:'center'}"),
//					'GBCost'        => array('colName'        => 'GBCost', 'colModel' => "{name:'GBCost',index:'GBCost', width:60, align:'center'}"),
//					'LeaderCost'    => array('colName'    => 'LeaderCost', 'colModel' => "{name:'LeaderCost',index:'LeaderCost', width:60, align:'center'}"),
//					'YitaCost'      => array('colName'      => 'YitaCost', 'colModel' => "{name:'YitaCost',index:'YitaCost', width:60, align:'center'}"),
//					'FBALowestCost' => array('colName' => 'FBALowestCost', 'colModel' => "{name:'FBALowestCost',index:'FBALowestCost', width:60, align:'center'}"),
//					'FBMLowestCost' => array('colName' => 'FBMLowestCost', 'colModel' => "{name:'FBMLowestCost',index:'FBMLowestCost', width:60, align:'center'}"),
//				);
//				break;
//
//			default:
//				//--ALL OTHER CATEGORIES
//				//
//				$columns = array(
//					'LastQuotedPrice' => array('colName' => 'LastQuotedPrice', 'colModel' => "{name:'LastQuotedPrice',index:'LastQuotedPrice', width:60, align:'center' }"),
//					'AvgPOCost'       => array('colName'       => 'AvgPOCost', 'colModel' => "{name:'AvgPOCost',index:'AvgPOCost', width:60, align:'center'}"),
//					'FBALowestCost'   => array('colName'   => 'FBALowestCost', 'colModel' => "{name:'FBALowestCost',index:'FBALowestCost', width:60, align:'center'}"),
//					'FBMLowestCost'   => array('colName'   => 'FBMLowestCost', 'colModel' => "{name:'FBMLowestCost',index:'FBMLowestCost', width:60, align:'center'}"),
//				);
//				break;
//		}

		$columns = array(
			'SupplierName' => array('colName' => 'SupplierName', 'colModel' => "{name:'SupplierName',index:'SupplierName', width:60, align:'center' }"),
			'Cost'       => array('colName'       => 'Cost', 'colModel' => "{name:'Cost',index:'Cost', width:60, align:'center'}"),
			'LeadTime'   => array('colName'   => 'LeadTime', 'colModel' => "{name:'LeadTime',index:'LeadTime', width:60, align:'center'}"),
			'SupplierSKU'   => array('colName'   => 'SupplierSKU', 'colModel' => "{name:'SupplierSKU',index:'SupplierSKU', width:60, align:'center'}"),
			'LastUpdated'   => array('colName'   => 'LastUpdated', 'colModel' => "{name:'LastUpdated',index:'LastUpdated', width:60, align:'center'}"),
		);

		$data['headers'] = $this->common->CreateColname($columns, 'colName');
		$data['body']    = $this->common->CreateColmodel($columns, 'colModel');

		$this->build_content($data);
		$this->render_page();
	}

	function gridData() {

		$page  = isset($_GET['page'])?$_GET['page']:1;// get the requested page
		$limit = isset($_GET['rows'])?$_GET['rows']:10;// get how many rows we want to have into the grid
		$sidx  = isset($_GET['sidx'])?$_GET['sidx']:'id';// get index row - i.e. user click to sort
		$sord  = isset($_GET['sord'])?$_GET['sord']:'desc';// get the direction

		$sku = $this->input->get('sku');
		$cat = $this->MCatalog->getCategoryId($sku);

//		switch ($cat) {
//			case '60':
//				$select = "SELECT Cast(inventory.[dbo].fn_GetLowestPriceFromSuppliersTable ('{$sku}') as Decimal(10,2))  AS 'LowestCost'
//                              ,Cast(IsNull(PC.[UnitCostAvgPO],0) AS Decimal(10,2)) AS 'AvgPOCost'
//                              ,FPHVCI.[MICost] AS 'MITCost'
//                              ,FPHVCI.[MoldGateCost] AS 'MgtCost'
//                              ,FPHVCI.[GrandBulbCost] AS 'GBCost'
//                              ,FPHVCI.[SouthernTechCost] AS 'STCCost'
//                              ,FPHVCI.[KWCost] AS 'KWCost'
//                              ,FPHVCI.[LeaderCost] AS 'LeaderCost'
//                              ,FPHVCI.[YitaCost] AS 'YitaCost'
//                              ,FPHVCI.[FYVCost] AS 'FYVCost'
//                              ,FPHVCI.[LampsChoiceCost] AS 'LampsChoiceCost'
//                              ,FPHVCI.[OnlyLampCost] AS 'OnlyLampCost'
//                        FROM [Inventory].[dbo].[FP-Housing-Vendor-CostInfo] AS FPHVCI
//                        LEFT OUTER JOIN [Inventory].[dbo].[ProductCatalog] AS PC ON (FPHVCI.[MITSKU] = PC.[ID])
//                        WHERE FPHVCI.[MITSKU] = '{$sku}'";
//			break;
//
//			case '24':
//			$select = "SELECT Cast(inventory.[dbo].fn_GetLowestPriceFromSuppliersTable ('{$sku}') as Decimal(10,2)) AS 'LowestCost'
//                          ,Cast(IsNull(PC.[UnitCostAvgPO],0) AS Decimal(10,2)) AS 'AvgPOCost'
//                          ,FPGVCI.[MiTechCost] AS 'MITCost'
//                          ,FPGVCI.[ArcLiteUnitCost] AS 'ArcliteCost'
//                          ,FPGVCI.[GloryUnitCost] AS 'GloryCost'
//                          ,FPGVCI.[ClpUnitCost] AS 'CLPCost'
//                          ,FPGVCI.[GrandBulbsCost] AS 'GBCost'
//                          ,FPGVCI.[LeaderCoCost] AS 'LeaderCost'
//                          ,FPGVCI.[YitaCost] AS 'YitaCost'
//                          ,IsNull(PC.[UnitCostFBA],0) AS 'FBALowestCost'
//                          ,IsNull(PC.[UnitCostFBM],0) AS 'FBMLowestCost'
//                      FROM [Inventory].[dbo].[FP-Generic-Vendor-CostInfo] AS FPGVCI
//                      LEFT OUTER JOIN [Inventory].[dbo].[ProductCatalog] AS PC ON (FPGVCI.[ID] = PC.[ID])
//                      WHERE FPGVCI.[ID] = '{$sku}'";
//			break;
//
//			default:
//			$select = "SELECT Cast(PC.[UnitCost] as Decimal(10,2)) AS 'LastQuotedPrice'
//                             ,Cast(IsNull(PC.[UnitCostAvgPO],0) AS Decimal(10,2)) AS 'AvgPOCost'
//                             ,IsNull(PC.[UnitCostFBA],0) AS 'FBALowestCost'
//                             ,IsNull(PC.[UnitCostFBM],0) AS 'FBMLowestCost'
//                       FROM [Inventory].[dbo].[ProductCatalog] AS PC
//                       WHERE PC.[ID] = '{$sku}'";
//			break;
//		}

		$select = "Select   SI.[SupplierName], 
							CAST(S.[UnitCost] AS Decimal(10,2)) AS [Cost],
							S.[DeliveryTimeDays] AS [LeadTime],
							S.[SupplierSKU] AS [SupplierSKU],
							S.[updated_at] AS [LastUpdated]
                       FROM [Inventory].[dbo].[Suppliers] AS S
                       LEFT OUTER JOIN [Inventory].[dbo].[SupplierInfo] AS SI ON (S.[SupplierID] = SI.[SupplierId])
                       WHERE S.[ProductCatalogID] ='{$sku}' AND S.[UnitCost] != '0' AND S.[UnitCost] IS NOT NULL
                       ORDER BY S.[UnitCost] ASC
                       ";

		$result = $this->MCommon->getSomeRecords($select);

		$count = count($result);

		if ($count > 0) {
			$total_pages = ceil($count/$limit);
		} else {
			$total_pages = 0;
		}
		if ($page > $total_pages) {
			$page = $total_pages;
		}

		$start  = $limit*$page-$limit;// do not put $limit*($page - 1)
		$start  = ($start <= 0)?1:$start;
		$finish = $start+$limit;

		$responce          = new stdClass();
		$responce->page    = $page;
		$responce->total   = $total_pages;
		$responce->records = $count;

		$i = 0;

	

//		switch ($cat) {
//			case '60':
//				foreach ($result as $row) {
//					$responce->rows[$i]['id']   = $row['LowestCost'];
//					$responce->rows[$i]['cell'] = array($row['LowestCost'],
//						$row['AvgPOCost'],
//						$row['MITCost'],
//						$row['MgtCost'],
//						$row['GBCost'],
//						$row['STCCost'],
//						$row['KWCost'],
//						$row['LeaderCost'],
//						$row['YitaCost'],
//						$row['FYVCost'],
//						$row['LampsChoiceCost'],
//						$row['OnlyLampCost'],
//					);
//					$i++;
//				}
//				break;
//			case '24':
//				foreach ($result as $row) {
//					$responce->rows[$i]['id']   = $row['LowestCost'];
//					$responce->rows[$i]['cell'] = array($row['LowestCost'],
//						$row['AvgPOCost'],
//						$row['MITCost'],
//						$row['ArcliteCost'],
//						$row['GloryCost'],
//						$row['CLPCost'],
//						$row['GBCost'],
//						$row['LeaderCost'],
//						$row['YitaCost'],
//						$row['FBALowestCost'],
//						$row['FBMLowestCost'],
//
//					);
//					$i++;
//				}
//				break;
//
//			default:
//				foreach ($result as $row) {
//					$responce->rows[$i]['id']   = $row['LastQuotedPrice'];
//					$responce->rows[$i]['cell'] = array($row['LastQuotedPrice'],
//						$row['AvgPOCost'] = utf8_encode($row['AvgPOCost']),
//						$row['FBALowestCost'] = utf8_encode($row['FBALowestCost']),
//						$row['FBMLowestCost'] = utf8_encode($row['FBMLowestCost']),
//					);
//					$i++;
//				}
//				break;
//		}

		foreach ($result as $row) {
			$responce->rows[$i]['id']   = $row['SupplierName'];
			$responce->rows[$i]['cell'] = array($row['SupplierName'],
				$row['Cost'] = utf8_encode($row['Cost']),
				$row['LeadTime'] = utf8_encode($row['LeadTime']),
				$row['SupplierSKU'] = utf8_encode($row['SupplierSKU']),
				$row['LastUpdated'] = utf8_encode($row['LastUpdated']),
			);
			$i++;
		}

		echo json_encode($responce);
	}

	function csvExportHistory($name) {

		header('Content-type: application/vnd.ms-excel');
		header("Content-Disposition: attachment; filename= history_".$name.".xls");
		header("Pragma: no-cache");

		$buffer = $_POST['csvBuffer'];

		try {
			echo $buffer;
		} catch (Exception $e) {

		}
	}

}

?>