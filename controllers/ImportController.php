<?php

require_once($_SERVER['DOCUMENT_ROOT'].'/student-building-monitor/controllers/BaseController.php');
require_once ($_SERVER['DOCUMENT_ROOT'].'/student-building-monitor/controllers/ModelAndView.php');
require_once ($_SERVER['DOCUMENT_ROOT'].'/student-building-monitor/app/Application.php');
require_once ($_SERVER['DOCUMENT_ROOT'].'/student-building-monitor/utils/FileService.php');

class ImportController extends BaseController {

    const SUBMIT_BUTTON = "submit";
    const FILE_INPUT = "file";

    public function get() : ModelAndView {
        return ModelAndView::withModelAndView(["message" => "Eeeekstra"], 'index.html');
    }

    public function post() : ModelAndView {
        if(isset($_POST[self::SUBMIT_BUTTON])) {
            $fileProperties = $_FILES[self::FILE_INPUT];
            if ($fileProperties["error"] > 0) {
                echo "Return Code: " . $_FILES["file"]["error"] . "<br />";
            } else {
                $fileName = $fileProperties["tmp_name"];
                $this->importFile($fileName, 'CARDHOLDERS');
                return ModelAndView::withModelAndView(["file" => $fileName],'index.html');
            }
        } else {
            echo "No file selected <br />";
        }

        return new ModelAndView();
    }

    private function importFile($fileName, $className) {
        try {
            $content = utils\readFile($fileName);
            $this->application->importDBEntities($content, $className);
        } catch (Exception $e) {
            throw new Exception("Importing file $fileName as $className failed.", 0, $e);
        }
    }
}