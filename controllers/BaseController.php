<?php

namespace monitor;

require_once ('controllers/ModelAndView.php');

class BaseController {
    protected $application;

    public function __construct($application) {
        $this->application = $application;
    }

    public function get() : ModelAndView {
        return new ModelAndView();
    }
    public function post() : ModelAndView {
        return new ModelAndView();
    }
}