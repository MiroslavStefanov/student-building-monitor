<?php

class Config {
    private $config;

    private static $instance = NULL;

    public static function getInstance() {
        if (self::$instance == NULL)
        {
            self::$instance = new Config();
        }

        return self::$instance;
    }

    private function __construct() {
        $this->config = [
            'database.host' => 'localhost',
            'database.port' => '3306',
            'database.name' => 'StudentBuildingMonitor',
            'database.user' => 'root',
            'database.password' => '',
            'application.root' => '/student-building-monitor',
            'application.pages' => '/View/dist/',
            'application.endpoints' => '/endpoints'
        ];
    }

    public function getProperty(string $key) {
        if(!array_key_exists($key, $this->config)) {
            return false;
        }

        return $this->config[$key];
    }
}
