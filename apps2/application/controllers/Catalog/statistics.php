<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Statistics extends BP_Controller {

    public function __construct() {
        parent::__construct();

        $is_logged_in = $this->session->userdata('is_logged_in');

        if (!isset($is_logged_in) || $is_logged_in != true) {
            redirect(base_url(), 'refresh');
        }
          if ($this->MUsers->isValidUser($this->session->userdata('userid'), 881110) != 1) {// 881100 prodcat Access
          echo'<a href="self.close ()">Close this Window</a>';
        }
        
    }

    function index($sku) {

        $this->title = "MI Technologiesinc - Statistics";

        $this->description = "Statistics";

        // Define custom javascript
        $this->javascript = array('highcharts/highcharts.js','highcharts/exporting.js');

        $this->hasNav = False;



        $data['search'] = $sku;
        $data['caption'] = 'Statistics';
        $data['from'] = '/Catalog/statistics/';

        $today = date('m/d/Y');
        $inityear = $this->fixIDatebyDays($today, 365);
        $initThisMonth = $this->fixIDatebyDays($today, 30);
        $initlast1Month = $this->fixIDatebyDays($today, 60);
        $finishlastMonth = $this->fixIDatebyDays($today, 31);
        $initlast3months = $this->fixIDatebyDays($today, 90);


        //Usage Year to date
        $sql = "SELECT SUM(b.Quantity) as ytd
            FROM [Inventory].[dbo].[InventoryAdjustments] a,
                 [Inventory].[dbo].[InventoryAdjustmentDetails] b
            Where (a.id = b.InventoryAdjustmentsID)
                 and (a.date between '{$inityear}' and '{$today}')
                 and (b.ProductCatalogID = {$data['search']})
                 and (Flow = 2)";

                

        $ytd = $this->MCommon->getOneRecord($sql);

        if ($ytd['ytd'] != '') {

            $data['ytd'] = $ytd['ytd'];


            //usage lastMonth;
            $sql1 = "SELECT SUM(b.Quantity) as lastMonth
            FROM [Inventory].[dbo].[InventoryAdjustments] a,
                 [Inventory].[dbo].[InventoryAdjustmentDetails] b
            Where (a.id = b.InventoryAdjustmentsID)
                 and (a.date between '{$initlast1Month}' and '{$finishlastMonth}')
                 and (b.ProductCatalogID = {$data['search']})
                 and (Flow = 2)";

            $lastMonth = $this->MCommon->getOneRecord($sql1);
            $data['lastMonth'] = $lastMonth['lastMonth'];


            // Usage this month
            $sql2 = "SELECT SUM(b.Quantity) as thisMonth
            FROM [Inventory].[dbo].[InventoryAdjustments] a,
                 [Inventory].[dbo].[InventoryAdjustmentDetails] b
            Where (a.id = b.InventoryAdjustmentsID)
                 and (a.date between '{$initThisMonth}' and '{$today}')
                 and (b.ProductCatalogID = {$data['search']})
                 and (Flow = 2)";

            $thisMonth = $this->MCommon->getOneRecord($sql2);

            $data['thisMonth'] = $thisMonth['thisMonth'];

            //Average Based on last 3 months
            $sql3 = "SELECT SUM(b.Quantity) as AvgLast3months
            FROM [Inventory].[dbo].[InventoryAdjustments] a,
                 [Inventory].[dbo].[InventoryAdjustmentDetails] b
            Where (a.id = b.InventoryAdjustmentsID)
                 and (a.date between '{$initlast3months}' and '{$today}')
                 and (b.ProductCatalogID = {$data['search']})
                 and (Flow = 2)";

		 
		 
            $AvgLast3months = $this->MCommon->getOneRecord($sql3);

            $data['AvgLast3months'] = $AvgLast3months['AvgLast3months'] / 3;


            $sql4 = "SELECT  sum(b.QuantityShipped - b.QuantityReturned ) as total
                  ,sum  (b.PricePerUnit * (b.QuantityShipped - b.QuantityReturned ) ) as qtyunits
                  FROM [OrderManager].[dbo].[Orders] a, [OrderManager].[dbo].[Order Details] b
                  where (a.OrderNumber = b.OrderNumber)
                  and (a.orderdate between '{$initThisMonth}' and '{$today}')
                  and (b.sku = '{$data['search']}')";


            $dates = $this->months();

            for ($i = 1; $i < 13; $i++) {
                $sql5 = "SELECT sum(b.QuantityShipped - b.QuantityReturned ) as total ,sum (b.PricePerUnit * (b.QuantityShipped - b.QuantityReturned ) ) as qtyunits 
                   FROM [OrderManager].[dbo].[Orders] a, [OrderManager].[dbo].[Order Details] b 
                   where (a.OrderNumber = b.OrderNumber)
                   and (year(a.orderdate) = {$dates[$i]['year']})
                   and (month(a.OrderDate) = {$dates[$i]['month']})
                   and (b.sku = '{$data['search']}')";

                $AvgPricing[$i] = $this->MCommon->getOneRecord($sql5);

                $mymonths[] = $this->getMonthName($dates[$i]['month']);
            }


            for ($i = 1; $i < 13; $i++) {
                if ($AvgPricing[$i]['qtyunits'] != '') {
                    $AvgPricing1[$i] = $AvgPricing[$i]['qtyunits'] / $AvgPricing[$i]['total'];
                } else {
                    $AvgPricing1[$i] = 0;
                }
            }



            $sql6 = "SELECT  max(b.PricePerUnit) as Expensive,
                                min(b.PricePerUnit) as chip
                  FROM [OrderManager].[dbo].[Orders] a, [OrderManager].[dbo].[Order Details] b 
                  where (a.OrderNumber = b.OrderNumber)
                  and (a.OrderDate between '{$initlast3months}' and '{$today}')
                  and (b.sku = '{$data['search']}')
                  and (a.cancelled = 0)
                  and (a.approved = 1)
                  and(b.PricePerUnit > 0)
                  ";
            $MaxMin = $this->MCommon->getOneRecord($sql6);


            $sql7 = "SELECT sum (b.QuantityShipped - QuantityReturned) as quantity
                 FROM [OrderManager].[dbo].[Orders] a, [OrderManager].[dbo].[Order Details] b 
                 where (a.OrderNumber = b.OrderNumber)
                 and (a.OrderDate between'{$initlast3months}' and '{$today}')
                 and (b.sku = '{$data['search']}')
                 and (not (b.status is null))
                 and (a.approved = 1)
                 and (a.cancelled = 0)
                 and (b.PricePerUnit = {$MaxMin['Expensive']})";

            $TotalSellMax = $this->MCommon->getOneRecord($sql7);


            $sql8 = "SELECT sum (b.QuantityShipped - QuantityReturned) as quantity
                 FROM [OrderManager].[dbo].[Orders] a, [OrderManager].[dbo].[Order Details] b 
                 where (a.OrderNumber = b.OrderNumber)
                 and (a.OrderDate between '{$initlast3months}' and '{$today}')
                 and (b.sku = '{$data['search']}')
                 and (not (b.status is null))
                 and (a.approved = 1)
                 and (a.cancelled = 0)
                 and (b.PricePerUnit = {$MaxMin['chip']})";

            $TotalSellMin = $this->MCommon->getOneRecord($sql8);


            $sql9 = "SELECT  avg(b.PricePerUnit) as avgpricing
                 FROM [OrderManager].[dbo].[Orders] a, [OrderManager].[dbo].[Order Details] b 
                 where (a.OrderNumber = b.OrderNumber)
                 and (a.OrderDate between '{$initlast3months}' and '{$today}')
                 and (b.sku = '{$data['search']}')
                 and (not (b.status is null))
                 and (a.approved = 1)
                 and (a.cancelled = 0)
                 and (b.PricePerUnit > 0)";
		 

            $avgPricingTreeMonths = $this->MCommon->getOneRecord($sql9);


            $data['AvgPricing'] = $AvgPricing1;
            $data['Months'] = $mymonths;
            $data['TotalSellMax'] = $TotalSellMax;
            $data['TotalSellMin'] = $TotalSellMin;
            $data['MaxMin'] = $MaxMin;
            $data['avgPricingTreeMonths'] = $avgPricingTreeMonths['avgpricing'];
        } else {

            $data['ytd'] = '';
        }

 
        $this->build_content($data);
        $this->render_page();
    }

    function months() {
        $months = array();
        $j = 1;
        for ($i = 11; $i > 0; $i--) {

            $timestamp = strtotime(-$i . 'month');
            $months[$j]['month'] = date('m', $timestamp);
            $months[$j]['year'] = date('Y', $timestamp);
            $j++;
        }
        $months[$j]['month'] = date('m');
        $months[$j]['year'] = date('Y');


        return $months;
    }

    function getMonthName($Month) {
        $strTime = mktime(1, 1, 1, $Month, 1, date("Y"));
        return date("M", $strTime);
    }

    function fixIDatebyDays($d, $days) {
        $arrayDate = explode("/", $d);
        $mda = getdate(mktime(0, 0, 0, $arrayDate[0], $arrayDate[1], $arrayDate[2]) - 24 * 60 * 60 * $days);
        $dateTo = $mda['mon'] . "/" . $mda['mday'] . "/" . $mda['year'];
        return $dateTo;
    }

}

?>
