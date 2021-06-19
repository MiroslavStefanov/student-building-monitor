<?php

namespace monitor;

require_once ('dao/BaseDAO.php');

class IdCounter {
    use FromArray;
	public $ID;
	public $TABLE_NAME;
	public $NEXT_ID;
}

?>