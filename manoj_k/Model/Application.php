<?php

class Application{

    private $dbParams = array(
        "servername" => "servername",
        "username" => "username",
        "password" => "password",
        "dbname" => "database_name"
    );

    private $connection;
    private $connectionLive = false;

    protected $table = "";
    protected $fields = array();

    protected $id = null;

    public function __construct(){
        $this->connectDb();
        include_once("Utils/Messages.php");
        $this->msg = new Messages();
    }

    private function connectDb(){
        $this->connection = new mysqli($this->dbParams['servername'], $this->dbParams['username'], $this->dbParams['password'], $this->dbParams['dbname']);

        if($this->connection->connect_error){
            die("Connection failed: " . $this->connection->connect_error);
        }
        $this->connectionLive = true;
    }

    protected function isDbConnectionLive(){
        return $this->connectionLive;
    }

    protected function getResultList($sql){
        $resultList = array();
        $result = $this->connection->query($sql);

        if($result){
            if($result->num_rows > 0){
                while($row = $result->fetch_assoc()){
                    $resultList[] = $row;
                }
            } else {
                $this->writeLog("No value found for query.", $sql);
            }
        } else {
            $this->writeLog("Result 'false' értékkel tért vissza", $sql);
        }

        return $resultList;
    }

    protected function getSingleResult($sql){
        $resultList = $this->getResultList($sql);

        if(!$resultList){
            $this->writeLog("No value found for query.", $sql);
            return array();
        } else {
            return $resultList[0];
        }

    }

    protected function writeLog($string, $sql = null){
        $logStr = $string;

        if($sql != null){
            $logStr .= " -- SQL QUERY: " . $sql;
        }

        //$log = fopen("log/log.txt", "a");
        //fwrite($log, $logStr);
        //fclose($log);
    }

    protected function isValidId($id){
        if(is_int($id) && $id > 0){
            return true;
        } else {
            return false;
        }
    }

    protected function validation($data){
        return true;
    }

    protected function create($data){
        $sql = "INSERT INTO {$this->table} ( ";

        $insert = array();
        $insertData = array();

        foreach($this->fields as $field){
            if($field != 'id' && $data[$field] != "unknown"){
                if($data[$field] == "unknown"){
                    $insert[] = $field;
                    $insertData[] = "null";
                } else {
                    $insert[] = $field;
                    $insertData[] = "'" . $data[$field] . "'";
                }
            }
        }

        $sql .= implode(", ", $insert) . " ) VALUES ( " . implode(", ", $insertData) . " )";
        return $this->execute($sql);

    }

    protected function modify($data){
        $sql = "update {$this->table} set ";

        $update = array();
        foreach($this->fields as $field){
            if($field != "id"){
                if($data[$field] == "unknown"){
                    $update[] = $field . ' = null';
                } else {
                    $update[] = $field . ' = "' . $data[$field] . '"';
                }
            }
        }

        $sql .= implode(", ", $update);
        $sql .= " where id = " . $data["id"];
        return $this->execute($sql);
    }

    protected function execute($sql){
        $res = $this->connection->query($sql);
        return $res;
    }

    protected function getLastInsertedId(){
        $sql = "SELECT id FROM {$this->table} ORDER BY id DESC LIMIT 1";
        $res = $this->getSingleResult($sql);
        return intval($res['id']);
    }

    protected function deleteRecordById($table, $id){
        $result =  $this->execute("update $table set active = 0 where id = $id");
        return $result;
    }

    protected function htmlvalidate($data){
        return $data;
    }
}
?>