<?php

namespace monitor;

use Exception;

require_once ('controllers/BaseController.php');
require_once ('controllers/JsonObject.php');
require_once ('app/Application.php');
require_once ('dao/DBEntity.php');
require_once ('dao/Student.php');
require_once ('dao/DBEnum.php');

class StudentController extends BaseController {
    const GROUP_INPUT = 'sortBy';

    private $entity = NULL;

    public function __construct($application)
    {
        parent::__construct($application);
        $this->entity = $application->getDBEntity('STUDENTS');
    }

    public function get() : ModelAndView {
        if(!isset($_GET[self::GROUP_INPUT])) {
            return new JsonObject(NULL);
        }

        $grouping = $_GET[self::GROUP_INPUT];
        $countData = $this->getCounts($grouping);
        return new JsonObject($countData);
    }

    private function getCounts($grouping) {
        $groupParameters = $this->getGroupParameters($grouping);
        $columns = "SP.NAME as LABEL, COUNT(ST.ID) AS COUNT";
        $joinColumn = $groupParameters['column'];
        $joins = ['AS ST ', $groupParameters['entity']->innerJoin('SP')." ON ST.$joinColumn = SP.ID "];
        $groupBy = "ST.$joinColumn";
        $result = $this->entity->select($columns, $joins, '', $groupBy);
        return $result;
    }

    private function getGroupParameters($grouping) {
        $column = '';
        $entity = NULL;
        switch ($grouping) {
            case "bySpec":
                $column = 'STUDY_DEGREE';
                $entity = $this->application->getDBEntity('NOM_ACADEMIC_SPECIALIZATION');
                break;
            default:
                throw new Exception("Unhandled students grouping $grouping");
        }
        return ['column' => $column, 'entity' => $entity];
    }

}