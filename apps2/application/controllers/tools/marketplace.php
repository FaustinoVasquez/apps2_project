<?php
header('Content-Type: text/html; charset=UTF-8'); 

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class MarketPlace extends BP_Controller {

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

    function index() { 

        $this->title = "MI Technologiesinc - Marketplace Feed Manager";
        $this->description = "Marketplace Feed Manager";
        $this->css = array('form.css', 'fluid.css', 'table.css', 'jqueryui/redmond/jquery-ui-1.8.21.custom.css', 'jqgrid/ui.jqgrid.css', 'menu.css',);

        // Define custom javascript
        $this->javascript = array('jqueryui/ui/jquery-ui-1.8.21.custom.js', 'jqgrid/i18n/grid.locale-en.js', 'jqgrid/jquery.jqGrid.min.js', 'jqgrid/jquery.jqGrid.fluid.js', 'site.js');

        $this->load->library('Layout');
        $menu = new Layout;

        $data = array( 
            'menu' => $menu->show_menu($this->MUsers->getUserFullName($this->session->userdata('userid'))),
            'from' => '/tools/marketplace',
            'caption' => 'Marketplace Feed Manager',
            'subgrid' => 'true',
            'export' => 'Marketplace_Feed',
            'sort' => 'desc',
            'listingType' => $this->MCatalog->fillListingType(),
            'baseUrl' => base_url(),
        );

        $data['colNames'] = "['FeedID','Manufacturer','PartNumber','Model','SKU','QOH','vQOH','tQOH','ListingType','Title','NoMPNToEbay','eBay1StoreName',
                              'eBay2StoreName','eBay3StoreName','MLMexicoTitle','NewEgg1Title','Rakuten1Title','MarkForDeletion','InsertedStamp',
                              'UpdatedStamp','MarkForDeletionStamp']";

        $data['colModel'] = "[
                {name:'FeedID',index:'FeedID', width:100, align:'center',title: false},
                {name:'Manufacturer',index:'Manufacturer', width:80, align:'center',title: false},
                {name:'PartNumber',index:'PartNumber', width:90, align:'center',title: false},  
                {name:'Model',index:'Model', width:100, align:'center',title: false},  
                {name:'SKU',index:'SKU', width:70, align:'center',title: false}, 
                {name:'QOH',index:'QOH', width:60, align:'center',title: false}, 
                {name:'vQOH',index:'vQOH', width:60, align:'center',title: false}, 
                {name:'tQOH',index:'tQOH', width:60, align:'center',title: false}, 
                {name:'ListingType',index:'ListingType', width:230, align:'center',title: false}, 
                {name:'Title',index:'Title', width:400, align:'left',title: false},  
                {name:'NoMPNToEbay',index:'NoMPNToEbay', width:170, align:'center', formatter:yesno,title: false}, 
                {name:'eBay1StoreName',index:'eBay1StoreName', width:170, align:'center',hidden:true,title: false}, 
                {name:'eBay2StoreName',index:'eBay2StoreName', width:170, align:'center',hidden:true,title: false}, 
                {name:'eBay3StoreName',index:'eBay3StoreName', width:170, align:'center',hidden:true,title: false}, 
                {name:'MLMexicoTitle',index:'MLMexicoTitle', width:80, align:'center',hidden:true,title: false},
                {name:'NewEgg1Title',index:'NewEgg1Title', width:170, align:'center',hidden:true,title: false}, 
                {name:'Rakuten1Title',index:'Rakuten1Title', width:170, align:'center',hidden:true,title: false}, 
                {name:'MarkForDeletion',index:'MarkForDeletion', width:100, align:'center', formatter:yesno,title: false }, 
                {name:'InsertedStamp',index:'InsertedStamp', width:150, align:'center', hidden:true,title: false}, 
                {name:'UpdatedStamp',index:'UpdatedStamp', width:150, align:'center', hidden:true,title: false}, 
                {name:'MarkForDeletionStamp',index:'MarkForDeletionStamp', width:150, align:'center', hidden:true,title: false}, 

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
        $autofill = $this->input->get('fil');
        $listing = $this->input->get('lis');

        //Select utilizado para realizar el conteo del numero de registros devueltos por la consulta.
        $selectCount = 'SELECT COUNT(*) AS rowNum';

        //Select General con los campos necesarios para la vista
        $select = 'SELECT CAF.[FeedID] AS FeedID 
                ,CAF.[Manufacturer] AS Manufacturer 
                ,CAF.[PartNumber] AS PartNumber 
                ,CAF.[ModelNumber] AS Model 
                ,CAF.[MITSKU] AS SKU 
                ,Cast(GS.[GlobalStock] AS int) AS QOH 
                ,Cast(GS.[VirtualStock] AS int) AS vQOH 
                ,Cast(GS.[TotalStock] AS int) AS tQOH 
                ,CAF.[ListingType] AS ListingType 
                ,CAF.[Title] AS Title 
                ,CAF.[NoMPNToEbay] AS NoMPNToEbay
                ,CAF.[eBay1StoreName] AS eBay1StoreName
                ,CAF.[eBay2StoreName] AS eBay2StoreName
                ,CAF.[eBay3StoreName] AS eBay3StoreName
                ,CAF.[MLMexicoTitle] AS MLMexicoTitle
                ,CAF.[NewEgg1Title] AS NewEgg1Title
                ,CAF.[Rakuten1Title] AS Rakuten1Title
                ,CAF.[MarkForDeletion] AS MarkForDeletion
                ,CAF.[InsertedStamp] AS InsertedStamp
                ,CAF.[UpdatedStamp] AS UpdatedStamp
                ,CAF.[MarkForDeletionStamp] AS MarkForDeletionStamp';

        $selectSlice = 'SELECT FeedID 
                ,Manufacturer 
                ,PartNumber 
                ,Model 
                ,SKU 
                ,QOH 
                ,vQOH 
                ,tQOH 
                ,ListingType 
                ,Title 
                ,NoMPNToEbay 
                ,eBay1StoreName 
                ,eBay2StoreName 
                ,eBay3StoreName 
                ,MLMexicoTitle
                ,NewEgg1Title
                ,Rakuten1Title
                ,MarkForDeletion
                ,InsertedStamp
                ,UpdatedStamp
                ,MarkForDeletionStamp';


        $from = ' from';
        $table = ' [Inventory].[dbo].[ChannelAdvisorFeed] AS CAF ';
        $join = ' LEFT OUTER JOIN [Inventory].[dbo].[Global_Stocks] AS GS ON (CAF.[MITSKU] = GS.[ProductCatalogId])';
        $where ='';
        //$wherefields = array('CAF.[FeedID]','CAF.[Manufacturer]','CAF.[PartNumber]','CAF.[ListingType]', 'CAF.[Title]');


        //Obtenemos todos los nombres de campos de la tabla productCatalog
        $fields = $this->MCommon->getAllfields('[Inventory].[dbo].[ChannelAdvisorFeed]');
        //creamos el where concatenando todos los nombres de campos y las palabras de busqueda
        $where .= $this->MCommon->concatAllWerefields($fields, $search);

        if($autofill){
            $where .= " AND (CAF.[PartNumber] = '{$autofill}')";
        }

        if($listing){
            $where .= " AND (CAF.[ListingType] = '{$listing}')";
        }

        $SQL = "{$selectCount}{$from}{$table}{$join}{$where}";
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
                                FROM {$table}{$join}{$where})
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
            
            $responce->rows[$i]['ID'] = $row['RowNumber'];
            $responce->rows[$i]['cell'] = array($row['FeedID'],
                $row['Manufacturer'] = utf8_encode($row['Manufacturer']), 
                $row['PartNumber'] = utf8_encode($row['PartNumber']), 
                $row['Model'] = utf8_encode($row['Model']),
                $row['SKU'] = utf8_encode($row['SKU']), 
                $row['QOH'] = utf8_encode($row['QOH']), 
                $row['vQOH'] = utf8_encode($row['vQOH']), 
                $row['tQOH'] = utf8_encode($row['tQOH']),
                $row['ListingType'] = utf8_encode($row['ListingType']), 
                $row['Title'] = utf8_encode($row['Title']),
                $row['NoMPNToEbay'] = utf8_encode($row['NoMPNToEbay']),
                $row['eBay1StoreName'] = utf8_encode($row['eBay1StoreName']),
                $row['eBay2StoreName'] = utf8_encode($row['eBay2StoreName']),
                $row['eBay3StoreName'] = utf8_encode($row['eBay3StoreName']),
                $row['MLMexicoTitle'] = utf8_encode($row['MLMexicoTitle']),
                $row['NewEgg1Title'] = utf8_encode($row['NewEgg1Title']), 
                $row['Rakuten1Title'] = utf8_encode($row['Rakuten1Title']), 
                $row['MarkForDeletion'] = utf8_encode($row['MarkForDeletion']),
                $row['InsertedStamp'] = utf8_encode($row['InsertedStamp']),
                $row['UpdatedStamp'] = utf8_encode($row['UpdatedStamp']), 
                $row['MarkForDeletionStamp'], 
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
                        <li><a href="#tabs-6">NewEgg-Discount-Merchant</a></li>
                        <li><a href="#tabs-7">Rakuten-Discount-Merchant</a></li>
                        <li><a href="#tabs-8">Timestamp</a></li>
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
                            <table id="ra'.$feedId.'"></table>
                            <div id="pager6"></div>
                        </center>
                    </div>
                    <div id="tabs-8">
                         <center>
                            <table id="tabs'.$feedId.'"></table>
                            <div id="pager7"></div>
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
        $f9='MLMexicoListingID';
        $f10='MLMexicoListingDate';
        $f11='MLMexicoUpdatedDate';
        $f12='MLMexicoIsActive';

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
        $f1='Rakuten1Title';
        $f2='Rakuten1TitleOverride';
        $f3='Rakuten1StoreName';
        $f4='Rakuten1UpsellLink';
        $f5='Rakuten1Template';
        $f6='Rakuten1Price';
        $f7='Rakuten1PriceOverride';
        $f8='Rakuten1IsActive';
        break;
    case 7:
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
                   ,CAF.['.$f10.'] AS '.$f10.' 
                   ,CAF.['.$f11.'] AS '.$f11.' 
                   ,CAF.['.$f12.'] AS '.$f12.' 
                   FROM  [Inventory].[dbo].[ChannelAdvisorFeed] AS CAF
                   WHERE CAF.[FeedID] ='.$feedId;
                break;

                case 5:
                case 6:
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

                case 7:
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
                        $row[$f10] = utf8_encode($row[$f10]),
                        $row[$f11] = utf8_encode($row[$f11]),
                        $row[$f12] = utf8_encode($row[$f12]),
                    );
                    $i++;
                }
                break;

                case 5:
                case 6:
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

             case 7:
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
                $eBay1Title = htmlentities($_POST['eBay1Title'], ENT_SUBSTITUTE, "UTF-8");
                $SQL = "update Inventory.dbo.ChannelAdvisorFeed set eBay1Title='{$eBay1Title}' where FeedID={$id}";
            }

            if (isset($_POST['eBay2Title'])) {
                $eBay2Title = htmlentities($_POST['eBay2Title'], ENT_SUBSTITUTE, "UTF-8");
                $SQL = "update Inventory.dbo.ChannelAdvisorFeed set eBay2Title='{$eBay2Title}' where FeedID={$id}";
            }

            if (isset($_POST['eBay3Title'])) {
                $eBay3Title = htmlentities($_POST['eBay3Title'], ENT_SUBSTITUTE, "UTF-8");
                $SQL = "update Inventory.dbo.ChannelAdvisorFeed set eBay3Title='{$eBay3Title}' where FeedID={$id}";
            }

            if (isset($_POST['MLMexicoTitle'])) {
                $MLMexicoTitle = htmlentities($_POST['MLMexicoTitle'], ENT_SUBSTITUTE, "UTF-8");
                $SQL = "update Inventory.dbo.ChannelAdvisorFeed set MLMexicoTitle='{$MLMexicoTitle}' where FeedID={$id}";
            }

            if (isset($_POST['NewEgg1Title'])) {
                $NewEgg1Title = htmlentities($_POST['NewEgg1Title'], ENT_SUBSTITUTE, "UTF-8");
                $SQL = "update Inventory.dbo.ChannelAdvisorFeed set NewEgg1Title='{$NewEgg1Title}' where FeedID={$id}";
            }

            if (isset($_POST['Rakuten1Title'])) {
                $Rakuten1Title = htmlentities($_POST['Rakuten1Title'], ENT_SUBSTITUTE, "UTF-8");
                $SQL = "update Inventory.dbo.ChannelAdvisorFeed set Rakuten1Title='{$Rakuten1Title}' where FeedID={$id}";
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

            if (isset($_POST['Rakuten1TitleOverride'])) {
                $Rakuten1TitleOverride = $_POST['Rakuten1TitleOverride'];
                $SQL = "update Inventory.dbo.ChannelAdvisorFeed set Rakuten1TitleOverride={$Rakuten1TitleOverride} where FeedID={$id}";
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

            if (isset($_POST['Rakuten1Price'])) {
                $Rakuten1Price = $_POST['Rakuten1Price'];
                $SQL = "update Inventory.dbo.ChannelAdvisorFeed set Rakuten1Price={$Rakuten1Price} where FeedID={$id}";
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

            if (isset($_POST['Rakuten1PriceOverride'])) {
                $Rakuten1PriceOverride = $_POST['Rakuten1PriceOverride'];
                $SQL = "update Inventory.dbo.ChannelAdvisorFeed set Rakuten1PriceOverride={$Rakuten1PriceOverride} where FeedID={$id}";
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

            if (isset($_POST['Rakuten1IsActive'])) {
                $Rakuten1IsActive = $_POST['Rakuten1IsActive'];
                $SQL = "update Inventory.dbo.ChannelAdvisorFeed set Rakuten1IsActive={$Rakuten1IsActive} where FeedID={$id}";
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
