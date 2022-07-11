<?php

include_once("Application.php");

class Hours extends Application{
    
    private $sql = array(
        'allHours' => "select h.* from hours h
                        WHERE h.active = 1",
        "hourById" => "select h.* FROM hours h
                        WHERE h.id = {id} and h.active = 1
                        LIMIT 1"
    );

    private $messages = array();

    protected $table = "hours";
    protected $fields = array("name");

    public function __construct(){
        parent::__construct();
    }

    public function getAllHours(){
        $hours = $this->getResultList($this->sql['allHours']);
        return $hours;
    }

    public function getHourById($id){
        if(!$this->isValidId($id)){
            return array();
        }

        $params = array(
            '{id}' => $id
        );

        $hour = $this->getSingleResult(strtr($this->sql['hourById'], $params));
        return $hour;
    }

    public function save($hour){
        if(!$this->validation($hour)){
            $this->writeLog("A kapott adatsor invalid! <br>" . implode("<br>", $this->messages));
            $this->msg->setSessionMessage("A form kitöltése nem megfelelő! <br>" . implode("<br>", $this->messages));
            return null;
        }

        /*if(isset($hour["id"]) && !empty($hour["id"])){
            if($this->isValidId(intval($hour["id"]))){
                $this->id = intval($hour["id"]);
                $filename = $this->fileUpload();

                if($filename){
                    $hour["file_hour"] = $filename;
                }

                $res = $this->modify($hour);

            } else {
                $this->writeLog("A kapott id invalid: " . $hour["id"]);
                $this->msg->setSessionMessage("A kapott id invalid: " . $hour["id"]);
            }
        } else {*/
            
            $this->create($hour);
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

        $res = $this->deleteRecordById('hours', $id);
        return $res;
    }
}

?>