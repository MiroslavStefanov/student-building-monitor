<?php

namespace monitor;

use Exception;

require_once ('controllers/BaseController.php');
require_once ('controllers/ModelAndView.php');
require_once ('app/Application.php');
require_once ('utils/FileService.php');
require_once ('utils/CSVService.php');

class IndexController extends BaseController {

    const SUBMIT_BUTTON = "submit";
    const FILE_INPUT = "file";
    const TARGET_INPUT = "tableName";

    public function get() : ModelAndView {
        return ModelAndView::withModelAndView([], 'index.html');
    }
}