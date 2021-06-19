<?php

namespace monitor;

use PDO;
use PDOException;

require_once ('dao/IdCounter.php');
require_once ('dao/CardHolder.php');

class DBEntity {
	
	private $className = '';
	private $table = '';
	private $columns = [];
	private $database = NULL;
	private $idEntity = NULL;


    public function __construct($database, $idEntity, string $class, string $tableName) {
			$this->className = $class;
			$this->table = $tableName;
			$this->database = $database;
			$this->idEntity = $idEntity;

			$classReflection = get_class_vars($class);
			foreach($classReflection as $property => $value) {
				array_push($this->columns, $property);
			}
        }
	
	public function getEntity(string $id) {
		try {
			$sql   = "SELECT * FROM {$this->table} where id = {$id}";
			$query = $this->database->query($sql) or die("failed!");
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
	
	public function getAllEntities() {
		try {
			$sql   = "SELECT * FROM {$this->table}";
			$query = $this->database->query($sql) or die("failed!");
			$query->setFetchMode(PDO::FETCH_CLASS, $this->className);
			$result = $query->fetchAll();
			return $result;
		} catch (PDOException $e){
			$error_msg = $e->getMessage();
            echo $error_msg;
			return false;
		}
	}

    public function saveEntity($entity) : bool {
		$sql = '';
		$idCounter = false;
		
		$isNewEntity = !property_exists($entity, 'ID') 
			|| !$entity->ID
			|| $this->getEntity($entity->ID) === false;
		if($isNewEntity) {
			$joinedColumns = implode(', ', $this->columns);
			$joinedBindings = implode(', :', $this->columns);
			$idCounter = $this->getIdCounter();
			$entity->ID = $idCounter->NEXT_ID;
			$sql = "INSERT INTO $this->table ($joinedColumns) VALUES (:$joinedBindings);";
		} else {
			$statements = array_map(function($name){ return "$name = :$name"; }, $this->columns);
			$joinedStatements = implode(', ', $statements);
			$sql = "UPDATE $this->table SET $joinedStatements WHERE id=$entity->ID;";
		}
		
		try {
			$values = array_reduce($this->columns, function($result, $value) use($entity) { 
					$result[$value] = $entity->$value; 
					return $result;
				}, []);
			$result = $this->database->prepare($sql)->execute($values);
			if($result && $isNewEntity) {
				$idCounter->NEXT_ID = $idCounter->NEXT_ID + 1;
				$this->idEntity->saveEntity($idCounter);
			}
			
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
			$result = $this->database->prepare($sql)->execute();
			return $result != false;
		} catch (PDOException $e){
			$error_msg = $e->getMessage();
            echo $error_msg;
			return false;
		}
	}
	
	private function getIdCounter() : IdCounter {
		$key = $this->getTableName();
		$entities = $this->idEntity->getAllEntities();
		$entities = array_filter($entities, function ($entity) use ($key) { return $entity->TABLE_NAME == $key;} );
		return $entities[0];
	}
	
	private function getTableName() : string {
		$elements = explode('.', $this->table);
		return $elements[1];
	}
}

?>