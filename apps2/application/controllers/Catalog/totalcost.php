<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Totalcost extends BP_Controller {

    public function __construct() {
        parent::__construct();

        $is_logged_in = $this->session->userdata('is_logged_in');

        if (!isset($is_logged_in) || $is_logged_in != true) {
            redirect(base_url(), 'refresh');
        }
        
         if ($this->MUsers->isValidUser($this->session->userdata('userid'), 881310) != 1) {
            redirect('Catalog/prodcat', 'refresh');
        }
 
    }

    function index() {
        
        $this->title = "MI Technologiesinc - Cost by ProductLine";

        $this->description = "Cost by ProductLine";

        // Define custom CSS
        $this->css = array('menu.css', 'fluid.css', 'jqueryui/redmond/jquery-ui-1.8.21.custom.css', 'jqgrid/ui.jqgrid.css', 'form.css');

        // Define custom javascript
        $this->javascript = array('jqueryui/ui/jquery-ui-1.8.21.custom.js', 'jqgrid/i18n/grid.locale-en.js', 'jqgrid/jquery.jqGrid.min.js', 'jqgrid/jquery.jqGrid.fluid.js', 'popup.js');
        //Cargamos la libreria donde esta el menu
       

        $this->hasNav = False;

        
         $data = array(
             'dateto'=>date('m/d/Y'),
             'caption'=> 'Cost By ProductLine',
             'from'=>'/Catalog/totalcost/',
         );
               
        
        $data['headers'] = "['ProductlineID','ProductLineName','ProductLineCost']";
       
        $data['body'] = "[
                {name:'ProductlineID',index:'ProductlineID', width:100, align:'left',sorttype:'int', hidden:true },
                {name:'ProductLineName',index:'ProductLineName', width:350, align:'left'},
                {name:'ProductLineCost',index:'ProductLineCost', width:130, align:'left',formatter:'currency', formatoptions:{decimalSeparator:'.', thousandsSeparator: ',', decimalPlaces: 2, prefix: '$ '} },
  	]";
        
        //Cargamos la libreria comumn
        $this->load->library('common');

        //Reemplazamos los campos del formularion por los que estan en el arreglo $_POST.
        $data = $this->common->fillPost('ALL', $data);
        
        
        
        $this->build_content($data);
        $this->render_page();
    }
    
    function gridDataTotalcost(){
        $examp = isset($_GET['q']) ? $_GET['q'] : 1;  //query number
        $page = isset($_GET['page']) ? $_GET['page'] : 1; // get the requested page
        $limit = isset($_GET['rows']) ? $_GET['rows'] : 100; // get how many rows we want to have into the gri
        $sidx = isset($_GET['sidx']) ? $_GET['sidx'] : 'productlineid'; // get index row - i.e. user click to sort
        $sord = isset($_GET['sord']) ? $_GET['sord'] : '';

        $dateto = isset($_GET['dateto']) ? $_GET['dateto'] : date("m/d/Y");
        

  

        if ($dateto != date("m/d/Y")){
            $datefixed= $this->MCommon->fixDateTo($dateto);

             $result = $this->MCatalog->getCostbyProducLineAtDate($datefixed);

        }else{
            $result = $this->MCatalog->getCostbyProducLine();
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
        $TotInv=0;

        foreach ($result as $row) {
            $responce->rows[$i]['id'] = $row['ProductlineID'];
            $responce->rows[$i]['cell'] = array($row['ProductlineID'],
                $row['ProductLineName'],
                $row['ProductLineCost'],
            );
             $TotInv += $row['ProductLineCost'];
            $i++;
        } 
        $responce->userdata['ProductLineName'] = 'TOTALS:';
        $responce->userdata['ProductLineCost'] = $TotInv;
        
        echo json_encode($responce);      
    }
    
    
}
?>