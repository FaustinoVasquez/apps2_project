<?php

class MUsers extends CI_Model {

    function __construct()
    {
        parent::__construct();
    }

    function verifyUser($u, $pw) {
        $this->db->select('ID,usr');
        $this->db->where('usr', $u);
        $this->db->where('Password', $pw);
        $this->db->where('IsActive', '1');
        $this->db->limit(1);
        $Q = $this->db->get('inventory.dbo.Users');
        
        if ($Q->num_rows() > 0) {
            $row = $Q->row_array();
            $data = array(
                'userid' => $row['ID'],
                'username' => $row['usr'],
                'is_logged_in' => true
            );
            
            $this->session->set_userdata($data);
            
            
        } else {
            $this->session->set_flashdata('error', 'Sorry, your username or password is incorrect!');
        }
    }

    function isValidUser($sesionuser, $code) {
        $SQL = "select  Inventory.dbo.fn_IsValidUserID(a.id,{$code}) as UserPermission
                       From inventory.dbo.Users a 
                       WHERE a.ID = {$sesionuser}";
                       
                       
        $result = $this->getOneRecord($SQL);
        return $result['UserPermission'];
    }

    function getOneRecord($sql) {
        $data = array();
        $result = mssql_query($sql);
        $data = mssql_fetch_array($result, MSSQL_ASSOC);

        return $data;
    }

    function getUsername_And_CartName($userid) {
        $data = '';
        $SQL = "select FullName, CartID, CustomerID from [Inventory].[dbo].[users] where id='{$userid}'";
        $data = $this->getoneRecord($SQL);
        return $data;
    }
    
     function getCartName($userid) {
        $data = '';
        $SQL = "select FullName, CartID, CustomerID from [Inventory].[dbo].[users] where id='{$userid}'";
        $data = $this->getoneRecord($SQL);
        return $data['CartID'];
        
    }
    
     function getCustomerId($userid) {
        $data = '';
        $SQL = "select FullName, CartID, CustomerID from [Inventory].[dbo].[users] where id='{$userid}'";
        $data = $this->getoneRecord($SQL);
        return $data['CustomerID'];
    }
    
     function getUserFullName($userid) {
        $SQL = "select FullName from [Inventory].[dbo].[users] where id='{$userid}'";
        $data = $this->MCommon->getoneRecord($SQL);
        return $data;
    }

    function isadminuser($sesionuser) {
        $adminuser = 800002;
        $SQL = "select  Inventory.dbo.fn_IsValidUserID(a.id,{$adminuser}) as UserPermission
                       From inventory.dbo.Users a 
                       WHERE a.ID = {$sesionuser}";
        $result = $this->getOneRecord($SQL);

        return $result['UserPermission'];
    }
    
    function iswebuser($sesionuser) {
        $webuser = 800001;
        $SQL = "select  Inventory.dbo.fn_IsValidUserID(a.id,{$webuser}) as UserPermission
                       From inventory.dbo.Users a 
                       WHERE a.ID = {$sesionuser}";
        $result = $this->MCommon->getOneRecord($SQL);

        return $result['UserPermission'];
    }

    function CustIdFullName(){
        $SQL = "Select ISNULL(Company, '') + ' -- '  + FullName as FullName, CustomerID
                FROM OrderManager.dbo.Customers
                WHERE CustomerID IN ( Select Distinct(CustomerID) FROM Inventory.dbo.CustomerSpecificPricing )";
          
        $result = $this->MCommon->fillDropDown('--', $SQL, 'CustomerID', 'FullName');       
   

        return $result;
    }
    
    

}

?>
