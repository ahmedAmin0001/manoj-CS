<?php

include_once("Application.php");

class Cleanersavailable extends Application{
    
    private $sql = array(
        'allCleanersavailable' => "select ca.* from caleanersavailable ca
                        WHERE ca.active = 1",
        "cleanersavailableById" => "select ca.* FROM caleanersavailable ca
                        WHERE ca.id = {id} and ca.active = 1
                        LIMIT 1"
    );

    private $messages = array();

    protected $table = "cleaners_available";
    protected $fields = array("cleaner_id", "day_id", "hour_id");

    public function __construct(){
        parent::__construct();
    }

    public function getAllCleanersavailable(){
        $cleanersavailable = $this->getResultList($this->sql['allCleanersavailable']);
        return $cleanersavailable;
    }

    public function getCleanersavailableById($id){
        if(!$this->isValidId($id)){
            return array();
        }

        $params = array(
            '{id}' => $id
        );

        $cleanersavailable = $this->getSingleResult(strtr($this->sql['cleanersavailableById'], $params));
        return $cleanersavailable;
    }

    public function save($cleaner_id, $cleanersavailable){
        if(!$this->validation($cleanersavailable)){
            $this->writeLog("A kapott adatsor invalid! <br>" . implode("<br>", $this->messages));
            $this->msg->setSessionMessage("A form kitöltése nem megfelelő! <br>" . implode("<br>", $this->messages));
            return null;
        }

        /*if(isset($cleanersavailable["id"]) && !empty($cleanersavailable["id"])){
            if($this->isValidId(intval($cleanersavailable["id"]))){
                $this->id = intval($cleanersavailable["id"]);
                $filename = $this->fileUpload();

                if($filename){
                    $cleanersavailable["file_cleanersavailable"] = $filename;
                }

                $res = $this->modify($cleanersavailable);

            } else {
                $this->writeLog("A kapott id invalid: " . $cleanersavailable["id"]);
                $this->msg->setSessionMessage("A kapott id invalid: " . $cleanersavailable["id"]);
            }
        } else {*/
            $res = true;

            foreach($cleanersavailable as $cleaneravailable){
                $cleaneravailable["cleaner_id"] = $cleaner_id;
                $re = $this->create($cleaneravailable);
                if(!$re){
                    $res == false;
                }
            }

            return $res;
        //}
    }

    /** Override */
    protected function validation($data){

        /*name
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
        }*/

        return true;
    }

    public function delete($id){
        if(!$this->isValidId($id)){
            return false;
        }

        $res = $this->deleteRecordById('Cleanersavailable', $id);
        return $res;
    }
}

?>