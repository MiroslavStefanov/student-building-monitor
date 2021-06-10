<?php

require('DBEntity.php');

class Database {
	
	private $properties = [];
    private $connection = NULL;


    public function __construct(array $properties) {
		$this->properties = $properties;
    }
	
	public function makeEntity(string $table, array $columns) : DBEntity {		
		return new DBEntity($this->getConntection(), $table, $columns);
	}
	
	public function executeScript(string $filename) {
		
		try {
			$file = fopen($filename, 'r');
			if($file == false) {
				throw new Exception("Cannot read file $filename");
			}
			
			$sql = fread($file, filesize($filename));
			
			echo "Executing script $filename<br/>";
			$result = $this->getConntection()->exec($sql);
			
			if(!$result) {
				echo "Failed<br/>";
			} else {
				echo "Success<br/>";
			}
			
		} catch (PDOException $e){
			$error_msg = $e->getMessage();
            echo $error_msg;
		} finally {
			if($file != false) {
				fclose($file);
			}
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
			throw $e;
        }

        return $this->connection;
    }
}

?>