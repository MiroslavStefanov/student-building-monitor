<?php

namespace monitor;
use Exception;

function readFile($fileName) : string {
    $absolutePath = $_SERVER['DOCUMENT_ROOT'].$fileName;
    $file = fopen($absolutePath, 'r');
    if($file == false) {
        throw new Exception("Cannot read file $fileName");
    }

    $content = fread($file, filesize($absolutePath));
    fclose($file);

    return $content;
}

function writeFile($fileName, $content) {
    $absolutePath = $_SERVER['DOCUMENT_ROOT'].$fileName;
    $file = fopen($absolutePath, 'w');
    if($file == false) {
        throw new Exception("Cannot write file $fileName");
    }

    fwrite($file, $content);
    fclose($file);
}

?>