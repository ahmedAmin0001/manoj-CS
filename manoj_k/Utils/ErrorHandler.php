<?php

class ErrorHandler {

    private $debug = true;

    public function __construct(){

    }

    public function errorAndDie($msg){
        //Logolja a hibát -> házi feladat!
        
        if(!$this->debug){
            $msg = "Szerver oldali hiba!";
        }

        $res = array(
            "err" => $msg
        );
        return $res;
    }
}

?>