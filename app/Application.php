<?php

namespace monitor;

use Exception;

require_once ('app/RequestHandler.php');
require_once ('dao/Database.php');
require_once ('dao/CardHolder.php');
require_once ('controllers/ImportController.php');

class Application {
    private $config = [];
	private $db = NULL;
	private $entities = [];
	private $requestHandler;

	public function __construct($config) {
	    $this->config = $config;
	    $this->requestHandler = new RequestHandler();
	    $this->requestHandler->registerController("/Import.php", new ImportController($this));
	    $this->initialize();
    }
	
	public function getDBEntity(string $className) {
		$doesExist = array_key_exists($className, $this->entities);
		if($doesExist) {
			return $this->entities[$className];
		}

		throw new Exception("Unhandled db entity for class $className");
	}

	public function handleReqeust() {
	    $path = strtok($_SERVER["REQUEST_URI"], '?');
	    $prefix = $this->config['app_root'] . $this->config['app_endpoints'];
	    $path = str_replace($prefix, '', $path);
	    $this->requestHandler->handleRequest($path, $this);
    }

    public function getConfig() {
	    return $this->config;
    }

    private function initialize() {
	    $db_init = $this->config['db_init'];
	    if($db_init == 'create') {
	        $this->createDB();
        }

//	    $this->defineDBEntity('IdCounter', 'ID_COUNTERS');
	    $this->defineDBEntity('CardHolder', 'CARDHOLDERS');
	    var_dump($this->entities);
    }

    private function defineDBEntity(string $className, string $table) {
        $db = $this->connectDB();
        $entity = $db->makeEntity($className, $table);
        $this->entities[$className] = $entity;
    }

    private function createDB() {
        $db = $this->connectDB();
        $db->executeScript("SQL/CreateDatabase.sql");
        $db->executeScript("SQL/Tables.sql");
        $db->executeScript("SQL/NomInserts.sql");
    }

    private function connectDB() {
        if($this->db == NULL) {
            $this->db = new Database($this->config);
        }
        return $this->db;
    }
}
?>