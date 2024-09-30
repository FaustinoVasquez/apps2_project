<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class School extends BP_Controller {

    public function __construct() {
        parent::__construct();

        $is_logged_in = $this->session->userdata('is_logged_in');

        if (!isset($is_logged_in) || $is_logged_in != true) {
            redirect(base_url(), 'refresh');
        }

        if ($this->MUsers->isValidUser($this->session->userdata('userid'), 999000) != 1) {
            redirect('Catalog/prodcat', 'refresh');
        }
    }

    public function index() {
        //Cargamos la base de datos de OrderManager
     //   $this->load->model('MOrders', '', TRUE);

        // Define Meta
        $this->title = "MI Technologiesinc - Schools";

        $this->description = "Schools";

        // Define custom CSS
        $this->css = array('menu.css', 'fluid.css', 'jqueryui/redmond/jquery-ui-1.8.21.custom.css', 'jqgrid/ui.jqgrid.css', 'form.css');

        // Define custom javascript
        $this->javascript = array('jqueryui/ui/jquery-ui-1.8.21.custom.js', 'jqgrid/i18n/grid.locale-en.js', 'jqgrid/jquery.jqGrid.min.js', 'jqgrid/jquery.jqGrid.fluid.js', 'popup.js');
        //Cargamos la libreria donde esta el menu
        $this->load->library('Layout');
        //Creamos un nuevo layout->menu
        $menu = new Layout;

        $data = array(
            'menu' => $menu->show_menu($this->MUsers->getUserFullName($this->session->userdata('userid'))),
            'from' => '/Schools/school/',
            'nameGrid' => 'SchoolsMX',
            'namePager' => 'School',
            'caption' => 'Schools Mexico',
            'subgrid' => 'false',
            'sort' => 'asc',
            'search' => '',
            'gridEdit'=>'/Schools/school/updateSchool',
   
        );


        //Cargamos al arreglo los colnames
        $data['colNames'] = "['id','Clave','Entidad','CentroEducativo','Domicilio','Contacto','Telefono','Telefono Alt','Email','Email Alt','Control','Servicio','Ambito','Turno','Cont', 'Municipio','Localidad']";
        //Cargamos al arreglo los colmodels
        $data['colModel'] = "[
                {name:'id',index:'id', width:50, align:'center', hidden:true},
                {name:'Clave',index:'Clave', width:100, align:'left'},
                {name:'Entidad',index:'Entidad', width:150, align:'left'},
                {name:'CentroEducativo',index:'CentroEducativo', width:450, align:'left'},
                {name:'Domicilio',index:'Domicilio', width:450, align:'left',editable:true,editoptions: {size:70}},
                {name:'Contacto',index:'Contacto', width:250, align:'center',editable:true ,editoptions: {size:50}},
                {name:'Telefono',index:'Telefono', width:250, align:'left',editable:true,editoptions: {size:40}},
                {name:'Telefono2',index:'Telefono2', width:250, align:'left',editable:true,editoptions: {size:40}},
                {name:'Email',index:'Email', width:250, align:'center',editable:true,editoptions: {size:40}},
                {name:'Email2',index:'Email2', width:250, align:'center',editable:true,editoptions: {size:40}},
                {name:'Control',index:'Control', width:90, align:'left'},
                {name:'Servicio',index:'Servicio', width:120, align:'left'}, 
                {name:'Ambito',index:'Ambito', width:80, align:'left'},
                {name:'Turno',index:'Turno', width:100, align:'left'},
                {name:'Cont',index:'Cont', width:80, align:'left',hidden:true},
                {name:'Municipio',index:'Municipio', width:180, align:'left'},
                {name:'Localidad',index:'Localidad', width:200, align:'left'},                
  	]";


        //Cargamos la libreria comumn
        $this->load->library('common');

        //Reemplazamos los campos del formulario por los que estan en el arreglo $_POST.
        $data = $this->common->fillPost('ALL', $data);


        //  $data['selectedqtyordered']=$qtyOrdered;
        //cadena de busqueda del grid 
        $data['gridSearch'] = $data['from'].'dataSchools?'.'&ds=' . $data['search'] ;
 


        //Generar el contenido....
        $this->build_content($data);
        $this->render_page();
    }

    function dataSchools() {
        $page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1; // get the requested page
        $limit = isset($_REQUEST['rows']) ? $_REQUEST['rows'] : 10; // get how many rows we want to have into the gri
        $sidx = isset($_REQUEST['sidx']) ? $_REQUEST['sidx'] : 'id'; // get index row - i.e. user click to sort
        $sord = isset($_REQUEST['sord']) ? $_REQUEST['sord'] : 'asc';

   //     $this->load->model('MOrders', '', TRUE);

        $dataSearch = isset($_REQUEST['ds']) ? $_GET['ds'] : '';


        $table = ' MIT_Analizer.dbo.Schools';
        $where = ' ';
        $from = ' FROM ';
        

        $select = " SELECT  id
                           ,Clave     
                           ,Entidad
                           ,CentroEducativo
                           ,Domicilio
                           ,Contacto
                           ,Telefono
                           ,Telefono2
                           ,Email
                           ,Email2
                           ,Control
                           ,Servicio
                           ,Ambito
                           ,Turno
                           ,Cont
                           ,Municipio
                           ,Localidad     
                   ";



        $selectSlice = "SELECT  id
                           ,Clave     
                           ,Entidad
                           ,CentroEducativo
                           ,Domicilio
                           ,Contacto
                           ,Telefono
                           ,Telefono2
                           ,Email
                           ,Email2
                           ,Control
                           ,Servicio
                           ,Ambito
                           ,Turno
                           ,Cont
                           ,Municipio
                           ,Localidad  
                    ";


        $wherefields = array('Entidad', 'Municipio', 
                             'Localidad', 'Ambito', 'Control', 
                             'Servicio', 'Clave', 'Turno', 'CentroEducativo', 
                             'Contacto', 'Telefono', 'Email','Telefono2', 'Email2');
        
        $where .=$this->MCommon->concatAllWerefields($wherefields, $dataSearch);


        $SQL = "{$select}{$from}{$table}{$where}";


        $result = $this->MCommon->getSomeRecordsFromServer($SQL,'Mit_Analizer');


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
        $finish = $start + $limit;


        $SQL = "WITH mytable AS ({$select},ROW_NUMBER() OVER (ORDER BY {$sidx} {$sord}) AS RowNumber
	FROM {$table} {$where}) 
               {$selectSlice},RowNumber
               FROM mytable
               WHERE RowNumber BETWEEN {$start} AND {$finish}";


        $result = $this->MCommon->getSomeRecords($SQL);


        $responce = new stdClass();
        $responce->page = $page;
        $responce->total = $total_pages;
        $responce->records = $count;
        $i = 0;


        foreach ($result as $row) {
            $responce->rows[$i]['id'] = $row['id'];
            $responce->rows[$i]['cell'] = array($row['id'],
                $row['Clave']= utf8_encode($row['Clave']),
                $row['Entidad']= utf8_encode($row['Entidad']),
                $row['CentroEducativo']= utf8_encode($row['CentroEducativo']),
                $row['Domicilio']=utf8_encode($row['Domicilio']),
                $row['Contacto']=utf8_encode($row['Contacto']),
                $row['Telefono']=utf8_encode($row['Telefono']),
                $row['Telefono2']=utf8_encode($row['Telefono2']),
                $row['Email']= utf8_encode($row['Email']),
                $row['Email2']= utf8_encode($row['Email2']),
                $row['Control']= utf8_encode($row['Control']),
                $row['Servicio']= utf8_encode($row['Servicio']),
                $row['Ambito']= utf8_encode($row['Ambito']),
                $row['Turno']= utf8_encode($row['Turno']),
                $row['Cont'],
                $row['Municipio']= utf8_encode($row['Municipio']),
                $row['Localidad']= utf8_encode($row['Localidad']),
         
            );
            $i++;
        }

        echo json_encode($responce);
    }

    /*
     * CSV Export
     */

    function csvExport($name) {

        header('Content-type: application/vnd.ms-excel');
        header("Content-Disposition: attachment; filename=" . $name . '_' . date("D-M-j") . ".xls");
        header("Pragma: no-cache");

        $buffer = $_POST['csvBuffer'];

        try {
            echo $buffer;
        } catch (Exception $e) {
            
        }
    }
    
    
    function updateSchool(){
        $id = $_POST['id'];
        $domicilio =isset($_POST['Domicilio']) ? $_POST['Domicilio'] : '';
        $contacto =isset($_POST['Contacto']) ? $_POST['Contacto'] : '';
        $telefono =isset($_POST['Telefono']) ? $_POST['Telefono'] : '';
        $telefono2 =isset($_POST['Telefono2']) ? $_POST['Telefono2'] : '';
        $email =isset($_POST['Email']) ? $_POST['Email'] : '';
        $email2 =isset($_POST['Email2']) ? $_POST['Email2'] : '';
        
        $SQL = "update MIT_Analizer.dbo.Schools set Domicilio='{$domicilio}', Contacto='{$contacto}', Telefono='{$telefono}', Telefono2='{$telefono2}', Email='{$email}', Email2='{$email2}' where id={$id}";
        
        $this->MCommon->saveRecord($SQL,'MIT_Analizer');
        
    }

}
?>





