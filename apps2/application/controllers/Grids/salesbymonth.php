<?php

class Salesbymonth extends BP_Controller {

    public function __construct() {
        parent::__construct();

        $is_logged_in = $this->session->userdata('is_logged_in');
        if (!isset($is_logged_in) || $is_logged_in != true) {
            redirect(base_url(), 'refresh');
        }
    }

    public function index() {

        $this->title = "MI Technologiesinc - Sales By Month";

        $this->description = "Sales By Month";

        $this->css = array('form.css', 'jqueryui/redmond/jquery-ui-1.8.21.custom.css', 'jqgrid/ui.jqgrid.css');

        $this->javascript = array('jqueryui/ui/jquery-ui-1.8.21.custom.js', 'jqgrid/i18n/grid.locale-en.js', 'highcharts/highcharts.js', 'highcharts/exporting.js', 'popup.js');

        $this->hasNav = False;

        $this->load->model('morders', '', TRUE);

        $data = array(
            'dateFrom' => $this->MCommon->InitMonth(date("m/d/Y")),
            'dateTo' => date("m/d/Y"),
            'graph' => 0,
            'from' => '/Grids/salesbymonth/',
            'selectedFyear' => date('Y')-1,
            'selectedTyear' => date('Y'),
            'administrator' => $this->MUsers->isadminuser($this->session->userdata('userid')),
            'cartid' => $this->MUsers->getCartName($this->session->userdata('userid')),
            'customerid' => $this->MUsers->getCustomerId($this->session->userdata('userid')),
            'baseyear' => 2007,
            'selectedcart' => 0,
            'customerId' => '',
            'FyearOptions' => $this->MCommon->getyearstonow(),
            'TyearOptions' =>   $this->MCommon->getyearstonow(),
            
           
        );

        
    
        
        //Cargamos el modelo con acceso a OM
        $this->load->model('MOrders', '', TRUE);

        //Cargamos la libreria comumn
        $this->load->library('common');

        //Reemplazamos los campos del formularion por los que estan en el arreglo $_POST.
        $data = $this->common->fillPost('ALL', $data);

        
         // Verificamos si el usuario activo no es un partner
        if ($data['customerid'] !=0) {
             //Obtenemos solo el nombre de un carro para usuario y enviamos el Id del usuario activo
            $data['cartOptions'] = $this->MOrders->getCartName($data['cartid']);
            $data['customerId'] = $this->MUsers->getCustomerId($this->session->userdata('userid'));

        } else {
            $data['cartOptions'] = $this->MOrders->getCartNames();
        }
        


        $SQL = "SELECT *  
              FROM OrderManager.dbo.fn_Sales_by_Month({$data['selectedTyear']},{$data['customerid']},{$data['selectedcart']},0)";
                 
        $result = $this->MCommon->getSomeRecords($SQL);

        $i = 0;
        if ($data['selectedTyear'] == date("Y")) {

            foreach ($result as $value) {

                if (($result[$i]['total'] == '') and ($result[$i]['id'] <= date("n"))) {
                    $result[$i]['total'] = 0;
                }
                if ($result[$i]['total'] == '') {
                    $result[$i]['total'] = 0;
                }
                $i++;
            }
        } else {
            foreach ($result as $value) {
                if ($result[$i]['total'] == '') {
                    $result[$i]['total'] = 0;
                }
                $i++;
            }
        }


        $SQL = "SELECT *  
              FROM OrderManager.dbo.fn_Sales_by_Month({$data['selectedFyear']},{$data['customerid']},{$data['selectedcart']},0)";

        $result1 = $this->MCommon->getSomeRecords($SQL);


        $i = 0;
        if ($data['selectedFyear'] == date("Y")) {
            foreach ($result1 as $value) {
                if (($result1[$i]['total'] == '') and ($result[$i]['id'] <= date("n"))) {
                    $result1[$i]['total'] = 0;
                }
                if ($result1[$i]['total'] == '') {
                    $result1[$i]['total'] = 0;
                }
                $i++;
            }
        } else {
            foreach ($result1 as $value) {
                if ($result1[$i]['total'] == '') {
                    $result1[$i]['total'] = 0;
                }
                $i++;
            }
        }



        $data['result'] = $result;
        $data['result1'] = $result1;

        $this->build_content($data);
        $this->render_page();
    }

  

}