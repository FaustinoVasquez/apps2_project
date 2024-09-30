<?php

class Salesbyyear extends BP_Controller {

    public function __construct() {
        parent::__construct();

        $is_logged_in = $this->session->userdata('is_logged_in');
        if (!isset($is_logged_in) || $is_logged_in != true) {
            redirect(base_url(), 'refresh');
        }
    }

    public function index() {

        $this->title = "MI Technologiesinc - Sales By Year";

        $this->description = "Sales By Year";

        $this->css = array('form.css', 'jqueryui/redmond/jquery-ui-1.8.21.custom.css', 'jqgrid/ui.jqgrid.css');

        $this->javascript = array('jqueryui/ui/jquery-ui-1.8.21.custom.js', 'jqgrid/i18n/grid.locale-en.js', 'highcharts/highcharts.js', 'highcharts/exporting.js', 'popup.js');

        $this->hasNav = False;

        $this->load->model('morders', '', TRUE);

        $data = array(
            'from' => '/Grids/salesbyyear/',
            'Fyear' => date('Y'),
            'baseyear' => 2007,
            'administrator' => $this->MUsers->isadminuser($this->session->userdata('userid')),
            'cartid' => $this->MUsers->getCartName($this->session->userdata('userid')),
            'customerid' => $this->MUsers->getCustomerId($this->session->userdata('userid')),
        );


        $SQL = "SELECT *  
              FROM OrderManager.dbo.fn_Sales_by_Year(2007,{$data['Fyear']},{$data['customerid']},{$data['cartid']})
              Order by year1";

        $data['result'] = $this->MCommon->getSomeRecords($SQL);


      $this->build_content($data);
        $this->render_page();
    }

}

?>
