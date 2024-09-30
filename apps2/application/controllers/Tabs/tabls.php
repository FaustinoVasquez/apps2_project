<?php
if (!defined('BASEPATH'))
	exit('No direct script access allowed');

class Tabs extends BP_Controller {

 private $admin;

	 public function __construct() {
        parent::__construct();

        $isLoggedIn = $this->session->userdata('isLoggedIn');

        if (!isset($isLoggedIn) || $isLoggedIn != true) {
            redirect(base_url(), 'refresh');
        }
        if ($this->MUsers->isValidUser($this->session->userdata('userId'), 889090) != 1) {
      		$this->admin=0;  //
    	}else{
      		$this->admin=1;
    	}    
       
    }

	public function index() {

		$data = array(
				'row_id' => "t_" . $this->input->get('sku'),
				'sku' => $this->input->get('sku')
		);

		$this->load->view("pages/tabs", $data);
	}


	public function createTabs($sku){


		$tabs= '	<section id="tabs">
					<div id="'.$sku.'">
						<ul>
							<li><a href="#'.$sku.'-1">Sku Data</a></li>
							<li><a href="#'.$sku.'-2">Custom fields</a></li>
							<li><a href="#'.$sku.'-3">Images</a></li>
							<li><a href="#'.$sku.'-4">Attachments</a></li>
						</ul>
						<div id="'.$sku.'-1"></div>
						<div id="'.$sku.'-2"></div>
						<div id="'.$sku.'-3">
				        	<iframe src ="http://photos.discount-merchant.com/photos/sku/'.$sku.'/index.php" width="100%" height=730px frameborder="0"></iframe>
				        </div>
				        <div id="'.$sku.'-4">
				        	<iframe src ="http://photos.discount-merchant.com/photos/sku/'.$sku.'/Attachments/" width="100%" height=730px frameborder="0"></iframe>
				        </div>
					</div>
				</section>';

	return $tabs;
	
	}

}