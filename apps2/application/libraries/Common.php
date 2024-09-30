<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Common {

   public $colmodel; 



    function fillPost($keys, $array, $exclude = null) {
        //  $array = array();
        // Crea un bucle con toda la iformacion del arreglo $_POST
        foreach ($_POST as $key => $val) {
            // Si la variable $key es un array
            if (is_array($keys)) {
                // Y la clave actual está dentro del array, entonces añadimos la clave y el valor en el nuevo array
                if (in_array($key, $keys))
                    $array[$key] = $val;
                // Si no es array y su contenido es exactamente ALL
            }elseif ($keys === "ALL") {
                // Si el exclude es especificado
                if (isset($exclude)) {
                    // Es un array
                    if (is_array($exclude)) {
                        // Y la clave actual NO está en el array, entonces añadimos la clave y el valor en el nuevo array
                        if (!in_array($key, $exclude))
                            $array[$key] = $val;
                        // Si no es un array
                    }else {
                        if ($key != $exclude)
                            $array[$key] = $val;
                    }
                    // Si no especificamos la exclude añadimos todos los valores
                }else {
                    $array[$key] = $val;
                }
                // Si no es ninguna de las anteriores (únicamente especificamos una clave) la devolvemos con su valor
            }else
                return $_POST[$keys];
        }
        return $array;
    }

    function CreateColName($array, $type) {

        foreach ($array as $key => $value) {
            $result[] = $array[$key][$type];
        }
            $result = "['" . implode($result, "','") . "']";
      
        return $result;
    }

    function CreateColmodel($array,$type) {
        foreach ($array as $key => $value) {
            $result[] = $array[$key][$type];
        }
        $result = "[" . implode($result, ",") . "]";
        return $result;
    }


    function myColName($array){

         foreach ($array as $key => $value) {
            $result[] = $key;
        }
            $result = "['" . implode($result, "','") . "']";
        return $result;
    }


    function addModel($model){

         $this->colmodel .= "{name:'{$model}',index:'{$model}'";
         return $this;
    }

    function addWidth($width){

         $this->colmodel .= ",width:{$width}";
         return $this;
    }

    function addAlign($align){

         $this->colmodel .= ",align:'{$align}'";
         return $this;
    }

    function addNlCr(){

         $this->colmodel .= '},';
         return $this;
    }


    function getModel(){

        return '['.$this->colmodel.']';
    }


}

