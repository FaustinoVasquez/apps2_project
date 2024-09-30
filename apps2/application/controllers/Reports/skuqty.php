<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Skuqty extends BP_Controller {

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


    function fbaqty($sku){

        $data = $this->getColAndModel();
     //   $data['from'] = '/Reports/skuqty/';
        $data['getData'] = base_url().'index.php/Reports/skuqty/getData';
        $data['caption'] = 'FBAQTY';
        $data['wildcard'] = 'FBAQty';
        $data['export'] = base_url().'index.php/Reports/skuqty/csvExport/fbaqty';
        $data['ord'] = 'MITSKU';
      //  $data['sku'] = $this->uri->segment(4);

        $this->sendtoView($data);
    }

    function fbainqty($sku){
        $data = $this->getColAndModel();

        $data['getData'] = base_url().'index.php/Reports/skuqty/getData';
        $data['caption'] = 'FBAINQTY';
        $data['wildcard'] = 'FBAInboundQty';
        $data['export'] = base_url().'index.php/Reports/skuqty/csvExport/fbainqty';
        $data['ord'] = 'MITSKU';

        $this->sendtoView($data);
    }

    function fbapenqty($sku){

        $data['colNames'] = "['OrderID','SKU','ASIN','MerchantSKU','OrderQty','OrderNotes','OrderDate','Completed?']";

        $data['colModel'] = "[
                {name:'OrderID',index:'OrderID', width:60, align:'center', sorttype: 'int'},
                {name:'SKU',index:'SKU', width:60, align:'center', sorttype: 'date'},  
                {name:'ASIN',index:'ASIN', width:60, align:'center', sorttype: 'int'}, 
                {name:'MerchantSKU',index:'MerchantSKU', width:60, align:'left', sorttype: 'text'}, 
                {name:'OrderQty',index:'OrderQty', width:60, align:'center', sorttype: 'text'}, 
                {name:'OrderNotes',index:'OrderNotes', width:60, align:'center', sorttype: 'text'}, 
                {name:'OrderDate',index:'OrderDate', width:80, align:'center', sorttype: 'int'},
                {name:'Completed?',index:'Completed?', width:60, align:'center', sorttype: 'int'},  
            ]";


        $data['getData'] = base_url().'index.php/Reports/skuqty/getData1';
        $data['caption'] = 'FBAPENQTY';
        $data['export'] = base_url().'index.php/Reports/skuqty/csvExport/fbapenqty';
        $data['ord'] = 'OrderID';
        $data['wildcard'] = '';
 
        
        $this->sendtoView($data);
            
    }


    function sendtoView($data = array()) { 

        $this->title = "MI Technologiesinc - Purchase Order List";
        $this->description = "PurchaseOrderList";
        $this->css = array('form.css', 
                           'fluid.css', 
                           'table.css', 
                           'jqueryui/redmond/jquery-ui-1.8.21.custom.css', 
                           'jqgrid/ui.jqgrid.css', 'menu.css'
                           );

        // Define custom javascript
        $this->javascript = array('jqueryui/ui/jquery-ui-1.8.21.custom.js',
                                  'jqgrid/i18n/grid.locale-en.js', 
                                  'jqgrid/jquery.jqGrid.min.js',
                                  'jqgrid/jquery.jqGrid.fluid.js', 
                                  'site.js'
                                  );

        $this->load->library('Layout');
        $menu = new Layout;

        $data['menu'] = $menu->show_menu($this->MUsers->getUserFullName($this->session->userdata('userid')));
        $data['sort'] = 'desc';
        $data['from'] = base_url().'index.php/Reports/skuqty/';
        $data['sku'] = $this->uri->segment(4);
            
        $this->build_content($data);
        $this->render_page();
    }



    function getColAndModel(){
        $data['colNames'] = "['SKU','MerchantSKU','ASIN','FNSKU','Name','FBAQty','FBAInboundQty','Channel','LastUpdate']";

        $data['colModel'] = "[
                {name:'SKU',index:'SKU', width:60, align:'center', sorttype: 'int'},
                {name:'MerchantSKU',index:'MerchantSKU', width:60, align:'center', sorttype: 'date'},  
                {name:'ASIN',index:'ASIN', width:60, align:'center', sorttype: 'int'}, 
                {name:'FNSKU',index:'FNSKU', width:60, align:'center', sorttype: 'text'}, 
                {name:'Name',index:'Name', width:160, align:'left', sorttype: 'text'}, 
                {name:'FBAQty',index:'FBAQty', width:60, align:'center', sorttype: 'text'}, 
                {name:'FBAInboundQty',index:'FBAInboundQty', width:60, align:'center', sorttype: 'int'},
                {name:'Channel',index:'Channel', width:60, align:'center', sorttype: 'int'},  
                {name:'LastUpdate',index:'LastUpdate', width:70, align:'center', sorttype: 'int'}, 
            ]";

        return $data;
    }



    function getData() {

        $page = !empty($_REQUEST['page']) ? $_REQUEST['page'] : '1'; // get the requested page
        $limit = !empty($_REQUEST['rows']) ? $_REQUEST['rows'] : '10'; // get how many rows we want to have into the gri
        $sidx = !empty($_REQUEST['sidx']) ? $_REQUEST['sidx'] : 'MITSKU'; // get index row - i.e. user click to sort
        $sord = !empty($_REQUEST['sord']) ? $_REQUEST['sord'] : 'asc';
        $examp = isset($_REQUEST['q']) ? $_REQUEST['q'] : 1;  //query number
        
        $search = $this->input->get('ds');
        $sku = $this->input->get('sku');
        $wildcard = $this->input->get('wc');
     

        //Select utilizado para realizar el conteo del numero de registros devueltos por la consulta.
        $selectCount = 'SELECT COUNT(*) AS rowNum';

        //Select General con los campos necesarios para la vista
        $select = "SELECT [MITSKU] AS 'SKU'
                  ,[MerchantSKU] AS 'MerchantSKU'
                  ,[ASIN] AS 'ASIN'
                  ,[FNSKU] AS 'FNSKU'
                  ,[Title] AS 'Name'
                  ,[FBAQty] AS 'FBAQty'
                  ,[FBAInboundQty] AS 'FBAInboundQty'
                  ,[Channel] AS 'Channel'
                  ,[EnteredTime] AS 'LastUpdate'";

        $selectSlice = "SELECT SKU
                          ,MerchantSKU
                          ,ASIN
                          ,FNSKU
                          ,Name
                          ,FBAQty
                          ,FBAInboundQty
                          ,Channel
                          ,LastUpdate ";

        $from = " FROM [Inventory].[dbo].[AmazonFBA] ";

        $wherefields = array('MITSKU','MerchantSKU','ASIN','FNSKU','Title','Channel');
        $where='';

        $where .= $this->MCommon->concatAllWerefields($wherefields, $search);

        $where .=" and [$wildcard] > 0 AND [MITSKU] = '{$sku}' ";


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

        $SQL = "WITH mytable AS ($select , ROW_NUMBER() OVER (ORDER BY {$sidx} {$sord}) AS RowNumber
                                {$from}{$where})
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
            $responce->rows[$i]['id'] = $row['SKU'];
            $responce->rows[$i]['cell'] = array($row['SKU'], 
                $row['MerchantSKU'], 
                $row['ASIN'], 
                $row['FNSKU'], 
                $row['Name']= utf8_encode($row['Name']), 
                $row['FBAQty'],
                $row['FBAInboundQty'], 
                $row['Channel'], 
                $row['LastUpdate'], 
            
            );
            $i++;
        }
         echo json_encode($responce);
    }




    function getData1() {

        $page = !empty($_REQUEST['page']) ? $_REQUEST['page'] : '1'; // get the requested page
        $limit = !empty($_REQUEST['rows']) ? $_REQUEST['rows'] : '10'; // get how many rows we want to have into the gri
        $sidx = !empty($_REQUEST['sidx']) ? $_REQUEST['sidx'] : 'AZFBAO.[OrderID]'; // get index row - i.e. user click to sort
        $sord = !empty($_REQUEST['sord']) ? $_REQUEST['sord'] : 'asc';
        $examp = isset($_REQUEST['q']) ? $_REQUEST['q'] : 1;  //query number
        
        $search = $this->input->get('ds');
        $sku = $this->input->get('sku');
        $wildcard = $this->input->get('wc');
     

        //Select utilizado para realizar el conteo del numero de registros devueltos por la consulta.
        $selectCount = 'SELECT COUNT(*) AS rowNum';

        //Select General con los campos necesarios para la vista
        $select = "SELECT DISTINCT AZFBAO.[OrderID] AS 'OrderID'
                         ,AZ.[ProductCatalogID] AS 'SKU'
                         ,AZFBAO.[ASIN] AS 'ASIN'
                         ,AZFBAO.[MerchantSKU] AS 'MerchantSKU'
                         ,AZFBAO.[OrderQty] AS 'OrderQty'
                         ,IsNull(AZFBAO.[OrderNotes],'') AS 'OrderNotes'
                         ,AZFBAO.[OrderDate] AS 'OrderDate'
                        ,AZFBAO.[Completed] AS 'Completed'";

        $selectSlice = "SELECT OrderID
                          ,SKU
                          ,ASIN
                          ,MerchantSKU
                          ,OrderQty
                          ,OrderNotes
                          ,OrderDate
                          ,Completed ";

        $from = " FROM [Inventory].[dbo].[AmazonFBAOrders] AS AZFBAO 
                  LEFT OUTER JOIN [Inventory].[dbo].[Amazon] AS AZ ON (AZFBAO.[ASIN] = AZ.[ASIN]) ";

        $wherefields = array('AZFBAO.[OrderID]','AZ.[ProductCatalogID]','AZFBAO.[ASIN]','AZFBAO.[OrderNotes]');
        $where='';

        $where .= $this->MCommon->concatAllWerefields($wherefields, $search);

        $where .=" and [Completed] = 'No' AND AZ.[CountryCode] = 'US' AND AZ.[ProductCatalogId] = '{$sku}' ";


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

        $SQL = "WITH mytable AS ($select , ROW_NUMBER() OVER (ORDER BY {$sidx} {$sord}) AS RowNumber
                                {$from}{$where})
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
            $responce->rows[$i]['id'] = $row['OrderID'];
            $responce->rows[$i]['cell'] = array($row['OrderID'], 
                $row['SKU'], 
                $row['ASIN'], 
                $row['MerchantSKU'], 
                $row['OrderQty'], 
                $row['OrderNotes'],
                $row['OrderDate'], 
                $row['Completed'], 
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
}

?>
