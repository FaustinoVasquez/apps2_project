<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Shipinf extends BP_Controller {

    public function __construct() {
        parent::__construct();

        $is_logged_in = $this->session->userdata('is_logged_in');

        if (!isset($is_logged_in) || $is_logged_in != true) {
            redirect(base_url(), 'refresh');
        }
    }

    function index($sku) {

        $this->title = "MI Technologiesinc - History";
        $this->description = " History";
        // Define custom CSS
        $this->css = array('menu.css', 'form.css', 'fluid.css', 'table.css', 'jqueryui/redmond/jquery-ui-1.8.21.custom.css');
        // Define custom javascript
        $this->javascript = array('jqueryui/ui/jquery-ui-1.8.21.custom.js');
        $this->hasNav = False;

        $SQL= "select [Id]
                     ,[CustomsName]
                     ,[CustomsValue]
                     ,[BoxSKU]
                     ,[BoxLength]
                     ,[BoxWidth]
                     ,[BoxHeight]
                     ,[BoxWeightOz]
                     ,[FlatRateOptions]
                from [Inventory].[dbo].[ProductCatalog]
                where id = ".$sku;


        $data =['result' => $this->MCommon->getOneRecord($SQL)];
        $this->build_content($data);
        $this->render_page();
    }


    function saveData() {
        $Id =$this->input->post('Id');
        $data = ['CustomsName'=>$this->input->post('CustomsName'),
                 'CustomsValue'=>floatval($this->input->post('CustomsValue')),
                 'BoxSKU'=>floatval($this->input->post('BoxSKU')),
                 'BoxLength'=>floatval($this->input->post('BoxLength')),
                 'BoxWidth'=>floatval($this->input->post('BoxWidth')),
                 'BoxHeight'=>floatval($this->input->post('BoxHeight')),
                 'BoxWeightOz'=>floatval($this->input->post('BoxWeightOz')),
                 'FlatRateOptions'=>$this->input->post('FlatRateOptions')
        ];

        $query = $this->db->update('[Inventory].[dbo].[ProductCatalog]', $data, "Id = $Id");

        if ($query){
            echo json_encode(array('status' => 'success','message'=> 'This Record has been updated.'));
        }else{
            return 'Something wrong happened, please try again.';
        }

    }


}

?>
