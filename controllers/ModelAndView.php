<?php

namespace monitor;

class ModelAndView {
    private $view = false;
    private $model = [];

    public static function withModelAndView($model, $view) : ModelAndView {
        $result = new ModelAndView();
        $result->view = $view;
        $result->model = $model;
        return $result;
    }

    public function render($application) {
        if($this->view === false) {
            return;
        }

        $config = $application->getConfig();
        include_once ($config['app_pages'].'/'.$this->view);
    }

    private function get(string $property) {
        echo $this->model[$property];
    }
}