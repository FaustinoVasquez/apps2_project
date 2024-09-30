<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Quotationsheet extends BP_Controller {

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
	$this->css = array('form.css', 'fluid.css', 'table.css','menu.css');

	// Define custom javascript
	$this->javascript = array('jqueryui/ui/jquery-ui-1.8.21.custom.js', 'jquery.validate.min.js');


	// If the page has menu
	$this->load->library('Layout');
	$menu = new Layout;

	$data = array(
	    'menu' => $menu->show_menu($this->MUsers->getUserFullName($this->session->userdata('userid'))),
	    'categories' => $this->MCatalog->fillCategories('None'),
	    'suppliers' => $this->MCatalog->fillSuppliers('None'),
	);

	$this->build_content($data);
	$this->render_page();
    }

    function SendEmail() {

    	$categoryID = $this->input->post('cat');
    	$supplierID = $this->input->post('supl');
    	$emailAddress = $this->input->post('em');

 	$select = " EXECUTE Inventory.[dbo].[sp_Email_QuotationSheet] {$categoryID},{$supplierID},'{$emailAddress}';";

	$this->db->query($select);

	return 'true';

    }
}
?>