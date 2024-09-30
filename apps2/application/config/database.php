<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------
| DATABASE CONNECTIVITY SETTINGS
| -------------------------------------------------------------------
| This file will contain the settings needed to access your database.
|
| For complete instructions please consult the 'Database Connection'
| page of the User Guide.
|
| -------------------------------------------------------------------
| EXPLANATION OF VARIABLES
| -------------------------------------------------------------------
|
|	['hostname'] The hostname of your database server.
|	['username'] The username used to connect to the database
|	['password'] The password used to connect to the database
|	['database'] The name of the database you want to connect to
|	['dbdriver'] The database type. ie: mysql.  Currently supported:
				 mysql, mysqli, postgre, odbc, mssql, sqlite, oci8
|	['dbprefix'] You can add an optional prefix, which will be added
|				 to thise table name when using the  Active Record class
|	['pconnect'] TRUE/FALSE - Whether to use a persistent connection
|	['db_debug'] TRUE/FALSE - Whether database errors should be displayed.
|	['cache_on'] TRUE/FALSE - Enables/disables query caching
|	['cachedir'] The path to the folder where cache files should be stored
|	['char_set'] The character set used in communicating with the database
|	['dbcollat'] The character collation used in communicating with the database
|				 NOTE: For MySQL and MySQLi databases, this setting is only used
| 				 as a backup if your server is running PHP < 5.2.3 or MySQL < 5.0.7
|				 (and in table creation queries made with DB Forge).
| 				 There is an incompatibility in PHP with mysql_real_escape_string() which
| 				 can make your site vulnerable to SQL injection if you are using a
| 				 multi-byte character set and are running versions lower than these.
| 				 Sites using Latin-1 or UTF-8 database character set and collation are unaffected.
|	['swap_pre'] A default table prefix that should be swapped with the dbprefix
|	['autoinit'] Whether or not to automatically initialize the database.
|	['stricton'] TRUE/FALSE - forces 'Strict Mode' connections
|							- good for ensuring strict SQL while developing
|
| The $active_group variable lets you choose which connection group to
| make active.  By default there is only one group (the 'default' group).
|
| The $active_record variables lets you determine whether or not to load
| the active record class
*/

$active_group = 'Inventory';
$active_record = TRUE;

$db['Inventory']['hostname'] = '192.168.0.236';
$db['Inventory']['username'] = 'tempuser';
$db['Inventory']['password'] = 'pLa13t1B';
$db['Inventory']['database'] = 'inventory';
$db['Inventory']['dbdriver'] = 'mssql';
$db['Inventory']['dbprefix'] = '';
$db['Inventory']['pconnect'] = FALSE;
$db['Inventory']['db_debug'] = TRUE;
$db['Inventory']['cache_on'] = FALSE;
$db['Inventory']['cachedir'] = '';
$db['Inventory']['char_set'] = 'utf8';
$db['Inventory']['dbcollat'] = 'utf8_general_ci';
$db['Inventory']['swap_pre'] = '';
$db['Inventory']['autoinit'] = TRUE;
$db['Inventory']['stricton'] = FALSE;


$db['InventorySave']['hostname'] = '192.168.0.236';
$db['InventorySave']['username'] = 'tempuser';
$db['InventorySave']['password'] = 'pLa13t1B';
$db['InventorySave']['database'] = 'inventory';
$db['InventorySave']['dbdriver'] = 'mssql';
$db['InventorySave']['dbprefix'] = '';
$db['InventorySave']['pconnect'] = FALSE;
$db['InventorySave']['db_debug'] = TRUE;
$db['InventorySave']['cache_on'] = FALSE;
$db['InventorySave']['cachedir'] = '';
$db['InventorySave']['char_set'] = 'utf8';
$db['InventorySave']['dbcollat'] = 'utf8_general_ci';
$db['InventorySave']['autoinit'] = TRUE;
$db['InventorySave']['stricton'] = FALSE;


$db['OrderManager']['hostname'] = "192.168.0.236";
$db['OrderManager']['username'] = "tempuser";
$db['OrderManager']['password'] = "pLa13t1B";
$db['OrderManager']['database'] = "OrderManager";
$db['OrderManager']['dbdriver'] = "mssql";
$db['OrderManager']['dbprefix'] = "";
$db['OrderManager']['pconnect'] = FALSE;
$db['OrderManager']['db_debug'] = TRUE;
$db['OrderManager']['cache_on'] = FALSE;
$db['OrderManager']['cachedir'] = "";
$db['OrderManager']['char_set'] = "utf8";
$db['OrderManager']['dbcollat'] = "utf8_general_ci";

$db['OrderManagerSave']['hostname'] = "192.168.0.236";
$db['OrderManagerSave']['username'] = "tempuser";
$db['OrderManagerSave']['password'] = "pLa13t1B";
$db['OrderManagerSave']['database'] = "OrderManager";
$db['OrderManagerSave']['dbdriver'] = "mssql";
$db['OrderManagerSave']['dbprefix'] = "";
$db['OrderManagerSave']['pconnect'] = FALSE;
$db['OrderManagerSave']['db_debug'] = TRUE;
$db['OrderManagerSave']['cache_on'] = FALSE;
$db['OrderManagerSave']['cachedir'] = "";
$db['OrderManagerSave']['char_set'] = "utf8";
$db['OrderManagerSave']['dbcollat'] = "utf8_general_ci";


$db['Mit_Analizer']['hostname'] = "192.168.0.236";
$db['Mit_Analizer']['username'] = "tempuser";
$db['Mit_Analizer']['password'] = "pLa13t1B";
$db['Mit_Analizer']['database'] = "Mit_Analizer";
$db['Mit_Analizer']['dbdriver'] = "mssql";
$db['Mit_Analizer']['dbprefix'] = "";
$db['Mit_Analizer']['pconnect'] = FALSE;
$db['Mit_Analizer']['db_debug'] = TRUE;
$db['Mit_Analizer']['cache_on'] = FALSE;
$db['Mit_Analizer']['cachedir'] = "";
$db['Mit_Analizer']['char_set'] = "utf8";
$db['Mit_Analizer']['dbcollat'] = "utf8_general_ci";


$db['Mitdb']['hostname'] = "192.168.0.236";
$db['Mitdb']['username'] = "tempuser";
$db['Mitdb']['password'] = "pLa13t1B";
$db['Mitdb']['database'] = "Mitdb";
$db['Mitdb']['dbdriver'] = "mssql";
$db['Mitdb']['dbprefix'] = "";
$db['Mitdb']['pconnect'] = FALSE;
$db['Mitdb']['db_debug'] = TRUE;
$db['Mitdb']['cache_on'] = FALSE;
$db['Mitdb']['cachedir'] = "";
$db['Mitdb']['char_set'] = "utf8";
$db['Mitdb']['dbcollat'] = "utf8_general_ci";

/* End of file database.php */
/* Location: ./application/config/database.php */
