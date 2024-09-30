<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Inventorybydate extends BP_Controller {

    public function __construct() {
	parent::__construct();

	$is_logged_in = $this->session->userdata('is_logged_in');

		if (!isset($is_logged_in) || $is_logged_in != true) {
		    redirect(base_url(), 'refresh');
		}

		// if ($this->MUsers->isValidUser($this->session->userdata('userid'), 883600) != 1) {// Access Code
		//     redirect('Catalog/prodcat', 'refresh');
		// }
    }


    

    function index() {

	$this->title = "MI Technologiesinc - Inventory By Date";

	$this->description = "Inventory By Date";

	$this->css = array('menu.css', 'fluid.css', 'jqueryui/redmond/jquery-ui-1.8.21.custom.css', 'jqgrid/ui.jqgrid.css', 'form.css');

        // Define custom javascript
    $this->javascript = array('jqueryui/ui/jquery-ui-1.8.21.custom.js', 'jqgrid/i18n/grid.locale-en.js', 'jqgrid/jquery.jqGrid.min.js', 'jqgrid/jquery.jqGrid.fluid.js','jquery/jquery-ui-timepicker-addon.js','popup.js');


	// If the page has menu
	$this->load->library('Layout');
	$menu = new Layout;


	$data = array(
	    'menu' => $menu->show_menu($this->MUsers->getUserFullName($this->session->userdata('userid'))),
	    'validate' => '/Reports/inventorybydate/validate',
	    'datefrom' => date("m/d/Y"),
	);



	$data['colNames'] = "['Id','ProductCatalogId','Name','CategoryId','CategoryName','US_QOH','MX_QOH','Defective_QOH','Transit_QOH','tQOH']";

	$data['colModel'] = "[	{name:'id',index:'id', width:60, align:'center',hidden:true},
			                {name:'ProductCatalogId',index:'ProductCatalogId', width:80, align:'center'},
			                {name:'name',index:'name', width:250, align:'left'},
							{name:'categoryId',index:'categoryId', width:60, align:'center', hidden:true},
			                {name:'CategoryName',index:'CategoryName', width:150, align:'left'},
			                {name:'US_Stock',index:'US_Stock', width:60, align:'center'},
			                {name:'MX_Stock',index:'MX_Stock', width:60, align:'center'},		
							{name:'Defective_Stock',index:'Defective_Stock', width:60, align:'center'},
			                {name:'Transit_Stock',index:'Transit_Stock', width:60, align:'center'},
			                {name:'Total_Stock',index:'Total_Stock', width:60, align:'center'},
			            ]";


	$this->build_content($data);
	$this->render_page();
    }

    function validate(){



    	$date = $this->input->post('datefrom');
		$email = $this->input->post('emailList');
		$now = date("m/d/Y H:i");
		
		$datetime = new DateTime($date);
		$datetimenow = new DateTime($now);
		if ($datetime >$datetimenow){
			$datetime = $datetimenow;
		}

		$sql = "EXECUTE Inventory.dbo.sp_Bins_Create_Inventory_At_Date '{$datetime}:00'";

		$this->db->query($sql);

		return 'finished';

    }

    function __destruct() {
    	$this->db->close();
	}


	function getData(){

		$page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1; // get the requested page
        $limit = isset($_REQUEST['rows']) ? $_REQUEST['rows'] : 10; // get how many rows we want to have into the gri
        $sidx = isset($_REQUEST['sidx']) ? $_REQUEST['sidx'] : 'id'; // get index row - i.e. user click to sort
        $sord = isset($_REQUEST['sord']) ? $_REQUEST['sord'] : 'desc';


		$date = $this->input->get('datefrom');
	

		$SQL = "SELECT ID from inventory.dbo.ProductCatalog";


        $result = $this->db->query($SQL);

        $count = $result->num_rows();


        if ($count > 0) {
            $total_pages = ceil($count / $limit);
        } else {
            $total_pages = 0;
        }
        if ($page > $total_pages)
            $page = $total_pages;

        $start = $limit * $page - $limit; // do not put $limit*($page - 1)
        $start = ($start < 0) ? 0 : $start;
        $finish = $start + $limit;

        $SQL = "EXECUTE Inventory.dbo.sp_Bins_Create_Inventory_At_Date '{$date}',$start,$finish";
        print_r("TEST");
        console.log($SQL);
	print_r($SQL);

		$query = $this->db->query($SQL);

		$responce = new stdClass();
        $responce->page = $page;
        $responce->total = $total_pages;
        $responce->records = $count;
        $i = 0;


        foreach ($query->result_array() as $row) {
            $responce->rows[$i]['id'] = $row['id'];
            $responce->rows[$i]['cell'] = array($row['id'],
		$row['ProductCatalogId'],
                $row['name']= utf8_encode($row['name']),
                $row['categoryId'],
                $row['CategoryName']= utf8_encode($row['CategoryName']),
                $row['US_Stock'],
                $row['MX_Stock'],
		        $row['Defective_Stock'],
                $row['Transit_Stock'],
                $row['Total_Stock'],
            );
            $i++;
        }
        echo json_encode($responce);

	}


	function csvExport() {

        header('Content-type: application/vnd.ms-excel');
        header("Content-Disposition: attachment; filename=" .'Inventory_' . date("D-M-j") . ".csv");
        header("Pragma: no-cache");

        $buffer = $_POST['csvBuffer'];

        try {
            echo $buffer;
        } catch (Exception $e) {
            
        }
    }
}
