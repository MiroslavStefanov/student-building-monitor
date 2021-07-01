<?php

namespace monitor;

use Exception;
use PDO;
use PDOException;

require_once ('utils/FileService.php');
require_once ('dao/DBEntity.php');
require_once ('dao/IdCounter.php');

class Database {
    private $config = [];
    private $connection = NULL;
	private $idEntity = NULL;

    public function __construct($config) {
        $this->config = $config;
		$this->idEntity = $this->makeEntity('IdCounter', "ID_COUNTERS");
    }
	
	public function makeEntity(string $class, string $table) : DBEntity {
        $database = $this->config["db_name"];
        if(!$database) {
            throw new Exception("Missing property db_name");
        }

        $classPath = 'monitor\\'.$class;
		return new DBEntity($this->getConntection(), $this->idEntity, $classPath, $database.".".$table);
	}
	
	public function executeScript(string $filename) {
        try {
            $filename = $this->config['app_root'].'/'.$filename;
			$sql = readFile($filename);
			$result = $this->getConntection()->exec($sql);
			
		} catch (Exception $e){
			throw $e;
		}
	}
		

	private function getConntection(){
		if($this->connection) {
			return $this->connection;
		}

        try {
			$host = $this->config["db_host"];
			$port = $this->config["db_port"];
			$url = "mysql:host=$host;port=$port;";
			$username = $this->config["db_user"];
			$password = $this->config["db_password"];
            $this->connection  = new PDO($url, $username, $password);
        }catch(PDOException $e){
            $error_msg = $e->getMessage();
			die("Couldn't connect to database! Error: $error_msg");
        }

        return $this->connection;
    }
}

?>