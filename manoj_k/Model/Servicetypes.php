<?php

include_once("Application.php");

class Servicetypes extends Application{
    
    private $sql = array(
        'allServicetypes' => "select s.* from servicetypes s
                        WHERE s.active = 1",
        "servicetypeById" => "select s.* FROM servicetypes s
                        WHERE s.id = {id} and s.active = 1
                        LIMIT 1"
    );

    private $messages = array();

    protected $table = "servicetypes";
    protected $fields = array("name", "price");

    public function __construct(){
        parent::__construct();
    }

    public function getAllServicetypes(){
        $ervicetypes = $this->getResultList($this->sql['allServicetypes']);
        return $ervicetypes;
    }

    public function getServicetypeById($id){
        if(!$this->isValidId($id)){
            return array();
        }

        $params = array(
            '{id}' => $id
        );

        $servicetype = $this->getSingleResult(strtr($this->sql['servicetypeById'], $params));
        return $servicetype;
    }

    public function save($servicetype){
        $servicetype = $this->htmlvalidate($servicetype);
        if(!$this->validation($servicetype)){
            $this->writeLog("A kapott adatsor invalid! <br>" . implode("<br>", $this->messages));
            $this->msg->setSessionMessage("A form kitöltése nem megfelelő! <br>" . implode("<br>", $this->messages));
            return null;
        }

        /*if(isset($servicetype["id"]) && !empty($servicetype["id"])){
            if($this->isValidId(intval($servicetype["id"]))){
                $this->id = intval($servicetype["id"]);
                $filename = $this->fileUpload();

                if($filename){
                    $servicetype["file_servicetype"] = $filename;
                }

                $res = $this->modify($servicetype);

            } else {
                $this->writeLog("A kapott id invalid: " . $servicetype["id"]);
                $this->msg->setSessionMessage("A kapott id invalid: " . $servicetype["id"]);
            }
        } else {*/
            
            $this->create($servicetype);
            $this->id = $this->getLastInsertedId();
        //}

        return $this->id;

    }

    protected function htmlvalidate($data){
        if(isset($data["name"])){
            $data["name"] = htmlspecialchars($data["first"]);
        }
        if(isset($data["price"])){
            $data["price"] = htmlspecialchars($data["price"]);
        }
        return $data;
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

        //price
        if(!isset($data["price"]) || empty($data["price"]) || $data["price"] == null){
            $this->messages[] = 'A price mező kitöltése kötelező.';
            return false;
        }

        if(!is_string($data["price"])){
            $this->messages[] = 'A price csak szöveg lehet.';
            return false;
        }

        if(strlen($data["price"]) > 255){
            $this->messages[] = 'A price hossza nem haladhatja meg a 255 karaktert.';
            return false;
        }
        return true;
    }

    public function delete($id){
        if(!$this->isValidId($id)){
            return false;
        }

        $res = $this->deleteRecordById('Servicetypes', $id);
        return $res;
    }
}

?>