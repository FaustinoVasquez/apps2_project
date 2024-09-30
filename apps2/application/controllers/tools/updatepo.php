<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Updatepo extends BP_Controller {

    public function __construct() {
	parent::__construct();

	$is_logged_in = $this->session->userdata('is_logged_in');

		if (!isset($is_logged_in) || $is_logged_in != true) {
		    redirect(base_url(), 'refresh');
		}

		if ($this->MUsers->isValidUser($this->session->userdata('userid'), 884400) != 1) {// Access Code
		    redirect('Catalog/prodcat', 'refresh');
		}

		$this->load->helper(array('form', 'url'));
    }

    function index() {

	$this->title = "MI Technologiesinc - Update Purchase Orders";

	$this->description = "Update Purchase Orders";

	$this->css = array('form.css', 'fluid.css', 'table.css', 'jqueryui/redmond/jquery-ui-1.8.21.custom.css', 'jqgrid/ui.jqgrid.css', 'menu.css',);

	// Define custom javascript
	$this->javascript = array('jqueryui/ui/jquery-ui-1.8.21.custom.js', 'jqgrid/i18n/grid.locale-en.js', 'jqgrid/jquery.jqGrid.min.js', 'jqgrid/jquery.jqGrid.fluid.js', 'popup.js');


	// If the page has menu
	$this->load->library('Layout');
	$menu = new Layout;


	$data = array(
	    'menu' => $menu->show_menu($this->MUsers->getUserFullName($this->session->userdata('userid'))),
	    'from' => 'tools/updatepo/',
	);


	$this->build_content($data);
	$this->render_page();
    }

    function Data() {

    set_time_limit(0);

	$select = " EXEC msdb.dbo.sp_start_job @job_name='UpdatePurchaseOrders';";

	$this->db->query($select);

	return 'true';

    }



	function do_upload()
	{

		$return = true;

		$config['upload_path'] = '/var/www/html/mnt/uploads/';
		$config['allowed_types'] = 'csv';
		$config['max_size'] = 1024 * 8;
		$config['overwrite'] = TRUE;
		$config['file_name']  = 'ExportPO.csv';

		 $this->load->library('upload', $config);

		if ( ! $this->upload->do_upload())
		{
			$error = array('error' => $this->upload->display_errors());
			 echo $error['error'];
		}
		else
		{
			$data = array('upload_data' => $this->upload->data());

			echo 'File Uploaded: "'.$data ['upload_data']['client_name'].'" Renamed as: "'.$data ['upload_data']['orig_name'].'" and Running  the "UpdatePurchaseOrders" Store Procedure, this action will be take more than 15 minutes..' ;

			$this->Data();

		}

		return 'true';
	}



}
?>
