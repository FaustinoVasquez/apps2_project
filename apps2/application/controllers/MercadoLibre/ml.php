<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Ml extends BP_Controller {

    public function __construct() {
        parent::__construct();

        $is_logged_in = $this->session->userdata('is_logged_in');

        if (!isset($is_logged_in) || $is_logged_in != true) {
            redirect(base_url(), 'refresh');
        }

        if ($this->MUsers->isValidUser($this->session->userdata('userid'), 883500) != 1) {// 881100 prodcat Access
            redirect('Catalog/prodcat', 'refresh');
        }
    }


    function index1() {  

        $this->title = "MI Technologiesinc - Mercadolibre Feed Manager";
        $this->description = "Mercadolibre Feed Manager";
        $this->css = array('form.css', 'fluid.css', 'table.css', 'jqueryui/redmond/jquery-ui-1.8.21.custom.css', 'jqgrid/ui.jqgrid.css', 'menu.css',);

        // Define custom javascript
        $this->javascript = array('jqueryui/ui/jquery-ui-1.8.21.custom.js', 'jqgrid/i18n/grid.locale-en.js', 'jqgrid/jquery.jqGrid.min.js', 'jqgrid/jquery.jqGrid.fluid.js', 'site.js');

        $this->load->library('Layout');
        $menu = new Layout;

        $data = array( 
            'menu' => $menu->show_menu($this->MUsers->getUserFullName($this->session->userdata('userid'))),
            'from' => '/MercadoLibre/mercadolibre',
            'caption' => 'Mercadolibre Feed Manager',
            'subgrid' => 'true',
            'export' => 'Mercadolibre_Feed',
            'sort' => 'desc',
            'baseUrl' => base_url(),
        );

        $data['colNames'] = "['FeedID','Title','PartNumber','Warranty','Quantity','Weight','Manufacturer','PartNumber','Condition',
                              'Price','Retail Price','CompatiblityList','LampType','IsActive']";

        $data['colModel'] = "[
                {name:'FeedID',index:'FeedID', width:100, align:'center'},
                {name:'Title',index:'Title', width:80, align:'center'},
                {name:'PartNumber',index:'PartNumber', width:90, align:'center'},  
                {name:'Warranty',index:'Warranty', width:100, align:'center'},  
                {name:'Quantity',index:'Quantity', width:70, align:'center'}, 
                {name:'Weight',index:'Weight', width:60, align:'center'}, 
                {name:'Manufacturer',index:'Manufacturer', width:60, align:'center'}, 
                {name:'PartNumber',index:'PartNumber', width:60, align:'center'}, 
                {name:'Condition',index:'Condition', width:230, align:'center'}, 
                {name:'Price',index:'Price', width:170, align:'center'}, 
                {name:'Retail Price',index:'Retail Price', width:170, align:'center'}, 
                {name:'Image5',index:'Image5', width:100, align:'center'}, 
                {name:'CompatiblityList',index:'CompatiblityList', width:150, align:'center'}, 
                {name:'LampType',index:'LampType', width:150, align:'center'}, 
                {name:'IsActive',index:'IsActive', width:150, align:'center'}, 

          	]";
            
        $this->build_content($data);
        $this->render_page();
    }

    function getData() {

        $page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1; // get the requested page
        $limit = isset($_REQUEST['rows']) ? $_REQUEST['rows'] : 10; // get how many rows we want to have into the gri
        $sidx = isset($_REQUEST['sidx']) ? $_REQUEST['sidx'] : 'CAF.[FeedID]'; // get index row - i.e. user click to sort
        $sord = isset($_REQUEST['sord']) ? $_REQUEST['sord'] : 'asc';
        
        $search = $this->input->get('ds');

        //Select utilizado para realizar el conteo del numero de registros devueltos por la consulta.
        $selectCount = 'SELECT COUNT(*) AS rowNum';

        //Select General con los campos necesarios para la vista
        $select = 'SELECT [FeedID]
                          ,[Title]
                          ,REPLACE([Description],'{{Warranty}}',REPLACE([Warranty],'days','dias')) AS Warranty
                          ,[Quantity]
                          ,[Weight]
                          ,[Manufacturer]
                          ,[PartNumber]
                          ,[Condition]
                          ,[Price]
                          ,[Retail Price]
                          ,[CompatiblityList]
                          ,[LampType]
                          ,[IsActive]';

        $selectSlice = 'SELECT [FeedID]
                              ,[Title]
                              ,[Warranty]
                              ,[Quantity]
                              ,[Weight]
                              ,[Manufacturer]
                              ,[PartNumber]
                              ,[Condition]
                              ,[Price]
                              ,[Retail Price]
                              ,[CompatiblityList]
                              ,[LampType]
                              ,[IsActive]';


        $from = ' from';
        $table = '  [Inventory].[dbo].[MLMexico-Feed] ';
        $where ='';
       // $wherefields = array('CAF.[FeedID]','CAF.[Manufacturer]','CAF.[PartNumber]','CAF.[ListingType]', 'CAF.[Title]');


        //Obtenemos todos los nombres de campos de la tabla productCatalog
        $fields = $this->MCommon->getAllfields('[Inventory].[dbo].[MLMexico-Feed]');
        //creamos el where concatenando todos los nombres de campos y las palabras de busqueda
        $where .= $this->MCommon->concatAllWerefields($fields, $search);

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

        $SQL = "WITH mytable AS ($select , ROW_NUMBER() OVER (ORDER BY CAF.[FeedID] {$sord}) AS RowNumber
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
            
            $responce->rows[$i]['ID'] = $row['FeedID'];
            $responce->rows[$i]['cell'] = array($row['Title'] = utf8_encode($row['Manufacturer']),
                $row['Warranty'], 
                $row['Warranty'],
                $row['Quantity'],
                $row['Weight'],
                $row['Manufacturer'],
                $row['PartNumber'],
                $row['Condition'],
                $row['Warranty'],
                $row['Price'],
                $row['Retail Price'],
                $row['CompatiblityList'],
                $row['LampType'],
                $row['IsActive'],
            );
            $i++;
        }
        echo json_encode($responce);
    }

    function fillselect()
    {
        $SQL = " SELECT DISTINCT TOP(10) PartNumber FROM [Inventory].[dbo].[ChannelAdvisorFeed] WHERE PartNumber  LIKE '{$_GET['term']}%' ORDER BY PartNumber ASC";
        echo json_encode($this->MCommon->fillAutoComplete($SQL, 'PartNumber'));
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


    function getTabs(){

        $feedId = $this->input->get('id');

         // ,CAF.[eBay1StoreName] AS eBay1StoreName
         //                ,CAF.[eBay2StoreName] AS eBay2StoreName
         //                ,CAF.[eBay3StoreName] AS eBay3StoreName
         //                ,CAF.[MLMexicoTitle] AS MLMexicoTitle
         //                ,CAF.[InsertedStamp]  AS InsertedStamp
         //                ,CAF.[UpdatedStamp] AS UpdatedStamp

        $SQL = " SELECT CAF.[ImageURL1] AS ImageURL1 
                        ,CAF.[ImageURL2] AS ImageURL2 
                        ,CAF.[ImageURL3] AS ImageURL3 
                        ,CAF.[ImageURL4] AS ImageURL4 
                        ,CAF.[ImageURL5] AS ImageURL5 

                FROM [Inventory].[dbo].[ChannelAdvisorFeed] AS CAF 
                LEFT OUTER JOIN [Inventory].[dbo].[Global_Stocks] AS GS ON (CAF.[MITSKU] = GS.[ProductCatalogId])
                WHERE CAF.[FeedID]= $feedId";


        $query = $this->db->query($SQL);

        $images =  '<a href="'.$query->row()->ImageURL1.'" target="_blank"><img src="'.$query->row()->ImageURL1.'"  height="150" width="150" /></a>';
        $images .= '<a href="'.$query->row()->ImageURL2.'" target="_blank"><img src="'.$query->row()->ImageURL2.'"  height="150" width="150" /></a>';
        $images .= '<a href="'.$query->row()->ImageURL3.'" target="_blank"><img src="'.$query->row()->ImageURL3.'"  height="150" width="150" /></a>';
        $images .= '<a href="'.$query->row()->ImageURL4.'" target="_blank"><img src="'.$query->row()->ImageURL4.'"  height="150" width="150" /></a>';
        $images .= '<a href="'.$query->row()->ImageURL5.'" target="_blank"><img src="'.$query->row()->ImageURL5.'"  height="150" width="150" /></a>';


        $tabs = '<div class="tabs">
                    <ul>
                        <li><a href="#tabs-1">Images</a></li>
                        <li><a href="#tabs-2">eBay-Discount-Merchant</a></li>
                        <li><a href="#tabs-3">eBay-DiscountTVLamps</a></li>
                        <li><a href="#tabs-4">eBay-LampTycoon</a></li>
                        <li><a href="#tabs-5">ML-Mexico</a></li>
                        <li><a href="#tabs-6">NewEgg</a></li>
                        <li><a href="#tabs-7">Timestamp</a></li>
                    </ul>
                    <div id="tabs-1"><center>'.$images.'</center></div>
                    <div id="tabs-2">
                        <center>
                           <table id="ebay1'.$feedId.'"></table>
                           <div id="pager1"></div>
                        </center>
                    </div>
                    <div id="tabs-3">
                         <center>
                            <table id="ebay2'.$feedId.'"></table>
                            <div id="pager2"></div>
                        </center>
                    </div>
                    <div id="tabs-4">
                         <center>
                           <table id="ebay3'.$feedId.'"></table>
                           <div id="pager3"></div>
                       </center>
                    </div>
                    <div id="tabs-5">
                         <center>
                            <table id="ml'.$feedId.'"></table>
                            <div id="pager4"></div>
                        </center>
                    </div>
                    <div id="tabs-6">
                         <center>
                            <table id="ne'.$feedId.'"></table>
                            <div id="pager5"></div>
                        </center>
                    </div>
                    <div id="tabs-7">
                         <center>
                            <table id="tabs'.$feedId.'"></table>
                            <div id="pager6"></div>
                        </center>
                    </div>
                </div>';
        echo $tabs;
    }


 function store($param) {

        $page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1; // get the requested page
        $limit = isset($_REQUEST['rows']) ? $_REQUEST['rows'] : 10; // get how many rows we want to have into the gri
        $sidx = isset($_REQUEST['sidx']) ? $_REQUEST['sidx'] : 'CAF.[FeedID]'; // get index row - i.e. user click to sort
        $sord = isset($_REQUEST['sord']) ? $_REQUEST['sord'] : 'asc';
        
        $feedId = $this->input->get('ds');

switch ($param) {
    case 1:
        $f1='eBay1Title';
        $f2='eBay1TitleOverride';
        $f3='eBay1StoreName';
        $f4='eBay1StoreCategory';
        $f5='eBay1MainCategory';
        $f6='eBay1UpsellLink';
        $f7='eBay1Template';
        $f8='eBay1Price';
        $f9='eBay1PriceOverride';
        $f10='eBay1IsActive';
        break;
    case 2:
        $f1='eBay2Title';
        $f2='eBay2TitleOverride';
        $f3='eBay2StoreName';
        $f4='eBay2StoreCategory';
        $f5='eBay2MainCategory';
        $f6='eBay2UpsellLink';
        $f7='eBay2Template';
        $f8='eBay2Price';
        $f9='eBay2PriceOverride';
        $f10='eBay2IsActive';
        break;
    case 3:
        $f1='eBay3Title';
        $f2='eBay3TitleOverride';
        $f3='eBay3StoreName';
        $f4='eBay3StoreCategory';
        $f5='eBay3MainCategory';
        $f6='eBay3UpsellLink';
        $f7='eBay3Template';
        $f8='eBay3Price';
        $f9='eBay3PriceOverride';
        $f10='eBay3IsActive';
        break;
    case 4:
        $f1='MLMexicoTitle';
        $f2='MLMexicoTitleOverride';
        $f3='MLMexicoStoreCategory';
        $f4='MLMexicoMainCategory';
        $f5='MLMexicoUpsellLink';
        $f6='MLMexicoTemplate';
        $f7='MLMexicoPrice';
        $f8='MLMexicoPriceOverride';
        $f9='MLMexicoIsActive';
        break;
    case 5:
        $f1='NewEgg1Title';
        $f2='NewEgg1TitleOverride';
        $f3='NewEgg1StoreName';
        $f4='NewEgg1UpsellLink';
        $f5='NewEgg1Template';
        $f6='NewEgg1Price';
        $f7='NewEgg1PriceOverride';
        $f8='NewEgg1IsActive';
        break;
    case 6:
        $f1='InsertedStamp';
        $f2='UpdatedStamp';
        $f3='MarkForDeletionStamp';
        break;
}


        //Select General con los campos necesarios para la vista
        $select = 'SELECT CAF.[FeedID] AS FeedID';
        switch ($param) {
            case 4:
                $select.= ',CAF.['.$f1.'] AS '.$f1.' 
                   ,CAF.['.$f2.'] AS '.$f2.' 
                   ,CAF.['.$f3.'] AS '.$f3.' 
                   ,CAF.['.$f4.'] AS '.$f4.' 
                   ,CAF.['.$f5.'] AS '.$f5.' 
                   ,CAF.['.$f6.'] AS '.$f6.' 
                   ,CAF.['.$f7.'] AS '.$f7.' 
                   ,CAF.['.$f8.'] AS '.$f8.' 
                   ,CAF.['.$f9.'] AS '.$f9.' 
                   FROM  [Inventory].[dbo].[ChannelAdvisorFeed] AS CAF
                   WHERE CAF.[FeedID] ='.$feedId;
                break;

                case 5:
                $select.= ',CAF.['.$f1.'] AS '.$f1.' 
                   ,CAF.['.$f2.'] AS '.$f2.' 
                   ,CAF.['.$f3.'] AS '.$f3.' 
                   ,CAF.['.$f4.'] AS '.$f4.' 
                   ,CAF.['.$f5.'] AS '.$f5.' 
                   ,CAF.['.$f6.'] AS '.$f6.' 
                   ,CAF.['.$f7.'] AS '.$f7.' 
                   ,CAF.['.$f8.'] AS '.$f8.' 
                   FROM  [Inventory].[dbo].[ChannelAdvisorFeed] AS CAF
                   WHERE CAF.[FeedID] ='.$feedId;
                break;

                case 6:
                $select.= ',CAF.['.$f1.'] AS '.$f1.' 
                   ,CAF.['.$f2.'] AS '.$f2.' 
                   ,CAF.['.$f3.'] AS '.$f3.' 
                   FROM  [Inventory].[dbo].[ChannelAdvisorFeed] AS CAF
                   WHERE CAF.[FeedID] ='.$feedId;
                break;
            
            default:
                $select.= ',CAF.['.$f1.'] AS '.$f1.' 
                   ,CAF.['.$f2.'] AS '.$f2.' 
                   ,CAF.['.$f3.'] AS '.$f3.' 
                   ,CAF.['.$f4.'] AS '.$f4.' 
                   ,CAF.['.$f5.'] AS '.$f5.' 
                   ,CAF.['.$f6.'] AS '.$f6.' 
                   ,CAF.['.$f7.'] AS '.$f7.' 
                   ,CAF.['.$f8.'] AS '.$f8.' 
                   ,CAF.['.$f9.'] AS '.$f9.' 
                   ,CAF.['.$f10.'] AS '.$f10.' 
                   FROM  [Inventory].[dbo].[ChannelAdvisorFeed] AS CAF
                   WHERE CAF.[FeedID] ='.$feedId;
                break;
        }


        $result = $this->MCommon->getSomeRecords($select); 

        $count = 1;

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


        $responce = new stdClass();
        $responce->page = $page;
        $responce->total = $total_pages;
        $responce->records = $count;

        $i = 0;

        switch ($param) {
            case 4:
                foreach ($result as $row) { 
                    $responce->rows[$i]['id'] = $row['FeedID'];
                    $responce->rows[$i]['cell'] = array($row['FeedID'],
                        $row[$f1] = utf8_encode($row[$f1]),
                        $row[$f2] = utf8_encode($row[$f2]),
                        $row[$f3] = utf8_encode($row[$f3]),
                        $row[$f4] = utf8_encode($row[$f4]),
                        $row[$f5] = utf8_encode($row[$f5]),
                        $row[$f6] = utf8_encode($row[$f6]),
                        $row[$f7] = utf8_encode($row[$f7]),
                        $row[$f8] = utf8_encode($row[$f8]),
                        $row[$f9] = utf8_encode($row[$f9]),
                    );
                    $i++;
                }
                break;

                case 5:
                foreach ($result as $row) { 
                    $responce->rows[$i]['id'] = $row['FeedID'];
                    $responce->rows[$i]['cell'] = array($row['FeedID'],
                        $row[$f1] = utf8_encode($row[$f1]),
                        $row[$f2] = utf8_encode($row[$f2]),
                        $row[$f3] = utf8_encode($row[$f3]),
                        $row[$f4] = utf8_encode($row[$f4]),
                        $row[$f5] = utf8_encode($row[$f5]),
                        $row[$f6] = utf8_encode($row[$f6]),
                        $row[$f7] = utf8_encode($row[$f7]),
                        $row[$f8] = utf8_encode($row[$f8]),
                    );
                    $i++;
                }
                break;

             case 6:
                foreach ($result as $row) { 
                    $responce->rows[$i]['id'] = $row['FeedID'];
                    $responce->rows[$i]['cell'] = array($row['FeedID'],
                        $row[$f1] = utf8_encode($row[$f1]),
                        $row[$f2] = utf8_encode($row[$f2]),
                        $row[$f3] = utf8_encode($row[$f3]),
                    );
                    $i++;
                }
                break;
            
            default:
                foreach ($result as $row) { 
                    $responce->rows[$i]['id'] = $row['FeedID'];
                    $responce->rows[$i]['cell'] = array($row['FeedID'],
                        $row[$f1] = utf8_encode($row[$f1]),
                        $row[$f2] = utf8_encode($row[$f2]),
                        $row[$f3] = utf8_encode($row[$f3]),
                        $row[$f4] = utf8_encode($row[$f4]),
                        $row[$f5] = utf8_encode($row[$f5]),
                        $row[$f6] = utf8_encode($row[$f6]),
                        $row[$f7] = utf8_encode($row[$f7]),
                        $row[$f8] = utf8_encode($row[$f8]),
                        $row[$f9] = utf8_encode($row[$f9]),
                        $row[$f10] = utf8_encode($row[$f10]),
                    );
                    $i++;
                }
                break;
        }

        echo json_encode($responce);
    }

    function storeEdit(){

         $id = $_POST['id'];


        //************************************ Actualizar Title *********************************************
         
            if (isset($_POST['eBay1Title'])) {
                $eBay1Title = $_POST['eBay1Title'];
                $SQL = "update Inventory.dbo.ChannelAdvisorFeed set eBay1Title='{$eBay1Title}' where FeedID={$id}";
            }

            if (isset($_POST['eBay2Title'])) {
                $eBay2Title = $_POST['eBay2Title'];
                $SQL = "update Inventory.dbo.ChannelAdvisorFeed set eBay2Title='{$eBay2Title}' where FeedID={$id}";
            }

            if (isset($_POST['eBay3Title'])) {
                $eBay3Title = $_POST['eBay3Title'];
                $SQL = "update Inventory.dbo.ChannelAdvisorFeed set eBay3Title='{$eBay3Title}' where FeedID={$id}";
            }

            if (isset($_POST['MLMexicoTitle'])) {
                $MLMexicoTitle = $_POST['MLMexicoTitle'];
                $SQL = "update Inventory.dbo.ChannelAdvisorFeed set MLMexicoTitle='{$MLMexicoTitle}' where FeedID={$id}";
            }

            if (isset($_POST['NewEgg1Title'])) {
                $NewEgg1Title = $_POST['NewEgg1Title'];
                $SQL = "update Inventory.dbo.ChannelAdvisorFeed set NewEgg1Title='{$NewEgg1Title}' where FeedID={$id}";
            }

        //************************************ Actualizar Title *********************************************

        //************************************ Actualizar TitleOverride *********************************************

            if (isset($_POST['eBay1TitleOverride'])) {
                $eBay1TitleOverride = $_POST['eBay1TitleOverride'];
                $SQL = "update Inventory.dbo.ChannelAdvisorFeed set eBay1TitleOverride={$eBay1TitleOverride} where FeedID={$id}";
            }

            if (isset($_POST['eBay2TitleOverride'])) {
                $eBay2TitleOverride = $_POST['eBay2TitleOverride'];
                $SQL = "update Inventory.dbo.ChannelAdvisorFeed set eBay2TitleOverride={$eBay2TitleOverride} where FeedID={$id}";
            }

            if (isset($_POST['eBay3TitleOverride'])) {
                $eBay3TitleOverride = $_POST['eBay3TitleOverride']; 
                $SQL = "update Inventory.dbo.ChannelAdvisorFeed set eBay3TitleOverride={$eBay3TitleOverride} where FeedID={$id}";
            }

            if (isset($_POST['MLMexicoTitleOverride'])) {
                $MLMexicoTitleOverride = $_POST['MLMexicoTitleOverride'];
                $SQL = "update Inventory.dbo.ChannelAdvisorFeed set MLMexicoTitleOverride={$MLMexicoTitleOverride} where FeedID={$id}";
            }

            if (isset($_POST['NewEgg1TitleOverride'])) {
                $NewEgg1TitleOverride = $_POST['NewEgg1TitleOverride'];
                $SQL = "update Inventory.dbo.ChannelAdvisorFeed set NewEgg1TitleOverride={$NewEgg1TitleOverride} where FeedID={$id}";
            }

        //************************************ Actualizar TitleOverride *********************************************

        //************************************ Actualizar Price *********************************************

            if (isset($_POST['eBay1Price'])) {
                $eBay1Price = $_POST['eBay1Price'];
                $SQL = "update Inventory.dbo.ChannelAdvisorFeed set eBay1Price={$eBay1Price} where FeedID={$id}";
            }

            if (isset($_POST['eBay2Price'])) {
                $eBay2Price = $_POST['eBay2Price'];
                $SQL = "update Inventory.dbo.ChannelAdvisorFeed set eBay2Price={$eBay2Price} where FeedID={$id}";
            }

            if (isset($_POST['eBay3Price'])) {
                $eBay3Price = $_POST['eBay3Price'];
                $SQL = "update Inventory.dbo.ChannelAdvisorFeed set eBay3Price={$eBay3Price} where FeedID={$id}";
            }

            if (isset($_POST['MLMexicoPrice'])) {
                $MLMexicoPrice = $_POST['MLMexicoPrice'];
                $SQL = "update Inventory.dbo.ChannelAdvisorFeed set MLMexicoPrice={$MLMexicoPrice} where FeedID={$id}";
            }

            if (isset($_POST['NewEgg1Price'])) {
                $NewEgg1Price = $_POST['NewEgg1Price'];
                $SQL = "update Inventory.dbo.ChannelAdvisorFeed set NewEgg1Price={$NewEgg1Price} where FeedID={$id}";
            }

        //************************************ Actualizar Price *********************************************

        //************************************ Actualizar PriceOverride *********************************************

            if (isset($_POST['eBay1PriceOverride'])) {
                $eBay1PriceOverride = $_POST['eBay1PriceOverride'];
                $SQL = "update Inventory.dbo.ChannelAdvisorFeed set eBay1PriceOverride={$eBay1PriceOverride} where FeedID={$id}"; 
            }

            if (isset($_POST['eBay2PriceOverride'])) {
                $eBay2PriceOverride = $_POST['eBay2PriceOverride'];
                $SQL = "update Inventory.dbo.ChannelAdvisorFeed set eBay2PriceOverride={$eBay2PriceOverride} where FeedID={$id}";
            }

            if (isset($_POST['eBay3PriceOverride'])) {
                $eBay3PriceOverride = $_POST['eBay3PriceOverride'];
                $SQL = "update Inventory.dbo.ChannelAdvisorFeed set eBay3PriceOverride={$eBay3PriceOverride} where FeedID={$id}";
            }

            if (isset($_POST['MLMexicoPriceOverride'])) {
                $MLMexicoPriceOverride = $_POST['MLMexicoPriceOverride'];
                $SQL = "update Inventory.dbo.ChannelAdvisorFeed set MLMexicoPriceOverride={$MLMexicoPriceOverride} where FeedID={$id}";
            }

            if (isset($_POST['NewEgg1PriceOverride'])) {
                $NewEgg1PriceOverride = $_POST['NewEgg1PriceOverride'];
                $SQL = "update Inventory.dbo.ChannelAdvisorFeed set NewEgg1PriceOverride={$NewEgg1PriceOverride} where FeedID={$id}";
            }

        //************************************ Actualizar PriceOverride *********************************************

        //************************************ Actualizar IsActive *********************************************
     
            if (isset($_POST['eBay1IsActive'])) {
                $eBay1IsActive = $_POST['eBay1IsActive'];
                $SQL = "update Inventory.dbo.ChannelAdvisorFeed set eBay1IsActive={$eBay1IsActive} where FeedID={$id}";
            }

            if (isset($_POST['eBay2IsActive'])) {
                $eBay2IsActive = $_POST['eBay2IsActive'];
                $SQL = "update Inventory.dbo.ChannelAdvisorFeed set eBay2IsActive={$eBay2IsActive} where FeedID={$id}";
            }

            if (isset($_POST['eBay3IsActive'])) {
                $eBay3IsActive = $_POST['eBay3IsActive'];
                $SQL = "update Inventory.dbo.ChannelAdvisorFeed set eBay3IsActive={$eBay3IsActive} where FeedID={$id}";
            }

            if (isset($_POST['MLMexicoIsActive'])) {
                $MLMexicoIsActive = $_POST['MLMexicoIsActive'];
                $SQL = "update Inventory.dbo.ChannelAdvisorFeed set MLMexicoIsActive={$MLMexicoIsActive} where FeedID={$id}";
            }

            if (isset($_POST['NewEgg1IsActive'])) {
                $NewEgg1IsActive = $_POST['NewEgg1IsActive'];
                $SQL = "update Inventory.dbo.ChannelAdvisorFeed set NewEgg1IsActive={$NewEgg1IsActive} where FeedID={$id}";
            }

        //************************************ Actualizar IsActive *********************************************

            if (isset($_POST['checkbox'])) {
               $name = ($_POST['checkbox']);
               $value = ($_POST['value']);
               $SQL = "update Inventory.dbo.ChannelAdvisorFeed set $name='{$value}' where FeedID={$id}";
          }

          $this->MCommon->saveRecord($SQL,'Inventory');

    }



    function subsubgrid($fields){

        $feedId = $this->input->get('id');

        $SQL = "SELECT FeedID,". $fields." FROM [Inventory].[dbo].[ChannelAdvisorFeed]
                   WHERE [FeedID] =".$feedId;
        $result = $this->MCommon->getSomeRecords($SQL); 

    $i=0; 

    foreach ($result as $row) { 
        $responce->rows[$i]['id'] = $row['FeedID'];
        $responce->rows[$i]['cell'] = array($row['FeedID'],
            $row[$fields],
        );
        $i++;
    }
    echo json_encode($responce);
    }
}

?>
