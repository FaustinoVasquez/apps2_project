<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Welcome extends BP_Controller {

    function index() {
       
        $this->verify();
    }

    function verify() {
        if ($this->input->post('username')) {
            $u = $this->input->post('username');
            $pw = $this->input->post('password');
            $query = $this->MUsers->verifyUser($u, $pw);
            
           $userid = $this->session->userdata('userid');


            if ($userid > 0) {
                if (($this->MUsers->isValidUser($userid, 880000) == 1) or ($this->MUsers->isValidUser($userid, 800001) == 1)){
                   
                 redirect('Catalog/prodcat', 'refresh');
                 
                   
                } else {
                     redirect('welcome/logout', 'refresh');
                }
            }
        }

         $this->title = "Welcome MI Technologies Inc.!";
         $this->description = "Login Page";
         $this->css = array("login.css");
         $this->javascript = array("login.js");
         $this->hasNav=false;
         
      $data = array(
            'from' => base_url(),
        );
        
        $this->build_content($data);
        $this->render_page();

    }



    function logout() {
        $this->session->sess_destroy();
        redirect('welcome/index');
    }

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */