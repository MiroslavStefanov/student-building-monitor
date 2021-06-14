<?php

require_once ($_SERVER['DOCUMENT_ROOT'].'/student-building-monitor/utils/FileService.php');
require_once ($_SERVER['DOCUMENT_ROOT'].'/student-building-monitor/app/Config.php');

class ModelAndView {
    private $view = false;
    private $model = [];

    public static function withModelAndView($model, $view) : ModelAndView {
        $result = new ModelAndView();
        $result->view = $view;
        $result->model = $model;
        return $result;
    }

    public function render() {
        if($this->view === false) {
            return;
        }

        $config = Config::getInstance();
        $filePath = $config->getProperty('application.root')
            .$config->getProperty('application.pages')
            .$this->view;
        $page = utils\readFile($filePath);
        echo $page;
    }
}