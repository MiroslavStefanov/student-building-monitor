<?php

namespace monitor;

require_once ('dao/BaseDAO.php');

class Passage {
    use FromArray;
	public $ID;
	public $CARDHOLDER_ID;
	public $DATE_TIME;
	public $ENTERING;
	public $BODY_TEMPERATURE;
}

?>