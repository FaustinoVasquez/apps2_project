<?php

class Toptenitemsordersbycsr extends BP_Controller {

    public function __construct() {
        parent::__construct();

        $is_logged_in = $this->session->userdata('is_logged_in');
        if (!isset($is_logged_in) || $is_logged_in != true) {
            redirect(base_url(), 'refresh');
        }
    }

    public function index() {

        $this->title = "MI Technologiesinc - TopTen Item Orders By CSR";

        $this->description = "TopTen Item Orders By CSR";

        $this->css = array('form.css', 'jqueryui/redmond/jquery-ui-1.8.21.custom.css', 'jqgrid/ui.jqgrid.css');

        $this->javascript = array('jqueryui/ui/jquery-ui-1.8.21.custom.js', 'jqgrid/i18n/grid.locale-en.js', 'highcharts/highcharts.js', 'highcharts/exporting.js', 'popup.js');

        $this->hasNav = False;

        $this->load->model('morders', '', TRUE);


        $data = array(
            'dateFrom' => $this->MCommon->InitMonth(date("m/d/Y")),
            'dateTo' => date("m/d/Y"),
            'graph' => 0,
            'from' => '/Grids/toptenitemsordersbycsr/',
        );


        //Cargamos la libreria comumn
        $this->load->library('common');

        //Reemplazamos los campos del formularion por los que estan en el arreglo $_POST.
        $data = $this->common->fillPost('ALL', $data);


         $SQL = "SELECT TOP(10) upper(a.EnteredBy) as EnteredBy,[OrderManager].[dbo].fn_Get_UserName(a.EnteredBy) as Csr
               , count(a.OrderNumber) as OrderCount,0 as itemcount
          FROM [OrderManager].[dbo].[Orders] a
     WHERE (a.OrderDate between '{$data['dateFrom']}' and '{$data['dateTo']}')
	     and (enteredBy <> 'AG')
             and (enteredBy <> 'JS')
             and (enteredBy <> 'AF')
             and (enteredBy <> 'SB')
             and (enteredBy <> 'SI')
             and (enteredBy <> 'JR')
             and (enteredBy <> 'IM')
             and (enteredBy <> 'AIT')
             and (enteredBy <> 'DS')
             and (enteredBy <> 'FAH')
             and (enteredBy <> 'KC')
       group by EnteredBy order by orderCount desc";
     
    

        $data['result'] = $this->MCommon->getSomeRecords($SQL);

        $items = array();
        foreach ($data['result'] as $value) {

            $SQL = "select sum( a.QuantityShipped) as items
                    from   [OrderManager].[dbo].[Order Details] a
                    where  a.ordernumber in
                    (
                        select b.OrderNumber
                        from   [OrderManager].[dbo].[Orders] b
                        where  (OrderDate between '{$data['dateFrom']}' and '{$data['dateTo']}')
                        and (enteredBy = '{$value['EnteredBy']}')
                     )
                  and not (a.Product like '%Shipping%')
                  and  not (a.Product like '%Product%')
                  and  not (a.Product like '%Discount%')
                  and  not (a.Product like '%Surcharge%')
                  and  not (a.Product like '%Sales Tax 1%')
                  and  not (a.Product like '%Sales Tax 2%')
                  and  not (a.Product like '%Sales Tax 3%')
                  and  not (a.Product like '%Sales Tax 4%')
                  and  not (a.Product like '%Sales Tax 5%')   
                        ";
                       
            $items[] = $this->MCommon->getOneRecord($SQL);
        }



        $data['items'] = $items;
        $this->build_content($data);
        $this->render_page();
    }

}
