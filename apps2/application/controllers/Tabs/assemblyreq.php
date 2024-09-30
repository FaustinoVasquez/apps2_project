<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Assemblyreq extends BP_Controller {

    public function __construct() {
        parent::__construct();

        $is_logged_in = $this->session->userdata('is_logged_in');

        if (!isset($is_logged_in) || $is_logged_in != true) {
            redirect(base_url(), 'refresh');
        }
    }

    function index() {

        $this->title = "MI Technologiesinc - Assembly Requirements";

        $this->description = "Assembly Requirements";

        // Define custom CSS
        $this->css = array('menu.css', 'form.css', 'fluid.css', 'table.css', 'jqueryui/redmond/jquery-ui-1.8.21.custom.css', 'jqgrid/ui.jqgrid.css');

        // Define custom javascript
        $this->javascript = array('jqueryui/ui/jquery-ui-1.8.21.custom.js', 'jqgrid/i18n/grid.locale-en.js', 'jqgrid/jquery.jqGrid.min.js', 'jqgrid/jquery.jqGrid.fluid.js');

        $this->hasNav = False;

        $data['search'] = $_GET['q'];
        $data['caption'] = 'Assembly Requirements';
        $data['from'] = '/Tabs/assemblyreq/';
        $data['headers'] = "['ID','SubSKU','Name','QtyRequired','QOH','vQOH','tQOH','Cost','Notes','ApprovedBy','IsRequired']";

        $data['body'] = "[
                {name:'ID',index:'ID', width:150, align:'left',sorttype:'int',hidden:true},
                {name:'SubSKU',index:'SubSKU', width:100, align:'left'},
                {name:'Name',index:'Name', width:390, align:'left'},
                {name:'QtyRequired',index:'QtyRequired', width:90, align:'center'},
                {name:'CurrentStock',index:'CurrentStock', width:90, align:'center',formatter:'number', formatoptions:{decimalSeparator:'.',thousandsSeparator: ',', decimalPlaces: 2 }},
                {name:'VirtualStock',index:'VirtualStock', width:90, align:'center',formatter:'number', formatoptions:{decimalSeparator:'.',thousandsSeparator: ',', decimalPlaces: 2 }},
                {name:'TotalStock',index:'VirtualStock', width:90, align:'center',formatter:'number', formatoptions:{decimalSeparator:'.',thousandsSeparator: ',', decimalPlaces: 2 }},
                {name:'Cost',index:'Cost', width:90, align:'center',formatter:popup},
                {name:'Notes',index:'Notes', width:180},
                {name:'ApprovedBy',index:'AprovedBy1', width:90, align:'center'},
                {name:'IsRequired',index:'IsRequired', width:90, align:'center',formatter:yesnoFmatter},
  	]";

        $this->build_content($data);
        $this->render_page();
    }

    function gridAssembleRequirements() {

        $search = $_GET['q'];
        //  $quantity = array();

        $page = isset($_REQUEST['page']) ? $_REQUEST['page'] : 1; // get the requested page
        $limit = isset($_REQUEST['rows']) ? $_REQUEST['rows'] : 10; // get how many rows we want to have into the gri
        $sidx = isset($_REQUEST['sidx']) ? $_REQUEST['sidx'] : 'id'; // get index row - i.e. user click to sort
        $sord = isset($_REQUEST['sord']) ? $_REQUEST['sord'] : '';

        $totalrows = isset($_REQUEST['totalrows']) ? $_REQUEST['totalrows'] : false;
        if ($totalrows) {
            $limit = $totalrows;
        }


        $SQL = "SELECT 
                    a.id,
                    a.SubSKU,
                    c.Name, 
                    a.SubSKUQTYRequired as QtyRequired, 
                    inventory.dbo.fn_get_Global_Stock(c.id) as CurrentStock, 
                    gs.VirtualStock,
                    gs.TotalStock,
                   
                    a.Notes, 
                    inventory.dbo.fn_GetUserName(a.ApprovedBy) as ApprovedBy,
                    a.IsRequired 
                FROM AssemblyDetails a, ProductCatalog c 
                LEFT OUTER JOIN inventory.dbo.global_stocks AS gs ON (c.ID = gs.ProductCatalogID)
                Where (a.SubSKU = c.ID) and (a.ProductCatalogID = '{$search}')";



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
            $responce->rows[$i]['SKU'] = $row['SubSKU'];
            $responce->rows[$i]['cell'] = array($row['SubSKU'],
                $row['SubSKU'] = utf8_encode($row['SubSKU']),
                $row['Name'] = utf8_encode($row['Name']),
                $row['QtyRequired'] = utf8_encode($row['QtyRequired']),
                $row['CurrentStock'] = utf8_encode($row['CurrentStock']),
                $row['VirtualStock'] = utf8_encode($row['VirtualStock']),
                $row['TotalStock'] = utf8_encode($row['TotalStock']),
              
                $row['Notes'] = utf8_encode($row['Notes']),
                $row['ApprovedBy'] = utf8_encode($row['ApprovedBy']),
                $row['IsRequired'] = utf8_encode($row['IsRequired']),
            );
            $i++;
        }

        echo json_encode($responce);
    }

}

?>
