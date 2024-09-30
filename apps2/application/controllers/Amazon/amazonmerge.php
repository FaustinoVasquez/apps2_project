<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Amazonmerge extends BP_Controller
{

    public function __construct()
    {
        parent::__construct();

        $is_logged_in = $this->session->userdata('is_logged_in');

        if (!isset($is_logged_in) || $is_logged_in != true) {
            redirect(base_url(), 'refresh');
        }

        if ($this->MUsers->isValidUser($this->session->userdata('userid'), 881100) != 1) {// Access Code
            redirect('Catalog/prodcat', 'refresh');
        }
    }

    function index()
    {

        $this->title = "MI Technologiesinc - Amazon Merge Items";

        $this->description = "AAmazon Merge Items";

        $this->css = array('form.css', 'fluid.css', 'table.css', 'jqueryui/redmond/jquery-ui-1.8.21.custom.css', 'jqgrid/ui.jqgrid.css', 'menu.css',);

        // Define custom javascript
        $this->javascript = array('jqueryui/ui/jquery-ui-1.8.21.custom.js', 'jqgrid/i18n/grid.locale-en.js', 'jqgrid/jquery.jqGrid.min.js', 'jqgrid/jquery.jqGrid.fluid.js', 'site.js');

        // If the page has menu
        $this->load->library('Layout');
        $menu = new Layout;


        $data = array(
            'menu' => $menu->show_menu($this->MUsers->getUserFullName($this->session->userdata('userid'))),
            'from' => '/amazon/amazonmerge/',
            'caption' => 'Amazon Merge Items',
            'export' => 'exportexcel',
            'subgrid' => 'false', // true or false depends
            'sort' => 'desc',
            'search' => '',
            'customerid' => $this->MUsers->getCustomerId($this->session->userdata('userid')),
        );


        $data['colNames'] = "['Id','DATE','RetainedASIN','RetainedSKU','RetainedBrand','MergedASIN','MergedSKU','MergedBrand','DartFBASKU','DartFBMSKU','DartMFPSKU']";

        $data['colModel'] = "[
                {name:'RowNumber',index:'RowNumber', width:50, align:'center', hidden:true},
                {name:'DATE',index:'[AAM].[Date]', width:80, align:'center'},
                {name:'RetainedASIN',index:'[AAM].[RetainedASIN]', width:80, align:'center'},
                {name:'RetainedSKU',index:'[AZMapping-Retained].[ProductCatalogId]', width:60, align:'center'},
                {name:'RetainedBrand',index:'[AZMapping-Retained].[BrandSell]', width:55, align:'center'},
                {name:'MergedASIN',index:'[AAM].[MergedASIN]', width:80, align:'center'},
				{name:'MergedSKU',index:'[AZMapping-Merged].[ProductCatalogId]', width:80, align:'center'},
				{name:'MergedBrand',index:'[AZMapping-Merged].[BrandSell]', width:80, align:'center'},
				{name:'DartFBASKU',index:'[AZMSKU].[DARTFBASKU]', width:80, align:'center'},
				{name:'DartFBMSKU',index:'AZMSKU].[DARTFBMSKU]', width:80, align:'center'},
				{name:'DartMFPSKU',index:'[AZMSKU].[DARTMFPSKU]', width:80, align:'center'},
               ]";

        $this->build_content($data);
        $this->render_page();
    }

    function getData()
    {

        $page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1; // get the requested page
        $limit = isset($_REQUEST['rows']) ? $_REQUEST['rows'] : 10; // get how many rows we want to have into the gri
        $sidx = isset($_REQUEST['sidx']) ? $_REQUEST['sidx'] : '[AAM].[Date]'; // get index row - i.e. user click to sort
        $sord = isset($_REQUEST['sord']) ? $_REQUEST['sord'] : 'asc';

        $search = $this->input->get('ds');


        //Select utilizado para realizar el conteo del numero de registros devueltos por la consulta.
        $selectCount = 'SELECT COUNT(*) AS rowNum';

        $table = ' [Inventory].[dbo].[AmazonASINMergers] AS [AAM]  ';

        $where = '';
        $from = ' FROM ';
        $join = ' LEFT OUTER JOIN [Inventory].[dbo].[AmazonMerchantSKU] AS [AZMSKU] ON ([AAM].[MergedASIN] = [AZMSKU].[ASIN])
			  LEFT OUTER JOIN [Inventory].[dbo].[Amazon] AS [AZMapping-Retained] ON ([AAM].[RetainedASIN] = [AZMapping-Retained].[ASIN])
			  LEFT OUTER JOIN [Inventory].[dbo].[Amazon] AS [AZMapping-Merged] ON ([AAM].[MergedASIN] = [AZMapping-Merged].[ASIN]) ';


        $select = "SELECT  [AAM].[Date] as 'Date'
					  ,[AAM].[RetainedASIN]  AS 'RetainedASIN'
					  ,[AZMapping-Retained].[ProductCatalogId] AS 'RetainedSKU'
					  ,[AZMapping-Retained].[BrandSell] AS 'RetainedBrand'
					  ,[AAM].[MergedASIN] AS 'MergedASIN'
					  ,[AZMapping-Merged].[ProductCatalogId] AS 'MergedSKU'
					  ,[AZMapping-Merged].[BrandSell] AS 'MergedBrand'
					  ,[AZMSKU].[DARTFBASKU] AS 'DartFBASKU'
					  ,[AZMSKU].[DARTFBMSKU] AS 'DartFBMSKU'
					  ,[AZMSKU].[DARTMFPSKU] AS 'DartMFPSKU'
			 ";

        $selectSlice = "SELECT Date
					  ,RetainedASIN
					  ,RetainedSKU
					  ,RetainedBrand
					  ,MergedASIN
					  ,MergedSKU
					  ,MergedBrand
					  ,DartFBASKU
					  ,DartFBMSKU
					  ,DartMFPSKU";

        $wherefields = array('[AAM].[RetainedASIN]'
        , '[AZMapping-Retained].[ProductCatalogId]'
        , '[AZMapping-Retained].[BrandSell]'
        , '[AAM].[MergedASIN]'
        , '[AZMapping-Merged].[ProductCatalogId]'
        , '[AZMapping-Merged].[BrandSell]'
        , '[AZMSKU].DARTFBASKU'
		, '[AZMSKU].DARTFBMSKU'
		, '[AZMSKU].DARTMFPSKU');

        $where .= $this->MCommon->concatAllWerefields($wherefields, $search);


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


        $SQL = "WITH mytable AS ($select , ROW_NUMBER() OVER (ORDER BY {$sidx} {$sord}) AS RowNumber
							FROM {$table}{$join}{$where})
		   {$selectSlice}, RowNumber
		   FROM mytable
			WHERE RowNumber BETWEEN {$start} AND {$finish}";

        $result = $this->MCommon->getSomeRecords($SQL);

        //	print_r($result);
        $responce = new stdClass();
        $responce->page = $page;
        $responce->total = $total_pages;
        $responce->records = $count;

        $i = 0;
        foreach ($result as $row) {
            $responce->rows[$i]['id'] = $row['RowNumber'];
            $responce->rows[$i]['cell'] = array($row['RowNumber'],
                $row['Date'],
                $row['RetainedASIN'],
                $row['RetainedSKU'],
                $row['RetainedBrand'],
                $row['MergedASIN'],
                $row['MergedSKU'],
                $row['MergedBrand'],
                $row['DartFBASKU'],
                $row['DartFBMSKU'],
                $row['DartMFPSKU'],
            );
            $i++;
        }

        echo json_encode($responce);
    }

    /*
     * CSV Export
     */

    function csvExport($name)
    {

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
