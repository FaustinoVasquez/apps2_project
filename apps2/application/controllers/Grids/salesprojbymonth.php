<?php

class Salesprojbymonth extends BP_Controller {

    public function __construct() {
        parent::__construct();

        $is_logged_in = $this->session->userdata('is_logged_in');
        if (!isset($is_logged_in) || $is_logged_in != true) {
            redirect(base_url(), 'refresh');
        }
    }

    public function index() {

        $this->title = "MI Technologiesinc - Projector Sales By Month";

        $this->description = "TopTen";

        $this->css = array('form.css', 'jqueryui/redmond/jquery-ui-1.8.21.custom.css', 'jqgrid/ui.jqgrid.css');

        $this->javascript = array('jqueryui/ui/jquery-ui-1.8.21.custom.js', 'jqgrid/i18n/grid.locale-en.js', 'highcharts/highcharts.js', 'highcharts/exporting.js', 'popup.js');

        $this->hasNav = False;

        $this->load->model('morders', '', TRUE);

        $data = array(
            'graph' => 0,
            'from' => '/Grids/salesprojbymonth/',
            'selectedFyear' => date('Y') - 1,
            'selectedTyear' => date('Y'),
            'administrator' => $this->MUsers->isadminuser($this->session->userdata('userid')),
            'cartid' => $this->MUsers->getCartName($this->session->userdata('userid')),
            'customerid' => $this->MUsers->getCustomerId($this->session->userdata('userid')),
            'selectedcart' => 0,
            'customerId' => '',
            'FyearOptions' => $this->MCommon->getyearstonow(),
            'TyearOptions' => $this->MCommon->getyearstonow(),
        );



        //Cargamos el modelo con acceso a OM
        $this->load->model('MOrders', '', TRUE);

        if ($this->MUsers->isadminuser($this->session->userdata('userid'))) {
            $data['cartOptions'] = $this->MOrders->getCartNames();
        } else {
            //Obtenemos solo el nombre de un carro para usuario y enviamos el Id del usuario activo
            $data['cartOptions'] = $this->MOrders->getCartName($data['cartid']);
            $data['customerId'] = $this->MUsers->getCustomerId($this->session->userdata('userid'));
        }

        //Cargamos la libreria comumn
        $this->load->library('common');

        //Reemplazamos los campos del formularion por los que estan en el arreglo $_POST.
        $data = $this->common->fillPost('ALL', $data);


        $SQL = "SELECT * FROM inventory.dbo.fn_Get_Monthly_sales({$data['selectedFyear']},{$data['selectedcart']})";


        $result = $this->MCommon->getSomeRecords($SQL);


        $SQL = "SELECT * FROM inventory.dbo.fn_Get_Monthly_sales({$data['selectedTyear']},{$data['selectedcart']})";

        $result1 = $this->MCommon->getSomeRecords($SQL);
         
       


        $data['result'] = $result;
        $data['result1'] = $result1;

        $this->build_content($data);
        $this->render_page();
    }

}

?>
