<?php

class DBEntity {
	
	private $table = '';
	private $columns = [];
	private $database = NULL;


    public function __construct($database, string $tableName, array $columns) {
			$this->table = $tableName;
			$this->columns = $columns;
			$this->database = $database;
        }
	
	public function defineColum(string $name) {
		array_push($this->columns, $name);
		$this->columns = array_unique($this->columns);
	}
	
	public function getEntity(string $id) {
		try {
			$db = $this->getDBConntection();
			$sql   = "SELECT * FROM {$this->table} where id = {$id}";
			$query = $db->query($sql) or die("failed!");
			$result = $query->fetch(PDO::FETCH_CLASS);
			if( gettype($row) == 'boolean'){ // not found
				return false;
			}  else {
				return $result;
			}
		} catch (PDOException $e){
			$error_msg = $e->getMessage();
            echo $error_msg;
			return false;
		}
	}

    public function saveEntity($entity, $id) : bool {
		$sql = '';
		$values = array_reduce($this->columns, function($result, $value) { 
			$result[$value] = $entity->$value; 
			return $result;
		}, []);
		
		if($this->getEntity() == false) {
			$joinedColumns = implode(', ', $this->columns);
			$joinedBindings = implode(', :', $this->columns);
			
			$sql = "INSERT INTO `products` ($joinedColumns) VALUES (:$joinedBindings);";
		} else {
			$statements = array_map(function($name){ return '$name = :$name'; }, $this->columns);
			$joinedStatements = implode(', ', $statements);
			$sql = "UPDATE $this->table SET $joinedStatements WHERE id=$id;";
		}
		
		try {
			$db = $this->getDBConntection();
			$result = $db->prepare($sql)->execute($values);
			return $result != false;
		} catch (PDOException $e){
			$error_msg = $e->getMessage();
            echo $error_msg;
			return false;
		}
	}
	
	private function getDBConntection() {
		if($this->database == NULL) {
			die ("Database not found");
		}
		
		return $this->database;
	}
}

?>