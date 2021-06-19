<?php

namespace monitor;

require_once ('dao/BaseDAO.php');

class Employee {
    use FromArray;
	public $ID;
	public $CARDHOLDER_ID;
	public $PHONE_NUM;
	public $POSITION;
}

?>