<?php

namespace monitor;

function readCSVEntities($fileName) : array {
    $file = fopen($fileName, 'r');
    // Headrow
    $head = array_map(function($name){return strtoupper($name);}, fgetcsv($file));
    $result = [];

    // Rows
    while($row = fgetcsv($file))
    {
        $namedRow = array_combine($head, $row);
        array_push($result, $namedRow);
    }

	return $result;
}

?>