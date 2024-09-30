<?php

class Qtybycat extends BP_Controller {

    public function __construct() {
        parent::__construct();

        $is_logged_in = $this->session->userdata('is_logged_in');
        if (!isset($is_logged_in) || $is_logged_in != true) {
            redirect(base_url(), 'refresh');
        }
    }

    public function index() {

        $this->title = "MI Technologiesinc - Qty by Cat";

        $this->description = "Quantity bu Category";

        $this->css = array('form.css', 'jqueryui/redmond/jquery-ui-1.8.21.custom.css', 'jqgrid/ui.jqgrid.css');

        // Define custom javascript
        $this->javascript = array('jqueryui/ui/jquery-ui-1.8.21.custom.js', 'jqgrid/i18n/grid.locale-en.js', 'highcharts/highcharts.js', 'highcharts/exporting.js', 'popup.js');


        $this->hasNav = False;

        $this->load->model('morders', '', TRUE);

        $data = array(
            'dateFrom' => $this->MCommon->InitMonth(date("m/d/Y")),
            'dateTo' => date("m/d/Y"),
            'graph' => 0,
            'from' => '/Grids/qytbycat/',
        );


        //Cargamos la libreria comumn
        $this->load->library('common');

        //Reemplazamos los campos del formularion por los que estan en el arreglo $_POST.
        $data = $this->common->fillPost('ALL', $data);

        $SQL = "SELECT sum(inventory.dbo.fn_Get_Global_Stock(id)) as Total_Qty
                FROM ProductCatalog
                WHERE ProductLineId = 33 and manufacturer <> 'Compatible'";

        $data['result'] = $this->MCommon->getSomeRecords($SQL);
        $this->build_content($data);
        $this->render_page();
    }

}
