<?php

namespace monitor;

use Exception;

require_once ('app/RequestHandler.php');
require_once ('dao/Database.php');
require_once ('dao/CardHolder.php');
require_once ('dao/Course.php');
require_once ('dao/Employee.php');
require_once ('dao/Passage.php');
require_once ('dao/Student.php');
require_once ('dao/Tutor.php');
require_once ('controllers/ImportController.php');
require_once ('controllers/IndexController.php');
require_once ('controllers/CardholderController.php');
require_once ('controllers/StudentController.php');
require_once ('controllers/AboutController.php');
require_once ('controllers/DatabaseController.php');

class Application {
    private $config = [];
	private $db = NULL;
	private $entities = [];
	private $requestHandler;

	public function __construct($config) {
	    $this->config = $config;
	    $this->requestHandler = new RequestHandler();
	    $this->initialize();
    }
	
	public function getDBEntity(string $table) {
		$doesExist = array_key_exists($table, $this->entities);
		if($doesExist) {
			return $this->entities[$table];
		}

		throw new Exception("Unhandled db entity for table $table");
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

    public function createDB() {
        $db = $this->connectDB();
        $db->executeScript("SQL/CreateDatabase.sql");
        $db->executeScript("SQL/Tables.sql");
        $db->executeScript("SQL/NomInserts.sql");
    }

    private function initialize() {
	    $this->defineDBEntity('CardHolder', 'CARDHOLDERS');
	    $this->defineDBEntity('Course', 'COURSES');
	    $this->defineDBEntity('Employee', 'EMPLOYEES');
	    $this->defineDBEntity('Passage', 'PASSAGES');
	    $this->defineDBEntity('Student', 'STUDENTS');
	    $this->defineDBEntity('Tutor', 'TUTORS');
	    $this->defineDBEntity('DBEnum', 'NOM_CARDHOLDER_TYPE');
	    $this->defineDBEntity('DBEnum', 'NOM_ACADEMIC_DEGREE');
	    $this->defineDBEntity('DBEnum', 'NOM_ACADEMIC_SPECIALIZATION');

      $this->requestHandler->registerController("/Import.php", new ImportController($this));
      $this->requestHandler->registerController("/Index.php", new IndexController($this));
      $this->requestHandler->registerController("/", new IndexController($this));
      $this->requestHandler->registerController("/Cardholders.php", new CardholderController($this));
			$this->requestHandler->registerController("/Students.php", new StudentController($this));
      $this->requestHandler->registerController("/About.php", new AboutController($this));
      $this->requestHandler->registerController("/Databases.php", new DatabaseController($this));
    }

    private function defineDBEntity(string $className, string $table) {
        $db = $this->connectDB();
        $entity = $db->makeEntity($className, $table);
        $this->entities[$table] = $entity;
    }

    private function connectDB() {
        if($this->db == NULL) {
            $this->db = new Database($this->config);
        }
        return $this->db;
    }
}
?>