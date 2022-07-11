<?php

include_once("Application.php");

class Images extends Application{
    
    private $sql = array(
        'allImages' => "select i.id, i.name from images i
                        WHERE i.active = 1",
        "imageById" => "select * FROM images i
                        WHERE i.id = {id} and i.active = 1
                        LIMIT 1"
    );

    private $messages = array();

    protected $table = "images";
    protected $fields = array("name");

    public function __construct(){
        parent::__construct();
    }

    public function getAllImages(){
        $images = $this->getResultList($this->sql['allImages']);
        return $images;
    }

    public function getImageById($id){
        if(!$this->isValidId($id)){
            return array();
        }

        $params = array(
            '{id}' => $id
        );

        $image = $this->getSingleResult(strtr($this->sql['imageById'], $params));
        return $image;
    }

    public function save($image){
        if(!$this->validation($image)){
            $this->writeLog("The resulting data set is invalid! <br>" . implode("<br>", $this->messages));
            $this->msg->setSessionMessage("The form is not filled in correctly! <br>" . implode("<br>", $this->messages));
            return null;
        }

        /*if(isset($image["id"]) && !empty($image["id"])){
            if($this->isValidId(intval($image["id"]))){
                $this->id = intval($image["id"]);
                $filename = $this->fileUpload();

                if($filename){
                    $image["file_image"] = $filename;
                }

                $res = $this->modify($image);

            } else {
                $this->writeLog("A kapott id invalid: " . $image["id"]);
                $this->msg->setSessionMessage("A kapott id invalid: " . $image["id"]);
            }
        } else {*/
            
            $this->create($image);
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

    public function fileUpload($data){
        if(isset($data) && !empty($data['file']['tmp_name'])){
            $targetDir = "Sources/uploads/";
            $targetFile = $targetDir . basename($data['file']['name']);

            $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

            $check = getimagesize($data['file']['tmp_name']);

            if($check !== false){

                if(file_exists($targetFile)){
                    $this->msg->setSessionMessage("The file already exists!");
                    return false;
                }

                if($data['file']['size'] > 5000000){
                    $this->msg->setSessionMessage("The file size is too large!");
                    return false;
                }

                $type = $data["file"]["type"];
                if($type != "image/jpg" && $type != "image/jpeg" && $type != "image/png" && $type != "image/gif"){
                    $this->msg->setSessionMessage("Allowed image formats: JPG, JPEG, PNG or GIF.");
                    return false;
                }

                /*
                if($imageFileType != 'jpg' && $imageFileType != 'png' && $imageFileType != 'jpeg' && $imageFileType != 'gif'){
                    $this->msg->setSessionMessage("A megengedett képformátumok: JPG, JPEG, PNG vagy GIF.");
                    return false;
                }*/

                if(move_uploaded_file($data['file']['tmp_name'], $targetFile)){
                    $filename = basename($data['file']['name']);
                    return $filename;
                } else {
                    $this->msg->setSessionMessage("File transfer failed!");
                    return false;
                }
            } else {
                $this->msg->setSessionMessage("The uploaded file is not an image!");
                return false;
            }
        }
        return false;
    }

    public function delete($id){
        if(!$this->isValidId($id)){
            return false;
        }

        $res = $this->deleteRecordById('images', $id);
        return $res;
    }
}

?>