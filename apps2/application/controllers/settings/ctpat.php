<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Ctpat extends BP_Controller {

    public function __construct() {
        parent::__construct();

        $is_logged_in = $this->session->userdata('is_logged_in');

        if (!isset($is_logged_in) || $is_logged_in != true) {
            redirect(base_url(), 'refresh');
        }

        // if ($this->MUsers->isValidUser($this->session->userdata('userid'), 881215) != 1) {// 881100 prodcat Access
        //     redirect('Catalog/prodcat', 'refresh');
        // }
    }

    function index() { 

        $this->title = "MI Technologiesinc - CTPAT";
        $this->description = "CTPAT";
        $this->css = array('form.css', 'fluid.css', 'table.css', 'jqueryui/redmond/jquery-ui-1.8.21.custom.css', 'jqgrid/ui.jqgrid.css', 'menu.css',);

        // Define custom javascript
        $this->javascript = array('jqueryui/ui/jquery-ui-1.8.21.custom.js', 'jqgrid/i18n/grid.locale-en.js', 'jqgrid/jquery.jqGrid.min.js', 'jqgrid/jquery.jqGrid.fluid.js', 'site.js', 'jquery.validate.min.js');

        $this->load->library('Layout');
        $menu = new Layout;

        $data = array( 
            'menu' => $menu->show_menu($this->MUsers->getUserFullName($this->session->userdata('userid'))),
            'from' => '/tools/ctpat',
            'caption' => 'CTPAT',
            'export' => 'CTPAT',
            'sort' => 'asc',
            'Stamp' => date("m/d/Y"),
            'baseUrl' => base_url(),
        );

        $data['colNames'] = "['ID','Stamp','PO','Tracking#','Called?','Notes']";

        $data['colModel'] = "[
                {name:'ID',index:'ID', width:50, align:'center', sorttype: 'int', hidden: true},
                {name:'Stamp',index:'Stamp', width:80, align:'left'},
                {name:'PO',index:'PO', width:80, align:'center'},
                {name:'Tracking',index:'Tracking', width:60, align:'center'},  
                {name:'Called',index:'Called', width:60, align:'center', formatter:yesno}, 
                {name:'Notes',index:'Notes', width:60, align:'center'}, 
            ]";
            
        $this->build_content($data);
        $this->render_page();
    }

    function getData() {

        $page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1; // get the requested page
        $limit = isset($_REQUEST['rows']) ? $_REQUEST['rows'] : 10; // get how many rows we want to have into the gri
        $sidx = $_GET['sidx']; // get index row - i.e. user click to sort
        $sord = $_GET['sord'];
        //if(!$sidx) $sidx =1;
        
        $search = $this->input->get('ds');

        //Select utilizado para realizar el conteo del numero de registros devueltos por la consulta.
        $selectCount = 'SELECT COUNT(*) AS rowNum';

        //Select General con los campos necesarios para la vista
        $select = "SELECT A.id
                         ,A.Stamp
                         ,A.PO
                         ,A.Tracking
                         ,A.Called
                         ,A.Notes ";

        $selectSlice = "SELECT id
                            ,Stamp
                            ,PO
                            ,Tracking
                            ,Called
                            ,Notes ";
        $from = " FROM (
                        SELECT [id]
                                ,[Stamp]
                                ,[PO]
                                ,[Tracking]
                                ,[Called]
                                ,[Notes]
                            FROM [Inventory].[dbo].[CTPATLogging]
                    ) A ";

        $where ='';
        $wherefields = array('A.[Stamp]','A.[PO]','A.[Tracking]','A.[Notes]');

        //Obtenemos todos los nombres de campos de la tabla productCatalog
      //  $fields = $this->MCommon->getAllfields($table);
        //creamos el where concatenando todos los nombres de campos y las palabras de busqueda
        $where .= $this->MCommon->concatAllWerefields($wherefields, $search);

        $SQL = "{$selectCount}{$from}{$where}";
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

        $SQL = "WITH mytable AS ($select , ROW_NUMBER() OVER (ORDER BY [$sidx] $sord) AS RowNumber
                    {$from}{$where})
               {$selectSlice}, RowNumber
               FROM mytable
                WHERE RowNumber BETWEEN {$start} AND {$finish}";

        //print_r($SQL);
        $result = $this->MCommon->getSomeRecords($SQL);
        $responce = new stdClass();
        $responce->page = $page;
        $responce->total = $total_pages;
        $responce->records = $count;

        $i = 0;
        foreach ($result as $row) {
            $responce->rows[$i]['id'] = $row['id'];
            $responce->rows[$i]['cell'] = array($row['id'],
                $row['Stamp'] = utf8_encode($row['Stamp']),
                $row['PO'] = utf8_encode($row['PO']),
                $row['Tracking'] = utf8_encode($row['Tracking']),
                $row['Called'] = utf8_encode($row['Called']),
                $row['Notes'] = utf8_encode($row['Notes']),
            );
            $i++;
        }
        echo json_encode($responce);
    }


    function SaveRec() {

        $stamp = $_POST['Stamp'];
        $po = $_POST['PO'];
        $track = $_POST['Tracking'];
        $call = $_POST['Called'];
        $notes = $_POST['Notes'];

        $SQL = " INSERT INTO [Inventory].[dbo].[CTPATLogging] (Stamp,PO,Tracking,Called,Notes)
                 OUTPUT INSERTED.id
                 VALUES ('{$stamp}',
                        '{$po}',
                        '{$track}',
                        '{$call}',
                        '{$notes}'); ";

        $this->MCommon->saveRecord($SQL,'InventorySave');
        $this->sendEmail($stamp, $po, $track, $call, $notes);

        return 'true';
    }

    function sendEmail($stamp, $po, $track, $call, $notes)
    {
       
       //Configuramos en envio de correo
        $config = Array(
            'protocol' => 'smtp',
            'smtp_host' => 'smarthost.coxmail.com', // ssl://smtp.googlemail.com
            'smtp_port' => 25,
            'mailtype' => 'html',
            'charset' => 'iso-8859-1',
            'wordwrap' => TRUE
        );
       
        //Creamos el Mensaje
        $message = $this->createMessage($stamp, $po, $track, $call, $notes);
       
        //Mandamos el correo
        $this->load->library('email', $config);
        $this->email->set_newline("\r\n");
        $this->email->from('autobot@mitechnologiesinc.com'); // change it to yours
        $this->email->to('ctpat@mitechnologiesinc.com'); // ordercancellations@mitechnologiesinc.com
        $this->email->subject('Pruebame');
        $this->email->message($message);
       
        if ($this->email->send()) 
        {
            echo 'Email sent.';
        } 
        else 
        {
            show_error($this->email->print_debugger());
        }
       
        return;
    }

    function createMessage($stamp, $po, $track, $call, $notes)
    {
        if($call == 1)
            $call = 'Yes';
        else
            $call = 'No';

        $message = '
        <table style="border-collapse:collapse;border-spacing:1;empty-cells:show;border:1px solid #cbcbcb;border-bottom:1px solid #cbcbcb; width:500px">
            <thead>
                <tr style="background-color:#e0e0e0;color:#000;text-align:center;vertical-align:bottom;border-left:1px solid #cbcbcb;border-width:1px 1px 0 1px;font-size:inherit;margin:0;overflow:visible;padding:.5em;">
                    <th style="border:1px solid #cbcbcb;">Timestamp</th>
                    <th style="border:1px solid #cbcbcb;">PO</th>
                    <th style="border:1px solid #cbcbcb;">Tracking#</th>
                    <th style="border:1px solid #cbcbcb;">Called?</th>
                    <th style="border:1px solid #cbcbcb;">Notes</th>
                </tr>
            </thead>

            <tbody>
                <tr style="background-color:#F9F7F7;">
                    <td style="border:1px solid #cbcbcb;">'.$stamp.'</td>
                    <td style="border:1px solid #cbcbcb;">'.$po.'</td>
                    <td style="border:1px solid #cbcbcb;">'.$track.'</td>
                    <td style="border:1px solid #cbcbcb;">'.$call.'</td>
                    <td style="border:1px solid #cbcbcb;">'.$notes.'</td>
                </tr>
            </tbody>
        </table>';

        return $message;
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