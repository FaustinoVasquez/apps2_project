<?

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Amazonfba extends BP_Controller {

    public function __construct() {
        parent::__construct();

        $is_logged_in = $this->session->userdata('is_logged_in');

        if (!isset($is_logged_in) || $is_logged_in != true) {
            redirect(base_url(), 'refresh');
        }

        if ($this->MUsers->isValidUser($this->session->userdata('userid'), 882200) != 1) {
            redirect('Catalog/prodcat', 'refresh');
        }
    }

    public function index() {


        // Define Meta
        $this->title = "MI Technologiesinc - Amazon FBA";

        $this->description = "Amazon FBA";

        // Define custom CSS
        $this->css = array('menu.css', 'form.css', 'fluid.css', 'jqueryui/redmond/jquery-ui-1.8.21.custom.css', 'jqgrid/ui.jqgrid.css');

        // Define custom javascript
        $this->javascript = array('jqueryui/ui/jquery-ui-1.8.21.custom.js', 'jqgrid/i18n/grid.locale-en.js', 'jqgrid/jquery.jqGrid.min.js', 'jqgrid/jquery.jqGrid.fluid.js', 'jqgrid/grid.base.js', 'jqgrid/grid.common.js', 'jqgrid/grid.formedit.js');
        //Cargamos la libreria donde esta el menu
        $this->load->library('Layout');
        //Creamos un nuevo layout->menu
        $menu = new Layout;

        $data = array(
            'menu' => $menu->show_menu($this->MUsers->getUserFullName($this->session->userdata('userid'))),
            'from' => '/tools/amazonfba/',
            'nameGrid' => 'amazonfba',
            'namePager' => 'amazonfbaPager',
            'caption' => 'Amazon FBA',
            'subgrid' => 'true',
            'sortorder' => 'asc',
            'sortname' => 'mitsku',
            'search' => '',
            'orderOptions' => Array(0 => "Products", 1 => "Pending Orders", 2 => "Complete Orders"),
            'selectedorder' => 0,
            'datefrom' => $this->MCommon->lastweek(),
            'dateto' => date("m/d/Y"),
            'export' => 'exportfba',
            'customerid' => $this->MUsers->getCustomerId($this->session->userdata('userid')),
        );

        //Cargamos la libreria comumn
        $this->load->library('common');

        //Reemplazamos los campos del formulario por los que estan en el arreglo $_POST.
        $data = $this->common->fillPost('ALL', $data);



        switch ($data['selectedorder']) {
            case 0:
                //Cargamos al arreglo los colnames
                $data['colNames'] = "['ASIN','Mit SKU','Bulb SKU','Merchant SKU', 'Fn SKU','Title','PartNumber']";

                //Cargamos al arreglo los colmodels
                $data['colModel'] = "[
                {name:'asin',index:'asin', width:70, align:'left',editable:true,editoptions:{size:25},formoptions:{ rowpos:1, label: 'Amazon ASIN', elmprefix:'(*)'},editrules:{required:true}},
                {name:'mitsku',index:'mitsku', width:55, align:'center', editable:false},
                {name:'bulbsku',index:'bulbsku', width:55, align:'center', editable:false},
                {name:'merchantsku',index:'merchantsku', width:70, align:'left', editable:false,editoptions:{size:25},formoptions:{ rowpos:4, label: 'MerchantSKU', elmprefix:'(*)'},editrules:{required:true}}, 
                {name:'fnsku',index:'fnsku', width:70, editable:true,editoptions:{size:25},formoptions:{ rowpos:3, label: 'Amazon FNSKU', elmprefix:'(*)'},editrules:{required:true} },
                {name:'title',index:'title', width:300, align:'left', editable:true,editoptions:{size:25},formoptions:{ rowpos:5, label: 'Amazon Desc:', elmprefix:'(*)'},editrules:{required:true}},
                {name:'partnumber',index:'partnumber', width:70,hidden:false, editable:true, edittype: 'custom', 
                editoptions: {'custom_element':function(value, options) 
                {return combobox_element(value, options,'120','partnumber','Klient')},'custom_value':function(elem, operation, value) {
                                 return $('input', $(elem)[0]).val();
                                 }
                                },
                formoptions:{ rowpos:2, label: 'Part Number:', elmprefix:'(*)'},editrules:{required:true}}]";
                //cadena de busqueda del grid 
                $data['gridSearch'] = 'dataProductsFBA?ds=' . $data['search'] . '&df=' . $data['datefrom'] . '&dt=' . $data['dateto'];
                break;

            case 1:

                $data['sortname'] = 'orderdate';
                $data['sortorder'] = 'desc';
                $data['colNames'] = "['OrderId','Mit SKU','Asin','FN Sku','Order Qty', 'Order Notes','Order Date','Bulb Sku','Title','Action']";
                //Cargamos al arreglo los colmodels
                $data['colModel'] = "[
                {name:'orderid',index:'orderid', width:60, align:'center'},
                {name:'mitsku',index:'mitsku', width:60, align:'center'},
                {name:'asin',index:'asin', width:80, align:'left'},
                {name:'fnsku',index:'fnsku', width:80, align:'left'}, 
                {name:'orderqty',index:'orderqty', width:50, align:'center'},
                {name:'ordernotes',index:'ordernotes', width:150, align:'left'},
                {name:'orderdate',index:'orderdate', width:70, align:'center',formatter:'date',formatoptions: {newformat:'F j, Y'}},
                {name:'bulbsku',index:'bulbsku', width:70, align:'center'},
                {name:'title',index:'title', width:300, align:'left',hidden:true},
                {name:'action',index:'action', width:50, align:'center',formatter: printticket}]";

                //cadena de busqueda del grid 
                $data['gridSearch'] = 'dataPendingOrderFBA?ds=' . $data['search'] . '&df=' . $data['datefrom'] . '&dt=' . $data['dateto'];

                break;
            case 2:
                //Cargamos al arreglo los colnames
                $data['sortname'] = 'orderdate';
                $data['sortorder'] = 'desc';
                //Cargamos al arreglo los colnames
                $data['colNames'] = "['OrderID','Mit SKU','ASIN','FN SKU', 'Order Qty','Order Notes','Order Date','Completed Date', 'Title','Tracking #','Completed?','Action']";
                //Cargamos al arreglo los colmodels
                $data['colModel'] = "[
                {name:'orderid',index:'orderid', width:60, align:'center'},
                {name:'mitsku',index:'mitsku', width:60, align:'center'},
                {name:'asin',index:'asin', width:80, align:'left'},
                {name:'fnsku',index:'fnsku', width:70, align:'left'}, 
                {name:'orderqty',index:'orderqty', width:50, align:'center'},
                {name:'ordernotes',index:'ordernotes', width:130, align:'left'},
                {name:'orderdate',index:'orderdate', width:80, align:'center',formatter:'date',formatoptions: {newformat:'F j, Y'}},
                {name:'completeddate',index:'completeddate', width:80, align:'center',formatter:'date',formatoptions: {newformat:'F j, Y'}}, 
                {name:'title',index:'title', width:300, align:'left',hidden:true},
                {name:'trackingnumber',index:'trackingnumber', width:80, align:'left'},
                {name:'completed',index:'completed', width:50, align:'center'},
                {name:'action',index:'action', width:50, align:'center',formatter: printticket}]";

                //cadena de busqueda del grid 
                $data['gridSearch'] = 'dataCompleteOrderFBA?ds=' . $data['search'] . '&df=' . $data['datefrom'] . '&dt=' . $data['dateto'];

                break;
        }




        //Generar el contenido....
        $this->build_content($data);
        $this->render_page();
    }

    function dataProductsFBA() {
        $page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1; // get the requested page
        $limit = isset($_REQUEST['rows']) ? $_REQUEST['rows'] : 10; // get how many rows we want to have into the gri
        $sidx = isset($_REQUEST['sidx']) ? $_REQUEST['sidx'] : 'a.mitsku'; // get index row - i.e. user click to sort
        $sord = isset($_REQUEST['sord']) ? $_REQUEST['sord'] : 'asc';

        $dataSearch = isset($_REQUEST['ds']) ? $_GET['ds'] : '';
        $status = '2';
        $cartid = isset($_REQUEST['caid']) ? $_GET['caid'] : '0';
        $customerid = isset($_GET['cuid']) ? $_GET['cuid'] : '';
        $InitialDate = isset($_REQUEST['df']) ? $_GET['df'] : $this->MCommon->lastweek();
        $FinalDate = isset($_REQUEST['dt']) ? $_GET['dt'] : date("m/d/Y");


        if (!$sidx)
            $sidx = 1;

        $table = 'AmazonFBA a';
        $table1 = 'ProductCatalog b';

        $where = '';
        $from = ' FROM ';
        $join = ' LEFT JOIN ' . $table1 . ' ON a.MITSKU=b.ID  ';


        $select = "SELECT a.asin, a.mitsku, b.CustomField04 AS bulbsku, a.merchantsku, a.fnsku, a.title ";


        $selectSlice = "SELECT asin,
                               mitsku,
                               bulbsku,
                               merchantsku,
                               fnsku,
                               title";

        $wherefields = array('a.ASIN', 'a.MITSKU', 'b.CustomField04', 'a.MerchantSKU', 'a.FNSKU', 'a.Title');

        $where .=$this->MCommon->concatAllWerefields($wherefields, $dataSearch);


        // $where .=' (((AmazonFBA.ASIN) Like '*' & Forms!AmazonFBASearch!SearchProducts & '*')) ';
        //   $where.= $this->MOrders->filterbyDate($InitialDate, $FinalDate);


        $SQL = "{$select}{$from}{$table}{$join}{$where}";



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
                FROM {$table}{$join}{$where} )
                    {$selectSlice}, RowNumber
               FROM mytable
                WHERE RowNumber BETWEEN {$start} AND {$finish}";

        // echo $SQL;

        $result = $this->MCommon->getSomeRecords($SQL);




        $responce->page = $page;
        $responce->total = $total_pages;
        $responce->records = $count;
        $i = 0;



        foreach ($result as $row) {
            $responce->rows[$i]['id'] = $row['asin'];
            $responce->rows[$i]['cell'] = array($row['asin'],
                $row['mitsku'],
                $row['bulbsku'],
                $row['merchantsku'],
                $row['fnsku'],
                $row['title'],
            );
            $i++;
        }

        echo json_encode($responce);
    }

    function dataPendingOrderFBA() {
        $page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1; // get the requested page
        $limit = isset($_REQUEST['rows']) ? $_REQUEST['rows'] : 10; // get how many rows we want to have into the gri
        $sidx = isset($_REQUEST['sidx']) ? $_REQUEST['sidx'] : 'orderdate'; // get index row - i.e. user click to sort
        $sord = isset($_REQUEST['sord']) ? $_REQUEST['sord'] : 'asc';

        $dataSearch = isset($_REQUEST['ds']) ? $_GET['ds'] : '';
        $status = '2';
        $cartid = isset($_REQUEST['caid']) ? $_GET['caid'] : '0';
        $customerid = isset($_GET['cuid']) ? $_GET['cuid'] : '';
        $InitialDate = isset($_REQUEST['df']) ? $_GET['df'] : $this->MCommon->lastweek();
        $FinalDate = isset($_REQUEST['dt']) ? $_GET['dt'] : date("m/d/Y");

        $FinalDate = $this->MCommon->fixFinalDate($FinalDate, 1);

        if (!$sidx)
            $sidx = 1;

        $table = 'AmazonFBA a';
        $table1 = 'AmazonFBAOrders b';
        $table2 = 'ProductCatalog c';

        $where = '';
        $from = ' FROM ';
        $join = ' LEFT JOIN ProductCatalog c ON a.mitsku=c.id   ';
        $fromjoin = " (AmazonFBA a RIGHT JOIN AmazonFBAOrders b ON  a.asin=b.asin) ";

        $select = "SELECT b.orderid, a.mitsku, a.asin, a.fnsku, b.orderqty, b.ordernotes, Cast(b.orderdate as date) as orderdate, c.customfield04 AS bulbsku,a.title ";


        $selectSlice = "SELECT orderid,
                               mitsku,
                               asin,
                               fnsku,
                               orderqty,
                               ordernotes,
                               orderdate,
                               bulbsku,
                               title";





        $wherefields = array('b.orderid', 'a.mitsku', 'a.asin', 'a.fnsku', 'b.orderqty', 'b.ordernotes', 'orderdate', 'c.customfield04', 'a.title');

        $where .=$this->MCommon->concatAllWerefields($wherefields, $dataSearch);

        $where.= "  and (orderdate between '{$InitialDate}' and '{$FinalDate}')";
        $where .= " and ((b.completed)='No') ";





        $SQL = "{$select}{$from}{$fromjoin}{$join}{$where}";



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
                FROM {$fromjoin}{$join}{$where} )
                    {$selectSlice}, RowNumber
               FROM mytable
                WHERE RowNumber BETWEEN {$start} AND {$finish}";

        //   echo $SQL;        
        $result = $this->MCommon->getSomeRecords($SQL);


        $responce->page = $page;
        $responce->total = $total_pages;
        $responce->records = $count;
        $i = 0;



        foreach ($result as $row) {
            $responce->rows[$i]['id'] = $row['orderid'];
            $responce->rows[$i]['cell'] = array($row['orderid'],
                $row['mitsku'],
                $row['asin'],
                $row['fnsku'],
                $row['orderqty'],
                $row['ordernotes'],
                $row['orderdate'],
                $row['bulbsku'],
                $row['title'],
            );
            $i++;
        }

        echo json_encode($responce);
    }

    function dataCompleteOrderFBA() {
        $page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1; // get the requested page
        $limit = isset($_REQUEST['rows']) ? $_REQUEST['rows'] : 10; // get how many rows we want to have into the gri
        $sidx = isset($_REQUEST['sidx']) ? $_REQUEST['sidx'] : 'orderdate'; // get index row - i.e. user click to sort
        $sord = isset($_REQUEST['sord']) ? $_REQUEST['sord'] : 'asc';

        $dataSearch = isset($_REQUEST['ds']) ? $_GET['ds'] : '';
        $status = '2';
        $cartid = isset($_REQUEST['caid']) ? $_GET['caid'] : '0';
        $customerid = isset($_GET['cuid']) ? $_GET['cuid'] : '';
        $InitialDate = isset($_REQUEST['df']) ? $_GET['df'] : $this->MCommon->lastweek();
        $FinalDate = isset($_REQUEST['dt']) ? $_GET['dt'] : date("m/d/Y");

        $FinalDate = $this->MCommon->fixFinalDate($FinalDate, 1);

        if (!$sidx)
            $sidx = 1;

        $table = 'AmazonFBA a';
        $table1 = 'AmazonFBAOrders b';


        $where = '';
        $from = ' FROM ';
        $fromjoin = " AmazonFBAOrders b LEFT JOIN AmazonFBA a ON b.asin=a.asin  ";

        $select = "SELECT b.orderid, a.mitsku, b.asin, a.fnsku, b.orderqty, b.ordernotes, Cast(b.orderdate as date) as orderdate, Cast(b.completeddate as date) as completeddate,a.title, b.trackingnumber, b.completed";


        $selectSlice = "SELECT orderid,mitsku,asin,fnsku,orderqty,ordernotes,orderdate,completeddate,title,trackingnumber,completed";



        $wherefields = array('b.orderid', 'a.mitsku', 'b.asin', 'a.fnsku', 'b.orderqty', 'b.ordernotes', 'orderdate', 'b.completeddate', 'a.title', 'b.trackingnumber', 'b.completed');

        $where .=$this->MCommon->concatAllWerefields($wherefields, $dataSearch);

        $where.= "  and (orderdate between '{$InitialDate}' and '{$FinalDate}')";
        $where .= " and ((b.completed)='Yes') ";





        $SQL = "{$select}{$from}{$fromjoin}{$where}";

        // echo $SQL;

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
                FROM {$fromjoin}{$where} )
                    {$selectSlice}, RowNumber
               FROM mytable
                WHERE RowNumber BETWEEN {$start} AND {$finish}";

        //   echo $SQL;        
        $result = $this->MCommon->getSomeRecords($SQL);


        $responce->page = $page;
        $responce->total = $total_pages;
        $responce->records = $count;
        $i = 0;

        foreach ($result as $row) {
            $responce->rows[$i]['id'] = $row['orderid'];
            $responce->rows[$i]['cell'] = array($row['orderid'],
                $row['mitsku'],
                $row['asin'],
                $row['fnsku'],
                $row['orderqty'],
                $row['ordernotes'],
                $row['orderdate'],
                $row['completeddate'],
                $row['title'],
                $row['trackingnumber'],
                $row['completed'],
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

    function showTicket() {

        // Define custom CSS
        $this->css = array('table.css', 'fluid.css');

        $this->javascript = array('jqueryui/ui/jquery.ui.widget.js', 'jqueryui/ui/jquery.ui.tabs.js', 'jqgrid/jquery.jqGrid.fluid.js');

        $this->hasNav = false;
        $this->hasFooter = false;

        $bs = isset($_REQUEST['bs']) ? $_REQUEST['bs'] : 'No BuilSKU'; // get the requested page
        $as = isset($_REQUEST['as']) ? $_REQUEST['as'] : 'NO ASIN'; // get how many rows we want to have into the gri
        $fn = isset($_REQUEST['fn']) ? $_REQUEST['fn'] : 'No FNSKU'; // get index row - i.e. user click to sort
        $des = isset($_REQUEST['des']) ? $_REQUEST['des'] : 'No description';


        $data = array(
            'buildsku' => $bs,
            'asin' => $as,
            'snsku' => $fn,
            'description' => $des,
            'title' => "Print Ticket",
            'from' => 'tools/amazonfba/',
            'printTicket' => 'printTicket?bs=' . $bs . '&as=' . $as . '&fn=' . $fn . '&des=' . $des,
        );

        $this->load->view("tickets/showticket", $data);
    }

    function add() {

        echo "holaaa";
    }

    function partnumber() {

        $sql = 'select distinct top(5) compatibility.partnumber as value, id from compatibility';

        $result = mssql_query($sql); //query the database for entries containing the term

        while ($row = mssql_fetch_array($result, MYSQL_ASSOC)) {//loop through the retrieved values
            $row['value'] = htmlentities(stripslashes($row['value']));
            $row['id'] = (int) $row['id'];
            $row_set[] = $row; //build an array
        }
        echo json_encode($row_set); //format the array into json data

    }
    
    
	
}
?>




