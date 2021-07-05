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
    private $cardholderEntity = NULL;
    private $passagesEntity = NULL;

    public function __construct($application)
    {
        parent::__construct($application);
        $this->entity = $application->getDBEntity('STUDENTS');
        $this->cardholderEntity = $application->getDBEntity('CARDHOLDERS');
        $this->passagesEntity = $application->getDBEntity('PASSAGES');
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
        $activeCardholders = $this->getActiveCardholders();
        $joins = ['AS ST ', $groupParameters['entity']->innerJoin('SP')." ON ST.$joinColumn = SP.ID ",
                                             "INNER JOIN ($activeCardholders) as AC ON AC.CH = ST.ID "];
        $groupBy = "ST.$joinColumn";
        $result = $this->entity->select($columns, $joins, '', $groupBy, '');
        return $result;
    }

    private function getActiveCardholders() {
        $columns = "C.ID as CH";
        $lastPasses = $this->getLastTimePassesQuery();
        $enteringPasses = $this->getEnteringPassesQuery();
        $joins = [
            'AS C ',
            "INNER JOIN ($lastPasses) as LAST_TIMES on C.ID = LAST_TIMES.CARDHOLDER",
            "INNER JOIN ($enteringPasses) as p2 on LAST_TIMES.LAST_TIME = p2.CT and LAST_TIMES.CARDHOLDER = p2.CH"
        ];
        $result = $this->cardholderEntity->createSelectStatement($columns, $joins, '', '', '');
        return $result;
    }

    private function getLastTimePassesQuery() {
        return $this->passagesEntity->createSelectStatement(
            "max(p.DATE_TIME) AS LAST_TIME, p.CARDHOLDER_ID AS CARDHOLDER",
            ["AS P "],
            '',
            'p.CARDHOLDER_ID',
            ''
        );
    }

    private function getEnteringPassesQuery() {
        return $this->passagesEntity->createSelectStatement(
            "p2.DATE_TIME AS CT, p2.CARDHOLDER_ID as CH",
            ["AS p2 "],
            'p2.ENTERING = 1',
            '',
            ''
        );
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