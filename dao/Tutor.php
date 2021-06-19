<?php

namespace monitor;

require_once ('dao/BaseDAO.php');

class Tutor {
    use FromArray;
	public $ID;
	public $CARDHOLDER_ID;
	public $TEACHING_SICE;
	public $PHONE_NUM;
	public $CABINET;
}

?>