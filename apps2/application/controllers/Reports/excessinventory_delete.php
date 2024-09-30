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
            'productLines' => '',
            'from' => 'Reports/excessinventory/',
            'administrator' => $this->MUsers->isadminuser($this->session->userdata('userid')),
            'productLineOptions' => $this->MCatalog->fillProductLines(),
            'datefrom' => $this->MCommon->fixInitialDate(date('m/d/Y', strtotime("-1 month")), 0),
            'dateto' => $this->MCommon->fixFinalDate(date("m/d/Y"), 0),
        );

        $data['caption'] = 'Excess Inventory Period: ' . $data['datefrom'] . ' - ' . $data['dateto'];


        $data['colNames'] = "['SKU','Manufacturer','Stock at the<br>begining','Stock<br>Added','Stock<br>at the End','Current Stock','Removed Stock','AVG Stock Removed <br>by Day','Remaining<br>Days of Stock','Target Stock<br>in Days','Days<br>out Stock','% Days<br> out Stock','Excess<br>Invent<br>Count']";
        $data['colModel'] = "[
                {name:'ID',index:'ID', width:55, align:'left',sorttype:'int', align:'center' },
                {name:'Manufacturer',index:'Manufacturer', width:90, align:'center'},
                {name:'StartPeriodInv',index:'StartPeriodInv', width:80, align:'right'},
                {name:'InvAdded',index:'InvAdded', width:80, align:'right'},
                {name:'StartInvPlusInvAdded',index:'StartInvPlusInvAdded', width:80, align:'right'},
                {name:'CurrentStock',index:'CurrentStock', width:80, align:'right'},
                {name:'Sold',index:'Sold', width:80, align:'right'},
                {name:'SoldPerDay',index:'SoldPerDay', width:80, align:'right'},
                {name:'DaysInv',index:'DaysInv', width:80, align:'right'},
                {name:'TargetInvDays',index:'TargetInvDays', width:80, align:'right'},
                {name:'ExcessInvDays',index:'ExcessInvDays', width:80, align:'right'},
                {name:'ExcessInvPercent',index:'ExcessInvPercent', width:80, align:'right'},
                {name:'ExcessInvCount',index:'ExcessInvCount', width:80, align:'right',hidden:true},
        ]";


        //Cargamos la libreria comumn
        $this->load->library('common');

        //Reemplazamos los campos del formularion por los que estan en el arreglo $_POST.
        $data = $this->common->fillPost('ALL', $data);

        //cadena de busequeda para el grid
        $data['gridSearch'] = '/Reports/excessinventory/gridDataExcessInvent?search=' . $data['search'] . '&pl=' . $data['productLines'] . '&df=' . $data['datefrom'] . '&dt=' . $data['dateto'];

        //Generar el contenido....
        $this->build_content($data);
        $this->render_page();
    }

    /*
     *
     */

    function StartInvPlusInvAdded($StartPeriodInv, $InvAdded) {

        $result = $StartPeriodInv + $InvAdded;
        return $result;
    }

    function EndPeriodInv($StartInvPlusInvAdded, $Sold) {
        $result = $StartInvPlusInvAdded - $Sold;
        return $result;
    }

    function SoldPerDay($Sold, $nodays) {

        $result = $Sold / $nodays;
        return $result;
    }

    function DaysInv($EndPeriodInv, $SoldPerDay) {

        if (($EndPeriodInv > 0) and ($SoldPerDay > 0)) {
            $result = $EndPeriodInv / $SoldPerDay;
        } else {
            $result = 0;
        }
        
        return $result;
    }



    function ExcessInvDays($DaysInv, $TargetInvDays) {
        
        $days = explode(" ", $DaysInv);
        if (($DaysInv > 0) and ($TargetInvDays > 0)) {
             $result =  $TargetInvDays - $days[0];
        } else {
            $result = 0;
        }
        
        return $result;
    }

    function excessInvPercent($TargetInvDays, $ExcessDays) {

        if (($TargetInvDays <> 0) and ($ExcessDays <> 0)) {
           $excessInvpercent =    ($ExcessDays * 100) / $TargetInvDays;
        } else {
            $excessInvpercent = 0;
        }

        return $excessInvpercent;
    }

    
    function excessInvCount($EndPeriodInventory, $ExcessInvPercent) {


        $ExcessInv = explode(" ", $ExcessInvPercent);

        if (($ExcessInv <> 0)) {
            $excessInvCount = ceil(($EndPeriodInventory * ($ExcessInv[0] / 100)));
        } else {
            $excessInvCount = 0;
        }

        return $excessInvCount;
    }

    function calcInventory($sku, $date) {


        $in = " SELECT SUM( b.Quantity) as stock FROM InventoryAdjustments a,  InventoryAdjustmentDetails b
                   WHERE ( a.ID = b.InventoryAdjustmentsID )
                   AND (a.Flow = 1)
                   AND ( b.ProductCatalogID = {$sku})
                   AND (a.date <= '{$date}')";
        $resultin = $this->Mcatalog->getOneRecord($in);


        $out = "SELECT SUM( b.Quantity) as stock
                   FROM InventoryAdjustments a,  InventoryAdjustmentDetails b
                   WHERE ( a.ID = b.InventoryAdjustmentsID )
                   AND (a.Flow = 2)
                   AND ( b.ProductCatalogID = {$sku})
                   AND (a.date <= '{$date}')";
        $resultout = $this->Mcatalog->getOneRecord($out);

        $counter = $resultin['stock'] - $resultout['stock'];

        return($counter);
    }

    function gridDataExcessInvent() {
        //Variables que envia el grid, incializarlas si estan vacias
        $page = !empty($_REQUEST['page']) ? $_REQUEST['page'] : 1; // get the requested page
        $limit = !empty($_REQUEST['rows']) ? $_REQUEST['rows'] : 10; // get how many rows we want to have into the gri
        $sidx = !empty($_REQUEST['sidx']) ? $_REQUEST['sidx'] : 'ID'; // get index row - i.e. user click to sort
        $sord = !empty($_REQUEST['sord']) ? $_REQUEST['sord'] : 'asc';
        $examp = isset($_REQUEST['q']) ? $_REQUEST['q'] : 1;  //query number
        //variables que envia el formulario inicializarlas si no existen
        $search = !empty($_GET['search']) ? $_REQUEST['search'] : '';
        $productLine = !empty($_REQUEST['pl']) ? $_REQUEST['pl'] : '0';
        $InitialDate = isset($_GET['df']) ? $_GET['df'] : $this->MCommon->fixInitialDate(date('m/d/Y', strtotime("-1 month")), 1);
        $FinalDate = date("m/d/Y");

        if (($_GET['df']) > ($_GET['dt'])) {
            $InitialDate = isset($_GET['df']) ? $_GET['df'] : $this->MCommon->fixInitialDate(date('m/d/Y', strtotime("-1 month")), 1);
            $FinalDate = isset($_GET['dt']) ? $_GET['dt'] : $date("m/d/Y");
        }


        $nodays = $this->MCommon->calcDays($FinalDate, $InitialDate);


        //Select utilizado para realizar el conteo del numero de registros devueltos por la consulta.
        $selectCount = 'Select count(*) as rowNum';

        //Select General con los campos necesarios para la vista
        $select = "select ID, 
                Manufacturer,
                0 as StartPeriodInv,
                0 as InvAdded";
               // 0 as Sold";

        //Se utiliza en la seccion que extrae los registros de la hoja activa
        $selectSlice = "select ID,
                Manufacturer,
                Inventory.dbo.fn_StockAtDate(ID,'$InitialDate') as StartPeriodInv,
                Inventory.dbo.fn_adjustmentsBetweenDates(ID,'{$InitialDate}','{$FinalDate} 11:59:59',1) as InvAdded,
                Inventory.dbo.fn_Get_Global_Stock(ID) as CurrentStock";
             //   Inventory.dbo.fn_adjustmentsBetweenDates(ID,'{$InitialDate}','{$FinalDate}',2) as Sold";

        $from = ' from ';
        $table = 'ProductCatalog';
        $where = '';

        //Obtenemos todos los nombres de campos de la tabla productCatalog
        $fields = $this->MCommon->getAllfields($table);
        //creamos el were concatenando todos los nombres de campos y las palabras de busqueda
        $where .=$this->MCommon->concatAllWerefields($fields, $search);
        //Agregamos al where la busqueda por categoria
        $where .=$this->MCatalog->filterByCategory($productLine);

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


        $SQL = "WITH mytable AS ({$select}, ROW_NUMBER() OVER (ORDER BY {$sidx} {$sord}) AS RowNumber
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
        $TotStartPeriodInv = 0;
        $TotInvAdded = 0;
        $StartInvPlusInvAdded = 0;
        $EndPeriodInv = 0;
        $Sold = 0;
        $SoldPerDay = 0;
        $ExcessInvCount = 0;
        foreach ($result as $row) {
            $row['StartInvPlusInvAdded'] = $this->StartInvPlusInvAdded($row['StartPeriodInv'], $row['InvAdded']);
          //  $row['EndPeriodInv'] = $this->EndPeriodInv($row['StartInvPlusInvAdded'], $row['Sold']);
            
            $row['Sold'] = ($row['StartPeriodInv'] + $row['InvAdded']) - $row['CurrentStock'];
            $row['SoldPerDay'] = $this->SoldPerDay($row['Sold'], $nodays);
           // $row['SoldPerDay'] = (($row['StartPeriodInv'] + $row['InvAdded']) - $row['CurrentStock']) / $nodays;
            
            $row['DaysInv'] = $this->DaysInv($row['CurrentStock'], $row['SoldPerDay']);
//$row['DaysInv'] = $this->DaysInv($row['EndPeriodInv'], $nodays);
            $row['TargetInvDays'] = 10;
            $row['ExcessInvDays'] = $this->ExcessInvDays($row['DaysInv'], $row['TargetInvDays']);
            $row['ExcessInvPercent'] = $this->excessInvPercent($row['TargetInvDays'], $row['ExcessInvDays']);
            $row['ExcessInvCount'] = $this->excessInvCount($row['CurrentStock'], $row['ExcessInvPercent']);

            $TotStartPeriodInv += $row['StartPeriodInv'];
            $TotInvAdded += $row['InvAdded'];
            $StartInvPlusInvAdded += $row['StartInvPlusInvAdded'];
            $EndPeriodInv += $row['CurrentStock'];
            $Sold += $row['Sold'];
            $SoldPerDay += $row['SoldPerDay'];
            $ExcessInvCount += $row['ExcessInvCount'];
            $responce->rows[$i]['ID'] = $row['ID'];
            $responce->rows[$i]['cell'] = array($row['ID'],
                $row['Manufacturer'],
                number_format($row['StartPeriodInv']),
                number_format($row['InvAdded']),
                number_format($row['StartInvPlusInvAdded']),
                number_format($row['CurrentStock']),
                number_format($row['Sold']),
                number_format($row['SoldPerDay'], 1),
                round($row['DaysInv']),
                $row['TargetInvDays'],
                round($row['ExcessInvDays']),
                number_format($row['ExcessInvPercent'], 1) . " %",
                number_format($row['ExcessInvCount']),
            );
            $i++;
        }

        $responce->userdata['Manufacturer'] = 'Totals:';
        $responce->userdata['StartPeriodInv'] = number_format($TotStartPeriodInv);
        $responce->userdata['InvAdded'] = number_format($TotInvAdded);
        $responce->userdata['StartInvPlusInvAdded'] = number_format($StartInvPlusInvAdded);
        $responce->userdata['CurrentStock'] = number_format($EndPeriodInv);
        $responce->userdata['SoldPerDay'] = number_format($SoldPerDay);
        $responce->userdata['Sold'] = number_format($Sold);
        $responce->userdata['ExcessInvCount'] = number_format($ExcessInvCount);



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
