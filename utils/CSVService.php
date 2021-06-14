<?php

function readCSVEntities($data) : array {
	while (($line = fgetcsv($data)) !== false) {
	    print_r($line);
	}
	return [];
}

?>