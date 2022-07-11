<?php

include_once("Application.php");

class Days extends Application{
    
    private $sql = array(
        'allDays' => "select d.* from days d
                        WHERE d.active = 1",
        "dayById" => "select d.* FROM days d
                        WHERE d.id = {id} and d.active = 1
                        LIMIT 1"
    );

    private $messages = array();

    protected $table = "days";
    protected $fields = array("name");

    public function __construct(){
        parent::__construct();
    }

    public function getAllDays(){
        $days = $this->getResultList($this->sql['allDays']);
        return $days;
    }

    public function getDayById($id){
        if(!$this->isValidId($id)){
            return array();
        }

        $params = array(
            '{id}' => $id
        );

        $day = $this->getSingleResult(strtr($this->sql['dayById'], $params));
        return $day;
    }

    public function save($day){
        if(!$this->validation($day)){
            $this->writeLog("A kapott adatsor invalid! <br>" . implode("<br>", $this->messages));
            $this->msg->setSessionMessage("A form kitöltése nem megfelelő! <br>" . implode("<br>", $this->messages));
            return null;
        }

        /*if(isset($day["id"]) && !empty($day["id"])){
            if($this->isValidId(intval($day["id"]))){
                $this->id = intval($day["id"]);
                $filename = $this->fileUpload();

                if($filename){
                    $day["file_day"] = $filename;
                }

                $res = $this->modify($day);

            } else {
                $this->writeLog("A kapott id invalid: " . $day["id"]);
                $this->msg->setSessionMessage("A kapott id invalid: " . $day["id"]);
            }
        } else {*/
            
            $this->create($day);
            $this->id = $this->getLastInsertedId();
        //}

        return $this->id;

    }

    /** Override */
    protected function validation($data){

        //name
        if(!isset($data["name"]) || empty($data["name"]) || $data["name"] == null){
            $this->messages[] = 'A név mező kitöltése kötelező.';
            return false;
        }

        if(!is_string($data["name"])){
            $this->messages[] = 'A név csak szöveg lehet.';
            return false;
        }

        if(strlen($data["name"]) > 255){
            $this->messages[] = 'A név hossza nem haladhatja meg a 255 karaktert.';
            return false;
        }

        return true;
    }

    public function delete($id){
        if(!$this->isValidId($id)){
            return false;
        }

        $res = $this->deleteRecordById('days', $id);
        return $res;
    }
}

?>