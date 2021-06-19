<?php

namespace monitor;

require_once ('dao/BaseDAO.php');

class CardHolder {
    use FromArray;
	public $ID;
	public $NAME;
	public $BIRTH_DATE;
	public $EMAIL;
	public $ACADEMIC_DEGREE;
	public $TYPE;
}

?>