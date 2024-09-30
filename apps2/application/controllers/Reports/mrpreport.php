<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Mrpreport extends BP_Controller {

    public function __construct() {
        parent::__construct();

        $is_logged_in = $this->session->userdata('is_logged_in');

        if (!isset($is_logged_in) || $is_logged_in != true) {
            redirect(base_url(), 'refresh');
        }

        if ($this->MUsers->isValidUser($this->session->userdata('userid'), 881100) != 1) {// 881100 prodcat Access
            redirect('Catalog/prodcat', 'refresh');
        }
    }

    public function index($sku =null) {

        // Define Meta
        $this->title = "MI Technologiesinc - MRP Report";

        $this->description = "MRP Report";

        $this->css = array('form.css', 'fluid.css', 'table.css', 'jqueryui/redmond/jquery-ui-1.8.21.custom.css', 'jqgrid/ui.jqgrid.css', 'menu.css', );

        // Define custom javascript
        $this->javascript = array('jqueryui/ui/jquery-ui-1.8.21.custom.js', 'jqgrid/i18n/grid.locale-en.js', 'jqgrid/jquery.jqGrid.min.js', 'jqgrid/jquery.jqGrid.fluid.js', 'popup.js','site.js');

        $this->load->library('Layout');
        $menu = new Layout;

        $data = array(
            'menu' => $menu->show_menu($this->MUsers->getUserFullName($this->session->userdata('userid'))),
            'caption' => 'MRP Report',
            'from' => 'Reports/mrpreport/',
        );
	

        $columns = array(
            'SKU'=>
                array('colName' => 'SKU',
                          'colModel' => "{ name:'ID',
                                           index:'ID',
                                           width:90,
                                           align:'center',
                                           frozen: true
                                          }"),

            'Name'=>
                array('colName' => 'Name',
                           'colModel' => "{ name:'Name',
                                            index:'Name',
                                            width:300,
                                            align:'left',
                                            frozen: true
                                           }"),

            'categoryName'=>
                array('colName' => 'Category',
                                   'colModel' => "{ name:'categoryName',
                                                    index:'categoryName',
                                                    width:150, align:'center',
                                                    sorttype: 'number'
                                                   }"),

            'Manufacturer'=>
                array('colName' => 'Manufacturer',
                                   'colModel' => "{ name:'Manufacturer',
                                                    index:'Manufacturer',
                                                    width:90, align:'center'
                                                   }"),

            'VPN'=>
                array('colName' => 'Manufacturer PN',
                          'colModel' => "{ name:'VPN',
                                           index:'VPN',
                                           width:110,
                                           align:'center',
                                          }"),

            'TotalStock'=>
                array('colName' => 'Total Qty on Hand',
                        'colModel' => "{ name:'TotalStock',
                                         index:'TotalStock',
                                         width:120,
                                         align:'right',
                                         formatter:'number',
                                         formatoptions:{ decimalSeparator:'.',
                                                         thousandsSeparator: ',',
                                                         decimalPlaces: 2,
                                                         prefix: ''
                                                        }
                                        }"),

            'BackOrders'=>
                array('colName' => 'Back Orders',
                                 'colModel' => "{ name:'BackOrders',
                                                  index:'BackOrders',
                                                  width:100,
                                                  align:'right',
                                                }"),

            'TotalQtyAvail' =>
                array('colName' => 'Total Qty Available',
                                     'colModel' => "{ name:'TotalQtyAvail',
                                                      index:'TotalQtyAvail',
                                                      width:130,
                                                      align:'right',
                                                      formatter:'number',
                                                      formatoptions:{ decimalSeparator:'.',
                                                                      thousandsSeparator: ',',
                                                                      decimalPlaces: 0,
                                                                      prefix: ''
                                                                     }
                                                    }"),

            '60daysCons'=>
                array('colName' => 'Sixty Days Consumption',
                                 'colModel' => "{ name:'DaysCons',
                                                  index:'DaysCons',
                                                  width:130,
                                                  align:'right',
                                                  formatter:'number',
                                                  formatoptions:{ decimalSeparator:'.',
                                                                  thousandsSeparator: ',',
                                                                  decimalPlaces: 0,
                                                                  prefix: ''
                                                                 }
                                                }"),

            'MonthAVG'=>
                array('colName' => 'Monthly Average',
                      'colModel' => "{ name:'MonthAVG',
                                       index:'MonthAVG',
                                       width:130,
                                       align:'right',
                                       formatter:'number',
                                       formatoptions:{ decimalSeparator:'.',
                                                       thousandsSeparator: ',',
                                                       decimalPlaces: 0,
                                                       prefix: ' '
                                                      }
                                      }"),

            'WeeklyAVG'=>
                array('colName' => 'Weekly Average',
                      'colModel' => "{ name:'WeeklyAVG',
                                       index:'WeeklyAVG',
                                       width:130,
                                       align:'right',
                                       formatter:'number',
                                       formatoptions:{ decimalSeparator:'.',
                                                       thousandsSeparator: ',',
                                                       decimalPlaces: 0,
                                                       prefix: ' '
                                                     }
                                   }"),

            'WeeksOfInv'=>
                array('colName' => 'Weeks of Inventory',
                      'colModel' => "{ name:'WeeksOfInv',
                                      index:'WeeksOfInv',
                                      width:130,
                                      align:'right',
                                      formatter:'number',
                                      formatoptions:{ decimalSeparator:'.',
                                                      thousandsSeparator: ',',
                                                      decimalPlaces: 1,
                                                      prefix: ' '
                                                    }
                                    }"),

            'SSQty'=>
                array('colName' => 'Safety Stock QTY',
                      'colModel' => "{ name:'SSQty',
                                       index:'SSQty',
                                       width:130,
                                       align:'right',
                                       formatter:'number',
                                       formatoptions:{ decimalSeparator:'.',
                                                       thousandsSeparator: ',',
                                                       decimalPlaces: 0,
                                                       prefix: ' '
                                                     }
                                      }"),

            'NeedOrd'=>
                array('colName' => 'Need to Order',
                      'colModel' => "{ name:'NeedOrd',
                                       index:'NeedOrd',
                                       width:130,
                                       align:'right'
                                     }"),

            'QtyOrd'=>
                array('colName' => 'QTY to Order',
                      'colModel' => "{ name:'QtyOrd',
                                       index:'QtyOrd',
                                       width:130,
                                       align:'right',
                                       formatter:'number',
                                       formatoptions:{ decimalSeparator:'.',
                                                       thousandsSeparator: ',',
                                                       decimalPlaces: 0,
                                                       prefix: ' '
                                                     }
                                     }"),

            'UnitCost'=>
                array('colName' => 'Unit Cost',
                      'colModel' => "{ name:'UnitCost',
                                       index:'UnitCost',
                                       width:130,
                                       align:'right',
                                       formatter:'number',
                                       formatoptions:{ decimalSeparator:'.',
                                                       thousandsSeparator: ',',
                                                       decimalPlaces: 4,
                                                       prefix: '$ '
                                                     }
                                     }"),

        );
        //Cargamos la libreria comumn
        $this->load->library('common');

        $data['colNames'] = $this->common->CreateColname($columns, 'colName');
        $data['colModel'] = $this->common->CreateColmodel($columns, 'colModel');

            //Generar el contenido....
        $this->build_content($data);
        $this->render_page();
    }


    function getData() {

        //Variables que envia el grid, incializarlas si estan vacias
        $page = !empty($_REQUEST['page']) ? $_REQUEST['page'] : '1'; // get the requested page
        $limit = !empty($_REQUEST['rows']) ? $_REQUEST['rows'] : '10'; // get how many rows we want to have into the gri
        $sidx = !empty($_REQUEST['sidx']) ? $_REQUEST['sidx'] : 'PC.ID'; // get index row - i.e. user click to sort
        $sord = !empty($_REQUEST['sord']) ? $_REQUEST['sord'] : 'asc';
        $examp = isset($_REQUEST['q']) ? $_REQUEST['q'] : 1;  //query number
        //variables que envia el formulario inicializarlas si no existen
        $search = !empty($_GET['ds']) ? $_REQUEST['ds'] : '';


        //Select utilizado para realizar el conteo del numero de registros devueltos por la consulta.
        $selectCount = 'Select count(*) as rowNum';


        //Select General con los campos necesarios para la vista
        $select = "SELECT PC.ID
                    ,PC.Name
                    ,PC.categoryID
                    ,PC.Manufacturer
                    ,GS.TotalStock
                    ,IsNull((SELECT SUM(Cast(QtyBackordered AS INT)) FROM Inventory.[dbo].[PurchaseOrderData] WHERE SKU = PC.ID),0) as BackOrders
                    ,(SELECT SUM(qty)
                                    FROM Inventory.dbo.Bins_History
                                    where Product_Catalog_ID = PC.ID AND ScanCode = 'OUT52' AND CONVERT(NVARCHAR,stamp,112) BETWEEN CONVERT(NVARCHAR,GETDATE()-60,112) AND CONVERT(NVARCHAR,GETDATE(),112)
                                    GROUP BY Product_Catalog_ID) AS DaysCons
                    ,PC.UnitCost
                   ";
        $selectSlice =  " SELECT ID
                                ,Name
                                ,categoryID
                                ,inventory.dbo.fn_GetCategoryName(categoryID) as categoryName
                                ,Manufacturer
                                ,[inventory].[dbo].fn_Vendor_Part_Number(ID) as VPN
                                ,TotalStock
                                ,BackOrders
                                ,TotalStock + BackOrders as TotalQtyAvail
                                ,DaysCons
                                ,UnitCost
                        ";

        $from = ' from ';
        $table = ' inventory.dbo.ProductCatalog AS PC';
        $join =  ' LEFT OUTER JOIN inventory.dbo.global_stocks AS GS ON (PC.ID = GS.ProductCatalogID)';
        $where = '';

        $wherefields = array('PC.ID','PC.Manufacturer','PC.Name','PC.categoryID');

        $fields = $this->MCommon->getAllfields($table);
        //creamos el were concatenando todos los nombres de campos y las palabras de busqueda
        $where .=$this->MCommon->concatAllWerefields($wherefields, $search);

        /*$where  .= " and (PC.categoryID in (69,70,71,49,50,51,52,53,54,55,56,57,58))";*/

        $where  .= " and (PC.categoryID in (35,39,40,41,42,43,44,45,46,47,48))";


        $SQL = "{$selectCount}{$from}{$table}{$join}{$where}";


        $query = $this->db->query($SQL);

        $count = $query->row('rowNum');


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


        $SQL = "WITH mytable AS ($select , ROW_NUMBER() OVER (ORDER BY PC.ID {$sord}) AS RowNumber
                                FROM {$table}{$join}{$where})
               {$selectSlice}, RowNumber
               FROM mytable
                WHERE RowNumber BETWEEN {$start} AND {$finish}";


        $query = $this->db->query($SQL);

        $responce = new stdClass();
        $responce->page = $page;
        $responce->total = $total_pages;
        $responce->records = $count;
        $i = 0;


        foreach ($query->result_array() as $row) {
            $responce->rows[$i]['id'] = $row['ID'];
            $responce->rows[$i]['cell'] = array($row['ID'],
                $row['Name'],
                $row['categoryName'],
                $row['Manufacturer'],
                $row['VPN'],
                $row['TotalStock'],
                $row['BackOrders'],
                $row['TotalQtyAvail'],
                $row['DaysCons'],
                $row['MonthAVG']= ceil($row['DaysCons'] / 2),
                $row['WeeklyAVG']= ceil($row['MonthAVG'] / 4),
                $row['WeeksOfInv']= ($row['WeeklyAVG']==0) ? 0 : $row['TotalQtyAvail'] / $row['WeeklyAVG'],
                $row['SSQty']= ceil($row['WeeklyAVG'] *2) ,
                $row['NeedOrd'] = ($row['TotalQtyAvail'] >= $row['SSQty'])?'No':'Yes',
                $row['QtyOrd'] = (($row['SSQty']-$row['TotalQtyAvail']) <= 0)?'0':ceil($row['SSQty']-$row['TotalQtyAvail']),
                $row['UnitCost'],
            );
            $i++;
        }

        echo json_encode($responce);
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
