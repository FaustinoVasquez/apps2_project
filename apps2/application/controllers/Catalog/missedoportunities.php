<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Missedoportunities extends BP_Controller {

    public function __construct() {
        parent::__construct();

        $is_logged_in = $this->session->userdata('is_logged_in');

        if (!isset($is_logged_in) || $is_logged_in != true) {
            redirect(base_url(), 'refresh');
        }
        if ($this->MUsers->isValidUser($this->session->userdata('userid'), 882300) != 1) {
            redirect('Catalog/prodcat', 'refresh');
        }
    }

    function index() {

        // Define Meta
        $this->title = "MI Technologiesinc - Missed Oportunities";

        $this->description = "Missed Oportunities";

        $this->css = array('menu.css', 'form.css', 'fluid.css', 'table.css', 'jqueryui/redmond/jquery-ui-1.8.21.custom.css', 'jqgrid/ui.jqgrid.css');

        // Define custom javascript
        $this->javascript = array('jqueryui/ui/jquery-ui-1.8.21.custom.js', 'jqgrid/i18n/grid.locale-en.js', 'jqgrid/jquery.jqGrid.min.js', 'jqgrid/jquery.jqGrid.fluid.js', 'popup.js');

        //Cargamos la libreria donde esta el menu
        $this->load->library('Layout');
        //Creamos un nuevo layout->menu
        $menu = new Layout;

        $data = array(
            'menu' => $menu->show_menu($this->MUsers->getUserFullName($this->session->userdata('userid'))),
            'title' => 'MI Technologiesinc - Missed Oportunities',
            'from' => '/Catalog/missedoportunities/',
            'caption' => 'Missed Oportunities',
        );

        $columns = array(
            'SKU'=>
                array('colName'  => 'SKU',
                      'colModel' => "{ name:'SKU',index:'SKU',width:70,align:'center'}"),
            'AssemblySKUNeeded'=>
                array('colName'  => 'AssemblySKUNeeded',
                      'colModel' => "{ name:'AssemblySKUNeeded',index:'AssemblySKUNeeded',width:70,align:'center'}"),
            'Quantity'=>
                array('colName'  => 'Quantity',
                      'colModel' => "{ name:'Quantity',index:'Quantity',width:70,align:'center'}"),
            'CustomerID'=>
                array('colName'  => 'CustomerID',
                      'colModel' => "{ name:'CustomerID',index:'CustomerID',width:70, align:'center'}"),
            'DateRequested'=>
                array('colName'  => 'DateRequested',
                      'colModel' => "{ name:'DateRequested',index:'DateRequested',width:70, align:'center'}"),
            'Notes'=>
                array('colName'  => 'Notes',
                      'colModel' => "{ name:'Notes',index:'Notes', width:270,align:'left'}"),

        );
        //Cargamos la libreria comumn
        $this->load->library('common');

        $data['colNames'] = $this->common->CreateColname($columns, 'colName');
        $data['colModel'] = $this->common->CreateColmodel($columns, 'colModel');

        $this->build_content($data);
        $this->render_page();
    }



    function getData() {
        //Variables que envia el grid, incializarlas si estan vacias
        $page = !empty($_REQUEST['page']) ? $_REQUEST['page'] : '1'; // get the requested page
        $limit = !empty($_REQUEST['rows']) ? $_REQUEST['rows'] : '10'; // get how many rows we want to have into the gri
        $sidx = !empty($_REQUEST['sidx']) ? $_REQUEST['sidx'] : 'SKU'; // get index row - i.e. user click to sort
        $sord = !empty($_REQUEST['sord']) ? $_REQUEST['sord'] : 'asc';
        $examp = isset($_REQUEST['q']) ? $_REQUEST['q'] : 1;  //query number
        //variables que envia el formulario inicializarlas si no existen
        $search = !empty($_GET['ds']) ? $_REQUEST['ds'] : '';


        //Select utilizado para realizar el conteo del numero de registros devueltos por la consulta.
        $selectCount = 'Select count(*) as rowNum';

        $select = "SELECT [SKU]
                         ,[AssemblySKUNeeded]
                         ,[Quantity]
                         ,[CustomerID]
                         ,[DateRequested]
                         ,[Notes]
                  ";
        $selectSlice = "SELECT [SKU]
                              ,[AssemblySKUNeeded]
                              ,[Quantity]
                              ,[CustomerID]
                              ,[DateRequested]
                              ,[Notes]
                         ";

        $from = ' from ';
        $table = ' [Inventory].[dbo].[MissedOpportunities] ';
        $where = '';
        $wherefields = array('SKU'
                            ,'AssemblySKUNeeded'
                            ,'Quantity'
                            ,'CustomerID'
                            ,'DateRequested'
                            ,'Notes'
                            );

        //creamos el were concatenando todos los nombres de campos y las palabras de busqueda
        $where .=$this->MCommon->concatAllWerefields($wherefields, $search);

        $SQL = "{$selectCount}{$from}{$table}{$where}";


        $query = $this->db->query($SQL);

        $count = $query->row('rowNum');



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


        $SQL = "WITH mytable AS ($select , ROW_NUMBER() OVER (ORDER BY {$sidx} {$sord}) AS RowNumber
                                FROM {$table}{$where})
               {$selectSlice}, RowNumber
               FROM mytable
                WHERE RowNumber BETWEEN {$start} AND {$finish}";


        $query = $this->db->query($SQL);

       // print_r($query->result_array());

        $responce = new stdClass();
        $responce->page = $page;
        $responce->total = $total_pages;
        $responce->records = $count;
        $i = 0;


        foreach ($query->result_array() as $row) {
            $responce->rows[$i]['id'] = $row['SKU'];
            $responce->rows[$i]['cell'] = array($row['SKU'],
                $row['AssemblySKUNeeded'],
                $row['Quantity'],
                $row['CustomerID'],
                $row['DateRequested'],
                $row['Notes'],
   

            );
            $i++;
        }

        echo json_encode($responce);
    }




     function do_upload()
    {

        $return = true;

        // $data = $this->input->post('filename');

        // echo $data;

        $file_element_name = 'Missedoportunities';



        $config['upload_path'] = '/var/www/html/mnt/systemimports/';
        $config['allowed_types'] = 'csv';
        $config['max_size'] = 1024 * 8;
        $config['overwrite'] = TRUE;
        $config['file_name']  = $file_element_name.'_'.date('Y-m-d_H-i-s').'.csv';

         $this->load->library('upload', $config);

        if ( ! $this->upload->do_upload())
        {
            $error = array('error' => $this->upload->display_errors());
             echo $error['error'];
        
            return 'false';
        
        }
        else
        {
            $data = array('upload_data' => $this->upload->data());

            $file = $data['upload_data']['orig_name'];
	    $userid = $this->session->userdata('userid');

            $SQL ="EXECUTE [Inventory].[dbo].[sp_ImportMissedOpportunities] 'c:\sharedb\systemimports\\". $file ."' , '".$userid."'";

          //echo $SQL;

          $this->db->query($SQL);

        return 'true';

        }

    }
}
?>

