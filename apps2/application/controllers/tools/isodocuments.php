<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Isodocuments extends BP_Controller {

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

	$this->title = "MI Technologiesinc - ISO Documents";

	$this->description = "ISO Documents";

	$this->css = array('fluid.css',  'jqueryui/redmond/jquery-ui-1.8.21.custom.css','Dynatree/ui.dynatree.css','menu.css',);

	// Define custom javascript
	$this->javascript = array('jqueryui/ui/jquery-ui-1.8.21.custom.js','Dynatree/jquery.dynatree.js','popup.js');


	// If the page has menu
	$this->load->library('Layout');
	$menu = new Layout;

	// If not have menu


	$data = array(
	    'menu' => $menu->show_menu($this->MUsers->getUserFullName($this->session->userdata('userid'))),
	    'from' => '/tools/isodocuments/',
	);

	$data['baseurl'] =  base_url();

	$this->load->helper('directory');
	$map = directory_map('/home/fvasquez/isoDoc/');

	
	// $my_map = array();

	// foreach($map as $k => $v )
	// {

	//     if(gettype($v) == 'array')
	//     {
	//         $my_map[$k] = $v;
	//     }else{
	//     	$my_map[$k] = $v;
	//     }
	// }

	// var_dump($my_map);
//	$data['mymap'] = $my_map;

	$this->build_content($data);
	$this->render_page();
    }


 //    function print_dir($in,$path)
	// {
 //    $buff = '';
	//     foreach ($in as $k => $v)
	//     {
	//         if (!is_array($v))
	//             $buff .= "[file]: ".$path.$v."\n";
	//         else
	//             $buff .= "[directory]: ".$path.$k."\n".print_dir($v,$path.$k.DIRECTORY_SEPARATOR);
	//     }
 //    return $buff;
	// }
}
?>