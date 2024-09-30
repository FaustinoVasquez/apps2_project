<?php

class Salesbystore extends BP_Controller {

    public function __construct() {
        parent::__construct();

        $is_logged_in = $this->session->userdata('is_logged_in');
        if (!isset($is_logged_in) || $is_logged_in != true) {
            redirect(base_url(), 'refresh');
        }
    }

    public function index() {

        $this->title = "MI Technologiesinc - Sales by Store";

        $this->description = "Sales by Store";

        $this->css = array('form.css', 'jqueryui/redmond/jquery-ui-1.8.21.custom.css', 'jqgrid/ui.jqgrid.css');

        $this->javascript = array('jqueryui/ui/jquery-ui-1.8.21.custom.js', 'jqgrid/i18n/grid.locale-en.js', 'highcharts/highcharts.js', 'highcharts/exporting.js', 'popup.js');

        $this->hasNav = False;

        $this->load->model('morders', '', TRUE);


        $data = array(
            'dateFrom' => $this->MCommon->InitMonth(date("m/d/Y")),
            'dateTo' => date("m/d/Y"),
            'graph' => 0,
            'from' => '/Grids/salesbystore/',
        );


        //Cargamos la libreria comumn
        $this->load->library('common');

        //Reemplazamos los campos del formularion por los que estan en el arreglo $_POST.
        $data = $this->common->fillPost('ALL', $data);


        $SQL = "SELECT cartname,
                 case
                    when cartname = 'Discount-Merchant.com' then 'DM'
                    when cartname = 'DiscountTVLamps.com' then 'DTVL'
                    when cartname = 'eBay Sites' then 'Ebay'
                    when cartname = 'MercadoDePartes' then 'MDP'
                    when cartname = 'MercadoDePartes - MercadoLibre' then 'MDP-ML'
                    when cartname = 'MI Technologies, Inc.' then 'MIT'
                    when cartname = 'MI Technologies S.A. de C.V.' then 'MITInt'
                    when cartname = 'DiscountTVLamps - Buy' then 'DTVL-Buy'
                    when cartname = 'Nevada Parts Co.' then 'NvP'
                    when cartname = 'NewEggMall' then 'NEM'
                    when cartname = 'PartSearch' then 'PartSh'
                    when cartname = 'SoCalOEM - Amazon' then 'SoCalOEM'
                    when cartname = 'Reseller Connect' then 'ResCon'
                    when cartname = 'TopSeller - Amazon' then 'TopS'
                    when cartname = 'Discount-Merchant - Amazon' then 'DM-Amazon'
                    when cartname = 'Media Monster' then 'MM'
                    when cartname = 'NewEgg.com' then 'NewEgg'
                    when cartname = 'Projector Parts' then 'Proj-Parts'
                    when cartname = 'Projector World' then 'Proj-World'
                    else cartname
                end as shortname,
               COUNT(ordernumber) as Orders,
               SUM(qtyreal) as Items,
               SUM(qtyreal * ppu) as RealBill
               FROM OrderManager.dbo.fn_DashBoard('{$data['dateFrom']}','{$data['dateTo']}')
               group by cartname";

        $data['result'] = $this->MCommon->getSomeRecords($SQL);

        $this->build_content($data);
        $this->render_page();
    }

}

?>