<?php

namespace monitor;

use Exception;

require_once ('controllers/BaseController.php');
require_once ('controllers/ModelAndView.php');
require_once ('app/Application.php');
require_once ('utils/FileService.php');
require_once ('utils/CSVService.php');

class ImportController extends BaseController {

    const SUBMIT_BUTTON = "submit";
    const FILE_INPUT = "file";
    const TARGET_INPUT = "tableName";

    public function get() : ModelAndView {
        $config = $this->application->getConfig();
        return ModelAndView::withModelAndView([], 'import.html');
    }

    public function post() : ModelAndView {
        if(isset($_POST[self::SUBMIT_BUTTON])) {
            $fileProperties = $_FILES[self::FILE_INPUT];
            if ($fileProperties["error"] > 0) {
                echo "Return Code: " . $_FILES["file"]["error"] . "<br />";
            } else {
                $fileName = $fileProperties["tmp_name"];
                $target = $_POST[self::TARGET_INPUT];
                $targetClass = $this->getImportTargetClass($target);
                $this->importFile($fileName, $targetClass);
                return ModelAndView::withModelAndView(["file" => $fileName],'index.html');
            }
        } else {
            echo "No file selected <br />";
        }

        return new ModelAndView();
    }

    private function importFile($fileName, $className) {
        echo "Importing file $fileName into class $className<br/>";
        try {
            //$content = readTempFile($fileName);
            $entities = readCSVEntities($fileName);
            $dbEntity = $this->application->getDBEntity($className);
            foreach ($entities as $entity) {
                var_dump($entity);
                echo'<br/>';
                $dbEntity->saveEntity($entity);
            }
        } catch (Exception $e) {
            throw new Exception("Importing file $fileName as $className failed.", 0, $e);
        }
    }

    private function getImportTargetClass($target) {
        switch ($target) {
            case 'CARDHOLDERS':
                return 'CardHolder';
        }
        throw new Exception('Unhandled import target: '.$target);
    }
}