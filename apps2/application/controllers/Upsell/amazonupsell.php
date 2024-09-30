<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Amazonupsell extends BP_Controller {

    public function __construct() {
        parent::__construct();

        $is_logged_in = $this->session->userdata('is_logged_in');

        if (!isset($is_logged_in) || $is_logged_in != true) {
            redirect(base_url(), 'refresh');
        }
        if ($this->MUsers->isValidUser($this->session->userdata('userid'), 882316) != 1) {
            redirect('Catalog/prodcat', 'refresh');
        }
    }

    function index() {

        // Define Meta
        $this->title = "MI Technologiesinc - Amazon Upsell";

        $this->description = "Amazon Upsell";

        $this->css = array('menu.css', 'form.css', 'fluid.css', 'table.css', 'jqueryui/redmond/jquery-ui-1.8.21.custom.css', 'jqgrid/ui.jqgrid.css');

        // Define custom javascript
        $this->javascript = array('jqueryui/ui/jquery-ui-1.8.21.custom.js', 'jqgrid/i18n/grid.locale-en.js', 'jqgrid/jquery.jqGrid.min.js', 'jqgrid/jquery.jqGrid.fluid.js', 'popup.js','site.js');

        //Cargamos la libreria donde esta el menu
        $this->load->library('Layout');
        //Creamos un nuevo layout->menu
        $menu = new Layout;

        $data = array(
            'menu' => $menu->show_menu($this->MUsers->getUserFullName($this->session->userdata('userid'))),
            'title' => 'MI Technologiesinc - Amazon Upsell',
            'from' => '/Upsell/amazonupsell/',
            'caption' => 'Amazon Upsell',
        );

        $columns = array(
            'AmazonOrderNumber'=>
                array('colName'  => 'AmazonOrderNumber',
                      'colModel' => "{ name:'AmazonOrderNumber',index:'AmazonOrderNumber',width:90,align:'center'}"),
            'OMOrderNumber'=>
                array('colName'  => 'OMOrderNumber',
                      'colModel' => "{ name:'OMOrderNumber',index:'OMOrderNumber',width:70,align:'center'}"),
            'SKU'=>
                array('colName'  => 'SKU',
                      'colModel' => "{ name:'SKU',index:'SKU',width:70, align:'center'}"),
            'MerchantSKU'=>
                array('colName'  => 'MerchantSKU',
                      'colModel' => "{ name:'MerchantSKU',index:'MerchantSKU',width:70, align:'center'}"),
            'Item'=>
                array('colName'  => 'Item',
                      'colModel' => "{ name:'Item',index:'Item', width:270,align:'left'}"),
            'PricePerUnit'=>
                array('colName'  => 'PricePerUnit',
                      'colModel' => "{ name:'PricePerUnit',index:'PricePerUnit',width:60,align:'right',
                                       formatter:'number',
                                       formatoptions:{ decimalSeparator:'.',
                                                       thousandsSeparator: ',',
                                                       decimalPlaces: 2,
                                                       prefix: '$ '
                                                     }

                      }"),
            'QuantityOrdered'=>
                array('colName'  => 'QuantityOrdered',
                      'colModel' => "{ name:'QuantityOrdered',index:'QuantityOrdered',width:50,align:'center'}"),
            'OrderDate'=>
                array('colName'  => 'OrderDate',
                      'colModel' => "{ name:'OrderDate',index:'OrderDate',width:50,align:'center'}"),
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
        $sidx = !empty($_REQUEST['sidx']) ? $_REQUEST['sidx'] : 'AZUSEB.[WebOrderNumber]'; // get index row - i.e. user click to sort
        $sord = !empty($_REQUEST['sord']) ? $_REQUEST['sord'] : 'asc';
        $examp = isset($_REQUEST['q']) ? $_REQUEST['q'] : 1;  //query number
        //variables que envia el formulario inicializarlas si no existen
        $search = !empty($_GET['ds']) ? $_REQUEST['ds'] : '';


        //Select utilizado para realizar el conteo del numero de registros devueltos por la consulta.
        $selectCount = 'Select count(*) as rowNum';

        $select = "SELECT
                --ORDER INFORMATION
                      AZUSEB.[WebOrderNumber] AS 'AmazonOrderNumber'
                      ,AZUSEB.[OrderNumber] AS 'OMOrderNumber'
                      ,AZUSEB.[SKU] AS 'SKU'
                      ,AZUSEB.[WebSKU] AS 'MerchantSKU'
                      ,AZUSEB.[Product] AS 'Item'
                      ,AZUSEB.[PricePerUnit] AS 'PricePerUnit'
                      ,AZUSEB.[QuantityOrdered] AS 'QuantityOrdered'
                      ,CONVERT(VARCHAR(11), AZUSEB.[OrderDate], 101) AS 'OrderDate'";
        $selectSlice = "SELECT
                --ORDER INFORMATION
                      AmazonOrderNumber
                      ,OMOrderNumber
                      ,SKU
                      ,MerchantSKU
                      ,Item
                      ,PricePerUnit
                      ,QuantityOrdered
                      ,OrderDate";

        $from = ' from ';
        $table = ' [Inventory].[dbo].[AmazonUpsellScripts-EB] AS AZUSEB ';
        $join =  ' LEFT OUTER JOIN [RecycleAPI].[dbo].[Product] AS P ON (C.[Id] = P.[CustomerId]) ';
        $where = '';
        $wherefields = array('AZUSEB.[WebOrderNumber]'
                            ,'AZUSEB.[OrderNumber]'
                            ,'AZUSEB.[SKU]'
                            ,'AZUSEB.[WebSKU]'
                            ,'AZUSEB.[Product]'
                            ,'AZUSEB.[ShipName]'
                            ,'AZUSEB.[ShipAddress]'
                            ,'AZUSEB.[ShipCity]'
                            ,'AZUSEB.[ShipZip]'
                            ,'AZUSEB.[ShipCountry]'
                            ,'AZUSEB.[ShipEmail]'
                            );

        //creamos el were concatenando todos los nombres de campos y las palabras de busqueda
        $where .=$this->MCommon->concatAllWerefields($wherefields, $search);

        $where .= "and AZUSEB.[UpsellSKUQty] > 0 AND AZUSEB.[TrackingNumber] IS NULL ";

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
            $responce->rows[$i]['id'] = $row['AmazonOrderNumber'];
            $responce->rows[$i]['cell'] = array($row['AmazonOrderNumber'],
                $row['OMOrderNumber'],
                $row['SKU'],
                $row['MerchantSKU'],
                $row['Item']= utf8_encode($row['Item']) ,
                $row['PricePerUnit'],
                $row['QuantityOrdered'],
                $row['OrderDate'],
            );
            $i++;
        }

        echo json_encode($responce);
    }


    function getTabs(){

     $orderid = $this->input->get('id');

       $SQL = "SELECT
        --SHIPPING INFORMATION
                AZUSEB.[ShipName] AS 'Name'
                ,IsNull(AZUSEB.[ShipCompany],'') AS 'Company'
                ,AZUSEB.[ShipAddress] AS 'Address'
                ,IsNull(AZUSEB.[ShipAddress2],'') AS 'Address2'
                ,AZUSEB.[ShipCity] AS 'City'
                ,AZUSEB.[ShipZip] AS 'Zip'
                ,AZUSEB.[ShipCountry] AS 'Country'
                ,AZUSEB.[ShipEmail] AS 'Email'
                ,AZUSEB.[ShipPhone] AS 'PhoneNumber'
                ,IsNull(AZUSEB.[TrackingNumber],'') AS 'TrackingNumber'
        --UPSELL INFORMATION
               ,AZUSEB.[UpsellSKU] AS 'UpSellSKU'
               ,AZUSEB.[UpsellSKUQty] AS 'UpSellSKUQty'
               ,AZUSEB.[UpsellItem] AS 'UpSellItem'
               ,AZUSEB.[UpsellBrand] AS 'UpSellBrand'
               ,IsNull(AZUSEB.[UpsellUPC],'') AS 'UpSellUPC'
               ,AZUSEB.[UpsellASIN] AS 'UpSellURL'
        --SCRIPTS (HTML)
              ,AZUSEB.[PhoneScript] as 'PhoneScript'
              ,AZUSEB.[AfterCallEmail] as 'AfterCallEmail'
             FROM [Inventory].[dbo].[AmazonUpsellScripts-EB] AS AZUSEB
             where   AZUSEB.[WebOrderNumber] = '{$orderid}'
            ";



       $query = $this->db->query($SQL);
        $row = $query->row();

        $shippingInfo = '<table cellspacing="5" cellpadding="5" border="2px" style="border:2px solid #83c0e3;">';
            $shippingInfo .= '<tr><td><strong>Name</strong></td><td>'.$row->Name.'</td></tr>';
            $shippingInfo .= '<tr><td><strong>Company</strong></td><td>'.$row->Company.'</td></tr>';
            $shippingInfo .= '<tr><td><strong>Address</strong></td><td>'.$row->Address.'</td></tr>';
            $shippingInfo .= '<tr><td><strong>Address2</strong></td><td>'.$row->Address2.'</td></tr>';
            $shippingInfo .= '<tr><td><strong>City</strong></strong></td><td>'.$row->City.'</td></tr>';
            $shippingInfo .= '<tr><td><strong>Zip</strong></td><td>'.$row->Zip.'</td></tr>';
            $shippingInfo .= '<tr><td><strong>Country</strong></td><td>'.$row->Country.'</td></tr>';
            $shippingInfo .= '<tr><td><strong>Email</strong></td><td>'.$row->Email.'</td></tr>';
            $shippingInfo .= '<tr><td><strong>PhoneNumber</strong></td><td>'.$row->PhoneNumber.'</td></tr>';
            $shippingInfo .= '<tr><td><strong>TrackingNumber</strong></td><td>'.$row->TrackingNumber.'</td></tr>';
        $shippingInfo .='</table>';


        $upsellInfo = '<table cellspacing="5" cellpadding="5" border="2px" style="border:2px solid #83c0e3;">';
            $upsellInfo .= '<tr><td><strong>UpSellSKU</strong></td><td>'.$row->UpSellSKU.'</td></tr>';
            $upsellInfo .= '<tr><td><strong>UpSellSKUQty</strong></td><td>'.$row->UpSellSKUQty.'</td></tr>';
            $upsellInfo .= '<tr><td><strong>UpSellItem</strong></td><td>'.$row->UpSellItem.'</td></tr>';
            $upsellInfo .= '<tr><td><strong>UpSellBrand</strong></td><td>'.$row->UpSellBrand.'</td></tr>';
            $upsellInfo .= '<tr><td><strong>UpSellUPC</strong></td><td>'.$row->UpSellUPC.'</td></tr>';
            $upsellInfo .= '<tr><td><strong>UpSellURL</strong></td><td><a href="'.$row->UpSellURL.'" target="_blank" >'.$row->UpSellURL.'</a></td></tr>';
        $upsellInfo .='</table>';

        $phonescript = '<table>';
            $phonescript .= '<tr><td>'.$row->PhoneScript.'</td></tr>';
        $phonescript .='</table>';


        $aftercallemail = '<table>';
            $aftercallemail .= '<tr><td>'.$row->AfterCallEmail.'</td></tr>';
        $aftercallemail .='</table>';




        $tabs = "<div class=\"tabs\">
                    <ul>
                        <li><a href=\"#tabs-1\">SHIPPING INFORMATION</a></li>
                        <li><a href=\"#tabs-2\">UPSELL INFORMATION</a></li>
                        <li><a href=\"#tabs-3\">PHONE SCRIPT</a></li>
                        <li><a href=\"#tabs-4\">AFTER CALL EMAIL</a></li>
                    </ul>
                    <div id=\"tabs-1\">{$shippingInfo}</div>
                    <div id=\"tabs-2\">{$upsellInfo}</div>
                    <div id=\"tabs-3\">{$phonescript}</div>
                    <div id=\"tabs-4\">{$aftercallemail}</div>
                </div>";
        echo $tabs;
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

