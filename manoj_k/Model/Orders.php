<?php

include_once("Application.php");

class Orders extends Application{
    
    private $sql = array(
        'allOrders' => "select o.*, s.name AS servicetype, s.price AS servicetype_price, c.first_name AS cleaner from orders o
                            LEFT JOIN servicetypes s ON s.id = o.servicetype_id
                            LEFT JOIN cleaners c ON c.id = o.cleaner_id
                            WHERE o.active = 1
                            GROUP BY o.id",
        "orderById" => "select o.*, s.name AS servicetype, s.price AS servicetype_price, c.first_name AS cleaner from orders o
                            LEFT JOIN servicetypes s ON s.id = o.servicetype_id
                            LEFT JOIN cleaners c ON c.id = o.cleaner_id
                            WHERE o.active = 1 and o.id = {id}
                            GROUP BY o.id
                            LIMIT 1",
        "freeOrders" => "select o.*, s.name AS servicetype, s.price AS servicetype_price, c.first_name AS cleaner from orders o
                            LEFT JOIN servicetypes s ON s.id = o.servicetype_id
                            LEFT JOIN cleaners c ON c.id = o.cleaner_id
                            WHERE o.active = 1 and o.isaccepted = 0
                            GROUP BY o.id",
        "acceptOrder" => "update orders o SET o.cleaner_id= {cleaner_id}, o.isaccepted = 1
                            WHERE o.id = {order_id}",
        "cleanerOrder" => "select o.*, s.name AS servicetype, s.price AS servicetype_price from orders o
                            LEFT JOIN servicetypes s ON s.id = o.servicetype_id
                            LEFT JOIN cleaners c ON c.id = o.cleaner_id
                            WHERE o.active = 1 and o.cleaner_id = {cleaner_id}
                            GROUP BY o.id",
        "userOrder" => "select o.*, s.name AS servicetype, s.price AS servicetype_price from orders o
                            LEFT JOIN servicetypes s ON s.id = o.servicetype_id
                            LEFT JOIN cleaners c ON c.id = o.cleaner_id
                            WHERE o.active = 1 and o.user_id = {user_id}
                            GROUP BY o.id"
    );

    private $messages = array();

    protected $table = "orders";
    protected $fields = array("first_name", "family_name", "email", "phone", "servicetype_id", "address", "state", "city", "postal", "country", "houseaccess", "when_date", "when_slot", "period", "user_id", "preferred_language");

    public function __construct(){
        parent::__construct();
    }

    public function getAllOrders(){
        $orders = $this->getResultList($this->sql['allOrders']);
        return $orders;
    }

    public function getFreeOrders(){
        $orders = $this->getResultList($this->sql['freeOrders']);
        return $orders;
    }

    public function getOrderById($id){
        if(!$this->isValidId($id)){
            return array();
        }

        $params = array(
            '{id}' => $id
        );

        $order = $this->getSingleResult(strtr($this->sql['orderById'], $params));
        return $order;
    }

    public function getCleanerOrder($cleaner_id){
        if(!$this->isValidId($cleaner_id)){
            return array();
        }

        $params = array(
            '{cleaner_id}' => $cleaner_id
        );

        $order = $this->getResultList(strtr($this->sql['cleanerOrder'], $params));
        return $order;
    }

    public function getUserOrder($user_id){
        if(!$this->isValidId($user_id)){
            return array();
        }

        $params = array(
            '{user_id}' => $user_id
        );

        $order = $this->getResultList(strtr($this->sql['userOrder'], $params));
        return $order;
    }

    public function save($order){
        if(!$this->validation($order)){
            $this->writeLog("The resulting data set is invalid! <br>" . implode("<br>", $this->messages));
            $this->msg->setSessionMessage("The form is not filled in correctly! <br>" . implode("<br>", $this->messages));
            return null;
        }


        /*if(isset($Order["id"]) && !empty($Order["id"])){
            if($this->isValidId(intval($Order["id"]))){
                $this->id = intval($Order["id"]);
                $filename = $this->fileUpload();

                if($filename){
                    $Order["file_Order"] = $filename;
                }

                $res = $this->modify($Order);

            } else {
                $this->writeLog("A kapott id invalid: " . $Order["id"]);
                $this->msg->setSessionMessage("A kapott id invalid: " . $Order["id"]);
            }
        } else {*/
            
            $this->create($order);
            $this->id = $this->getLastInsertedId();
        //}

        return $this->id;
    }


    /** Override */
    protected function validation($data){

        /*
        //family_name
        if(!isset($data["family_name"]) || empty($data["family_name"]) || $data["family_name"] == null){
            $this->messages[] = 'A név mező kitöltése kötelező.';
            return false;
        }

        if(!is_string($data["family_name"])){
            $this->messages[] = 'A név csak szöveg lehet.';
            return false;
        }

        if(strlen($data["family_name"]) > 255){
            $this->messages[] = 'A név hossza nem haladhatja meg a 255 karaktert.';
            return false;
        }

        //email

        //full_price

        //discount

        //is_beneficiary

        //discount_reason

        //advance

        //installment
        */
        return true;
    }

    public function delete($id){
        if(!$this->isValidId($id)){
            return false;
        }

        $res = $this->deleteRecordById('orders', $id);
        return $res;
    }

    public function acceptOrder($cleaner_id, $order_id){
        if(!$this->isValidId($cleaner_id) || !$this->isValidId($order_id)){
            return false;
        }

        $params = array(
            '{cleaner_id}' => $cleaner_id,
            '{order_id}' => $order_id
        );

        $res = $this->execute(strtr($this->sql['acceptOrder'], $params));
        return $res;
    }
}

?>