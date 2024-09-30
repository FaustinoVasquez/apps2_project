<?php

class Dropshipordersbytopten extends BP_Controller {

    public function __construct() {
        parent::__construct();

        $is_logged_in = $this->session->userdata('is_logged_in');
        if (!isset($is_logged_in) || $is_logged_in != true) {
            redirect(base_url(), 'refresh');
        }
    }

    public function index() {

        $this->title = "MI Technologiesinc - DropShipOrders TopTen";

        $this->description = "DropShipOrders TopTen";

        $this->css = array('form.css', 'jqueryui/redmond/jquery-ui-1.8.21.custom.css', 'jqgrid/ui.jqgrid.css');

        // Define custom javascript
        $this->javascript = array('jqueryui/ui/jquery-ui-1.8.21.custom.js', 'jqgrid/i18n/grid.locale-en.js', 'highcharts/highcharts.js', 'highcharts/exporting.js', 'popup.js');


        $this->hasNav = False;

        $this->load->model('morders', '', TRUE);

        $data = array(
            'dateFrom' => $this->MCommon->InitMonth(date("m/d/Y")),
            'dateTo' => date("m/d/Y"),
            'graph' => 0,
            'from' => '/Grids/dropshipordersbytopten/',
        );


        //Cargamos la libreria comumn
        $this->load->library('common');

        //Reemplazamos los campos del formularion por los que estan en el arreglo $_POST.
        $data = $this->common->fillPost('ALL', $data);

        $SQL = "Select top 10 
                od.sku
                ,sum(OD.[QuantityOrdered]) AS QtyOrdered
                    ,sum(Cast((OD.[QuantityOrdered] * OD.[PricePerUnit]) as money)) AS SoldTotal
                    ,avg((Cast((OD.[QuantityOrdered] * OD.[PricePerUnit]) as money)) / OD.[QuantityOrdered] ) as avgprice

                FROM OrderManager.dbo.Orders AS O
                LEFT OUTER JOIN OrderManager.dbo.[Order Details] AS OD ON (O.[OrderNumber] = OD.[OrderNumber])
                LEFT OUTER JOIN OrderManager.dbo.[ShoppingCarts] AS SC ON (O.[CartID] = SC.[ID])
                WHERE
                ( O.[OrderSource] = 'AS')
                AND  (O.OrderStatus != 'Order Canceled')
                AND  (O.OrderStatus != 'Item Returned')
                AND  (O.OrderStatus != 'Item Exchanged')
                AND  (O.OrderStatus != 'Credit Issued')
                AND  (O.Shipping    != 'Customer Pickup')
                AND  (OD.Adjustment = '0')
                AND (OD.WebSKU LIKE 'DS%')
                AND (O.OrderDate between '{$data['dateFrom']}' AND '{$data['dateTo']}')
                group by od.sku
                order by QtyOrdered desc";

        $data['result'] = $this->MCommon->getSomeRecords($SQL);
        $this->build_content($data);
        $this->render_page();
    }

}
