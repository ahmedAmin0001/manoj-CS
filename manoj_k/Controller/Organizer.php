<?php

include_once("AppController.php");

class Organizer extends AppController{

    public function __construct(){
        parent::__construct();

        $this->addAllowedMethods("guest", array("index", "getAllUsers", "getAllOrders", "getAllCleaners", "getAllAuthorities", "getAllDays", "getAllHours", "getAllImages", "getAllServicetypes", "getUserById", "getOrderById", "getCleanerById", "getAuthorityById", "getImageById", "getDayById", "getHourById", "getServicetypeById", "saveUser", "saveOrder", "saveCleaner", "setAvailable", "fileUpload"));
        $this->addAllowedMethods("user", array("index", "getAllUsers", "getAllOrders", "getAllCleaners", "getAllAuthorities", "getAllDays", "getAllHours", "getAllImages", "getAllServicetypes", "getUserById", "getOrderById", "getCleanerById", "getAuthorityById", "getImageById", "getDayById", "getHourById", "getServicetypeById", "saveUser", "saveOrder", "saveCleaner", "setAvailable", "fileUpload", "getUserOrder", "changeUserDetail", "changeCleanerDetail"));
        $this->addAllowedMethods("cleaner", array("index", "getAllUsers", "getAllOrders", "getAllCleaners", "getAllAuthorities", "getAllDays", "getAllHours", "getAllImages", "getAllServicetypes", "getUserById", "getOrderById", "getCleanerById", "getAuthorityById", "getImageById", "getDayById", "getHourById", "getServicetypeById", "saveUser", "saveOrder", "saveCleaner", "setAvailable", "getFreeOrders", "acceptOrder", "fileUpload", "getCleanerOrder", "changeUserDetail", "changeCleanerDetail"));
        $this->addAllowedMethods("admin", array("index", "backend", "getAllUsers", "getAllOrders", "getAllCleaners", "getAllAuthorities", "getAllDays", "getAllHours", "getAllImages", "getAllServicetypes", "getUserById", "getOrderById", "getCleanerById", "getAuthorityById", "getImageById", "getDayById", "getHourById", "getServicetypeById", "saveUser", "saveOrder", "saveCleaner", "setAvailable", "deleteUser", "deleteOrder", "deleteCleaner", "getFreeOrders", "acceptOrder", "fileUpload", "getCleanerOrder", "getUserOrder", "changeUserDetail", "changeCleanerDetail"));
    }

    public function index(){
        $this->template = 'frontend';
    }

    public function getAllUsers(){
        $this->useModels(array("Users"));

        $users = $this->Users->getAllUsers();
        return $users;
    }

    public function getAllOrders(){
        $this->useModels(array("Orders"));

        $orders = $this->Orders->getAllOrders();
        return $orders;
    }

    public function getAllCleaners(){
        $this->useModels(array("Cleaners"));

        $cleaners = $this->Cleaners->getAllCleaners();
        return $cleaners;
    }

    public function getAllAuthorities(){
        $this->useModels(array("Authorities"));

        $authorities = $this->Authorities->getAllAuthorities();
        return $authorities;
    }

    public function getAllDays(){
        $this->useModels(array("Days"));

        $days = $this->Days->getAllDays();
        return $days;
    }

    public function getAllHours(){
        $this->useModels(array("Hours"));

        $hours = $this->Hours->getAllHours();
        return $hours;
    }

    public function getAllImages(){
        $this->useModels(array("Images"));

        $images = $this->Images->getAllImages();
        return $images;
    }

    public function getAllServicetypes(){
        $this->useModels(array("Servicetypes"));

        $servicetypes = $this->Servicetypes->getAllServicetypes();
        return $servicetypes;
    }

    

    // By ID

    public function getUserById(){
        $this->useModels(array("Users"));
        $user = [];
        if(isset($_POST["id"]) && !empty($_POST["id"])){
            $user = $this->Users->getUserById(intval($_POST["id"]));
        }
        return $user;
    }

    public function getOrderById(){
        $this->useModels(array("Orders"));
        $order = [];
        if(isset($_POST["id"]) && !empty($_POST["id"])){
            $order = $this->Orders->getOrderById(intval($_POST["id"]));
        }
        return $order;
    }

    public function getCleanerById(){
        $this->useModels(array("Cleaners"));
        $cleaner = [];
        if(isset($_POST["id"]) && !empty($_POST["id"])){
            $cleaner = $this->Cleaners->getCleanerById(intval($_POST["id"]));
        }
        return $cleaner;
    }

    public function getAuthorityById(){
        $this->useModels(array("Authorities"));
        $authority = [];
        if(isset($_POST["id"]) && !empty($_POST["id"])){
            $authority = $this->Authorities->getAuthorityById(intval($_POST["id"]));
        }
        return $authority;
    }

    public function getDayById(){
        $this->useModels(array("Days"));
        $day = [];
        if(isset($_POST["id"]) && !empty($_POST["id"])){
            $day = $this->Days->getDayById(intval($_POST["id"]));
        }
        return $day;
    }

    public function getHourById(){
        $this->useModels(array("Hours"));
        $hour = [];
        if(isset($_POST["id"]) && !empty($_POST["id"])){
            $hour = $this->Hours->getHourById(intval($_POST["id"]));
        }
        return $hour;
    }

    public function getImageById(){
        $this->useModels(array("Images"));
        $image = [];
        if(isset($_POST["id"]) && !empty($_POST["id"])){
            $image = $this->Images->getImageById(intval($_POST["id"]));
        }
        return $image;
    }

    public function getServicetypeById(){
        $this->useModels(array("Servicetypes"));
        $servicetype = [];
        if(isset($_POST["id"]) && !empty($_POST["id"])){
            $servicetype = $this->Servicetypes->getImageById(intval($_POST["id"]));
        }
        return $servicetype;
    }

    

    //Save methods

    public function saveUser(){
        $this->useModels(array("Users", "Authorities"));
        $clea = $this->Authorities->getAuthorityByName("user");
        $id = null;
        if(isset($_POST['data']) && !empty($_POST["data"])){
            $_POST["data"]["authority_id"] = $clea['id'];
            $id = $this->Users->save($_POST["data"]);
            
            if(!empty($id)){
                $this->msg->setSessionMessage("A mentés sikeres!");
            } else {
                $this->msg->setSessionMessage("A mentés sikertelen!");
            }
        }
        return $id;
    }

    public function saveOrder(){
        $this->useModels(array("Orders"));

        $id = null;
        if(isset($_POST['data']) && !empty($_POST["data"])){
            $id = $this->Orders->save($_POST["data"]);
            
            if(!empty($id)){
                $this->msg->setSessionMessage("A mentés sikeres!");
            } else {
                $this->msg->setSessionMessage("A mentés sikertelen!");
            }
        }
        return $id;
    }

    public function saveCleaner(){
        $this->useModels(array("Cleaners", "Cleanersavailable", "Authorities"));
        $clea = $this->Authorities->getAuthorityByName("cleaner");
        $id = null;
        if(isset($_POST['data']) && !empty($_POST["data"])){
            $_POST["data"]["authority_id"] = $clea['id'];
            $id = $this->Cleaners->save($_POST["data"]);

            if(!empty($id)){
                if(isset($_POST["data"]["available"])){
                    $this->Cleanersavailable->save($id, $_POST["data"]["available"]);
                }
                $this->msg->setSessionMessage("A mentés sikeres!");
            } else {
                $this->msg->setSessionMessage("A mentés sikertelen!");
            }
        }
        return $id;
    }

    public function changeUserDetail(){
        $this->useModels(array("Users"));

        if(isset($_POST["id"]) && !empty($_POST["id"])){
            $res = $this->Users->changeDetail($_POST);
            if($res){
                $this->msg->setSessionMessage("Succesfully saved!");
            } else {
                $this->msg->setSessionMessage("Save failed!");
            }
            return $res;
        }
        return false;

    }

    public function changeCleanerDetail(){
        $this->useModels(array("Cleaners"));

        if(isset($_POST["id"]) && !empty($_POST["id"])){
            $res = $this->Cleaners->changeDetail($_POST);
            return $res;
        }
        return null;

    }
    //Delete methods

    public function deleteUser(){
        $this->useModels(array("Users"));

        if(isset($_POST['id']) && !empty($_POST["id"])){
            $res = $this->Users->delete(intval($_POST["id"]));
            return $res;
        }
    }

    public function deleteOrder(){
        $this->useModels(array("Orders"));

        if(isset($_POST['id']) && !empty($_POST["id"])){
            $res = $this->Orders->delete(intval($_POST["id"]));
            return $res;
        }
    }

    public function deleteCleaner(){
        $this->useModels(array("Cleaners"));

        if(isset($_POST['id']) && !empty($_POST["id"])){
            $res = $this->Cleaners->delete(intval($_POST["id"]));
            return $res;
        }
    }

    //
    public function getFreeOrders(){
        $this->useModels(array("Orders"));

        $orders = $this->Orders->getFreeOrders();
        return $orders;
    }

    public function acceptOrder(){
        $this->useModels(array("Orders"));

        if(isset($_POST["cleaner_id"]) && !empty($_POST["cleaner_id"]) && isset($_POST["order_id"]) && !empty($_POST["order_id"])){
            $cleaner_id = htmlspecialchars($_POST["cleaner_id"]);
            $order_id = htmlspecialchars($_POST["order_id"]);
            $res = $this->Orders->acceptOrder(intval($cleaner_id), intval($order_id));
            return $res;
        }
        return null;
    }

    public function fileUpload(){
        $this->useModels(array("Images"));
        if(isset($_FILES)){
            $filename = $this->Images->fileUpload($_FILES);
            if($filename != false){
                $img = array(
                    "name" => $filename
                );
                $res = $this->Images->save($img);
                return $res;
            }
            return null;
        }
        return null;
    }

    public function getCleanerOrder(){
        $this->useModels(array("Orders"));
        if(isset($_POST["cleaner_id"]) && !empty($_POST["cleaner_id"])){
            $res = $this->Orders->getCleanerOrder(intval($_POST["cleaner_id"]));
            return $res;
        }
        return null;
    }

    public function getUserOrder(){
        $this->useModels(array("Orders"));
        if(isset($_POST["user_id"]) && !empty($_POST["user_id"])){
            $res = $this->Orders->getUserOrder(intval($_POST["user_id"]));
            return $res;
        }
        return null;
    }
    //
    
    public function setAvailable(){
        $res = [];
        if(isset($_POST["dates"]) || !empty($_POST["dates"])){
            $this->useModels(array("Days", "Hours"));
            $dates = $_POST["dates"];
            foreach($dates as $day){
                $d = $this->Days->getDayById(intval($day[0]));
                $hours = [];
                foreach($day[1] as $hour){
                    $h = $this->Hours->getHourById(intval($hour));
                    $hours[] = $h;
                }
                $res[] = array(
                    "day" => $d,
                    "hours" => $hours
                );
            }
        }
        return $res;
    }


}
?>