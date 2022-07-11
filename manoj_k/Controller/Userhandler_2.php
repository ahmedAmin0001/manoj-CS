<?php

include_once("AppController.php");

class Userhandler_2 extends AppController{

    public function __construct(){
        parent::__construct();

        $this->addAllowedMethods("guest", array("login_user", "login_cleaner", "logout", "getCustomer"));
        $this->addAllowedMethods("user", array("login_user", "login_cleaner", "logout", "getCustomer"));
        $this->addAllowedMethods("cleaner", array("login_user", "login_cleaner", "logout", "getCustomer"));
        $this->addAllowedMethods("admin", array("login_user", "login_cleaner", "logout", "getCustomer"));

    }

    public function login_user(){
        $data = $_POST;
        if(isset($data["username"]) && isset($data["password"])){
            $username = $data["username"];
            $password = $data["password"];
            $this->useModels(array("Users"));
            $user = $this->Users->getUserByUsername($username);
            if($user != array()){
                if($username ==  $user["username"] && md5($password) == $user["pmd5"]){
                    $_SESSION["user_manoj"] = $user;
                }
            } else {
                $this->msg->setSessionMessage("Incorrect username or password!");
            }
        }
        $phpsessid = session_id();
        $_SESSION["user_manoj"]["phpsessid"] = $phpsessid;
        return $_SESSION["user_manoj"];
    }

    public function login_cleaner(){
        $data = $_POST;
        if(isset($data["username"]) && isset($data["password"])){
            $username = $data["username"];
            $password = $data["password"];
            $this->useModels(array("Cleaners"));
            $user = $this->Cleaners->getCleanerByUsername($username);
            if($user != array()){
                if($username ==  $user["username"] && md5($password) == $user["pmd5"]){
                    $_SESSION["user_manoj"] = $user;
                }
            } else {
                $this->msg->setSessionMessage("Incorrect username or password!");
            }
        }
        $phpsessid = session_id();
        $_SESSION["user_manoj"]["phpsessid"] = $phpsessid;
        return $_SESSION["user_manoj"];
    }

    public function logout(){
        $_SESSION["user_manoj"] = null;
        return null;
    }

    public function getCustomer(){
        return $_SESSION["user_manoj"];
    }

    

}

?>