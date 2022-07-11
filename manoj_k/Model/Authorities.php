<?php

include_once("Application.php");

class Authorities extends Application{
    
    private $sql = array(
        'allAuthorities' => "select a.id, a.name from authorities a
                        WHERE a.active = 1",
        "authorityById" => "select * FROM authorities a
                        WHERE a.id = {id} and a.active = 1
                        LIMIT 1",
        "authorityByName" => "select * FROM authorities a
                                WHERE a.name = '{name}' and a.active = 1
                                LIMIT 1"
    );

    private $messages = array();

    protected $table = "authorities";
    protected $fields = array("name");

    public function __construct(){
        parent::__construct();
    }

    public function getAllAuthorities(){
        $authorities = $this->getResultList($this->sql['allAuthorities']);
        return $authorities;
    }

    public function getAuthorityById($id){
        if(!$this->isValidId($id)){
            return array();
        }

        $params = array(
            '{id}' => $id
        );

        $authority = $this->getSingleResult(strtr($this->sql['authorityById'], $params));
        return $authority;
    }

    public function getAuthorityByName($name){
        $params = array(
            '{name}' => $name
        );

        $authority = $this->getSingleResult(strtr($this->sql['authorityByName'], $params));
        return $authority;
    }

    public function save($authority){
        if(!$this->validation($authority)){
            $this->writeLog("The resulting data set is invalid! <br>" . implode("<br>", $this->messages));
            $this->msg->setSessionMessage("The form is not filled in correctly! <br>" . implode("<br>", $this->messages));
            return null;
        }

        /*if(isset($authority["id"]) && !empty($authority["id"])){
            if($this->isValidId(intval($authority["id"]))){
                $this->id = intval($authority["id"]);
                $filename = $this->fileUpload();

                if($filename){
                    $authority["file_authority"] = $filename;
                }

                $res = $this->modify($authority);

            } else {
                $this->writeLog("A kapott id invalid: " . $authority["id"]);
                $this->msg->setSessionMessage("A kapott id invalid: " . $authority["id"]);
            }
        } else {*/
            
            $this->create($authority);
            $this->id = $this->getLastInsertedId();
        //}

        return $this->id;

    }

    /** Override */
    protected function validation($data){

        //name
        if(!isset($data["name"]) || empty($data["name"]) || $data["name"] == null){
            $this->messages[] = 'The name field is required.';
            return false;
        }

        if(!is_string($data["name"])){
            $this->messages[] = 'The name can only be text.';
            return false;
        }

        if(strlen($data["name"]) > 255){
            $this->messages[] = 'The length of the name cannot exceed 255 characters.';
            return false;
        }

        return true;
    }

    public function delete($id){
        if(!$this->isValidId($id)){
            return false;
        }

        $res = $this->deleteRecordById('authorities', $id);
        return $res;
    }
}

?>