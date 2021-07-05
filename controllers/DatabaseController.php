<?php

namespace monitor;

require_once ('controllers/BaseController.php');
require_once ('app/Application.php');

class DatabaseController extends BaseController {

    public function get() : ModelAndView {
        return ModelAndView::withModelAndView([], 'database.html');
    }

    public function post() : ModelAndView {
        $this->application->createDB();
        return ModelAndView::withModelAndView([], 'database.html');
    }
}