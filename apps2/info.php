<?php
$myServer = "192.168.0.236";
$myUser = "sistema";
$myPass = "Z91bM4";
$myDB = "inventory";

$dbhandle = mssql_connect($myServer, $myUser, $myPass)
      or die("Couldn't connect to SQL Server on $myServer" . mssql_get_last_message());
?>

