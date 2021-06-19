<?php

namespace monitor;

require_once ('dao/BaseDAO.php');

class Student {
    use FromArray;
	public $ID;
	public $CARDHOLDER_ID;
	public $STUDY_DEGREE;
	public $FACULTY_NUM;
	public $YEAR;
	public $GROUP;
}

?>