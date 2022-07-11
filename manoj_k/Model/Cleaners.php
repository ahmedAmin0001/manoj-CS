<?php

include_once("Application.php");

class Cleaners extends Application{

    private $sql = array(
        'allCleaners' => "select c.*, a.name AS authority, i.name AS image, GROUP_CONCAT(DISTINCT ca.day_id, ca.hour_id SEPARATOR ',') AS available from cleaners c
                            LEFT JOIN images i ON i.id = c.image_id
                            LEFT JOIN authorities a ON a.id = c.authority_id
                            LEFT JOIN cleaners_available ca ON ca.cleaner_id = c.id
                            WHERE c.active = 1
                            GROUP BY c.id",
        'cleanerById' => "select c.*, a.name AS authority, i.name AS image from cleaners c
                        LEFT JOIN images i ON i.id = c.image_id
                        LEFT JOIN authorities a ON a.id = c.authority_id
                        where c.id = {id} and c.active = 1
                        limit 1",
        'cleanerByUsername' => "select c.*, a.name AS authority, i.name AS image from cleaners c
                        LEFT JOIN images i ON i.id = c.image_id
                        LEFT JOIN authorities a ON a.id = c.authority_id
                        where c.username = '{username}' and c.active = 1
                        limit 1",
        "changeDetail" => "update cleaners SET `{key}` = '{new}'  WHERE id = {id}"
    );

    private $messages = array();

    protected $table = "cleaners";
    protected $fields = array("first_name", "family_name", "username", "password", "pmd5", "email", "image_id", "phone", "birthdate", "address", "state", "city", "postal", "country", "language");

    public function __construct(){
        parent::__construct();
    }

    public function getAllCleaners(){
        $cleaners = $this->getResultList($this->sql['allCleaners']);
        return $cleaners;
    }

    public function changeDetail($data){
        $key = $data["key"];
        $new = $data["new"];
        $id = $data["id"];

        if($key == "password"){
            $pmd5 = md5($new);

            $params = array(
                "{key}" => $key,
                "{new}" => $new,
                "{id}" => $id
            );
            $res1 = $this->execute(strtr($this->sql["changeDetail"], $params));

            $key = "pmd5";
            $params = array(
                "{key}" => $key,
                "{new}" => $pmd5,
                "{id}" => $id
            );
            $res2 = $this->execute(strtr($this->sql["changeDetail"], $params));

            $res = false;
            if($res1 && $res2){
                $res = true;
            }
            return $res;

        } else {
            $params = array(
                "{key}" => $key,
                "{new}" => $new,
                "{id}" => $id
            );
    
            $res = $this->execute(strtr($this->sql["changeDetail"], $params));
            return $res;
        }
    }

    public function getCleanerById($id){
        if(!$this->isValidId($id)){
            return array();
        }

        $params = array(
            '{id}' => $id
        );

        $cleaner = $this->getSingleResult(strtr($this->sql['cleanerById'], $params));
        return $cleaner;
    }

    public function getCleanerByUsername($username){

        if(!is_string($username)){
            $this->msg->setSessionMessage("The received username is not text" . $username);
            return array();
        }

        if(strlen($username) > 255){
            $this->msg->setSessionMessage("The length of the username cannot exceed 255 characters.");
            return array();
        }

        $params = array(
            "{username}" => $username
        );

        $cleaner = $this->getSingleResult(strtr($this->sql['cleanerByUsername'], $params));
        return $cleaner;
    }

    public function save($cleaner){
        $cleaner = $this->htmlvalidate($cleaner);
        if(!$this->validation($cleaner)){
            $this->writeLog("The resulting data set is invalid! <br>" . implode("<br>", $this->messages));
            $this->msg->setSessionMessage("The form is not filled in correctly! <br>" . implode("<br>", $this->messages));
            return null;
        }

        $cleaner["pmd5"] = md5($cleaner["password"]);

        
        if(isset($cleaner['id']) && !empty($cleaner['id'])){
            if($this->isValidId(intval($cleaner['id']))){
                $this->id = intval($cleaner['id']);
                $res = $this->modify($cleaner);
            } else {
                $this->writeLog("The received id invalid" . $cleaner['id']);
                $this->msg->setSessionMessage("The received id invalid" . $cleaner['id']);
            }

        } else {
            $res = $this->create($cleaner);
            $this->id = $this->getLastInsertedId();
        }

        return $this->id;
    }

    protected function htmlvalidate($data){
        if(isset($data["first_name"])){
            $data["first_name"] = htmlspecialchars($data["first_name"]);
        }
        if(isset($data["family_name"])){
            $data["family_name"] = htmlspecialchars($data["family_name"]);
        }
        if(isset($data["username"])){
            $data["username"] = htmlspecialchars($data["username"]);
        }
        if(isset($data["password"])){
            $data["password"] = htmlspecialchars($data["password"]);
        }
        if(isset($data["email"])){
            $data["email"] = htmlspecialchars($data["email"]);
        }
        if(isset($data["image_id"])){
            $data["image_id"] = htmlspecialchars($data["image_id"]);
        }
        if(isset($data["phone"])){
            $data["phone"] = htmlspecialchars($data["phone"]);
        }
        if(isset($data["birthdate"])){
            $data["birthdate"] = htmlspecialchars($data["birthdate"]);
        }
        if(isset($data["address"])){
            $data["address"] = htmlspecialchars($data["address"]);
        }
        if(isset($data["state"])){
            $data["state"] = htmlspecialchars($data["state"]);
        }
        if(isset($data["city"])){
            $data["city"] = htmlspecialchars($data["city"]);
        }
        if(isset($data["postal"])){
            $data["postal"] = htmlspecialchars($data["postal"]);
        }
        if(isset($data["country"])){
            $data["country"] = htmlspecialchars($data["country"]);
        }
        if(isset($data["authority_id"])){
            $data["authority_id"] = htmlspecialchars($data["authority_id"]);
        }
        if(isset($data["language"])){
            $data["language"] = htmlspecialchars($data["language"]);
        }
        return $data;
    }

    protected function validation($data){

        //first_name
        if(!isset($data['first_name']) || empty($data['first_name']) || $data['first_name'] == null){
            $this->messages[] = 'The first name field is required.';
            return false;
        }

        if(!is_string($data['first_name'])){
            $this->messages[] = 'The first name can only be text.';
            return false;
        }

        if(strlen($data['first_name']) > 255){
            $this->messages[] = 'The length of the first name cannot exceed 255 characters.';
            return false;
        }

        //family_name
        if(!isset($data['family_name']) || empty($data['family_name']) || $data['family_name'] == null){
            $this->messages[] = 'The family name field is required.';
            return false;
        }

        if(!is_string($data['family_name'])){
            $this->messages[] = 'The family name can only be text.';
            return false;
        }

        if(strlen($data['family_name']) > 255){
            $this->messages[] = 'The length of the family name cannot exceed 255 characters.';
            return false;
        }

        //username
        if(!isset($data['username']) || empty($data['username']) || $data['username'] == null){
            $this->messages[] = 'The username field is required.';
            return false;
        }

        if(!is_string($data['username'])){
            $this->messages[] = 'The username can only be text.';
            return false;
        }

        if(strlen($data['username']) > 255){
            $this->messages[] = 'The length of the username cannot exceed 255 characters.';
            return false;
        }

        //email
        if(!isset($data['email']) || empty($data['email']) || $data['email'] == null){
            $this->messages[] = 'The email field is required.';
            return false;
        }

        if(strlen($data['email']) > 255){
            $this->messages[] = 'The length of the email cannot exceed 255 characters.';
            return false;
        }

        //image_id
        if(!isset($data['image_id']) || empty($data['image_id']) || $data['image_id'] == null){
            $this->messages[] = 'The image_id is required.';
            return false;
        }

        if(!is_string($data['image_id'])){
            $this->messages[] = 'The image_id can only be text.';
            return false;
        }

        if(strlen($data['image_id']) > 255){
            $this->messages[] = 'The length of the image_id cannot exceed 255 characters.';
            return false;
        }

        //phone
        if(!isset($data['phone']) || empty($data['phone']) || $data['phone'] == null){
            $this->messages[] = 'The phone field is required.';
            return false;
        }

        if(!is_string($data['phone'])){
            $this->messages[] = 'The phone can only be text.';
            return false;
        }

        if(strlen($data['phone']) > 255){
            $this->messages[] = 'The length of the phone cannot exceed 255 characters.';
            return false;
        }

        //birthdate
        if(!isset($data['birthdate']) || empty($data['birthdate']) || $data['birthdate'] == null){
            $this->messages[] = 'The birthdate field is required.';
            return false;
        }

        if(!is_string($data['birthdate'])){
            $this->messages[] = 'The birthdate can only be text.';
            return false;
        }

        if(strlen($data['birthdate']) > 255){
            $this->messages[] = 'The length of the birthdate cannot exceed 255 characters.';
            return false;
        }

        //address
        if(!isset($data['address']) || empty($data['address']) || $data['address'] == null){
            $this->messages[] = 'The address field is required.';
            return false;
        }

        if(!is_string($data['address'])){
            $this->messages[] = 'The address can only be text.';
            return false;
        }

        if(strlen($data['address']) > 255){
            $this->messages[] = 'The length of the address cannot exceed 255 characters.';
            return false;
        }

        //state
        if(!isset($data['state']) || empty($data['state']) || $data['state'] == null){
            $this->messages[] = 'The state field is required.';
            return false;
        }

        if(!is_string($data['state'])){
            $this->messages[] = 'The state can only be text.';
            return false;
        }

        if(strlen($data['state']) > 255){
            $this->messages[] = 'The length of the state cannot exceed 255 characters.';
            return false;
        }

        //city
        if(!isset($data['city']) || empty($data['city']) || $data['city'] == null){
            $this->messages[] = 'The city field is required.';
            return false;
        }

        if(!is_string($data['city'])){
            $this->messages[] = 'The city can only be text.';
            return false;
        }

        if(strlen($data['city']) > 255){
            $this->messages[] = 'The length of the city cannot exceed 255 characters.';
            return false;
        }

        //postal
        if(!isset($data['postal']) || empty($data['postal']) || $data['postal'] == null){
            $this->messages[] = 'The postal field is required.';
            return false;
        }

        if(!is_string($data['postal'])){
            $this->messages[] = 'The postal can only be text.';
            return false;
        }

        if(strlen($data['postal']) > 255){
            $this->messages[] = 'The length of the postal cannot exceed 255 characters.';
            return false;
        }

        //country
        if(!isset($data['country']) || empty($data['country']) || $data['country'] == null){
            $this->messages[] = 'The country field is required.';
            return false;
        }

        if(!is_string($data['country'])){
            $this->messages[] = 'The country can only be text.';
            return false;
        }

        if(strlen($data['country']) > 255){
            $this->messages[] = 'The length of the country cannot exceed 255 characters.';
            return false;
        }

        //authority_id
        if(!isset($data['authority_id']) || empty($data['authority_id']) || $data['authority_id'] == null){
            $this->messages[] = 'The authority_id field is required.';
            return false;
        }

        if(!is_string($data['authority_id'])){
            $this->messages[] = 'The authority_id can only be text.';
            return false;
        }

        if(strlen($data['authority_id']) > 255){
            $this->messages[] = 'The length of the authority_id cannot exceed 255 characters.';
            return false;
        }

        //language
        if(!isset($data['language']) || empty($data['language']) || $data['language'] == null){
            $this->messages[] = 'The language field is required.';
            return false;
        }

        if(!is_string($data['language'])){
            $this->messages[] = 'The language can only be text.';
            return false;
        }

        if(strlen($data['language']) > 255){
            $this->messages[] = 'The length of the language cannot exceed 255 characters.';
            return false;
        }
        
        //password
        if(!isset($data['password']) || empty($data['password']) || $data['password'] == null){
            $this->messages[] = 'The password field is required.';
            return false;
        }

        if(strlen($data['password']) > 255){
            $this->messages[] = 'The length of the password cannot exceed 255 characters.';
            return false;
        }
        
        return true;
    }


    public function delete($id){
        if(!$this->isValidId($id)){
            return false;
        }

        $res = $this->deleteRecordById('cleaners', $id);
        return $res;
    }

}

?>