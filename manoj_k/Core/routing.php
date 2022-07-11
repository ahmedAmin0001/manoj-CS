<?php

if(isset($_POST["phpsessid"]) && $_POST["phpsessid"] != "" && $_POST["phpsessid"] != "undefined"){
    session_id($_POST["phpsessid"]);
}

session_start();

include_once("Utils/debug.php");

include_once("Utils/ErrorHandler.php");
$errorHandler = new ErrorHandler();

$getKey = array_keys($_GET);
$urlSegments = array();

if(isset($getKey[0])){
    $urlSegments = explode('/', $getKey[0]);
}

$object = null;
$className = isset($urlSegments[0]) ? ucfirst($urlSegments[0]) : false;

if($className){
    $file = "Controller/$className.php";
    if(!file_exists($file)){
        $json = json_encode(null);
        $msg = json_encode($errorHandler->errorAndDie("This file could not be found: " . $file));
        $r = array(
            'res' => $json,
            'msg' => $msg
        );
        $res = json_encode($r);
        echo $res;
    }

    include_once($file);

    if(!class_exists($className)){
        $json = json_encode(null);
        $msg = json_encode($errorHandler->errorAndDie("Invalid url! No such class name: " . $className));
        $r = array(
            'res' => $json,
            'msg' => $msg
        );
        $res = json_encode($r);
        echo $res;
    }
    
    $object = new $className();

    $method = 'login_user';

    if(isset($urlSegments[1])){
        $method = $urlSegments[1];
    }

    if(method_exists($object, $method)){
        if(isset($_SESSION["user_manoj"])){
            if($object->hasRole($method)){
                $json = json_encode($object->$method());
                $msg = json_encode($object->msg->getSessionMessage());
                $r = array(
                    'res' => $json,
                    'msg' => $msg
                );
                $res = json_encode($r);
                echo $res;
            } else {
                $json = json_encode(null);
                $msg = json_encode($errorHandler->errorAndDie("You are not authorized to view this page!"));
                $r = array(
                    'res' => $json,
                    'msg' => $msg
                );
                $res = json_encode($r);
                echo $res;
            }
        } else {
            $_SESSION["user_manoj"] = null;
            $_SESSION["user_manoj"]["authority"] = "guest";
            if($object->hasRole($method)){
                $json = json_encode($object->$method());
                $msg = json_encode($object->msg->getSessionMessage());
                $r = array(
                    'res' => $json,
                    'msg' => $msg
                );
                $res = json_encode($r);
                echo $res;
            } else {
                $json = json_encode(null);
                $msg = json_encode($errorHandler->errorAndDie("You are not authorized to view this page!"));
                $r = array(
                    'res' => $json,
                    'msg' => $msg
                );
                $res = json_encode($r);
                echo $res;
            }
        }
    } else {
        $json = json_encode(null);
        $msg = json_encode($errorHandler->errorAndDie("The $method method you are looking for cannot be found!"));
        $r = array(
            'res' => $json,
            'msg' => $msg
        );
        $res = json_encode($r);
        echo $res;
    }
} else {
    $json = json_encode(null);
    $msg = json_encode($errorHandler->errorAndDie("Invalid url! The class name is not specified!"));
    $r = array(
        'res' => $json,
        'msg' => $msg
    );
    $res = json_encode($r);
    echo $res;
}

?>