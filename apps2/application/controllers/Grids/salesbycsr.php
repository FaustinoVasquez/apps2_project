<?php

class Salesbycsr extends BP_Controller {

    public function __construct() {
        parent::__construct();

        $is_logged_in = $this->session->userdata('is_logged_in');
        if (!isset($is_logged_in) || $is_logged_in != true) {
            redirect(base_url(), 'refresh');
        }
    }

    public function index() {

        $this->title = "MI Technologiesinc - TopTen";

        $this->description = "TopTen";

        $this->css = array('form.css', 'jqueryui/redmond/jquery-ui-1.8.21.custom.css', 'jqgrid/ui.jqgrid.css');

        $this->javascript = array('jqueryui/ui/jquery-ui-1.8.21.custom.js', 'jqgrid/i18n/grid.locale-en.js', 'highcharts/highcharts.js', 'highcharts/exporting.js', 'popup.js');

        $this->hasNav = False;

        $this->load->model('morders', '', TRUE);


        $data = array(
            'dateFrom' => $this->MCommon->InitMonth(date("m/d/Y")),
            'dateTo' => date("m/d/Y"),
            'graph' => 0,
            'from' => '/Grids/salesbycsr/',
        );

        //Cargamos la libreria comumn
        $this->load->library('common');

        //Reemplazamos los campos del formularion por los que estan en el arreglo $_POST.
        $data = $this->common->fillPost('ALL', $data);
	
	

        $SQL = "SELECT upper(a.EnteredBy) as EnteredBy, sum(a.ProductTotal) as OrderCount
                FROM [OrderManager].[dbo].[Orders] a
                WHERE (a.OrderDate between '{$data['dateFrom']}' and '{$data['dateTo']}')
               and ((enteredBy = 'JS') or (enteredBy = 'SI') or (enteredBy ='JR'))
                group by EnteredBy order by OrderCount desc";

		
        $data['result'] = $this->MCommon->getSomeRecords($SQL);

        $this->build_content($data);
        $this->render_page();
    }

}