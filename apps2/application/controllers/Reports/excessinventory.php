<?php                                                                                                                    
                                                                                                                         
if (!defined('BASEPATH'))                                                                                                
    exit('No direct script access allowed');                                                                             
                                                                                                                         
class Excessinventory extends BP_Controller {                                                                            
                                                                                                                         
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
        $this->title = "MI Technologiesinc - ExcessInventory";                                                           
                                                                                                                         
        $this->description = "ExcessInventory";                                                                          
                                                                                                                         
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
            'from' => 'Reports/excessinventory/',
            'administrator' => $this->MUsers->isadminuser($this->session->userdata('userid')),
            'productLineOptions' => $this->MCatalog->fillProductLines(),
            'datefrom' => $this->MCommon->fixInitialDate(date('m/d/Y', strtotime("-1 week")), 0),
            'dateto' => $this->MCommon->fixFinalDate(date("m/d/Y"), 0),
        );

        $data['caption'] = 'Excess Inventory Period: ' . $data['datefrom'] . ' - ' . $data['dateto'];


        $data['colNames'] = "['SKU','Manufacturer','InvStart','InvTotal<br>Added','InvTotal<br>Removed','InvCurrent','AvgRemoved<br>PerDay','TargetStock<br>InDays',
                              'DaysUntil<br>WeRunOut','InvAdded<br>AuditInput','InvAddedLamp<br>Production','InvAdded<br>Assembly','InvAdded<br>RMAReturn',
                              'InvAdded<br>FBAReturn','InvAdded<br>PurchaseOrder','InvAdded<br>Other','InvRemoved<br>AuditAdj','InvRemoved<br>Order','Removed<br>Assembly',
                              'InvRemoved<br>RMAExchange','InvRemoved<br>FBAShipment','InvRemoved<br>POAdjustment','InvRemoved<br>Other']";
        $data['colModel'] = "[
                {name:'SKU',index:'SKU', width:55, align:'left',sorttype:'int', align:'center' },
                {name:'Manufacturer',index:'Manufacturer', width:100, align:'left'},
                {name:'InvStart',index:'InvStart', width:80, align:'right'},
                {name:'InvTotalAdded',index:'InvTotalAdded', width:80, align:'right'},
                {name:'InvTotalRemoved',index:'InvTotalRemoved', width:80, align:'right'},
                {name:'InvCurrent',index:'InvCurrent', width:80, align:'right'},
                {name:'AvgRemovedPerDay',index:'AvgRemovedPerDay', width:80, align:'right'},
                {name:'TargetStockInDays',index:'TargetStockInDays', width:80, align:'right'},
                {name:'DaysUntilWeRunOut',index:'DaysUntilWeRunOut', width:80, align:'right'},
                {name:'InvAddedAuditInput',index:'InvAddedAuditInput', width:80, align:'right'},
                {name:'InvAddedLampProduction',index:'InvAddedLampProduction', width:80, align:'right'},
                {name:'InvAddedAssembly',index:'InvAddedAssembly', width:80, align:'right'},
                {name:'InvAddedRMAReturn',index:'InvAddedRMAReturn', width:80, align:'right'},
                {name:'InvAddedFBAReturn',index:'InvAddedFBAReturn', width:80, align:'right'},
                {name:'InvAddedPurchaseOrder',index:'InvAddedPurchaseOrder', width:80, align:'right'},
                {name:'InvAddedOther',index:'InvAddedOther', width:80, align:'right'},
                {name:'InvRemovedAuditAdj',index:'InvRemovedAuditAdj', width:80, align:'right'},
                {name:'InvRemovedOrder',index:'InvRemovedOrder', width:80, align:'right'},
                {name:'RemovedAssembly',index:'RemovedAssembly', width:80, align:'right'},
                {name:'InvRemovedRMAExchange',index:'InvRemovedRMAExchange', width:80, align:'right'},
                {name:'InvRemovedFBAShipment',index:'InvRemovedFBAShipment', width:80, align:'right'},
                {name:'InvRemovedPOAdjustment',index:'InvRemovedPOAdjustment', width:80, align:'right'},
                {name:'InvRemovedOther',index:'InvRemovedOther', width:80, align:'right'},
        ]";


        //Generar el contenido....
        $this->build_content($data);
        $this->render_page();
    }


    function gridData() {
        //Variables que envia el grid, incializarlas si estan vacias
        $page = !empty($_REQUEST['page']) ? $_REQUEST['page'] : 1; // get the requested page
        $limit = !empty($_REQUEST['rows']) ? $_REQUEST['rows'] : 10; // get how many rows we want to have into the gri
        $sidx = !empty($_REQUEST['sidx']) ? $_REQUEST['sidx'] : 'PC.ID'; // get index row - i.e. user click to sort
        $sord = !empty($_REQUEST['sord']) ? $_REQUEST['sord'] : 'desc';
        $examp = isset($_REQUEST['q']) ? $_REQUEST['q'] : 1;  //query number
        //variables que envia el formulario inicializarlas si no existen

        $search = $this->input->get('search');
        $datefrom =$this->input->get('from');
        $dateto=$this->input->get('to');
        $category =$this->input->get('category');
      

        // -----OOOOJOOOO
      // Validar fechas en vista
      // 
      // 
      // 
      // 
      // 

        $select = " SELECT DISTINCT PC.ID AS id
            ,PC.Manufacturer As [Manufacturer] 
            ,PC.TargetInventory As [TargetStockInDays]
         ";

        $selectSlice = "SELECT  id as SKU
            ,PC.Manufacturer As [Manufacturer] 
            ,Inventory.dbo.[fn_Bins_Sku_Stock_At_Date](pc.id,'{$datefrom} 00:00:00') as [InvStart]
            ,Inventory.dbo.fn_bins_sum_qty(pc.id,'IN%','{$datefrom} 00:00:00','{$dateto} 23:59:59') as [InvTotalAdded]
            ,Inventory.dbo.fn_bins_sum_qty(pc.id,'OUT%','{$datefrom} 00:00:00','{$dateto} 23:59:59') as [InvTotalRemoved]
            ,Cast(Inventory.dbo.fn_Get_Total_Stock(PC.ID) as int) As [InvCurrent]

            ,(Select IsNull(Sum(BH2.Qty),'0') FROM [Inventory].[dbo].[Bins_History] AS BH2 WHERE BH2.Product_Catalog_ID = pc.id AND BH2.ScanCode Like 'OUT%' AND BH2.Stamp BETWEEN '{$datefrom} 00:00:00' AND '{$dateto} 23:59:59')/DATEDIFF(day,'{$datefrom} 00:00:00','{$dateto} 23:59:59') AS [AvgRemovedPerDay]
            ,[TargetStockInDays]
            ,CEILING(
            Inventory.dbo.fn_Get_Total_Stock(PC.ID)/
                (
                    Case Inventory.dbo.fn_bins_sum_qty(pc.id,'OUT','{$datefrom} 00:00:00','{$dateto} 23:59:59')
                        when 0 then 0.001
                        else Inventory.dbo.fn_bins_sum_qty(pc.id,'OUT','{$datefrom} 00:00:00','{$dateto} 23:59:59')
                    End
                )
                /  DATEDIFF(day,'{$datefrom} 00:00:00','{$dateto} 23:59:59')
                )
                As [DaysUntilWeRunOut]
            ,'AddedDetails---->' As [AddedDetails---->] 
            , Inventory.dbo.fn_bins_sum_qty(pc.id,'IN50','{$datefrom} 00:00:00','{$dateto} 23:59:59') as [InvAddedAuditInput]
            , Inventory.dbo.fn_bins_sum_qty(pc.id,'IN51','{$datefrom} 00:00:00','{$dateto} 23:59:59') as [InvAddedLampProduction]
            , Inventory.dbo.fn_bins_sum_qty(pc.id,'IN52','{$datefrom} 00:00:00','{$dateto} 23:59:59') as [InvAddedAssembly]
            , Inventory.dbo.fn_bins_sum_qty(pc.id,'IN53','{$datefrom} 00:00:00','{$dateto} 23:59:59') as [InvAddedRMAReturn]
            , Inventory.dbo.fn_bins_sum_qty(pc.id,'IN54','{$datefrom} 00:00:00','{$dateto} 23:59:59') as [InvAddedFBAReturn]
            , Inventory.dbo.fn_bins_sum_qty(pc.id,'IN55','{$datefrom} 00:00:00','{$dateto} 23:59:59') as [InvAddedPurchaseOrder]
            , Inventory.dbo.fn_bins_sum_qty(pc.id,'IN56','{$datefrom} 00:00:00','{$dateto} 23:59:59') as [InvAddedOther]
            ,'RemovedDetails---->' As [RemovedDetails---->]
            ,Inventory.dbo.fn_bins_sum_qty(pc.id,'OUT51','{$datefrom} 00:00:00','{$dateto} 23:59:59') as[InvRemovedAuditAdj]
            ,Inventory.dbo.fn_bins_sum_qty(pc.id,'OUT52','{$datefrom} 00:00:00','{$dateto} 23:59:59') as[InvRemovedOrder]
            ,Inventory.dbo.fn_bins_sum_qty(pc.id,'OUT53','{$datefrom} 00:00:00','{$dateto} 23:59:59') as[RemovedAssembly]
            ,Inventory.dbo.fn_bins_sum_qty(pc.id,'OUT54','{$datefrom} 00:00:00','{$dateto} 23:59:59') as[InvRemovedRMAExchange]
            ,Inventory.dbo.fn_bins_sum_qty(pc.id,'OUT55','{$datefrom} 00:00:00','{$dateto} 23:59:59') as[InvRemovedFBAShipment]
            ,Inventory.dbo.fn_bins_sum_qty(pc.id,'OUT56','{$datefrom} 00:00:00','{$dateto} 23:59:59') as[InvRemovedPOAdjustment]
            ,Inventory.dbo.fn_bins_sum_qty(pc.id,'OUT57','{$datefrom} 00:00:00','{$dateto} 23:59:59') as[InvRemovedOther]
        ";


        $from = ' FROM ';
        $table = ' [Inventory].[dbo].[ProductCatalog] As PC';
        $where = '';

        //Obtenemos todos los nombres de campos de la tabla productCatalog
        $wherefields = array('PC.ID','PC.Manufacturer','PC.TargetInventory');
        //creamos el were concatenando todos los nombres de campos y las palabras de busqueda
        $where .=$this->MCommon->concatAllWerefields($wherefields, $search);


        //Agregamos al where la busqueda por categoria
        if ($category){
            $where .=" and PC.ProductLineID = {$category} "; 
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


         $SQL = "WITH mytable AS ({$select}, ROW_NUMBER() OVER (ORDER BY {$sidx} {$sord}) AS RowNumber
                                 FROM {$table}{$where})
                     {$selectSlice}, RowNumber
                FROM mytable PC
                        WHERE RowNumber BETWEEN {$start} AND {$finish} order by SKU";
        
        

         $result = $this->MCommon->getSomeRecords($SQL);
        
       

        $responce = new stdClass();
        $responce->page = $page;
        $responce->total = $total_pages;
        $responce->records = $count;
        $i = 0;




        foreach ($result as $row) {
           $responce->rows[$i]['id'] = $row['SKU'];
           $responce->rows[$i]['cell'] = array($row['SKU'],
           $row['Manufacturer'],
           $row['InvStart'],
           $row['InvTotalAdded'],
           $row['InvTotalRemoved'],
           $row['InvCurrent'],
           $row['AvgRemovedPerDay'],
           $row['TargetStockInDays'],
           $row['DaysUntilWeRunOut'],
           $row['InvAddedAuditInput'],
           $row['InvAddedLampProduction'],
           $row['InvAddedAssembly'],
           $row['InvAddedRMAReturn'],
           $row['InvAddedFBAReturn'],
           $row['InvAddedPurchaseOrder'],
           $row['InvAddedOther'],
           $row['InvRemovedAuditAdj'],
           $row['InvRemovedOrder'],
           $row['RemovedAssembly'],
           $row['InvRemovedRMAExchange'],
           $row['InvRemovedFBAShipment'],
           $row['InvRemovedPOAdjustment'],
           $row['InvRemovedOther'],
           );
           $i++;
       }
        echo json_encode($responce);
    }

    function csvExport() {

        header('Content-type: application/vnd.ms-excel');
        header("Content-Disposition: attachment; filename=ExcessInv-" . date("D-M-j") . ".xls");
        header("Pragma: no-cache");
        $buffer = $_POST['csvBuffer'];

        try {
            echo $buffer;
        } catch (Exception $e) {
            
        }
    }

}

?>
