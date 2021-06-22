<?php

namespace monitor;

use Exception;

require_once ('controllers/BaseController.php');
require_once ('controllers/JsonObject.php');
require_once ('app/Application.php');
require_once ('dao/DBEntity.php');
require_once ('dao/CardHolder.php');
require_once ('dao/DBEnum.php');

class CardholderController extends BaseController {
    const GROUP_INPUT = 'sortBy';

    private $entity = NULL;

    public function __construct($application)
    {
        parent::__construct($application);
        $this->entity = $application->getDBEntity('CARDHOLDERS');
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
        $columns = "T.NAME as LABEL, COUNT(C.ID) AS COUNT";
        $joinColumn = $groupParameters['column'];
        $joins = ['AS C ', $groupParameters['entity']->innerJoin('T')." ON C.$joinColumn = T.ID "];
        $groupBy = "C.$joinColumn";
        $result = $this->entity->select($columns, $joins, '', $groupBy);
        return $result;
    }

    private function getGroupParameters($grouping) {
        $column = '';
        $entity = NULL;
        switch ($grouping) {
            case "byType":
                $column = 'TYPE';
                $entity = $this->application->getDBEntity('NOM_CARDHOLDER_TYPE');
                break;
            case "byDegree":
                $column = 'ACADEMIC_DEGREE';
                $entity = $this->application->getDBEntity('NOM_ACADEMIC_DEGREE');
                break;
            default:
                throw new Exception("Unhandled cardholders grouping $grouping");
        }
        return ['column' => $column, 'entity' => $entity];
    }

}