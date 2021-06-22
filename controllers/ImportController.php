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

        $targetTable = $_POST[self::TARGET_INPUT];
        $fileName = $fileProperties["tmp_name"];
        $importCount = $this->importFile($fileName, $targetTable);
        echo "Rows imported: $importCount<br/>";
        return ModelAndView::withModelAndView(["file" => $fileName],'import.html');
    }

    private function importFile($fileName, $table) : int {
        echo "Importing file $fileName into table $table<br/>";
        try {
            $entities = readCSVEntities($fileName);
            $dbEntity = $this->application->getDBEntity($table);
            $className = $dbEntity->getClass();
            foreach ($entities as $entity) {
                $e = $className::fromArray($entity);
                $dbEntity->saveEntity($e);
            }
            return count($entities);
        } catch (Exception $e) {
            throw new Exception("Importing file $fileName as $className failed.", 0, $e);
        }
    }

    private function getImportTargetClass($target) {
        switch ($target) {
            case 'CARDHOLDERS':
                return 'CardHolder';
            case 'STUDENTS':
                return 'Student';
            case 'TUTORS':
                return 'Tutor';
            case 'EMPLOYEES':
                return 'Employee';
            case 'COURSES':
                return 'Course';
            case 'PASSAGES':
                return 'Passage';
        }
        throw new Exception('Unhandled import target: '.$target);
    }
}