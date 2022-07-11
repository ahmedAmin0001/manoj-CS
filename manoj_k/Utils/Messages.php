<?php

class Messages{
    private $message = array();
    private $type = '';

    public function __construct(){
        if(!isset($_SESSION['messages'])){
            $_SESSION['messages'] = array();
            $_SESSION['message_flag'] = false;
        }
    }

    public function setErrorMsg($str){
        $this->message[] = $str;
        $this->type = "error_msg";
    }

    public function messages(){
        if(!empty($this->message)){
            echo "<div class='{$this->type} msg'>" . implode("<br>", $this->message) . "</div>";
        }
    }

    public function setSessionMessage($str){
        $_SESSION["messages"][] = $str;
        $_SESSION['message_flag'] = false;
    }

    public function getSessionMessage(){
        if(!$_SESSION['message_flag']){
            $r = implode(", ", $_SESSION['messages']);
            $_SESSION['message_flag'] = true;
            $_SESSION['messages'] = array();
            $res = array(
                "sessionmsg" => $r
            );
            return $res;
        }
        return "";
    }
}

?>