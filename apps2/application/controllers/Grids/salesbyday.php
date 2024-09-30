<?php

class Salesbyday extends BP_Controller {

    public function __construct() {
        parent::__construct();

        $is_logged_in = $this->session->userdata('is_logged_in');
        if (!isset($is_logged_in) || $is_logged_in != true) {
            redirect(base_url(), 'refresh');
        }
    }

    public function index() {

        $this->title = "MI Technologiesinc - Sales By Day";

        $this->description = "Sales By Day";

        $this->css = array('form.css', 'jqueryui/redmond/jquery-ui-1.8.21.custom.css', 'jqgrid/ui.jqgrid.css');

        $this->javascript = array('jqueryui/ui/jquery-ui-1.8.21.custom.js', 'jqgrid/i18n/grid.locale-en.js', 'highcharts/highcharts.js', 'highcharts/exporting.js', 'popup.js');

        $this->hasNav = False;

        $this->load->model('morders', '', TRUE);

        $data = array(
            'from' => '/Grids/salesbyday/',
            'Fyear' => date('Y'),
            'dateNow' => date("m/d/Y"),
            'baseyear' => 2007,
            'administrator' => $this->MUsers->isadminuser($this->session->userdata('userid')),
            'cartid' => $this->MUsers->getCartName($this->session->userdata('userid')),
            'customerid' => $this->MUsers->getCustomerId($this->session->userdata('userid')),
        );

        //Cargamos la libreria comumn
        $this->load->library('common');

        //Reemplazamos los campos del formularion por los que estan en el arreglo $_POST.
        $data = $this->common->fillPost('ALL', $data);
       
	$today = date('m/d/Y', strtotime($data['dateNow']) );
		
       for ($i = 29; $i >= 0; $i--) {
	   
	   $computertime =  strtotime("-{$i} day", strtotime($today));
           
	   $date = date('m/d/Y', $computertime);

            $SQL1 = "SELECT sum(a.ProductTotal) as OrderCount 
                     FROM [OrderManager].[dbo].[Orders] a 
                     WHERE (a.OrderDate = '{$date}' ) 
                     and (SourceOrderNumber is null) 
                     and (OrderManager.dbo.fn_Get_UserName(a.EnteredBy) like '%*csr%')";
		    
            $result = $this->MCommon->getOneRecord($SQL1);
            if ($result['OrderCount'] == "") {
                $result['OrderCount'] = 0;
            };
            $resulta[] = $result['OrderCount'];
	    $dates[] = $date;
        }

	
        $datelast = $dates[0];

        $dateb = strtotime($datelast);
        $data['day'] = date("d", $dateb);
        $data['month'] = date("m", $dateb);
        $data['year'] = date("Y", $dateb);

        //Cargamos el modelo con acceso a OM
	
        $this->load->model('MOrders', '', TRUE);

        if ($this->MUsers->isadminuser($this->session->userdata('userid'))) {
            $data['cartOptions'] = $this->MOrders->getCartNames();
        } else {
            //Obtenemos solo el nombre de un carro para usuario y enviamos el Id del usuario activo
            $data['cartOptions'] = $this->MOrders->getCartName($data['cartid']);
            $data['customerId'] = $this->MUsers->getCustomerId($this->session->userdata('userid'));
        }

        $data['result'] = $resulta;

        $this->build_content($data);
        $this->render_page();
    }

}

?>