<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There area two reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router what URI segments to use if those provided
| in the URL cannot be matched to a valid route.
|
*/
$route['Reports/purchaseorderlist/(:num)']= "Reports/purchaseorderlist/index/$1";
$route['Catalog/prodcat/(:num)'] = "Catalog/prodcat/index/$1";
$route['Tabs/showskudata/(:num)'] = "Tabs/showskudata/index/$1";
$route['Tabs/invanalitics/(:num)'] = "Tabs/invanalitics/index/$1";
$route['Tabs/customfields/(:num)'] = "Tabs/customfields/index/$1";
$route['Catalog/statistics/(:num)'] = "Catalog/statistics/index/$1";
$route['Catalog/showpo/(:num)'] = "Catalog/showpo/index/$1";
$route['Tabs/shipinf/(:num)'] = "Tabs/shipinf/index/$1";
$route['Tabs/history/(:num)'] = "Tabs/history/index/$1";
$route['Tabs/historynew/(:num)'] = "Tabs/historynew/index/$1";
$route['Tabs/invdetailsnew/(:num)'] = "Tabs/invdetailsnew/index/$1";
$route['Tabs/vendorfpcomp/(:num)'] = "Tabs/vendorfpcomp/index/$1";
$route['Tabs/pohistory/(:num)']= "Tabs/pohistory/index/$1";
$route['Tabs/vqohdetails/(:num)']= "Tabs/vqohdetails/index/$1";
$route['Tabs/costinfo/(:num)']= "Tabs/costinfo/index/$1";
$route['Tabs/advanced/(:num)']= "Tabs/advanced/index/$1";
$route['/Reports/backorderreport/(:num)']= "/Reports/backorderreport/index/$1";
$route['tools/buyonline/(:num)']= "tools/buyonline/index/$1";
$route['dashboard/(:any)']= "dashboard/index/$1";



$route['default_controller'] = "welcome";
$route['404_override'] = '';


/* End of file routes.php */
/* Location: ./application/config/routes.php */