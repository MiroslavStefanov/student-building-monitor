<?php

require_once($_SERVER['DOCUMENT_ROOT'].'/student-building-monitor/app/Config.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/student-building-monitor/utils/FileService.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/student-building-monitor/dao/DBEntity.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/student-building-monitor/dao/IdCounter.php');

class Database {

    private $connection = NULL;
	private $idEntity = NULL;

    public function __construct() {
		$this->idEntity = $this->makeEntity('IdCounter', "ID_COUNTERS");
    }
	
	public function makeEntity(string $class, string $table) : DBEntity {
        $database = Config::getInstance()->getProperty("database.name");
        if(!$database) {
            throw new Exception("Missing property database.name");
        }
		return new DBEntity($this->getConntection(), $this->idEntity, $class, $database.".".$table);
	}
	
	public function executeScript(string $filename) {
        echo "Executing script $filename<br/>";
        try {
			$sql = readFile($filename);
			$result = $this->getConntection()->exec($sql);

			if($result === false) {
				echo "Fail<br/>";
			} else {
			    echo "Success<br/>";
            }
			
		} catch (Exception $e){
			$error_msg = $e->getMessage();
            echo "Fail<br/>";
            echo "$error_msg<br/>";
		}
	}
		

	private function getConntection(){
		if($this->connection) {
			return $this->connection;
		}

        try {
			$host = $this->properties["database.host"];
			$port = $this->properties["database.port"];
			$dbname = $this->properties["database.name"];
			$url = "mysql:host=$host;port=$port;";
			$username = $this->properties["database.user"];
			$password = $this->properties["database.password"];
            $this->connection  = new PDO($url, $username, $password);
        }catch(PDOException $e){
            $error_msg = $e->getMessage();
            echo $error_msg;
			die("Couldn't connect to database! Error: $error_msg");
        }

        return $this->connection;
    }
}

?>