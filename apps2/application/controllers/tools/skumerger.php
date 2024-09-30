<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class SkuMerger extends BP_Controller {

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

	$this->title = "MI Technologiesinc - Quotation Sheet";

	$this->description = "Quotation Sheet";

	$this->css = array('form.css', 'fluid.css', 'table.css','menu.css', 'jqueryui/redmond/jquery-ui-1.8.21.custom.css', 'jqgrid/ui.jqgrid.css');

	// Define custom javascript
	$this->javascript = array('jqueryui/ui/jquery-ui-1.8.21.custom.js', 'jquery.validate.min.js');

	// If the page has menu
	$this->load->library('Layout');
	$menu = new Layout;


	$data = array(
	    'menu' => $menu->show_menu($this->MUsers->getUserFullName($this->session->userdata('userid'))),
	);

	$this->build_content($data);
	$this->render_page();
    }

    function MergerSKU() {

    	$retainSku = $this->input->post('rs');
    	$deleteSku = $this->input->post('dk');

 		$select = " SET ANSI_WARNINGS ON 
 			UPDATE OrderManager.dbo.[Order Details] 
			SET SKU = '{$retainSku}' 
			WHERE SKU = '{$deleteSku}' 

			UPDATE [OrderManager].[dbo].[Lists] 
			SET Text2 = '{$retainSku}' 
			WHERE Text2 = '{$deleteSku}' 

			UPDATE [OrderManager].[dbo].[AliasSKUs] 
			SET ParentSKU =  '{$retainSku}' 
			WHERE ParentSKU =  '{$deleteSku}' 

			UPDATE Inventory.dbo.AssemblyDetails 
			SET SubSKU = '{$retainSku}' 
			WHERE SubSKU = '{$deleteSku}' 

			UPDATE Inventory.dbo.Amazon 
			SET ProductCatalogId = '{$retainSku}' 
			WHERE ProductCatalogId = '{$deleteSku}' 

			UPDATE Inventory.dbo.AmazonFBA 
			SET MITSKU = '{$retainSku}' 
			WHERE MITSKU = '{$deleteSku}' 
 
			UPDATE [Inventory].[dbo].[ChannelManagementDB] 
			SET [ProductCatalogID] = '{$retainSku}' 
			WHERE [ProductCatalogID] = '{$deleteSku}' 

			UPDATE [Inventory].[dbo].[Compatibility] 
			SET [ProductCatalogID] = '{$retainSku}' 
			WHERE [ProductCatalogID] = '{$deleteSku}' 
 
			UPDATE MITDB.dbo.ProjectorData 
			SET EncSKU = '{$retainSku}' 
			WHERE EncSKU = '{$deleteSku}' 

			UPDATE MITDB.dbo.ProjectorData 
			SET EncSKUPH = '{$retainSku}' 
			WHERE EncSKUPH = '{$deleteSku}' 

			UPDATE MITDB.dbo.ProjectorData 
			SET BareSKU = '{$retainSku}' 
			WHERE BareSKU = '{$deleteSku}' 

			UPDATE MITDB.dbo.ProjectorData 
			SET BareSKUPH = '{$retainSku}' 
			WHERE BareSKUPH = '{$deleteSku}' 

			UPDATE MITDB.dbo.ProjectorData 
			SET BareSKUOS = '{$retainSku}' 
			WHERE BareSKUOS = '{$deleteSku}' 

			UPDATE [184.106.104.133].[DiscountTVL9.3.1].[dbo].[Inventory] 
			SET [VendorFullSKU] = '{$retainSku}' 
			WHERE [VendorFullSKU] = '{$deleteSku}' 

			UPDATE [23.253.164.78,49687].[mitechnologies9.4].[dbo].[Inventory] 
			SET [VendorFullSKU] = '{$retainSku}' 
			WHERE [VendorFullSKU] = '{$deleteSku}' 

			UPDATE [BTData].[dbo].Inventory 
			SET PartNum = '{$retainSku}' 
			WHERE PartNum = '{$deleteSku}' 

			UPDATE [BTDataDM].[dbo].Inventory 
			SET PartNum = '{$retainSku}' 
			WHERE PartNum = '{$deleteSku}' 

			UPDATE [BTDataDTVL].[dbo].Inventory 
			SET PartNum = '{$retainSku}' 
			WHERE PartNum = '{$deleteSku}' 

			UPDATE [BTDataLampTycoons].[dbo].Inventory 
			SET PartNum = '{$retainSku}' 
			WHERE PartNum = '{$deleteSku}' 

			DELETE FROM Inventory.dbo.Suppliers 
			WHERE ProductCatalogId = '{$deleteSku}' ";

			$this->db->query($select);

			return 'true';
    }

}
?>