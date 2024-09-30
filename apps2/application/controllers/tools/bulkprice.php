<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class BulkPrice extends BP_Controller {

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

        $this->title = "MI Technologiesinc - Bulk Price Management";
        $this->description = "Supplier Pricing";
        $this->css = array('form.css', 'fluid.css', 'table.css', 'jqueryui/redmond/jquery-ui-1.8.21.custom.css', 'jqgrid/ui.jqgrid.css', 'menu.css',);

        // Define custom javascript
        $this->javascript = array('jqueryui/ui/jquery-ui-1.8.21.custom.js', 'jqgrid/i18n/grid.locale-en.js', 'jqgrid/jquery.jqGrid.min.js', 'jqgrid/jquery.jqGrid.fluid.js', 'site.js');

        $this->load->library('Layout');
        $menu = new Layout;

        $data = array( 
            'menu' => $menu->show_menu($this->MUsers->getUserFullName($this->session->userdata('userid'))),
            'from' => '/tools/bulkprice',
            'caption' => 'Bulk Price Management',
            'export' => 'BulkPriceManagement',
            'sort' => 'asc',
            'categoria' => $this->MCatalog->fillCustomerCategory(),
            'customer' => $this->MCatalog->fillCustomerName(),
            'baseUrl' => base_url(),
        );

        $data['colNames'] = "['ID','CustomerID','CategoryID','CustomerName','CategoryName', 'CostPlus%', 'AddAmount', 'DSEconomyDefault',
                                'DS2ndDayDefault','DS1DayDefault','IsActive']";

        $data['colModel'] = "[
                {name:'ID',index:'ID', width:50, align:'center', hidden: true},
                {name:'CustomerID',index:'CustomerID', width:50, align:'center', hidden: true},
                {name:'CategoryID',index:'CategoryID', width:50, align:'center', hidden: true},
                {name:'CustomerName',index:'CustomerName', width:65, align:'left', sorttype: 'text'},
                {name:'CategoryName',index:'CategoryName', width:50, align:'left', sorttype: 'text'},
                {name:'CostPlus',index:'CostPlus', width:25, align:'center', editable:true, sorttype: 'int',editrules: { number: true, minValue: 15.00},formatter:'currency', formatoptions:{decimalSeparator:'.', thousandsSeparator: ',', decimalPlaces: 1, suffix: ' %'}},  
                {name:'AddAmount',index:'AddAmount', width:25, align:'center', editable:true, sorttype: 'int',editrules:{number:true},formatter:'currency', formatoptions:{decimalSeparator:'.', thousandsSeparator: ',', decimalPlaces: 2, prefix: '$ '}},
                {name:'DSEconomyDefault',index:'DSEconomyDefault', width:25, align:'center', editable:true,sorttype: 'int',editrules:{number:true},formatter:'currency', formatoptions:{decimalSeparator:'.', thousandsSeparator: ',', decimalPlaces: 2, prefix: '$ '}},  
                {name:'DS2ndDayDefault',index:'DS2ndDayDefault', width:25, align:'center', editable:true, sorttype: 'int', editrules:{number:true},formatter:'currency', formatoptions:{decimalSeparator:'.', thousandsSeparator: ',', decimalPlaces: 2, prefix: '$ '}}, 
                {name:'DS1DayDefault',index:'DS1DayDefault', width:25, align:'center', editable:true, sorttype: 'int', editrules:{number:true},formatter:'currency', formatoptions:{decimalSeparator:'.', thousandsSeparator: ',', decimalPlaces: 2, prefix: '$ '}}, 
                {name:'IsActive',index:'IsActive', width:25, align:'center', formatter: checkboxFormatter,edittype: 'checkbox'}, 
                
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

        $cust = $this->input->get('cu');
        $cate = $this->input->get('ca');

        //Select utilizado para realizar el conteo del numero de registros devueltos por la consulta.
        $selectCount = 'SELECT COUNT(*) AS rowNum';

        //Select General con los campos necesarios para la vista
        $select = " SELECT A.ID,
                            A.CustomerID, 
                            A.CategoryID, 
                            A.CustomerName,
                            A.CategoryName,
                            A.CostPlus,
                            A.AddAmount, 
                            A.DSEconomyDefault, 
                            A.DS2ndDayDefault, 
                            A.DS1DayDefault, 
                            A.IsActive ";

        $selectSlice = " SELECT ID,
                                CustomerID, 
                                CategoryID, 
                                CustomerName,
                                CategoryName,
                                CostPlus,
                                AddAmount, 
                                DSEconomyDefault, 
                                DS2ndDayDefault, 
                                DS1DayDefault, 
                                IsActive ";

        $from =  " FROM (
                    SELECT CSPM.[ID] 
                        ,CSPM.[CustomerID]
                        ,CSPM.[CategoryID]
                        ,CUST.FirstName + ' ' + CUST.LastName + ' (' + CUST.Company + ')' AS [CustomerName]
                        ,CAT.[Name] AS [CategoryName]
                        ,CSPM.[CostPlusPercent] AS [CostPlus]
                        ,CSPM.[AddAmount] AS [AddAmount]
                        ,CSPM.[DSEconomyDefault] AS [DSEconomyDefault]
                        ,CSPM.[DS2ndDayDefault] AS [DS2ndDayDefault]
                        ,CSPM.[DS1DayDefault] AS [DS1DayDefault]
                        ,CSPM.[IsActive] AS [IsActive]
                    FROM [Inventory].[dbo].[CustomerSpecificPricingManagement] AS CSPM
                    LEFT OUTER JOIN [OrderManager].[dbo].[Customers] AS CUST ON (CSPM.[CustomerID] = CUST.[CustomerID])
                    LEFT OUTER JOIN [Inventory].[dbo].[Categories] AS CAT ON (CSPM.[CategoryID] = CAT.[ID])
                ) A WHERE 1=1 ";

        $where ='';

        if($cust)
        { $where .= " and (A.[CustomerID] = '{$cust}') "; }

        if($cate)
        { $where .= " and (A.[CategoryID] = '{$cate}') "; }

        $SQL = "{$selectCount}{$from}{$where}";
        $result = $this->MCommon->getOneRecord($SQL);
     //   print_r($SQL);

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
            $responce->rows[$i]['id'] = $row['ID'];
            $responce->rows[$i]['cell'] = array($row['ID'],
                $row['CustomerID'],
                $row['CategoryID'],
                $row['CustomerName'] = utf8_encode($row['CustomerName']),
                $row['CategoryName'] = utf8_encode($row['CategoryName']),
                $row['CostPlus'],
                $row['AddAmount'],
                $row['DSEconomyDefault'],
                $row['DS2ndDayDefault'],
                $row['DS1DayDefault'],
                $row['IsActive'],
            );
            $i++;
        }
        echo json_encode($responce);
    }

    function editSupplier()
    {
        $sup = $this->input->post('id');

        if (isset($_POST['CostPlus'])) {
                $CostPlus = $_POST['CostPlus'];
                $SQL = "UPDATE [Inventory].[dbo].[CustomerSpecificPricingManagement] SET CostPlusPercent={$CostPlus} WHERE ID={$sup}";
        }

        if (isset($_POST['AddAmount'])) {
                $AddAmount = $_POST['AddAmount'];
                $SQL = "UPDATE [Inventory].[dbo].[CustomerSpecificPricingManagement] SET AddAmount='{$AddAmount}' WHERE ID={$sup}";
        }

        if (isset($_POST['DSEconomyDefault'])) {
                $DSEconomyDefault = $_POST['DSEconomyDefault'];
                $SQL = "UPDATE [Inventory].[dbo].[CustomerSpecificPricingManagement] SET DSEconomyDefault='{$DSEconomyDefault}' 
                WHERE ID={$sup}";
        }

        if (isset($_POST['DS2ndDayDefault'])) {
                $DS2ndDayDefault = $_POST['DS2ndDayDefault'];
                $SQL = "UPDATE [Inventory].[dbo].[CustomerSpecificPricingManagement] SET DS2ndDayDefault='{$DS2ndDayDefault}' 
                WHERE ID={$sup}";
        }

        if (isset($_POST['DS1DayDefault'])) {
                $DS1DayDefault = $_POST['DS1DayDefault'];
                $SQL = "UPDATE [Inventory].[dbo].[CustomerSpecificPricingManagement] SET DS1DayDefault='{$DS1DayDefault}' WHERE ID={$sup}";
        }

        if (isset($_POST['checkbox'])) {
               $name = ($_POST['checkbox']);
               $value = ($_POST['value']);

               $value = ($_POST['value'] === 'true') ? 1:0;

               $SQL = "UPDATE [Inventory].[dbo].[CustomerSpecificPricingManagement] set $name='{$value}' where ID={$sup}";
        }
          //print_r($SQL);
        $this->MCommon->saveRecord($SQL,'Inventory');
    }

    function updateCustomer()
    {
        $CustomerID = $this->input->post('supl');

        $select = " EXECUTE [Inventory].[dbo].[sp_CustomerSpecificPricingManagement-Update] {$CustomerID};";

        //print_r($select);
        $this->db->query($select);

        return 'true';
    }

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
