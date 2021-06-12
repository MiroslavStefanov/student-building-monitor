<?php

require_once('dao/Database.php');
require_once('dao/CardHolder.php');

class Application {
	
	private $properties = [
		'database.host' => 'localhost',
		'database.port' => '3306',
		'database.name' => 'StudentBuildingMonitor',
		'database.user' => 'root',
		'database.password' => ''
	];
	
	private $db = NULL;
	private $entities = [];
	
	private function connectDB() {
		if($this->db == NULL) {
			$this->db = new Database($this->properties);
		}
		return $this->db;
	}
	
	public function createDB() {
		$db = $this->connectDB();
		$db->executeScript("SQL/CreateDatabase.sql");
		$db->executeScript("SQL/Tables.sql");
		$db->executeScript("SQL/NomInserts.sql");
	}
	
	public function getDBEntity(string $className, string $table) {
		$doesExist = array_key_exists($className, $this->entities);
		if($doesExist) {
			return $this->entities[$className];
		}
		
		$db = $this->connectDB();
		$entity = $db->makeEntity($className, $table);
		$this->entities[$className] = $entity;
		return $entity;
	}
	
}

class Nom {
	public $ID = '';
	public $NAME = '';
	
	public function getName() {return $this->NAME;}
}

$app = new Application();
$app->createDB();
$cardHolders = $app->getDBEntity('CardHolder', 'CARDHOLDERS');

$pesho = new CardHolder();
$pesho->NAME = 'Pesho';
$pesho->BIRTH_DATE = date('Y-m-d');
$pesho->EMAIL = "pesho@abv.bg";
$pesho->ACADEMIC_DEGREE = 1;
$pesho->TYPE = 1;
$cardHolders->saveEntity($pesho);
$pesho->TYPE = 2;
$cardHolders->saveEntity($pesho);
echo var_dump($cardHolders->getEntity($pesho->ID));
$cardHolders->deleteEntity($pesho->ID);
?>