<?php

class MSuppliers extends CI_Model {

     function __construct() {
          parent::__construct();
     }
     
    function getSuppliers() {
        $SQL = "select ID, Name from Inventory.dbo.Accounts where family = 'PROVIDER'";
        $result = $this->MCommon->fillDropDown('ALL Suppliers', $SQL, 'ID', 'Name');
        return $result;
    }
     
}
