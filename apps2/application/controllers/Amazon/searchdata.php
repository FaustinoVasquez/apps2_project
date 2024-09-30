<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Searchdata extends BP_Controller {

    public function __construct() {
		parent::__construct();

		$is_logged_in = $this->session->userdata('is_logged_in');

		if (!isset($is_logged_in) || $is_logged_in != true) {
		    redirect(base_url(), 'refresh');
		}

		if ($this->MUsers->isValidUser($this->session->userdata('userid'), 855500) != 1) {// Access Code
		    redirect('Catalog/prodcat', 'refresh');
		}
    }

    public function index() {
    	 // Define custom CSS
   $this->css = array('bootstrap.min.css','search.css');
   $this->javascript = array('jqueryui/ui/jquery-ui-1.8.21.custom.js');

	$this->title = "MI Technologiesinc - Amazon Search Data";

	$this->description = "Amazon Search Data";
 	$this->load->library('Layout');
    $menu = new Layout;

    $data = array(
  		'menu' => '',
  	);

	$this->build_content($data);
	$this->render_page();
    } 

 

    public function getRawAsin() {

    $brand = ($this->input->get('br')) ? $this->input->get('br'):'3M';
   	$countryCode = ($this->input->get('cc')) ? $this->input->get('cc'):'US';
    $statusAsin = ($this->input->get('sa')) ? $this->input->get('sa'):'';
    $id = ($this->input->get('id'))?$this->input->get('id'):0;
    $flag = ($this->input->get('fg'))?$this->input->get('fg'):'';

    $userId = $this->session->userdata('userid');

    $data = array();
	$SQL = "EXEC Inventory.dbo.sp_GetFncAmazonSearchDATA '$brand', '$countryCode', '$statusAsin', '$userId', '$id', '$flag'";
	//echo $SQL;

	$query = $this->db->query($SQL);

	if ($query->num_rows() > 0) {
	
		foreach($query->result() as $row) {
	    	$data['rSearchTerm'] = $row->SearchTerm;
	    	$data['rASIN'] = $row->ASIN;
	    	$data['rPotentialSKU'] = $row->PotentialSKU;
	    	$data['rManufacturer'] = $row->Manufacturer;
	    	$data['rBrand'] = $row->Brand;
	    	$data['rTitle'] = $row->Title;
	    	$data['rStatusAsin'] = $row->Status;
			$data['rImage1URL'] = $row->Image1URL;
	    	$data['rCountryCode'] = $row->CountryCode;
	    	$data['rBullet1'] = $row->Bullet1;
	    	$data['rBullet2'] = $row->Bullet2;
	    	$data['rBullet3'] = $row->Bullet3;
	    	$data['rBullet4'] = $row->Bullet4;
	    	$data['rBullet5'] = $row->Bullet5;
	    	$data['rDescription'] = $row->Description;
	    	$data['rAutoAnalytics'] = $row->AutoAnalytics;
	    	$data['rId'] = $row->ID;
	    }
	}

	echo json_encode($data);
    }


    public function saveApproved(){ 
    	$sku = ($this->input->post('sku'))?$this->input->post('sku'):'';
    	$altSku = ($this->input->post('altSku'))?$this->input->post('altSku'):'';
    	$asin = $this->input->post('asin');
    	$countryCode = $this->input->post('cc');
    	$userId = $this->session->userdata('userid');

    	if($altSku){

    		$altSku = explode(";", $altSku);
    		$mysku = $altSku[0];
    		$myManufacturer = $altSku[1];
    		$myPartNumber = $altSku[2];


			$SQL= " UPDATE [Inventory].[dbo].[AmazonSearchDATA]
		 	SET MapToSKU='{$mysku}' , StatusUserID = {$userId}, Status ='Approved', StatusManufacturer ='{$myManufacturer}', StatusPartNumber ='{$myPartNumber}'
			WHERE ASIN='{$asin}' and CountryCode = '{$countryCode}' ";

    	}else{

     	  	$SQL= " UPDATE [Inventory].[dbo].[AmazonSearchDATA]
		 	SET MapToSKU='{$sku}' , StatusUserID = {$userId}, Status ='Approved'
			WHERE ASIN='{$asin}' and CountryCode = '{$countryCode}' ";

    	}

		$query = $this->db->query($SQL);
		echo $query;
    }

    public function declineSkip(){
    	$message = $this->input->post('message');
    	$asin = $this->input->post('asin');
    	$countryCode = $this->input->post('cc');
    	$userId = $this->session->userdata('userid');
    	
    	$SQL= " UPDATE [Inventory].[dbo].[AmazonSearchDATA]
		 		SET  StatusUserID = {$userId}, Status ='{$message}'
				WHERE ASIN='{$asin}' and CountryCode = '{$countryCode}'";
		
		$query = $this->db->query($SQL);
		echo $query;

    }



 	public function getBrands() {


		$SQL = "SELECT distinct A.[Brand]
		FROM [Inventory].[dbo].[AmazonSearchDATA] A 
	    LEFT OUTER JOIN [Inventory].[dbo].[Amazon] B ON B.[ASIN] = A.[ASIN] 
	 	WHERE B.[ASIN] IS NULL" ;
		
		$data['rawBrand'] = $this->create_dropdown($SQL,'Brand','Brand','3M','');
		echo json_encode($data);

    }

    public function getCountryCodes() {

		$brand = $this->input->get('br');

		$SQL = "SELECT  distinct A.[CountryCode]
		FROM [Inventory].[dbo].[AmazonSearchDATA] A 
	    LEFT OUTER JOIN [Inventory].[dbo].[Amazon] B ON B.[ASIN] = A.[ASIN] 
	 	WHERE B.[ASIN] IS NULL AND  A.[Brand] = '{$brand}'" ;
		
		$data['rawCountry'] = $this->create_dropdown($SQL,'CountryCode','CountryCode','US','');	
		echo json_encode($data);

    }

    public function getStatusAsin() {

		$brand = $this->input->get('br');
		$country = $this->input->get('cc');
		$userId = $this->session->userdata('userid');

		$SQL = "SELECT A.[Status]
		FROM [Inventory].[dbo].[AmazonSearchDATA] A 
		LEFT OUTER JOIN [Inventory].[dbo].[Amazon] B ON B.[ASIN] = A.[ASIN] 
		WHERE B.[ASIN] IS NULL AND A.[Brand] = '{$brand}' AND A.[CountryCode] = '{$country}' AND  A.[StatusUserID] = {$userId}
		GROUP BY A.[Status]";
		
		$data['rawStatusAsin'] = $this->create_dropdown($SQL,'Status','Status','','');	
		echo json_encode($data);
    }

	public function getPotencialSkuSelectWH() {

		$potSku = $this->input->get('sku');

		$SQL= " DECLARE @temp TABLE(SKU nvarchar(max), categoryname nvarchar(MAX))
				insert into @temp(SKU,categoryname)(
		        SELECT DISTINCT CAST(COMP.[ProductCatalogID] AS NVARCHAR(MAX)) AS 'SKU', CAST(COMP.[ProductCatalogID] AS NVARCHAR(MAX)) + ' / ' + CS.Name AS 'categoryname'
				   FROM [Inventory].[dbo].[Compatibility] AS COMP
				   LEFT OUTER JOIN [Inventory].[dbo].[ProductCatalog] AS PC ON (COMP.[ProductCatalogID] = PC.[ID])
				   LEFT JOIN [Inventory].[dbo].[Categories] AS CS
				   ON CS.ID = PC.CategoryID
				   WHERE PC.[CategoryID] IN ('10','11','12','13','14','20','21','22','23','24','62','64')
				   AND COMP.[PartNumber] IN (SELECT COMP2.[PartNumber] FROM [Inventory].[dbo].[Compatibility] AS COMP2 WHERE COMP2.[ProductCatalogID] = '{$potSku}')
				)
				select SKU, categoryname from @temp

				UNION ALL

				SELECT '------------' AS SKU, '------------' AS 'categoryname'

				UNION ALL

				SELECT DISTINCT CAST(PC.[ID] AS NVARCHAR(MAX)) AS 'SKU', CAST(PC.[ID] AS NVARCHAR(MAX)) + ' / ' + CS.Name AS 'categoryname'
				FROM [Inventory].[dbo].[ProductCatalog] AS PC
				LEFT JOIN [Inventory].[dbo].[Categories] AS CS
				ON CS.ID = PC.CategoryID
				WHERE PC.[CategoryID] IN ('10','11','12','13','14','20','21','22','23','24','62','64') AND PC.[ID] NOT IN (SELECT SKU FROM @temp)";


	      	$data['psSelect'] = $this->create_dropdown($SQL,'SKU','categoryname', $potSku,'');

		echo json_encode($data);
	}


	public function getPotencialSkuSelectBL(){

		$potSku = $this->input->get('sku');

		$SQL= " SELECT DISTINCT CAST(COMP.[ProductCatalogID] AS NVARCHAR(MAX)) AS 'SKU', CAST(COMP.[ProductCatalogID] AS NVARCHAR(MAX)) + ' / ' + CS.Name AS 'categoryname'
				FROM [Inventory].[dbo].[Compatibility] AS COMP
				LEFT OUTER JOIN [Inventory].[dbo].[ProductCatalog] AS PC ON (COMP.[ProductCatalogID] = PC.[ID])
				LEFT JOIN [Inventory].[dbo].[Categories] AS CS
				ON CS.ID = PC.CategoryID
				WHERE PC.[CategoryID] IN ('5','6','7','8','9','15','16','17','18','19','61','63','87','88','89','90','91')
				AND COMP.[PartNumber] IN (SELECT COMP2.[PartNumber] FROM [Inventory].[dbo].[Compatibility] AS COMP2 WHERE COMP2.[ProductCatalogID] = '{$potSku}')

				UNION ALL

				SELECT '------------' AS SKU, '------------' AS 'categoryname'

				UNION ALL

				SELECT DISTINCT CAST(PC.[ID] AS NVARCHAR(MAX)) AS 'SKU', CAST(PC.[ID] AS NVARCHAR(MAX)) + ' / ' + CS.Name AS 'categoryname'
				FROM [Inventory].[dbo].[ProductCatalog] AS PC
				LEFT JOIN [Inventory].[dbo].[Categories] AS CS
				ON CS.ID = PC.CategoryID
				WHERE PC.[CategoryID] IN ('5','6','7','8','9','15','16','17','18','19','61','63','87','88','89','90','91')";

	      	$data['psSelect'] = $this->create_dropdown($SQL,'SKU','categoryname', $potSku,'');

		echo json_encode($data);
	}

    public function getPotencialSkuElements(){

    	$potSku = $this->input->get('sku');
    	$data = array();


      	$SQL = " SELECT *
					 FROM [Inventory].[dbo].[productCatalog] 
				WHERE ID = $potSku ";


		$query = $this->db->query($SQL);

		if ($query->num_rows() > 0) {
		
			foreach($query->result() as $row) {
		    	$data['lSKU'] = $row->ID;
		    	$data['lmanufacturer'] = $row->Manufacturer;
		    	$data['lName'] = $row->Name;
		    	$data['lImage1URL'] = "http://photos.discount-merchant.com/photos/sku/".$row->ID."/".$row->ID."-1.jpg";
		    	$data['lImage2URL'] = "http://photos.discount-merchant.com/photos/sku/".$row->ID."/".$row->ID."-2.jpg";
		    	$data['lImage3URL'] = "http://photos.discount-merchant.com/photos/sku/".$row->ID."/".$row->ID."-3.jpg";
		    	$data['lImage4URL'] = "http://photos.discount-merchant.com/photos/sku/".$row->ID."/".$row->ID."-4.jpg";
		    	$data['lImage5URL'] = "http://photos.discount-merchant.com/photos/sku/".$row->ID."/".$row->ID."-5.jpg";
		    }

	    }

	    $extraInfo = $this->getExtraInfo($potSku);
	    
	    if($extraInfo){
	    	$data['extraInfo'] = $extraInfo;
	    }

       echo json_encode($data);
	}

	public function searchArternativeSkus(){

		$search = $this->input->get('sd');

		$SQL= "	SELECT DISTINCT COMP.[PartNumber] AS 'SearchTerm'
			      	,COMP.[ProductCatalogID]
			      	,COMP.[Manufacturer]
			      	,COMP.[PartNumber]
			      	,CAT.[Name]
				FROM [Inventory].[dbo].[Compatibility] AS COMP
				LEFT OUTER JOIN [Inventory].[dbo].[ProductCatalog] AS PC ON (COMP.[ProductCatalogID] = PC.[ID])
				LEFT OUTER JOIN [Inventory].[dbo].[Categories] AS CAT ON (PC.[CategoryID] = CAT.[ID])
				WHERE COMP.[PartNumber] LIKE '%{$search}%' AND PC.[CategoryID] NOT IN ('59','60')

				UNION ALL

				SELECT DISTINCT COMPAPN.[AlternativePN]
				   	,COMP.[ProductCatalogID]
				   	,COMP.[Manufacturer]
				   	,COMP.[PartNumber]
				   	,CAT.[Name]
			  	FROM [Inventory].[dbo].[CompatibilityAlternativePN] AS COMPAPN
			  	LEFT OUTER JOIN [Inventory].[dbo].[Compatibility] AS COMP ON (COMPAPN.[OriginalManufacturer] = COMP.[Manufacturer] AND COMPAPN.[PartNumber] = COMP.[PartNumber])
				LEFT OUTER JOIN [Inventory].[dbo].[ProductCatalog] AS PC ON (COMP.[ProductCatalogID] = PC.[ID])
				LEFT OUTER JOIN [Inventory].[dbo].[Categories] AS CAT ON (PC.[CategoryID] = CAT.[ID])
			  	WHERE COMPAPN.[AlternativePN] LIKE '%{$search}%' AND PC.[CategoryID] NOT IN ('59','60')

				UNION ALL

				SELECT DISTINCT CD.[Model]
				   	,COMP.[ProductCatalogID]
				   	,COMP.[Manufacturer]
				   	,COMP.[PartNumber]
				   	,CAT.[Name]
				FROM [Inventory].[dbo].[CompatibilityDetails] AS CD
				LEFT OUTER JOIN [Inventory].[dbo].[Compatibility] AS COMP ON (CD.[OriginalManufacturer] = COMP.[Manufacturer] AND CD.[PartNumber] = COMP.[PartNumber])
				LEFT OUTER JOIN [Inventory].[dbo].[ProductCatalog] AS PC ON (COMP.[ProductCatalogID] = PC.[ID])
				LEFT OUTER JOIN [Inventory].[dbo].[Categories] AS CAT ON (PC.[CategoryID] = CAT.[ID])
				WHERE CD.[Model] LIKE '%{$search}%' AND PC.[CategoryID] NOT IN ('59','60')

				UNION ALL

				SELECT DISTINCT CAM.[AlternativeModel]
				  	,COMP.[ProductCatalogID]
				    ,COMP.[Manufacturer]
				    ,COMP.[PartNumber]
				    ,CAT.[Name]
				FROM [Inventory].[dbo].[CompatibilityAlternativeModels] AS CAM
				LEFT OUTER JOIN [Inventory].[dbo].[CompatibilityDetails] AS CD ON (CAM.[OriginalManufacturer] = CD.[OriginalManufacturer] AND CAM.[OriginalModel] = CD.[Model])
				LEFT OUTER JOIN [Inventory].[dbo].[Compatibility] AS COMP ON (CD.[OriginalManufacturer] = COMP.[Manufacturer] AND CD.[PartNumber] = COMP.[PartNumber])
				LEFT OUTER JOIN [Inventory].[dbo].[ProductCatalog] AS PC ON (COMP.[ProductCatalogID] = PC.[ID])
				LEFT OUTER JOIN [Inventory].[dbo].[Categories] AS CAT ON (PC.[CategoryID] = CAT.[ID])
				WHERE CAM.[AlternativeModel] LIKE '%{$search}%' AND PC.[CategoryID] NOT IN ('59','60')";

		$query = $this->db->query($SQL);

		$data='';

		foreach ($query->result() as $row) {


			$data .= "<option value='".$row->ProductCatalogID.";".$row->Manufacturer.";".$row->PartNumber.";".$row->Name."'>".$row->ProductCatalogID." / ".$row->Manufacturer." / ".$row->PartNumber. " / ". $row->Name. "</option>";
		}
	

		echo json_encode($data);

	}


	public function getExtraInfo($sku){
		
		$SQL = "EXECUTE [Inventory].[dbo].[sp_GetCompatibilityBySKU-HTML] '$sku' ";
		$query = $this->db->query($SQL);
		
		$data='';

		if ($query->result() > 0){
			foreach($query->result() as $row) {
		    	$data = $row->extraInfo;
		    }

			return $data;
		}

		return false;

	}

	public function sendEmailMiTechnologiesInc()
	{
		$ID = $this->input->get('id');
		$Note = $this->input->get('note');
		$Subject = $this->input->get('sbj');
		if($Subject == null){$Subject = 'Check this article';}
		$EmailTo = $this->input->get('to');
		$value = explode(";",$EmailTo);
		for($i=0; $i <= (count($value)-1) ; $i++)
		{
			$select = "EXEC Inventory.[dbo].[sp_Email_AmazonSearchDATA] '$ID', '$Note', '$value[$i]', '$Subject'";
			$this->db->query($select);
		}
		return true;
	}


	public function create_dropdown($SQL,$fieldID,$fieldName,$selected,$selectedText){

		$data='';

		if (!$selected){
			if (!$selectedText){
				$data.="<option value='0'>ALL</option>";
			}else{
				$data.="<option value='0'>".$selectedText."</option>";
			}
		}

		$query = $this->db->query($SQL);

		foreach ($query->result_array() as $row) {

			if ($row[$fieldID] == $selected){
			 	$data .= "<option value='{$row[$fieldID]}' selected='selected'>{$row[$fieldName]}</option>";
			 }else{
				$data .= "<option value='".$row[$fieldID]."'>".$row[$fieldName]."</option>";
			}	
		}
		return $data;
	}
}
?>