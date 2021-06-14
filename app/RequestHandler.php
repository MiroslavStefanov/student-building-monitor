<?php
require_once ($_SERVER['DOCUMENT_ROOT'].'/student-building-monitor/controllers/BaseController.php');
require_once ($_SERVER['DOCUMENT_ROOT'].'/student-building-monitor/controllers/ModelAndView.php');

class RequestHandler {
    private $controllers;

    public function __construct() {
        $this->controllers = array();
    }
	
	public function registerController(string $path, BaseController $controller) {
        $this->controllers[$path] = $controller;
    }

    public function handleRequest(string $path) {
        if(!array_key_exists($path, $this->controllers)) {
            throw new Exception("Unresolved request path: $path");
        }

        $controller = $this->controllers[$path];
        $method = $_SERVER['REQUEST_METHOD'];
        $result = false;
        if($method == 'GET') {
            $result = $controller->get();
        } else if($method == 'POST') {
            $result = $controller->post();
        } else {
            throw new Exception("Unhandled http method $method");
        }

        $result->render();
    }
}
?>