<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Compat extends BP_Controller {

    public function __construct() {
        parent::__construct();

        $is_logged_in = $this->session->userdata('is_logged_in');

        if (!isset($is_logged_in) || $is_logged_in != true) {
            redirect(base_url(), 'refresh');
        }
    }
    
    
    
     public function index() {
       
          
    $this->title = "MI Technologiesinc - Compatibilities";

        $this->description = " Compatibilities";

        // Define custom CSS
        $this->css = array('menu.css', 'form.css', 'fluid.css', 'table.css', 'jqueryui/redmond/jquery-ui-1.8.21.custom.css', 'jqgrid/ui.jqgrid.css');

        // Define custom javascript
        $this->javascript = array('jqueryui/ui/jquery-ui-1.8.21.custom.js', 'jqgrid/i18n/grid.locale-en.js', 'jqgrid/jquery.jqGrid.min.js', 'jqgrid/jquery.jqGrid.fluid.js');

        $this->hasNav = False;




        $SQL = "SELECT a.ProductLineID ,b.ID,
                b.CompatibilityCF01, b.CompatibilityCF02, b.CompatibilityCF03, b.CompatibilityCF04, b.CompatibilityCF05,
                b.CompatibilityCF06, b.CompatibilityCF07, b.CompatibilityCF08, b.CompatibilityCF09, b.CompatibilityCF10,
                b.CompatibilityCF11, b.CompatibilityCF12, b.CompatibilityCF13, b.CompatibilityCF14, b.CompatibilityCF15,
                b.CompatibilityCF16, b.CompatibilityCF17, b.CompatibilityCF18 ,b.CompatibilityCF19, b.CompatibilityCF20
                FROM Inventory.dbo.ProductCatalog a, Inventory.dbo.ProductLines b WHERE (a.ID = '{$_GET['q']}') and (a.ProductLineID = b.ID)";

      //  $compatibility = $this->Mcatalog->getOneRecord($SQL);
        $compatibility = $this->MCommon->getOneRecord($SQL);
        
         $data = array(
          
            //Datos para el grid
            'compatibility' => $compatibility,
            'from' => 'Tabs/compat/',
            'caption' =>'Compatibilities',
            'compatibilities' => "list10",
            'compDetails' => "list10_d",
             

            //Campos del formulario
            'search' => $_GET['q'],

        );
        

       
        $data['headers'] = "['id','PartNumber','Manufacturer','Comments','ApprovedBy',
                            '{$compatibility['CompatibilityCF01']}', '{$compatibility['CompatibilityCF02']}',
                            '{$compatibility['CompatibilityCF03']}', '{$compatibility['CompatibilityCF04']}',
                            '{$compatibility['CompatibilityCF05']}', '{$compatibility['CompatibilityCF06']}',
                            '{$compatibility['CompatibilityCF07']}', '{$compatibility['CompatibilityCF08']}',
                            '{$compatibility['CompatibilityCF09']}', '{$compatibility['CompatibilityCF10']}',
                            '{$compatibility['CompatibilityCF11']}', '{$compatibility['CompatibilityCF12']}',
                            '{$compatibility['CompatibilityCF13']}', '{$compatibility['CompatibilityCF14']}',
                            '{$compatibility['CompatibilityCF15']}', '{$compatibility['CompatibilityCF16']}',
                            '{$compatibility['CompatibilityCF17']}', '{$compatibility['CompatibilityCF18']}',
                            '{$compatibility['CompatibilityCF19']}', '{$compatibility['CompatibilityCF20']}',
            ]";

        $data['body'] = "[
		{name:'id',index:'id', width:80, align:'left',hidden:true},
                {name:'PartNumber',index:'PartNumber', width:80, align:'left'},
		{name:'Manufacturer',index:'Manufacturer', width:80, align:'left'},
                {name:'Comments',index:'Comments', width:100, align:'left',hidden:true, editrules : {edithidden:true}},
                {name:'ApprovedBy',index:'ApprovedBy', width:120, align:'left'},
                {name:'CustomField1',index:'CustomField1', width:150, align:'left',hidden:true, editrules : {edithidden:true}},
                {name:'CustomField2',index:'CustomField2', width:150, align:'left',hidden:true, editrules : {edithidden:true}},
                {name:'CustomField3',index:'CustomField3', width:150, align:'left',hidden:true, editrules : {edithidden:true}},
                {name:'CustomField4',index:'CustomField4', width:150, align:'left',hidden:true, editrules : {edithidden:true}},
                {name:'CustomField5',index:'CustomField5', width:150, align:'left',hidden:true, editrules : {edithidden:true}},
                {name:'CustomField6',index:'CustomField6', width:150, align:'left',hidden:true, editrules : {edithidden:true}},
                {name:'CustomField7',index:'CustomField7', width:150, align:'left',hidden:true, editrules : {edithidden:true}},
                {name:'CustomField8',index:'CustomField8', width:150, align:'left',hidden:true, editrules : {edithidden:true}},
                {name:'CustomField9',index:'CustomField9', width:150, align:'left',hidden:true, editrules : {edithidden:true}},
                {name:'CustomField10',index:'CustomField10', width:150, align:'left',hidden:true, editrules : {edithidden:true}},
                {name:'CustomField11',index:'CustomField11', width:150, align:'left',hidden:true, editrules : {edithidden:true}},
                {name:'CustomField12',index:'CustomField12', width:150, align:'left',hidden:true, editrules : {edithidden:true}},
                {name:'CustomField13',index:'CustomField13', width:150, align:'left',hidden:true, editrules : {edithidden:true}},
                {name:'CustomField14',index:'CustomField14', width:150, align:'left',hidden:true, editrules : {edithidden:true}},
                {name:'CustomField15',index:'CustomField15', width:150, align:'left',hidden:true, editrules : {edithidden:true}},
                {name:'CustomField16',index:'CustomField16', width:150, align:'left',hidden:true, editrules : {edithidden:true}},
                {name:'CustomField17',index:'CustomField17', width:150, align:'left',hidden:true, editrules : {edithidden:true}},
                {name:'CustomField18',index:'CustomField18', width:150, align:'left',hidden:true, editrules : {edithidden:true}},
                {name:'CustomField19',index:'CustomField19', width:150, align:'left',hidden:true, editrules : {edithidden:true}},
                {name:'CustomField20',index:'CustomField20', width:150, align:'left',hidden:true, editrules : {edithidden:true}},
                ]";

        
        
         $this->build_content($data);
        $this->render_page();
        
    }

    /*
     *
     * Method to generate data for the first grid of For in
     * Compatibilities
     *
     */

    function CompatData() {


        $search = $_GET["q"];
        $page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1; // get the requested page
        $limit = isset($_REQUEST['rows']) ? $_REQUEST['rows'] : 10; // get how many rows we want to have into the gri
        $sidx = isset($_REQUEST['sidx']) ? $_REQUEST['sidx'] : 'id'; // get index row - i.e. user click to sort
        $sord = isset($_REQUEST['sord']) ? $_REQUEST['sord'] : '';



        $totalrows = isset($_REQUEST['totalrows']) ? $_REQUEST['totalrows'] : false;
        if ($totalrows) {
            $limit = $totalrows;
        }

        if (!$sidx)
            $sidx = 1;


        $SQL = "SELECT id,PartNumber,Manufacturer,Comments,ApprovedBy
                    FROM Inventory.dbo.Compatibility
                    WHERE ProductCatalogID = '{$search}'
                    ORDER BY Manufacturer,PartNumber";



        $result = $this->MCommon->getSomeRecords($SQL);


        $count = count($result);
        if ($count > 0) {
            $total_pages = ceil($count / $limit);
        } else {
            $total_pages = 0;
        }
        if ($page > $total_pages)
            $page = $total_pages;
        $start = $limit * $page - $limit; // do not put $limit*($page - 1)
        if ($start < 0)
            $start = 0;

        $SQL = "SELECT a.id,a.PartNumber,a.Manufacturer,a.Comments,inventory.dbo.fn_GetUserName(ApprovedBy) as ApprovedBy
                        FROM Inventory.dbo.Compatibility a
                        WHERE (a.ProductCatalogID = '{$search}')
                        ORDER BY  Manufacturer,PartNumber";

        $result = $this->MCommon->getSomeRecords($SQL);

        $responce = new stdClass();
        $responce->page = $page;
        $responce->total = $total_pages;
        $responce->records = $count;

        $i = 0;
        foreach ($result as $row) {
            $responce->rows[$i]['id'] = $row['id'];
            $responce->rows[$i]['cell'] = array($row['id'],
                $row['PartNumber'] = utf8_encode($row['PartNumber']),
                $row['Manufacturer']= utf8_encode($row['Manufacturer']),
                $row['Comments']= utf8_encode($row['Comments']),
                $row['ApprovedBy']= utf8_encode($row['ApprovedBy'])
            );
            $i++;
        }


        echo json_encode($responce);
    }

    /*
     *
     * Method to generate Details of compatibility to the other
     * three grids in compatibilities section
     *
     */

    function compatibilityDetails() {
        $examp = $_GET["q"]; //query number
        $page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1; // get the requested page
        $limit = isset($_REQUEST['rows']) ? $_REQUEST['rows'] : 10; // get how many rows we want to have into the gri
        $sidx = isset($_REQUEST['sidx']) ? $_REQUEST['sidx'] : 'id'; // get index row - i.e. user click to sort
        $sord = isset($_REQUEST['sord']) ? $_REQUEST['sord'] : '';
        $partNumber = isset($_GET['pn']) ? $_GET['pn'] : '';
        $id = isset($_GET['id']) ? $_GET['id'] : '';
        $manufacturer = isset($_GET['mf']) ? $_GET['mf'] : '';
        $model = isset($_GET['mo']) ? ($_GET['mo']) : '';
        $originalManufacturer = isset($_GET['oma']) ? ($_GET['oma']) : '';

        $totalrows = isset($_REQUEST['totalrows']) ? $_REQUEST['totalrows'] : false;
        if ($totalrows) {
            $limit = $totalrows;
        }

        if (!$sidx)
            $sidx = 1; // connect to the database

        switch ($examp) {
            case 1:
                $SQL = "SELECT id, Model, Comments, ApprovedBy
                    FROM Inventory.dbo.CompatibilityDetails
                    WHERE (PartNumber = '{$partNumber}') and (OriginalManufacturer = '{$manufacturer}')
                    ORDER BY Model ";

                $result = $result = $this->MCommon->getSomeRecords($SQL);


                $count = count($result);
                if ($count > 0) {
                    $total_pages = ceil($count / $limit);
                } else {
                    $total_pages = 0;
                }
                if ($page > $total_pages)
                    $page = $total_pages;
                $start = $limit * $page - $limit; // do not put $limit*($page - 1)
                if ($start < 0)
                    $start = 0;

                $SQL = "SELECT a.id, a.Model, a.Comments,a.OriginalManufacturer,  b.FullName as ApprovedBy
                    FROM Inventory.dbo.CompatibilityDetails a, Inventory.dbo.Users b
                    WHERE (partnumber = '{$partNumber}') and (OriginalManufacturer = '{$manufacturer}')and (a.ApprovedBy = b.ID)
                    ORDER BY $sidx $sord";

                $result = $result = $this->MCommon->getSomeRecords($SQL);

                $responce = new stdClass();
                $responce->page = $page;
                $responce->total = $total_pages;
                $responce->records = $count;

                $i = 0;

                foreach ($result as $row) {
                    $responce->rows[$i]['id'] = $row['id'];
                    $responce->rows[$i]['cell'] = array($row['id'],
                        $row['Model']= utf8_encode($row['Model']),
                        $row['Comments']= utf8_encode($row['Comments']),
                        $row['OriginalManufacturer']= utf8_encode($row['OriginalManufacturer']),
                        $row['ApprovedBy']= utf8_encode($row['ApprovedBy'])
                    );
                    $i++;
                }


                echo json_encode($responce);
                break;


            case 2:

                $SQL = "SELECT id, AlternativePN, comments, ApprovedBy
                       FROM Inventory.dbo.CompatibilityAlternativePN
                       WHERE (PartNumber = '{$partNumber}') AND (OriginalManufacturer = '{$manufacturer}')
                       ORDER BY AlternativePN";


                $result = $result = $this->MCommon->getSomeRecords($SQL);


                $count = count($result);
                if ($count > 0) {
                    $total_pages = ceil($count / $limit);
                } else {
                    $total_pages = 0;
                }
                if ($page > $total_pages)
                    $page = $total_pages;
                $start = $limit * $page - $limit; // do not put $limit*($page - 1)
                if ($start < 0)
                    $start = 0;


                $SQL = "SELECT a.id, a.AlternativePN, a.Comments, b.FullName as AddedBy, b.FullName as ApprovedBy
                       FROM Inventory.dbo.CompatibilityAlternativePN a, Inventory.dbo.Users b
                       WHERE (a.PartNumber = '{$partNumber}') AND (a.OriginalManufacturer = '{$manufacturer}') and (a.AddedBy = b.ID)
                       ORDER BY a.AlternativePN";


                $result = $result = $this->MCommon->getSomeRecords($SQL);
                
                $responce = new stdClass();
                $responce->page = $page;
                $responce->total = $total_pages;
                $responce->records = $count;

                $i = 0;

                foreach ($result as $row) {
                    $responce->rows[$i]['id'] = $row['id'];
                    $responce->rows[$i]['cell'] = array($row['id'],
                        $row['AlternativePN']= utf8_encode($row['AlternativePN']),
                        $row['Comments']= utf8_encode($row['Comments']),
                        $row['AddedBy']= utf8_encode($row['AddedBy']),
                        $row['ApprovedBy']= utf8_encode($row['ApprovedBy'])
                    );
                    $i++;
                }


                echo json_encode($responce);
                break;


            case 3:

                $SQL = "SELECT id, OriginalModel, AlternativeModel, Comments, AddedBy, ApprovedBy, OriginalManufacturer
                        FROM Inventory.dbo.CompatibilityAlternativeModels
                        WHERE (OriginalModel = '{$model}') AND (OriginalManufacturer = '{$originalManufacturer}')
                        ORDER BY AlternativeModel";


                $result = $result = $this->MCommon->getSomeRecords($SQL);


                $count = count($result);
                if ($count > 0) {
                    $total_pages = ceil($count / $limit);
                } else {
                    $total_pages = 0;
                }
                if ($page > $total_pages)
                    $page = $total_pages;
                $start = $limit * $page - $limit; // do not put $limit*($page - 1)
                if ($start < 0)
                    $start = 0;

                $SQL = "SELECT a.id, a.AlternativeModel, a.Comments, b.FullName as AddedBy, b.FullName as ApprovedBy
                        FROM Inventory.dbo.CompatibilityAlternativeModels a, Inventory.dbo.Users b
                        WHERE (a.OriginalModel = '{$model}') AND (a.OriginalManufacturer = '{$originalManufacturer}') and (a.AddedBy = b.ID) 
                        ORDER BY AlternativeModel";



                $result = $result = $this->MCommon->getSomeRecords($SQL);

               
                
                $responce = new stdClass();
                $responce->page = $page;
                $responce->total = $total_pages;
                $responce->records = $count;

                $i = 0;

                foreach ($result as $row) {
                    $responce->rows[$i]['id'] = $row['id'];
                    $responce->rows[$i]['cell'] = array($row['id'],
                        $row['AlternativeModel']= utf8_encode($row['AlternativeModel']),
                        $row['Comments']= utf8_encode($row['Comments']),
                        $row['AddedBy']= utf8_encode($row['AddedBy']),
                        $row['ApprovedBy']= utf8_encode($row['ApprovedBy'])
                    );
                    $i++;
                }


                echo json_encode($responce);
                break;
        }
    }
    
}

?>
