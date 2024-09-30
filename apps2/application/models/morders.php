<?php

class MOrders extends CI_Model {

    private $OrderManager; 

    function __construct() {
        parent::__construct();
        $this->OrderManager = $this->load->database('OrderManager', TRUE);
    }

    function getCartNames() {
        $SQL = "select distinct id,CartName From [OrderManager].[dbo].[ShoppingCarts] Where not ( CartName is null)  order by CartName";
        $result = $this->MCommon->fillDropDown('All Carts', $SQL, 'id', 'CartName');
        return $result;
    }

    function filterbystatus($status) {
        switch ($status) {
            case 1: return " and (OrderStatus='Shipped')";
                break;
            case 2: return " and (OrderStatus<>'Shipped')";
        }
    }

    function filterbycartId($cartid) {
        return " and (CartID = {$cartid})";
    }

    function filterbyDate($InitialDate, $FinalDate) {
        return "  and (OrderDate between '{$InitialDate}' and '{$FinalDate}')";
    }

    //-------------------------------------------------------------

    function getOrderStatus() {
        $SQL = "select distinct OrderStatus From [OrderManager].[dbo].[Orders] Where not ( OrderStatus is null)  order by Orderstatus";
        $result = $this->MCommon->getSomeRecords($SQL);
        return $result;
    }

    function getCartName($cartid) {
        $SQL = "select CartName From [OrderManager].[dbo].[ShoppingCarts] Where id =" . $cartid;
        $result = $this->MCommon->fillDropDown('All Carts', $SQL, 'id', 'CartName');
        return $result;
    }

    function getSpecificCartName($ordernumber){
       $SQL = "select [OrderManager].[dbo].fn_Get_CartName_From_Order($ordernumber) as CartName ";
        $result = $this->MCommon->getOneRecord($SQL);
        return $result['CartName']; 
    }
    
    function getSpecificSalesrep($ordernumber){
       $SQL = "SELECT [OrderManager].[dbo].fn_Get_UserName(EnteredBy) as Csr FROM [OrderManager].[dbo].orders where OrderNumber=" .$ordernumber;
        $result = $this->MCommon->getOneRecord($SQL);
        return $result['Csr']; 
    }

    
    
    function getCustomerName($customerid) {
        $SQL = "SELECT a.FullName
                FROM [OrderManager].[dbo].[Customers] a
                Where CustomerID = '{$customerid}'";

        $result = $this->MCommon->getOneRecord($SQL);
        return $result;
    }

    function getCustomerIDData($customerid) {
        $SQL = "SELECT a.CustomerID,a.FullName, a.Address, a.City,a.State,a.Zip, a.Country, a.Email, a.Phone, a.Company
                FROM [OrderManager].[dbo].[Customers] a
                Where CustomerID = '{$customerid}'";

        $result = $this->MCommon->getOneRecord($SQL);
        return $result;
    }

    function getAllCustomerOrders($customerid, $InitialDate, $FinalDate) {
        $SQL = "SELECT a.OrderNumber,   
                       CAST(a.OrderDate as date) AS OrderDate,
                       a.GrandTotal as OrderTotal, 
                       [OrderManager].[dbo].fn_Sum_Amount(a.OrderNumber) as PaidAmount, 
                       a.BalanceDue,
                       a.PONumber
              FROM [OrderManager].[dbo].[Orders] a
              where (a.OrderDate between '{$InitialDate}' and '{$FinalDate}') 
              and   (a.CustomerID = {$customerid})";

        $result = $this->MCommon->getSomeRecords($SQL);
        return $result;
    }

    function getSelectetCustomerOrders($customerid, $InitialDate, $FinalDate) {
        $SQL = "SELECT a.OrderNumber,   
                       CAST(a.OrderDate as date) AS OrderDate,
                       a.GrandTotal as OrderTotal, 
                       [OrderManager].[dbo].fn_Sum_Amount(a.OrderNumber) as PaidAmount, 
                       a.BalanceDue,
                       a.PONumber
              FROM [OrderManager].[dbo].[Orders] a
              where (a.OrderDate between '{$InitialDate}' and '{$FinalDate}') 
              and   (a.CustomerID = {$customerid})";

        $result = $this->MCommon->getSomeRecords($SQL);
        return $result;
    }

    function getSoldData($ordernumber) {   
        $SQL = "SELECT a.CustomerID,a.Name, a.Address, a.City,a.State,a.Zip, a.Country, a.Email, a.Phone, a.Company
               FROM [OrderManager].[dbo].[Orders] a
               Where OrderNumber = {$ordernumber}";

        $result = $this->MCommon->getOneRecord($SQL);
        return $result;
    }
    

    function getShipData($ordernumber) {
        $SQL = "SELECT a.ShipName, a.ShipAddress, a.ShipCity, a.ShipState,a.ShipZip, a.ShipCountry, a.ShipEmail, a.ShipPhone,a.ShipCompany
                FROM [OrderManager].[dbo].[Orders] a
               Where OrderNumber = {$ordernumber}";

        $result = $this->MCommon->getOneRecord($SQL);
        return $result;
    }

    function getComments($ordernumber) {
        $SQL = "SELECT a.Comments
                FROM [OrderManager].[dbo].[Orders] a
                Where OrderNumber = {$ordernumber}";

        $result = $this->MCommon->getOneRecord($SQL);

        return $result;
    }

    function getStatusOrder($ordernumber) {
        $SQL = "select OrderStatus From [OrderManager].[dbo].[Orders] Where OrderNumber = {$ordernumber}";
        $result = $this->MCommon->getOneRecord($SQL);
        return $result['OrderStatus'];
    }

//-------------------------------------------------------------------------------------------------------
    //Billing Center functions
    //Obtenemos Paid in Full

    function paidForm($datefrom, $dateto, $status, $customerid,$cartid) {
        $SQL='';
        
        $where =" where (a.OrderDate between '{$datefrom}' and '{$dateto}')";
        
        if ($cartid != 0){
          $where.=" and (CartID = {$cartid}) ";
        } 
       
        
        if ($status == 1) {
            $SQL="SELECT SUM (a.FinalGrandTotal) as OrderTotal
              FROM [OrderManager].[dbo].[Orders] a";
            $SQL.= $where;
            $SQL.=" and a.BalanceDue = 0";
            $ret='OrderTotal';
        }
        if ($status == 2) {
            $SQL = "SELECT SUM (a.BalanceDue) as BalanceDue
              FROM [OrderManager].[dbo].[Orders] a";
            $SQL.= $where;
            $SQL.=" and a.BalanceDue > 0";
            $ret='BalanceDue';
        }
        if ($status == 3) {
            $SQL.="SELECT SUM (a.BalanceDue) as CreditDue
              FROM [OrderManager].[dbo].[Orders] a";
            $SQL.=$where;
            $SQL.=" and a.BalanceDue < 0";
            $ret='CreditDue';
        }
        if ($customerid != 0) {
            $SQL.=' and a.customerID= ' . $customerid;
        } else {
            if ($customerid != '') {
                $SQL.=' and a.customerID= ' . $customerid;
            }
        }

    

        $result = $this->MCommon->getOneRecord($SQL);
      
        
        return $result[$ret];
    }


    
}
?>

