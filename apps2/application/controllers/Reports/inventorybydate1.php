<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Inventorybydate1 extends BP_Controller {

    public function __construct() {
		parent::__construct();

		$is_logged_in = $this->session->userdata('is_logged_in');

		if (!isset($is_logged_in) || $is_logged_in != true) {
		    redirect(base_url(), 'refresh');
		}

		// if ($this->MUsers->isValidUser($this->session->userdata('userid'), 884400) != 1) {// Access Code
		//     redirect('Catalog/prodcat', 'refresh');
		// }
    }

    function index() {

	$this->title = "MI Technologiesinc - Inventory by Date";

	$this->description = "Inventory by Date";

	$this->css = array('form.css', 'fluid.css', 'table.css', 'jqueryui/redmond/jquery-ui-1.8.21.custom.css', 'jqgrid/ui.jqgrid.css', 'menu.css',);

	// Define custom javascript
	$this->javascript = array('jqueryui/ui/jquery-ui-1.8.21.custom.js', 'jqgrid/i18n/grid.locale-en.js', 'jqgrid/jquery.jqGrid.min.js', 'jqgrid/jquery.jqGrid.fluid.js', 'popup.js');


	// If the page has menu
	$this->load->library('Layout');
	$menu = new Layout;

	// If not have menu

	$data = array(
	    'menu' => $menu->show_menu($this->MUsers->getUserFullName($this->session->userdata('userid'))),

	);
 

	$this->build_content($data);
	$this->render_page();
    }

    function Data() {

    	echo 'inicio';
		$command = "php /var/www/html/apps2/application/controllers/Reports/sendEmails."& > /dev/null";
		$v = popen($command, 'w');
		pclose($v);
		echo 'fin';
    }
}
?>