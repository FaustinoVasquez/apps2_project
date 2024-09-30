<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Sysopt extends BP_Controller {

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

	$this->title = "MI Technologiesinc - System Options";

	$this->description = "System Options";

	$this->css = array('form.css', 'fluid.css', 'table.css', 'jqueryui/redmond/jquery-ui-1.8.21.custom.css', 'jqgrid/ui.jqgrid.css', 'menu.css',);

	// Define custom javascript
	$this->javascript = array('jqueryui/ui/jquery-ui-1.8.21.custom.js', 'jqgrid/i18n/grid.locale-en.js', 'jqgrid/jquery.jqGrid.min.js', 'jqgrid/jquery.jqGrid.fluid.js', 'popup.js');


	// If the page has menu
	$this->load->library('Layout');
	$menu = new Layout;

	$sp = $this->uri->segment(3);



	 $data = array(
	     'menu'  => $menu->show_menu($this->MUsers->getUserFullName($this->session->userdata('userid'))),
	);


	$this->build_content($data);
	$this->render_page();
    }

    function processkiller() {

	$select = " EXECUTE Inventory.DBO.[sp_Proccess_Killer];";
	
	$this->db->query($select);

	return 'true';

    }


    function amazonMerchantSKU() {

	$select = " Inventory].[dbo].[CreateAmazonMerchantSKU];";

	$this->db->query($select);

	return 'true';

    }


    function UpdateProductCatalogFloorPrice() {

	$select = " Inventory].[dbo].[sp_UpdateProductCatalogFloorPrice];";

	$this->db->query($select);

	return 'true';

    }


    function CalculateAvgShipCost() {

	$select = " Inventory].[dbo].[sp_CalculateAvgShipCost];";

	$this->db->query($select);

	return 'true';

    }


    function UpdateAmazonTable() {

	$select = " Inventory].[dbo].[sp_UpdateAmazonTable-MITSKU];";

	$this->db->query($select);

	return 'true';

    }


    function updateVirtStockBulbsAndKits() {

	$select = " Inventory].[dbo].[sp_Update_VirtStock_BulbsAndKits];";

	$this->db->query($select);

	return 'true';

    }


    function updateVirtStockLampWithHousing() {

	$select = " Inventory].[dbo].[sp_Update_VirtStock_LampWithHousing];";

	$this->db->query($select);

	return 'true';

    }


    function reindexInventoryDatabase() {

	$select = " exec expressmaint
			   @database = 'Inventory',
			   @optype = 'REINDEX',
			   @report = 0";

	$this->db->query($select);

	return 'true';

    }


    function reindexOrderManagerDatabase() {

	$select = " exec expressmaint
			   @database = 'OrderManager',
			   @optype = 'REINDEX',
			   @report = 0";

	$this->db->query($select);

	return 'true';

    }


}
?>