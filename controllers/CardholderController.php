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

    private $entity = NULL;
    private $typeEntity = NULL;

    public function __construct($application)
    {
        parent::__construct($application);

        $this->entity = $application->getDBEntity('CardHolder');
        $this->typeEntity = $application->getDBEntity('DBEnum');
    }

    public function get() : ModelAndView {
        $countData = $this->getCounts();
        return new JsonObject($countData);
    }

    private function getCounts() {
        $columns = "T.NAME as LABEL, COUNT(C.ID) AS COUNT";
        $joins = ['AS C ', $this->typeEntity->innerJoin('T')." ON C.TYPE = T.ID "];
        $groupBy = "C.TYPE";
        $result = $this->entity->select($columns, $joins, '', $groupBy);
        return $result;
    }

}