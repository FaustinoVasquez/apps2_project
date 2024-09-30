<?php                                                                                                                    
                                                                                                                         
if (!defined('BASEPATH'))                                                                                                
    exit('No direct script access allowed');                                                                             
                                                                                                                         
class Productreviewcalls extends BP_Controller {                                                                            
                                                                                                                         
    public function __construct() {                                                                                      
        parent::__construct();                                                                                           
                                                                                                                         
        $is_logged_in = $this->session->userdata('is_logged_in');                                                        
                                                                                                                         
        if (!isset($is_logged_in) || $is_logged_in != true) {                                                            
            redirect(base_url(), 'refresh');                                                                             
        }                                                                                                                
                                                                                                                         
        //  if ($this->MUsers->isValidUser($this->session->userdata('userid'), 883100) != 1) {                              
        //     redirect('Catalog/prodcat', 'refresh');                                                                      
        // }                                                                                                                
    }                                                                                                                    
                                                                                                                         
    function index() {    

        // Define Meta                                                                                                    
        $this->title = "MI Technologiesinc - Product Review Calls";                                                           
                                                                                                                         
        $this->description = "ProductReviewCalls";                                                                          
                                                                                                                         
        // Define custom CSS                                                                                             
        $this->css = array('menu.css', 'form.css', 'fluid.css', 'table.css', 'jqueryui/redmond/jquery-ui-1.8.21.custom.css', 'jqgrid/ui.jqgrid.css');

        // Define custom javascript
        $this->javascript = array('jqueryui/ui/jquery-ui-1.8.21.custom.js', 'jqgrid/i18n/grid.locale-en.js', 'jqgrid/jquery.jqGrid.min.js', 'jqgrid/jquery.jqGrid.fluid.js', 'popup.js');

        $this->load->library('Layout');
        $menu = new Layout;


        $data = array(
            'menu' => $menu->show_menu($this->MUsers->getUserFullName($this->session->userdata('userid'))),
            //Datos para el grid
            'search' => '',
            'from' => 'Reports/productreviewcalls/',
            'administrator' => $this->MUsers->isadminuser($this->session->userdata('userid')),
            'productLineOptions' => $this->MCatalog->fillProductLines(),
            'datefrom' => $this->MCommon->fixInitialDate(date('m/d/Y', strtotime("-1 week")), 0),
            'dateto' => $this->MCommon->fixFinalDate(date("m/d/Y"), 0),
        );

        $data['caption'] = 'ReviewCalls Period: ' . $data['datefrom'] . ' - ' . $data['dateto'];


        $data['colNames'] = "['OrderNumber','SourceOrderID','OrderDate','DateShipped','CustomerName','Address','Address2','ShipCity','State','Zip','ShipToPhone','SKU','Product','CartName','TrackingID','Comments']";
        $data['colModel'] = "[
                {name:'OrderNumber',index:'OrderNumber', width:80, align:'left',sorttype:'int', align:'center',frozen:true},
                {name:'SourceOrderID',index:'SourceOrderID', width:130, align:'left', frozen:true},
                {name:'OrderDate',index:'OrderDate', width:80, align:'center', frozen:true},
                {name:'DateShipped',index:'DateShipped', width:80, align:'center'},
                {name:'CustomerName',index:'CustomerName', width:180, align:'left'},
                {name:'Address',index:'Address', width:200, align:'left'},
                {name:'Address2',index:'Address2', width:200, align:'left'},
                {name:'ShipCity',index:'ShipCity', width:160, align:'left'},
                {name:'State',index:'State', width:80, align:'left'},
                {name:'Zip',index:'Zip', width:80, align:'left'},
                {name:'ShipToPhone',index:'ShipToPhone', width:100, align:'left',editable: true},
                {name:'SKU',index:'SKU', width:80, align:'center'},
                {name:'Product',index:'Product', width:290, align:'left'},
                {name:'CartName',index:'CartName', width:200, align:'center'},
                {name:'TrackingID',index:'TrackingID', width:140, align:'center'},
                {name:'Comments',index:'Comments', width:290, align:'left',editable: true},
        ]";


        //Generar el contenido....
        $this->build_content($data);
        $this->render_page();
    }


    function gridData() {
        //Variables que envia el grid, incializarlas si estan vacias
        $page = !empty($_REQUEST['page']) ? $_REQUEST['page'] : 1; // get the requested page
        $limit = !empty($_REQUEST['rows']) ? $_REQUEST['rows'] : 10; // get how many rows we want to have into the gri
        $sidx = !empty($_REQUEST['sidx']) ? $_REQUEST['sidx'] : 'O.[OrderNumber]'; // get index row - i.e. user click to sort
        $sord = !empty($_REQUEST['sord']) ? $_REQUEST['sord'] : 'desc';
        $examp = isset($_REQUEST['q']) ? $_REQUEST['q'] : 1;  //query number
        //variables que envia el formulario inicializarlas si no existen

        $search = $this->input->get('search');

      

        $select = " SELECT O.[OrderNumber] AS 'OrderNumber'
                              ,IsNull(O.[SourceOrderID],'') AS 'SourceOrderID'
                              ,CAST(O.[OrderDate] AS Date)  AS 'OrderDate'
                              ,CAST(TRK.[DateAdded] AS Date)  AS 'DateShipped'
                              ,O.[Name] AS 'CustomerName'
                              ,O.[ShipAddress] AS 'Address'
                              ,O.[ShipAddress2] AS 'Address2'
                              ,O.[ShipCity] AS 'ShipCity'
                              ,O.[ShipState] AS 'State'
                              ,O.[ShipZip] AS 'Zip'
                              ,O.[Phone] AS 'ShipToPhone'
                              ,OD.[SKU] AS 'SKU'
                              ,OD.[Product] AS 'Product'
                              ,CART.[CartName] AS 'CartName'
                              ,IsNull(TRK.[TrackingID],' ') AS 'TrackingID'
                              ,O.[Comments] AS 'Comments' ";

        $selectSlice = "SELECT  OrderNumber
                              ,SourceOrderID
                              ,OrderDate
                              ,DateShipped
                              ,CustomerName
                              ,Address
                              ,Address2
                              ,ShipCity
                              ,State
                              ,Zip
                              ,ShipToPhone
                              ,SKU
                              ,Product
                              ,CartName
                              ,TrackingID
                              ,Comments
        ";


        $from   = ' FROM ';
        $table  = ' [OrderManager].[dbo].[Orders] AS O';
        $join   = ' LEFT OUTER JOIN [OrderManager].[dbo].[Order Details] AS OD ON (O.[OrderNumber] = OD.[OrderNumber])
                   LEFT OUTER JOIN [OrderManager].[dbo].[ShoppingCarts] AS CART ON (O.[CartID] = CART.[ID])
                   LEFT OUTER JOIN [OrderManager].[dbo].[Tracking] AS TRK ON (O.[OrderNumber] = TRK.[NumericKey]) ';
        $where  = '';

        //Obtenemos todos los nombres de campos de la tabla productCatalog
        $wherefields = array('O.[OrderNumber]',',OD.[SKU]','OD.[Product]','O.[Name]');
        //creamos el were concatenando todos los nombres de campos y las palabras de busqueda
        $where .=$this->MCommon->concatAllWerefields($wherefields, $search);
        $where .= " AND (TRK.TrackingID is not null)
                    AND OD.[Adjustment] = 0
                    AND O.[OrderDate] BETWEEN GETDATE()-20 AND GETDATE()-3
                    AND OD.[Product] LIKE '%Lutema%'
                    AND (O.[Comments] IS NULL OR O.[Comments] LIKE '' OR O.[Comments] NOT LIKE '%R55%') ";


        $SQL = "{$select}{$from}{$table}{$join}{$where}";
  
	//print_r($SQL);

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


         $SQL = "WITH mytable AS ({$select}, ROW_NUMBER() OVER (ORDER BY {$sidx} {$sord}) AS RowNumber
                                 FROM {$table}{$join}{$where})
                     {$selectSlice}, RowNumber
                FROM mytable PC
                        WHERE  RowNumber BETWEEN {$start} AND {$finish} order by SKU";
        
        $result = $this->MCommon->getSomeRecords($SQL);
       

        $responce = new stdClass();
        $responce->page = $page;
        $responce->total = $total_pages;
        $responce->records = $count;
        $i = 0;


        foreach ($result as $row) {
           $responce->rows[$i]['id'] = $row['OrderNumber'];
           $responce->rows[$i]['cell'] = array($row['OrderNumber'],
           $row['SourceOrderID'],
           $row['OrderDate'],
           $row['DateShipped'],
           $row['CustomerName'] = utf8_encode($row['CustomerName']),
           $row['Address'] = utf8_encode($row['Address']),
           $row['Address2'] = utf8_encode($row['Address2']),
           $row['ShipCity'],
           $row['State'],
           $row['Zip'],
           $row['ShipToPhone'],
           $row['SKU'],
           $row['Product'] = utf8_encode($row['Product']),
           $row['CartName']= utf8_encode($row['CartName']),
           $row['TrackingID'],
           $row['Comments'] = utf8_encode($row['Comments']),
           );
           $i++;
       }
        echo json_encode($responce);
    }

    function csvExport() {

        header('Content-type: application/vnd.ms-excel');
        header("Content-Disposition: attachment; filename=ProducReview-" . ".xls");
        header("Pragma: no-cache");
        $buffer = $_POST['csvBuffer'];

        try {
            echo $buffer;
        } catch (Exception $e) {
            
        }
    }

    public function editData()
    {
     // $phone = $this->input->post('ShipToPhone');
     // $comments = ($this->input->post('Comments'))?$this->input->post('Comments'):'';
      $orderNumber = $this->input->post('id');


        if (isset($_POST['ShipToPhone'])) {
                $phone = $this->input->post('ShipToPhone');
                $SQL = "UPDATE [OrderManager].[dbo].[Orders] SET Phone='{$phone}' WHERE OrderNumber={$orderNumber}";
        }

        if (isset($_POST['Comments'])) {
                $comments = $this->input->post('Comments');
                $SQL = "UPDATE [OrderManager].[dbo].[Orders] SET Comments='{$comments}' WHERE OrderNumber={$orderNumber}";
        }

  
    //    echo $SQL;
      
      $this->db->query($SQL);

       return true;
    }

}

?>
