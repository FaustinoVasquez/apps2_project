<?php

class Asinbyday extends BP_Controller {

    public function __construct() {
        parent::__construct();

        $is_logged_in = $this->session->userdata('is_logged_in');
        if (!isset($is_logged_in) || $is_logged_in != true) {
            redirect(base_url(), 'refresh');
        }
    }

    public function index() {

        $this->title = "MI Technologiesinc - Asin by Date";

        $this->description = "Asin by Date";

        $this->css = array('form.css', 'jqueryui/redmond/jquery-ui-1.8.21.custom.css', 'jqgrid/ui.jqgrid.css');

        $this->javascript = array('jqueryui/ui/jquery-ui-1.8.21.custom.js', 'jqgrid/i18n/grid.locale-en.js', 'highcharts/highcharts.js', 'highcharts/exporting.js', 'popup.js');

        $this->hasNav = False;


        $data = array(
            'from' => '/Grids/asinbyday/',
	    'dateFrom'=> date("m/d/Y",strtotime("-1 month")),
            'dateTo' => date("m/d/Y"),
	    
        );

        //Cargamos la libreria comumn
        $this->load->library('common');

        //Reemplazamos los campos del formularion por los que estan en el arreglo $_POST.
        $data = $this->common->fillPost('ALL', $data);

	
	//Obtnemos el rango de fechas que hay entre datefrom y dateto
	$daterange = $this->MCommon->dateRange($data['dateFrom'],$data['dateTo']);


	
	foreach ($daterange as $value) {
	    $SQL1 = "SELECT  count(ASIN) as asincount FROM [Inventory].[dbo].Amazon where (stamp > '{$value} 00:00:00') and (stamp < '{$value} 23:59:59')";
	    $result = $this->MCommon->getOneRecord($SQL1);
	    if ($result['asincount'] == "") {
                $result['asincount'] = 0;
            };
	    $resulta[$value] = $result['asincount'];
	}
	
	
	$dateb = strtotime( $data['dateFrom']);
        $data['day'] = date("d", $dateb);
        $data['month'] = date("m", $dateb);
        $data['year'] = date("Y", $dateb);
	
        $data['result'] = $resulta;

        $this->build_content($data);
        $this->render_page();
    }

}

?>