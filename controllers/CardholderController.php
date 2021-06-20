<?php

namespace monitor;

use Exception;

require_once ('controllers/BaseController.php');
require_once ('controllers/JsonObject.php');
require_once ('app/Application.php');

class CardholderController extends BaseController {

    private $entity = NULL;

    public function __construct($application)
    {
        parent::__construct($application);

        $this->entity = $application->getDBEntity('CardHolder');
    }

    public function get() : ModelAndView {
        $all = $this->entity->getAllEntities();
        return new JsonObject($all);
    }


}