<?php

class DBEntity {
	
	private $className = '';
	private $table = '';
	private $columns = [];
	private $database = NULL;


    public function __construct($database, string $class, string $tableName, array $columns) {
			$this->className = $class;
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
			$query->setFetchMode(PDO::FETCH_CLASS, $this->className);
			$result = $query->fetch();
			if(!$result){ // not found
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
		$values = array_reduce($this->columns, function($result, $value) use($entity) { 
			$result[$value] = $entity->$value; 
			return $result;
		}, []);
		
		if($this->getEntity($id) == false) {
			$joinedColumns = implode(', ', $this->columns);
			$joinedBindings = implode(', :', $this->columns);
			
			$sql = "INSERT INTO $this->table ($joinedColumns) VALUES (:$joinedBindings);";
		} else {
			$statements = array_map(function($name){ return "$name = :$name"; }, $this->columns);
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
	
	public function deleteEntity($id) : bool {
		$sql = "DELETE FROM $this->table WHERE id = $id";
		
		try {
			echo "$sql<br/>";
			$db = $this->getDBConntection();
			$result = $db->prepare($sql)->execute();
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