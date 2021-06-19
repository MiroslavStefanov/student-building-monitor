<?php

namespace monitor;

function readCSVEntities($fileName) : array {
    $file = fopen($fileName, 'r');
    // Headrow
    $head = fgetcsv($file, 4096, ';', '"');
    $result = [];

    // Rows
    while($row = fgetcsv($file, 4096, ';', '"'))
    {
        $namedRow = array_combine($head, $row);
        array_push($result, (object)$namedRow);
    }

	return $result;
}

?>