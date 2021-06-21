<?php

namespace monitor;

require_once ('controllers/ModelAndView.php');

class JsonObject extends ModelAndView {
    private $object = NULL;

    public function __construct($object) {
        $this->object = $object;
    }

    public function render($application) {
        if(!$this->object) {
            return;
        }

        header('Content-type: application/json; charset=utf-8');
        $result = json_encode($this->object,JSON_UNESCAPED_UNICODE);
        echo $result;
    }
}