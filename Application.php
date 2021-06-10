<?php

require('dao/Database.php');
// require('DBEntity.php');

class Application {
	
	private $properties = [
		'database.host' => 'localhost',
		'database.port' => '3306',
		'database.name' => 'StudentBuildingMonitor',
		'database.user' => 'root',
		'database.password' => ''
	];
	
	private $db = NULL;
	
	public function connectDB(bool $create) {
		if($this->db == NULL) {
			$this->db = new Database($this->properties);
		}
		if($create) {
			$this->db->executeScript("SQL/CreateDatabase.sql");
			$this->db->executeScript("SQL/Tables.sql");
			$this->db->executeScript("SQL/NomInserts.sql");
		}
		
		return $this->db;
	}
	
	
}

class Nom {
	public $ID = '';
	public $NAME = '';
	
	public function getName() {return $this->NAME;}
}

$app = new Application();
$db = $app->connectDB(true);

?>