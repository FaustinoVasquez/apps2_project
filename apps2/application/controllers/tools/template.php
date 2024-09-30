<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Template extends BP_Controller {

    public $data = array();

    public function __construct() {
        parent::__construct();

        $is_logged_in = $this->session->userdata('is_logged_in');

        if (!isset($is_logged_in) || $is_logged_in != true) {
            redirect(base_url(), 'refresh');
        }

        if ($this->MUsers->isValidUser($this->session->userdata('userid'), 881215) != 1) {// 881100 prodcat Access
            redirect('Catalog/prodcat', 'refresh');
        }

       // $this->load->helper('url'); //You should autoload this one ;)
        $this->load->helper('ckeditor');

        $this->data['ckeditor'] = array(
 
            //ID of the textarea that will be replaced
          //  'id'    =>  'content',
            'path'  =>  'js/ckeditor',
            'otro' => 'prueba',
 
            //Optionnal values
            'config' => array(
                'toolbar'   =>  "Full",     //Using the Full toolbar
                'width'     =>  "1000px",    //Setting a custom width
                'height'    =>  '250px',    //Setting a custom height
                'extraPlugins' => 'savebtn',//savebtn is the plugin's name
                'saveSubmitURL' => 'editTemplate',//link to serverside script to handle the post
            ), 
        );
    }

    function index() { 

        $this->title = "MI Technologiesinc - Template Manager";
        $this->description = "Template Manager";
        $this->css = array('form.css', 'fluid.css', 'table.css', 'jqueryui/redmond/jquery-ui-1.8.21.custom.css', 'jqgrid/ui.jqgrid.css', 'menu.css',);

        // Define custom javascript
        $this->javascript = array('jqueryui/ui/jquery-ui-1.8.21.custom.js', 'jqgrid/i18n/grid.locale-en.js', 'jqgrid/jquery.jqGrid.min.js', 'jqgrid/jquery.jqGrid.fluid.js', 'site.js','ckeditor/ckeditor.js');

        $this->load->library('Layout');
        $menu = new Layout;

        $data = array( 
            'menu' => $menu->show_menu($this->MUsers->getUserFullName($this->session->userdata('userid'))),
            'from' => '/tools/template',
            'caption' => 'Template Manager',
            'export' => 'TemplateManager',
            'sort' => 'desc',
            'TemplateStore' => $this->MCatalog->fillTemplateStore(),
            'baseUrl' => base_url(),
        );

        $data['colNames'] = "['IndexID', 'TemplateID','TemplateStoreID','ShortDescription','Description']";

        $data['colModel'] = "[
                {name:'IndexID',index:'IndexID', width:180, align:'left', hidden:true},
                {name:'TemplateID',index:'TemplateID', width:180, align:'left'},
                {name:'TemplateStoreID',index:'TemplateStoreID', width:100, align:'left'},  
                {name:'ShortDescription',index:'ShortDescription', width:450, align:'left', editable:true}, 
                {name:'Description',index:'Description', width:450, align:'left', editable:true}, 

            ]";
            
        $this->build_content($data);
        $this->render_page();
    }

    function getData() {

        $page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1; // get the requested page
        $limit = isset($_REQUEST['rows']) ? $_REQUEST['rows'] : 10; // get how many rows we want to have into the gri
        $sidx = isset($_REQUEST['sidx']) ? $_REQUEST['sidx'] : 'TemplateID'; // get index row - i.e. user click to sort
        $sord = isset($_REQUEST['sord']) ? $_REQUEST['sord'] : 'asc';
        
        $TemplateStore = $this->input->get('ts');
        $search = $this->input->get('ds');

        //Select utilizado para realizar el conteo del numero de registros devueltos por la consulta.
        $selectCount = 'SELECT COUNT(*) AS rowNum';

        //Select General con los campos necesarios para la vista
        $select = 'SELECT [IndexID]
                            ,[TemplateID]
                            ,[TemplateStoreID]
                            ,[ShortDescription]
                            ,[Description]';

        $selectSlice = 'SELECT IndexID
                            ,TemplateID
                            ,TemplateStoreID
                            ,ShortDescription
                            ,Description';


        $from = ' from';
        $table = ' [Inventory].[dbo].[ChannelAdvisorTemplates] ';
        $where ='';


        //Obtenemos todos los nombres de campos de la tabla productCatalog
        $fields = $this->MCommon->getAllfields($table);
        //creamos el where concatenando todos los nombres de campos y las palabras de busqueda
        $where .= $this->MCommon->concatAllWerefields($fields, $search);

        if ($TemplateStore){
            $where .= " and ([TemplateStoreID] = '{$TemplateStore}') ";
        }

        $SQL = "{$selectCount}{$from}{$table}{$where}";
        $result = $this->MCommon->getOneRecord($SQL);   

        $count = $result['rowNum'];

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

        $SQL = "WITH mytable AS ($select , ROW_NUMBER() OVER (ORDER BY [IndexID] {$sord}) AS RowNumber
                                FROM {$table}{$where})
               {$selectSlice}, RowNumber
               FROM mytable
                WHERE RowNumber BETWEEN {$start} AND {$finish}";

        $result = $this->MCommon->getSomeRecords($SQL);

        $responce = new stdClass();
        $responce->page = $page;
        $responce->total = $total_pages;
        $responce->records = $count;

        $i = 0;
        foreach ($result as $row) {
            $responce->rows[$i]['id'] = $row['IndexID'];
            $responce->rows[$i]['cell'] = array($row['IndexID'],
                $row['TemplateID'] = utf8_encode($row['TemplateID']),
                $row['TemplateStoreID'] = utf8_encode($row['TemplateStoreID']),
                $row['ShortDescription'] = utf8_encode($row['ShortDescription']),
                $row['Description'] = utf8_encode($row['Description']), 
            );
            $i++;
        }
        echo json_encode($responce);
    }

      function getTabs(){

        $id =  $this->input->get('id');
        // $tempId = $this->input->get('idTemp');
        // $tempStoreId= $this->input->get('idStore');


        $SQL = " SELECT [TemplateContent]
                            FROM [Inventory].[dbo].[ChannelAdvisorTemplates]
                       WHERE [IndexID]=".$id;


       $query = $this->db->query($SQL);

       $template = $query->row()->TemplateContent;
       $this->data['ckeditor']['id'] = 'temp_'.$id;

       $tabs = '<div class="tabs">
                    <ul>
                        <li><a href="#tabs-1">Template</a></li>
                    </ul>
                    <div id="tabs-1">
                        <center><div id="temp_'.$id.'" name="temp_'.$id.'">'.$template.'</div></center>
                    </div>
                </div>';
        echo $tabs;
        echo display_ckeditor($this->data['ckeditor']);
    }


    function EditData(){

        $id = $_POST['id'];
     
        if (isset($_POST['ShortDescription'])) {
            $ShortDescription = $_POST['ShortDescription'];
            $SQL = "update [Inventory].[dbo].[ChannelAdvisorTemplates] set ShortDescription='{$ShortDescription}' where IndexID={$id}";
        }

        if (isset($_POST['Description'])) {
            $Description = $_POST['Description'];
            $SQL = "update [Inventory].[dbo].[ChannelAdvisorTemplates] set Description='{$Description}' where IndexID={$id}";
        }

          $this->MCommon->saveRecord($SQL,'Inventory');

    }

    function editTemplate(){
        $templateName = $this->input->post('id'); 
        $text = $this->input->post('text');

        if (isset($templateName)){

        $elements = explode('_', $templateName);
        $id = $elements[1];
        $SQL = "update [Inventory].[dbo].[ChannelAdvisorTemplates] set TemplateContent='{$text}' where IndexID={$id}";
        $this->MCommon->saveRecord($SQL,'Inventory');
        }
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
}

?>

