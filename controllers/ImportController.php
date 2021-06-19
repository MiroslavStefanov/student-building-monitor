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
        return ModelAndView::withModelAndView([], 'import.html');
    }

    public function post() : ModelAndView {
        if(!isset($_POST[self::SUBMIT_BUTTON])) {
            return ModelAndView::withModelAndView(['errors' => ['No file selected']], 'import.html');
        }

        $fileProperties = $_FILES[self::FILE_INPUT];
        if ($fileProperties["error"] > 0) {
            return ModelAndView::withModelAndView(['errors' => [$_FILES["file"]["error"]]], 'import.html');
        }

        $target = $_POST[self::TARGET_INPUT];
        $fileName = $fileProperties["tmp_name"];
        $targetClass = $this->getImportTargetClass($target);
        $this->importFile($fileName, $targetClass);
        return ModelAndView::withModelAndView(["file" => $fileName],'import.html');
    }

    private function importFile($fileName, $className) {
        echo "Importing file $fileName into class $className<br/>";
        try {
            $entities = readCSVEntities($fileName);
            $dbEntity = $this->application->getDBEntity($className);
            $fullClass = 'monitor\\'.$className;
            foreach ($entities as $entity) {
                $e = $fullClass::fromArray($entity);
                $dbEntity->saveEntity($e);
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