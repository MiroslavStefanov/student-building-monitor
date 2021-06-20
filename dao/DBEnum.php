<?php

namespace monitor;

require_once ('dao/BaseDAO.php');

class DBEnum {
    use FromArray;
	public $ID;
	public $NAME;
}

?>