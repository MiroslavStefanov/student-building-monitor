<?php

require_once ($_SERVER['DOCUMENT_ROOT'].'/student-building-monitor/app/Config.php');
require_once ($_SERVER['DOCUMENT_ROOT'].'/student-building-monitor/app/RequestHandler.php');
require_once ($_SERVER['DOCUMENT_ROOT'].'/student-building-monitor/dao/Database.php');
require_once ($_SERVER['DOCUMENT_ROOT'].'/student-building-monitor/dao/CardHolder.php');
require_once ($_SERVER['DOCUMENT_ROOT'].'/student-building-monitor/utils/CSVService.php');
require_once ($_SERVER['DOCUMENT_ROOT'].'/student-building-monitor/controllers/ImportController.php');

class Application {
    private static $instance = NULL;


	private $db = NULL;
	private $entities = [];
	private $requestHandler;

	public static function getInstance() {
        if (self::$instance == NULL)
        {
            self::$instance = new Application();
        }

        return self::$instance;
    }

	private function __construct() {
	    $this->requestHandler = new RequestHandler();
	    $this->requestHandler->registerController("/Import.php", new ImportController($this));
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
	
	public function importDBEntities(string $data, string $className) {
		readCSVEntities($data);
	}

	public function handleReqeust() {
	    $path = strtok($_SERVER["REQUEST_URI"], '?');
	    $config = Config::getInstance();
	    $prefix = $config->getProperty('application.root')
            . $config->getProperty('application.endpoints');
	    $path = str_replace($prefix, '', $path);
	    $this->requestHandler->handleRequest($path);
    }

    private function connectDB() {
        if($this->db == NULL) {
            $this->db = new Database();
        }
        return $this->db;
    }
}
?>