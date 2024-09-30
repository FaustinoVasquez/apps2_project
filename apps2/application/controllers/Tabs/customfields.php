<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Customfields extends BP_Controller {

    public function __construct() {
        parent::__construct();

        $is_logged_in = $this->session->userdata('is_logged_in');

        if (!isset($is_logged_in) || $is_logged_in != true) {
            redirect(base_url(), 'refresh');
        }

    }

    public function index($sku =null) {

        $this->css = array( 'table.css',);
        $this->load->library('Layout');
        $this->hasNav = False;
        $this->hasFooter = false;
  

        $data = array(
              'customfields' =>  $this->MCatalog->showCustomFields($sku),
              'skuData' => $this->MCatalog->showSku($sku)

        );
	
        //Generar el contenido....
        $this->build_content($data);
        $this->render_page();
    } 
}