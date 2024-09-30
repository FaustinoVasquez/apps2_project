<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Invdetails extends BP_Controller {

    public function __construct() {
        parent::__construct();

        $is_logged_in = $this->session->userdata('is_logged_in');

        if (!isset($is_logged_in) || $is_logged_in != true) {
            redirect(base_url(), 'refresh');
        }
    }

    function index() {

        $this->title = "MI Technologiesinc - Inventory Details";

        $this->description = "Inventory Details";

        // Define custom CSS
        $this->css = array('menu.css', 'form.css', 'fluid.css', 'table.css', 'jqueryui/redmond/jquery-ui-1.8.21.custom.css', 'jqgrid/ui.jqgrid.css');

        // Define custom javascript
        $this->javascript = array('jqueryui/ui/jquery-ui-1.8.21.custom.js', 'jqgrid/i18n/grid.locale-en.js', 'jqgrid/jquery.jqGrid.min.js', 'jqgrid/jquery.jqGrid.fluid.js');

        $this->hasNav = False;
        
        $data['search'] = $_GET['q'];
        $data['caption'] = 'Inventory Details';
        $data['from'] = '/Tabs/invdetails/';
        $data['headers'] = "['Warehouse','Product Catalog Name','Current Stock']";

        $data['body'] = "[
                {name:'ID',index:'ID', width:150, align:'left',sorttype:'int' },
                {name:'ProductCatalogName',index:'ProductCatalogName', width:200, align:'left'},
                {name:'CurrentStock',index:'CurrentStock', width:150, align:'center', formatter: currencyFmatter},
  	]";

        $this->build_content($data);
        $this->render_page();
    }

    function gridDataInvDetails() {

        $search = $_GET['q'];
        $quantity = array();

        $page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1; // get the requested page
        $limit = isset($_REQUEST['rows']) ? $_REQUEST['rows'] : 10; // get how many rows we want to have into the gri
        $sidx = isset($_REQUEST['sidx']) ? $_REQUEST['sidx'] : 'ID'; // get index row - i.e. user click to sort
        $sord = isset($_REQUEST['sord']) ? $_REQUEST['sord'] : '';

       

      
        $warehouse = $this->MCatalog->Wharehouse();


        $i = 0;
        foreach ($warehouse as $key) {
            $quantity[$i] = $this->MCatalog->Quantity($search, $key['ID']);
            if ($quantity[$i]["CurrentStock"] != 0) {
                $result[] = $quantity[$i];
            };
        }

        $count = count($result);


        if ($count > 0) {
            $total_pages = ceil($count / $limit);
        } else {
            $total_pages = 0;
        }
        if ($page > $total_pages)
            $page = $total_pages;

        $start = $limit * $page - $limit; // do not put $limit*($page - 1)
        $start = ($start < 0) ? 0 : $start;
        
        $responce = new stdClass();
        $responce->page = $page;
        $responce->total = $total_pages;
        $responce->records = $count;

        $i = 0;
        foreach ($result as $row) {
            $responce->rows[$i]['id'] = $row['ID'];
            $responce->rows[$i]['cell'] = array($row['ID'],
                $row['ProductCatalogName'],
                $row['CurrentStock'],
            );
            $i++;
        }

        echo json_encode($responce);
    }

}

?>
