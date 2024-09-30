<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Skumovdetailed extends BP_Controller {

    public function __construct() {
        parent::__construct();

        $is_logged_in = $this->session->userdata('is_logged_in');

        if (!isset($is_logged_in) || $is_logged_in != true) {
            redirect(base_url(), 'refresh');
        }
        
        // if ($this->MUsers->isValidUser($this->session->userdata('userid'), 883850) != 1) {
        //        redirect('Catalog/prodcat', 'refresh');
        //    }
    }

    public function index() {
        //Cargamos la base de datos de OrderManager
        $this->load->model('MOrders', '', TRUE);

        // Define Meta
        $this->title = "MI Technologiesinc - SKU Mov Detailed";

        $this->description = "SKU Mov Detailed"; 

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
            'from' => '/Reports/binqty/',
            'caption' => 'Mov Detailed',
            'search' => '',
            'datefrom' => $this->MCommon->lastweek(),
            'dateto' => date("m/d/Y"),
           // 'categories' => '0',
            'categoriesOptions' => $this->MCatalog->fillCategories3(),
            'transferReasons' => $this->MCatalog->getTransferReasons(),
        );

        //Cargamos la libreria comumn
        $this->load->library('common');

        //Cargamos al arreglo los colnames
        $data['colNames'] = "['SKU','Name', 'Stamp','Bin_Id','Location','Qty','FlowCaption','ScanCode','UserName','Comments']";
        //Cargamos al arreglo los colmodels


        $this->common->addModel('Product_Catalog_Id')->addWidth(60)->addAlign('center')->addNlCr()
                     ->addModel('Name')->addWidth(120)->addAlign('left')->addNlCr()
                     ->addModel('Stamp')->addWidth(90)->addAlign('center')->addNlCr()
                     ->addModel('Bin_Id')->addWidth(80)->addAlign('center')->addNlCr()
                     ->addModel('Location')->addWidth(80)->addAlign('center')->addNlCr()
                     ->addModel('Qty')->addWidth(50)->addAlign('center')->addNlCr()
                     ->addModel('FlowCaption')->addWidth(50)->addAlign('center')->addNlCr()
                     ->addModel('ScanCode')->addWidth(80)->addAlign('center')->addNlCr()
                     ->addModel('UserName')->addWidth(100)->addAlign('center')->addNlCr()
                     ->addModel('Comments')->addWidth(100)->addAlign('center')->addNlCr();
    
        $data['colModel'] = $this->common->getModel();

        //Generar el contenido....
       $this->build_content($data);
       $this->render_page();
    }

    function getData() {
        $page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1; // get the requested page
        $limit = isset($_REQUEST['rows']) ? $_REQUEST['rows'] : 10; // get how many rows we want to have into the gri
        $sidx = isset($_REQUEST['sidx']) ? $_REQUEST['sidx'] : 'Product_Catalog_Id'; // get index row - i.e. user click to sort
        $sord = isset($_REQUEST['sord']) ? $_REQUEST['sord'] : 'asc';

       // $dataSearch = isset($_REQUEST['ds']) ? $_GET['ds'] : '';


    //Corregimos las fechas para ajustar el tiempo
   // $InitialDateFixed = $this->MCommon->fixInitialDate($InitialDate, 1);


        $search = $this->input->get('search');
        $datefrom = $this->input->get('from');
        $scanCode = $this->input->get('scanCode');
        $dateto = $this->input->get('to');
        $flow= $this->input->get('flow');
        $category= $this->input->get('category');
        $reason= $this->input->get('reason');

        // $InitialDate = isset($from) ? $datefrom : $this->MCommon->lastweek();
        // $FinalDate = isset($to) ?  $dateto  : date("m/d/Y");
    

        $table = ' Inventory.dbo.Bins_History';
        $join = ' LEFT OUTER JOIN Inventory.dbo.All_List b
                  ON b.listvalue = a.ScanCode ';
        $where = '';
        $from = ' FROM ';
        $orderby= ' Order By SKU Asc';
	
	

       $select = "SELECT Product_Catalog_Id
                        ,Stamp
                        ,Bin_Id
                        ,Qty
                        ,Flow
                        ,ScanCode
                        ,User_Id
                        ,[Inventory].[dbo].fn_GetCategoryName([Inventory].[dbo].fn_GetCategoryId(Product_Catalog_ID)) as category_name
                        ,Comments
                  ";

	
        $selectSlice = "SELECT Product_Catalog_Id
                              ,[Inventory].[dbo].fn_GetProductName(Product_Catalog_Id) as Name
                              ,Stamp
                              ,Bin_Id
                              ,[inventory].[dbo].fn_Bins_BinLocation(Bin_Id) as Location
                              ,Qty
                              ,Flow
                              ,CASE When Flow = 1 and ScanCode not like 'TR%' then 'IN' WHEN Flow = 2 AND ScanCode not like 'TR%' THEN 'OUT' 
                                When Flow = 1 and ScanCode like 'TR%' Then 'TRN IN' When Flow = 2 and ScanCode like 'TR%' Then 'TRN OUT' END as FlowCaption
                              ,b.[listdisplay] AS ScanCode
                              ,User_Id
                              ,[Inventory].[dbo].fn_GetUserName(User_Id) as UserName
                              ,category_name
                              ,Comments
                       ";

        $wherefields = array('Product_Catalog_Id');

        $where .=$this->MCommon->concatAllWerefields($wherefields, $search);



        $where.= " and (Stamp between '{$datefrom} 00:00:00' AND '{$dateto} 23:59:59')";



        
        if ($scanCode){
            $where .= " AND ([ScanCode]  LIKE '%{$scanCode}%')";
        }

         if ($flow){
            $where .= " AND ([ScanCode]  LIKE '%{$flow}%')";
        }

        if ($category){
            switch($category)
            {
                case 10000:  $where  .= " AND ([Inventory].[dbo].fn_GetCategoryId(Product_Catalog_ID) between 39 and 48) ";
                         break;
                case 20000:  $where  .= " AND ([Inventory].[dbo].fn_GetCategoryId(Product_Catalog_ID) in (69,72,70,49,50,51,52,58,53,54,55,56,57) ) ";
                        break;
                default: $where  .= " AND ([Inventory].[dbo].fn_GetCategoryId(Product_Catalog_ID)  = $category ) ";
                        break;
            }
            
        }

        if ($reason){
            $where .= " AND ([ScanCode] = '$reason')";
        }



        $SQL = "{$select}{$from}{$table}{$where}";

       

   
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
        $start = ($start < 0) ? 0 : $start;
        $finish = $start + $limit;

      //  echo $total_pages;

        $SQL = "WITH mytable AS ({$select}, ROW_NUMBER() OVER (ORDER BY {$sidx} {$sord}) AS RowNumber
                                FROM {$table}{$where})
               {$selectSlice}, RowNumber
               FROM mytable a {$join}
                WHERE RowNumber BETWEEN {$start} AND {$finish} ";
	
   // echo $SQL;

       $result = $this->MCommon->getSomeRecords($SQL);



        $responce = new stdClass();
        $responce->page = $page;
        $responce->total = $total_pages;
        $responce->records = $count;
        $i = 0;

        foreach ($result as $row) {
            $responce->rows[$i]['id'] = $row['RowNumber'];
            $responce->rows[$i]['cell'] = array($row['Product_Catalog_Id'],
                $row['Name'],
                $row['Stamp'],
                $row['Bin_Id'],
                $row['Location'],
                $row['Qty'],
                $row['FlowCaption'],
		        $row['ScanCode'],
                $row['UserName'],
                $row['Comments'],
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

    function adjustemenIDData() {
        $this->load->model('morders', '', TRUE);
        $adjustmentID = isset($_REQUEST['aid']) ? $_GET['aid'] : '';
        $page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1; // get the requested page
        $limit = isset($_REQUEST['rows']) ? $_REQUEST['rows'] : 10; // get how many rows we want to have into the gri
        $sidx = isset($_REQUEST['sidx']) ? $_REQUEST['sidx'] : 'InventoryAdjustmentsID'; // get index row - i.e. user click to sort
        $sord = isset($_REQUEST['sord']) ? $_REQUEST['sord'] : 'Desc';


        $totalrows = isset($_REQUEST['totalrows']) ? $_REQUEST['totalrows'] : false;
        if ($totalrows) {
            $limit = $totalrows;
        }

        if (!$sidx)
            $sidx = 1;


        $SQL = "SELECT ProductCatalogID,
		       ProductName,
                       Quantity,
                       UserName,
		       Comments
		      FROM [Inventory].[dbo].[vw_InventoryDetails]
			where  InventoryAdjustmentsID = {$adjustmentID} 
		    ";


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
        $start = ($start < 0) ? 0 : $start;

        $responce = new stdClass();
        $responce->page = $page;
        $responce->total = $total_pages;
        $responce->records = $count;


        $i = 0;

        foreach ($result as $row) {
            $responce->rows[$i]['id'] = $row['ProductCatalogID'];
            $responce->rows[$i]['cell'] = array($row['ProductCatalogID'],
             
		$row['ProductName'],
                $row['Quantity'],
                $row['UserName'] = utf8_encode($row['UserName']),
		$row['Comments'] = utf8_encode($row['Comments']),
            );
            $i++;
        }


        echo json_encode($responce);
    }

}
?>




