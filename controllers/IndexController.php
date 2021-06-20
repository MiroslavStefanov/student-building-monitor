<?php

namespace monitor;

use Exception;

require_once ('controllers/BaseController.php');
require_once ('controllers/ModelAndView.php');

class IndexController extends BaseController {

    public function get() : ModelAndView {
        return ModelAndView::withModelAndView([], 'index.html');
    }
}