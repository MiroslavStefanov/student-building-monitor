<?php

namespace monitor;

require_once ('dao/BaseDAO.php');

class Course {
    use FromArray;
	public $ID;
	public $NAME;
	public $START_DATE;
	public $END_DATE;
	public $LECTURE_HALL;
	public $MANDATORY;
}

?>