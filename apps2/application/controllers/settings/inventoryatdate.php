<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class InventoryAtDate extends BP_Controller {

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
	    'dateto' => date("m/d/Y"),
	);

	$this->build_content($data);
	$this->render_page();
    }

    function SendEmail() {

    	$dateto = $this->input->post('dt');
    	$emailAddress = $this->input->post('em');

 	$select = " EXECUTE Inventory.dbo.[sp_Bins_Email_Inventory_At_Date] '{$dateto}',0,7000,'{$emailAddress}'";

	$this->db->query($select);

	return 'true';
    }
}
?>