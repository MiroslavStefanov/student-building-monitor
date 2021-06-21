<?php

namespace monitor;

use Exception;

require_once ('controllers/BaseController.php');
require_once ('controllers/ModelAndView.php');

class AboutController extends BaseController {

    public function get() : ModelAndView {
        return ModelAndView::withModelAndView([], 'about.html');
    }
}