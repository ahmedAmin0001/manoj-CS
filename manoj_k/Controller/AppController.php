<?php
class AppController{

    protected $template = "";
    protected $variables = array();

    public $msg = null;

    private $allowedMethodLists = array();

    public function __construct(){
        include_once("Utils/Messages.php");
        $this->msg = new Messages();
    }

    protected function addAllowedMethods($authority, $methods){
        $this->allowedMethodLists[$authority] = $methods;
    }

    public function getAllowedMethods(){
        return $this->allowedMethodLists;
    }

    public function hasRole($method){
        if($_SESSION["user_manoj"] == null){
            $authority = "guest";
        } else {
            if($_SESSION["user_manoj"]["authority"] == null){
                $authority = "guest";
            } else {
                $authority = $_SESSION["user_manoj"]["authority"];
            }
        }
        
        if(!in_array($method, $this->allowedMethodLists[$authority])){
            return false;
        } else {
            return true;
        }
    }

    public function getTemplate(){
        return $this->template;
    }

    protected function useModels($models){
        foreach($models as $model){
            include_once("Model/$model.php");
            $this->{$model} = new $model();
        }
    }

    protected function set($name, $value){
        $this->variables[$name] = $value;
    }

    public function getContent(){
        $msg = $this->msg;
        $object = $this;

        if(empty($this->getTemplate() || !$this->getTemplate())){
            $this->template = "error";
        }

        foreach($this->variables as $name => $value){
            ${$name} = $value;
        }

        include_once("View/" . $this->getTemplate() . ".php");
    }

}

?>