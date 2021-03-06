<?php

namespace monitor;

use Exception;
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

    public function getClass() : string {
        return $this->className;
    }

    public function innerJoin(string $alias) : string {
        return "INNER JOIN $this->table AS $alias";
    }

	public function getEntity(string $id) {
        $sql   = "SELECT * FROM {$this->table} where id = $id";
        $result = $this->executeStatement($sql, function ($statement) {
            $result = $statement->execute();
            if($result === false) {
                return false;
            }
            $statement->setFetchMode(PDO::FETCH_CLASS, $this->className);
            return $statement->fetch();
        });
        return $result;
	}
	
	public function getAllEntities() : array {
        $sql   = "SELECT * FROM {$this->table}";
        $result = $this->executeStatement($sql, function ($statement) {
            $result = $statement->execute();
            if($result === false) {
                return false;
            }
            $statement->setFetchMode(PDO::FETCH_CLASS, $this->className);
            return $statement->fetchAll();
        });
        return $result;
	}

    public function saveEntity($entity) {
		$idCounter = false;
		
		$isNewEntity = !property_exists($entity, 'ID') 
			|| !$entity->ID
			|| $this->getEntity($entity->ID) === false;
		if($isNewEntity) {
		    $escapedColumns = array_map(function ($column) { return "`$column`"; }, $this->columns);
			$joinedColumns = implode(', ', $escapedColumns);
			$joinedBindings = implode(', :', $this->columns);
			$idCounter = $this->getIdCounter();
			$entity->ID = $idCounter->NEXT_ID;
			$sql = "INSERT INTO $this->table ($joinedColumns) VALUES (:$joinedBindings);";
		} else {
			$statements = array_map(function($name){ return "`$name` = :$name"; }, $this->columns);
			$joinedStatements = implode(', ', $statements);
			$sql = "UPDATE $this->table SET $joinedStatements WHERE id=$entity->ID;";
		}

			$values = array_reduce($this->columns, function($result, $value) use($entity) {
					$result[$value] = $entity->$value;
					return $result;
				}, []);

			$result = $this->executeStatement($sql, function ($s) use ($values) {return $s->execute($values);});
			if($isNewEntity) {
				$idCounter->NEXT_ID = $idCounter->NEXT_ID + 1;
				$this->idEntity->saveEntity($idCounter);
			}
	}
	
	public function deleteEntity($id) {
		$sql = "DELETE FROM $this->table WHERE id = $id";
		$this->executeStatement($sql, function ($s) {return $s->execute();});
	}

	public function createSelectStatement(string $columns, array $joins, string $where, string $groupBy, string $orderBy, int $limit = 0) : string {
        $sql = "SELECT $columns FROM $this->table ";

        if(!empty($joins)) {
            $sql .= implode(" ", $joins);
        } else{
            $sql .= ' ';
        }

        if(!empty($where)) {
            $sql .= "WHERE $where ";
        }else{
            $sql .= ' ';
        }

        if (!empty($groupBy)) {
            $sql .= "GROUP BY $groupBy ";
        }else{
            $sql .= '';
        }

        if(!empty($orderBy)) {
            $sql .= "ORDER BY $orderBy ";
        }else{
            $sql .= ' ';
        }

        if($limit > 0) {
            $sql .= "LIMIT ".strval($limit);
        }

        return $sql;
    }

	public function select(string $columns, array $joins, string $where, string $groupBy, string $orderBy, int $limit = 0) : array {
        $sql = $this->createSelectStatement($columns, $joins, $where, $groupBy, $orderBy, $limit);
        //echo $sql;
        writeFile("/student-building-monitor/logs/log.txt", $sql);
        $result = $this->executeStatement($sql, function ($s) {
            $result = $s->execute();
            if($result === false) {
                return false;
            }
            return $s->fetchAll(PDO::FETCH_ASSOC);
        });
        return $result;
    }

    private function executeStatement(string $sql, $executor) {
        try {
            $statement = $this->database->prepare($sql);
            if($statement === false) {
                $errors = $this->database->errorInfo();
                $sqlError = $errors[2];
                throw new Exception("Error $sqlError in statement $sql");
            }
            $result = $executor($statement);
            if($result === false) {
                $errors = $statement->errorInfo();
                $sqlError = $errors[2];
                throw new Exception("Error $sqlError in statement $sql");
            }
            return $result;
        } catch (PDOException $e){
            throw new Exception("Sql statement failed $sql", 0, $e);
        }
    }
	
	private function getIdCounter() : IdCounter {
		$key = $this->getTableName();
		$entities = $this->idEntity->getAllEntities();
		$entities = array_filter($entities, function ($entity) use ($key) { return $entity->TABLE_NAME == $key;} );
		return reset($entities);
	}
	
	private function getTableName() : string {
		$elements = explode('.', $this->table);
		return $elements[1];
	}
}

?>