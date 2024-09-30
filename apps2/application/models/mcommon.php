<?php

class MCommon extends CI_Model {

    function __construct() {
	parent::__construct();
    }

    /*
     * Metodo que devuelve el resultado de la consulta en un array asociativo
     */

    function getSomeRecords($sql) {
	$data = array();
	$result = mssql_query($sql);
	while ($row = mssql_fetch_array($result, MSSQL_ASSOC)) {
	    $data[] = $row;
	}
	return $data;
    }

    /*
     * Metodo que devuelve el resultado de la consulta en un array asociativo especificando
     * un servidor para la extraccion
     * ejemplo:
     * $result=getSomeRecordsFromServer($SQL,'InventorySave')
     * 
     */

    function getSomeRecordsFromServer($sql,$database) {
        $CI =& get_instance();
    	$CI->readdb = $this->load->database($database, TRUE);
        $query = $this->readdb->query($sql);

    	$data = array();
    	foreach ($query->result_array() as $row) {
    		$data[] = $row;
    	}
    	return $data;
    }
   
    /*
     * 
     * Funcion que devuelve solamente un registro en una consulta SQL.
     * 
     * 
     */

    function getOneRecord($sql) {

        $data = array();
	$result = mssql_query($sql);
           
	$data = mssql_fetch_array($result, MSSQL_ASSOC);
        
        

	return $data;
    }
    
    
     function getOneRecordFromServer($sql,$database) {
        $CI =& get_instance();
    	$CI->readdb = $this->load->database($database, TRUE);
        $query = $this->readdb->query($sql);
       
        return $query->result_array();
    }

    /*
     * Metodo que salva un registro a una tabla.
     */

    function saveRecord($sql,$database) {

    	$CI =& get_instance();

    	$CI->savedb = $this->load->database($database, TRUE);

    	$result = $this->savedb->query($sql);

    	if (!$result) {
    		die('Invalid query');
    	}
        return;
    }

    /*
     * Metodo que ejecuta un query.
     */

    function executeQuery($sql,$database) {

        $CI =& get_instance();

    	$CI->savedb = $this->load->database($database, TRUE);

    	$result = $this->savedb->query($sql);
        
	if (!$result) {
	    die('Invalid query');
	}
        return;
    }

    /*
     * Metodo que obtiene todos los nombres de campos de una tabla
     */

    function getAllfields($table) {
	$query = "SELECT top 1 * FROM  " . $table;
	$result = mssql_query($query);
	$numfields = mssql_num_fields($result);
	for ($i = 0; $i < $numfields; $i++) { // Header
	    $data[] = mssql_field_name($result, $i);
	}

	return $data;
    }

    /*
     * Metodo que genera el WHERE para la consulta por cualquier campo 
     * en la tabla ProductCatalog.
     * Recibe como parametos los nombres de campo de la tabla Product Catalog y 
     * la cadena con las palabras de busqueda.
     */

    function concatAllWerefields($fields, $search) {

	$words = explode(" ", $search);
	$where = ' WHERE ';
	if ($search != '') {
	    $where .= "(";
	    foreach ($words as $word) {
		foreach ($fields as $field) {
		    $where .= '(CONVERT(varchar(250),' . $field . ',0)) + ';
		}
		$where = substr($where, 0, -4);
		$where.= ') like ' . "'%" . $word . "%'" . ' and ';
	    }
	    $where = substr($where, 0, -4);
	    $where.= ')';
	} else {
	    $where .= '1=1';
	}
	return $where;
    }

    /*
     * 
     * 
     * FillDropDown($firstLine, $query, $id, $name)
     * Recibe cuatro parametros
     * $fistLine = el texto que queremos ver en la primera linea del dropdown, la opcion 0
     * $query = Linea SQL
     * $id = campo identificador numerico para cada linea
     * $name campo de indetificador de texto que se desplegara
     *  
     *  
     */

    function fillDropDown($firstLine, $query, $id, $name) {
	if (!empty($firstLine)) {
	    $Options = array($firstLine);
	}

	$result = mssql_query($query);
	while ($row = mssql_fetch_assoc($result)) {
	    $Options[$row[$id]] = $row[$name];
	}
        
        asort($Options);
        
	return $Options;
    }



    function fillDropDown1($firstLine, $query, $id, $name) {
    if (!empty($firstLine)) {
        $Options = array($firstLine);
    }

    $result = mssql_query($query);
    while ($row = mssql_fetch_assoc($result)) {
        $Options[$row[$id]] = $row[$name];
    }
        
        
    return $Options;
    }


    function fillDropDown2($firstId,$firstLine, $query, $id, $name) {
    if (!empty($firstLine)) {
        $Options = array($firstId => $firstLine);
    }

    $result = mssql_query($query);
    while ($row = mssql_fetch_assoc($result)) {
        $Options[$row[$id]] = $row[$name];
    }
        
        
    return $Options;
    }


    function fillDropDown3($labels, $query, $id, $name) {


    if (!empty($labels)) {
        foreach ($labels as $key => $label) {
          $Options[$key] = $label;
        }
    }

    $result = mssql_query($query);
    while ($row = mssql_fetch_assoc($result)) {
        $Options[$row[$id]] = $row[$name];
    }
        
        
    return $Options;
    }
    
    
    function fillAutoComplete($SQL, $field) {
	// $SQL = "SELECT distinct PartNumber FROM [Inventory].[dbo].[Compatibility] where PartNumber LIKE '%$letra%'";
	$result = mssql_query($SQL);
	while ($row = mssql_fetch_array($result, MSSQL_ASSOC)) {
	    $Options[] = array('value' => $row[$field]);
	}


	return $Options;
    }


    function fillautocompleteajax($SQL,$field){

       $result = mssql_query($SQL);
       while ($row = mssql_fetch_array($result, MSSQL_ASSOC)) {
        $Options[] =  $row[$field];
        }

        return  "['" . implode($Options, "','") . "']";

    }


    function fillCombo($SQL, $field) {
	//field es el campo que se va a desplegar...
	$result = mssql_query($SQL);

	while ($row = mssql_fetch_array($result, MSSQL_ASSOC)) {
	    //   $Options[] = array ('value' => $row[$field]);
	    echo "<option value='{$row[$field]}'>{$row[$field]}</option>";
	}


	//   return $Options;
    }

    //**************************************************************************                                                                      
    //                                                                       
    //  FUNCIONES PARA MANEJO DE FECHAS                                      
    //                                                                                                                                              
    //**************************************************************************


    /*
     * 
     * Funcion que devuelve los nombres de los meses
     * 
     */


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

    /*
     * Funcion que devuelve el nombre de un mes
     */

    function getMonthName($Month) {
	$strTime = mktime(1, 1, 1, $Month, 1, date("Y"));
	return date("M", $strTime);
    }

    /*
     * Funcion que devuelve la fecha incial de un numero de dias 
     * Pasar como parametro  (date(m/d/Y),Nodias)
     */

    function fixIDatebyDays($d, $days) {
	$arrayDate = explode("/", $d);
	$mda = getdate(mktime(0, 0, 0, $arrayDate[0], $arrayDate[1], $arrayDate[2]) - 24 * 60 * 60 * $days);
	if ($mda['mon'] <= 9) {
	    $mda['mon'] = "0" . $mda['mon'];
	}
	if ($mda['mday'] <= 9) {
	    $mda['mday'] = "0" . $mda['mday'];
	}
	
	$dateTo = $mda['mon'] . "/" . $mda['mday'] . "/" . $mda['year'] . ' ' . $mda['hours'] . ':' . $mda['minutes'] . ':' . $mda['seconds'];
	return $dateTo;
    }

    function fixFDatebyDays($d, $days) {
	$arrayDate = explode("/", $d);
	$mda = getdate(mktime(23, 59, 59, $arrayDate[0], $arrayDate[1], $arrayDate[2]) - 24 * 60 * 60 * $days);
	if ($mda['mon'] <= 9) {
	    $mda['mon'] = "0" . $mda['mon'];
	}
	if ($mda['mday'] <= 9) {
	    $mda['mday'] = "0" . $mda['mday'];
	}
	$dateTo = $mda['mon'] . "/" . $mda['mday'] . "/" . $mda['year'] . ' ' . $mda['hours'] . ':' . $mda['minutes'] . ':' . $mda['seconds'];
	return $dateTo;
    }

    function InitMonth($d) {
	$arrayDate = explode("/", $d);
	$mda = getdate(mktime(0, 0, 0, $arrayDate[0], $arrayDate[1], $arrayDate[2]));
	$mda['mday'] = 01;
	if ($mda['mon'] <= 9) {
	    $mda['mon'] = "0" . $mda['mon'];
	}
	if ($mda['mday'] <= 9) {
	    $mda['mday'] = "0" . $mda['mday'];
	}
	$dateTo = $mda['mon'] . "/" . $mda['mday'] . "/" . $mda['year'];
	return $dateTo;
    }

    function lastweek() {
	$lastweek = $this->InitialDate(date("m/d/Y"), 7, 0);
	return $lastweek;
    }
    
    function lastmonth() {
	$lastmonth = $this->InitialDate(date("m/d/Y"), 30, 0);
	return $lastmonth;
    }
    
    

    /*
     * 
     * Funciones para corregir una fecha dada 
     * Pasar como parametro  fixFinalDate(date(m/d/Y), 0)
     *  0 devuelve el formato sin minutos y segundos
     */

    function fixFinalDate($d, $format) {
	$arrayDate = explode("/", $d);
	$mda = getdate(mktime(23, 59, 59, $arrayDate[0], $arrayDate[1], $arrayDate[2]));
	if ($mda['mon'] <= 9) {
	    $mda['mon'] = "0" . $mda['mon'];
	}
	if ($mda['mday'] <= 9) {
	    $mda['mday'] = "0" . $mda['mday'];
	}
	if ($format == 0) {
	    $dateTo = $mda['mon'] . "/" . $mda['mday'] . "/" . $mda['year'];
	} else {
	    $dateTo = $mda['mon'] . "/" . $mda['mday'] . "/" . $mda['year'] . ' ' . $mda['hours'] . ':' . $mda['minutes'] . ':' . $mda['seconds'];
	}

	return $dateTo;
    }

    /*
     * Essta funcion reemplazara a fixInitialDate fixIDatebyDays, 
     * pondra las dos en una sola 
     * 
     */

    function InitialDate($d, $days, $format) {
	$arrayDate = explode("/", $d);
	$mda = getdate(mktime(0, 0, 0, $arrayDate[0], $arrayDate[1], $arrayDate[2]) - 24 * 60 * 60 * $days);
	if ($mda['mon'] <= 9) {
	    $mda['mon'] = "0" . $mda['mon'];
	}
	if ($mda['mday'] <= 9) {
	    $mda['mday'] = "0" . $mda['mday'];
	}
	if ($format == 0) {
	    $dateTo = $mda['mon'] . "/" . $mda['mday'] . "/" . $mda['year'];
	} else {
	    $dateTo = $mda['mon'] . "/" . $mda['mday'] . "/" . $mda['year'] . ' ' . $mda['hours'] . ':' . $mda['minutes'] . ':' . $mda['seconds'];
	}

	return $dateTo;
    }

    /*
     * Si se pasa como format 0 la fecha retornada no tiene horas minutos ni segudos
     * cualquier otro valor retorna la fecha con horas minutos y segunros
     * 
     */

    function fixInitialDate($d, $format) {
	$arrayDate = explode("/", $d);
	$mda = getdate(mktime(0, 0, 0, $arrayDate[0], $arrayDate[1], $arrayDate[2]));
	if ($mda['mon'] <= 9) {
	    $mda['mon'] = "0" . $mda['mon'];
	}
	if ($mda['mday'] <= 9) {
	    $mda['mday'] = "0" . $mda['mday'];
	}
	if ($format == 0) {
	    $dateTo = $mda['mon'] . "/" . $mda['mday'] . "/" . $mda['year'];
	} else {
	    $dateTo = $mda['mon'] . "/" . $mda['mday'] . "/" . $mda['year'] . ' ' . $mda['hours'] . ':' . $mda['minutes'] . ':' . $mda['seconds'];
	}

	return $dateTo;
    }

    /*
     * Funcion que calcula el numero de dias entre dos fechas
     */

    function calcDays($df, $dt) {
	$arrayDate1 = explode("/", $df);
	$arrayDate2 = explode("/", $dt);
	$mda1 = mktime(12, 0, 0, $arrayDate1[0], $arrayDate1[1], $arrayDate1[2]);
	$mda2 = mktime(12, 0, 0, $arrayDate2[0], $arrayDate2[1], $arrayDate2[2]);
	$days = (floor(($mda1 - $mda2) / 60 / 60 / 24));
	return $days;
    }

    function showHistory($id, $InitialDate, $FinalDate) {

	$sql = "Select count(*) as rowNum
            FROM Inventory.dbo.InventoryAdjustments a, Inventory.dbo.InventoryAdjustmentDetails b
            WHERE (a.ID = b.InventoryAdjustmentsID)and (b.ProductCatalogID =" . $id . ")
                   and (a.Date between '" . $InitialDate . "' and '" . $FinalDate . "')";


	$result = $this->getOneRecord($sql);

	return $result['rowNum'];
    }

    //Funcion que obtiene todos los a;os desde el 2007 a la fecha en un arreglo asociativo
    function getyearstonow() {
	$result = array();
	for ($i = date('Y'); $i > 2007; $i--) {
	    $result[$i] = (string) $i;
	}
	return $result;
    }

    function completeMonths($array) {
	$count = count($array);
	if ($count < 11) {
	    for ($i = $count; $i < 12; $i++) {
		$array[$i]['mymonth'] = $count + 1;
		$array[$i]['SalesByMonth'] = 0;
		$count++;
	    }
	}
	$months = array("Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec");

	foreach ($array as $key => $value) {
	    $array[$key]['mymonth'] = $months[$key];
	}

	return $array;
    }

    /**
     * creating between two date
     * @param string since
     * @param string until
     * @param string step
     * @param string date format
     * @return array
     * @author
     */
    function dateRange($first, $last, $step = '+1 day', $format = 'm/d/Y') {

	$dates = array();
	$current = strtotime($first);
	$last = strtotime($last);

	while ($current <= $last) {

	    $dates[] = date($format, $current);
	    $current = strtotime($step, $current);
	}

	return $dates;
    }
    
    
    //**************************************************************************                                                                      
    //                                                                       
    //  FUNCIONES PARA MANEJO DE DIRECTORIOS                                      
    //                                                                                                                                              
    //**************************************************************************


    /*
     * 
     * Funcion que devuelve verdadero falso si el directorio existe..
     * 
     */
    
    public function url_exists($url)
    {
        $ch = @curl_init($url);
        @curl_setopt($ch, CURLOPT_HEADER, TRUE);
        @curl_setopt($ch, CURLOPT_NOBODY, TRUE);
        @curl_setopt($ch, CURLOPT_FOLLOWLOCATION, FALSE);
        @curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $status = array();
        preg_match('/HTTP\/.* ([0-9]+) .*/', @curl_exec($ch) , $status);
        return ($status[1] == 200);
    }
    

}


?>
