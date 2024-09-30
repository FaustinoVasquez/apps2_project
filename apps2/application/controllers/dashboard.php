<?php

class Dashboard extends BP_Controller {

    public function __construct() {
        parent::__construct();

        $is_logged_in = $this->session->userdata('is_logged_in');
        if (!isset($is_logged_in) || $is_logged_in != true) {
            redirect(base_url(), 'refresh');
        }
    }

    public function index($category='') {
        // Define Meta
        $this->title = "Dashboard";
        $this->description = "Mi Technologies Inc -- Dashboard";
        // Define custom CSS
        $this->css = array('form.css','menu.css');

        $this->load->library('Layout');
        $menu = new Layout;

        $data = array(
                     'menu' => $menu->show_menu($this->MUsers->getUserFullName($this->session->userdata('userid'))),
                     'title' => 'MI Technologiesinc - Dashboard',
                     'from' => 'dashboard/');
        
        
        
        switch($category){
            
            case 'sales':
                 $data['salesbymonth'] = $this->MUsers->isValidUser($this->session->userdata('userid'), 990002);
                 $data['salesprojbymonth'] = $this->MUsers->isValidUser($this->session->userdata('userid'), 990002);
                 $data['salesbyyear'] = $this->MUsers->isValidUser($this->session->userdata('userid'), 990008);
                 $data['salesbystore'] = $this->MUsers->isValidUser($this->session->userdata('userid'), 990001);
                 $data['toptensku'] = $this->MUsers->isValidUser($this->session->userdata('userid'), 990003);
                 $data['category'] = 'sales';
                 break;
                 
            case 'dropship':
                 $data['dropshiporders'] = $this->MUsers->isValidUser($this->session->userdata('userid'), 990002);
                 $data['dropshipordersbytopten'] = $this->MUsers->isValidUser($this->session->userdata('userid'), 990002);
                 $data['category'] = 'dropship';
                break;
            
            case 'csr':
                 $data['salesbyday'] = $this->MUsers->isValidUser($this->session->userdata('userid'), 990006); 
                 $data['salesbycsrmx'] = $this->MUsers->isValidUser($this->session->userdata('userid'), 990006); 
                 $data['itemsordersbycsrmx'] = $this->MUsers->isValidUser($this->session->userdata('userid'), 990005); 
                 $data['itemsorderbystore'] = $this->MUsers->isValidUser($this->session->userdata('userid'), 990002); 
                 $data['salesbysalesperson'] = $this->MUsers->isValidUser($this->session->userdata('userid'), 990004); 
                 $data['category'] = 'csr';
                break;
        }
        
      //  print_r($data);
        
   /*     $data = array(
            'menu' => $menu->show_menu($this->MUsers->getUserFullName($this->session->userdata('userid'))),
            'title' => 'MI Technologiesinc - Dashboard',
            'from' => 'dashboard/',
            'UserName' => $this->MUsers->getUserFullName($this->session->userdata('userid')),
         //   'salesbymonth' => $this->MUsers->isValidUser($this->session->userdata('userid'), 990002),
         //   'salesbystore' => $this->MUsers->isValidUser($this->session->userdata('userid'), 990001),
        //    'itemsorderbystore' => $this->MUsers->isValidUser($this->session->userdata('userid'), 990002),
        //    'toptensku' => $this->MUsers->isValidUser($this->session->userdata('userid'), 990003),
            'salesbysalesperson' => $this->MUsers->isValidUser($this->session->userdata('userid'), 990004),
       //     'itemsordersbycsrmx' => $this->MUsers->isValidUser($this->session->userdata('userid'), 990005),
      //      'salesbycsrmx' => $this->MUsers->isValidUser($this->session->userdata('userid'), 990006),
      //      'salesbyyear' => $this->MUsers->isValidUser($this->session->userdata('userid'), 990008),
            'administrator' => $this->MUsers->isadminuser($this->session->userdata('userid')),
      //      'dropshiporders' => $this->MUsers->isValidUser($this->session->userdata('userid'), 990002)
        );*/

        $this->build_content($data);
        $this->render_page();
    }

   
}

?>
