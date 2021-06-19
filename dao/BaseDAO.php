<?php

namespace monitor;

trait FromArray {
    public static function fromArray(array $data = []) {
        foreach (get_object_vars($obj = new self) as $property => $default) {
            $propertyExists = array_key_exists($property, $data);
            if (!$propertyExists) continue;
            $obj->{$property} = $data[$property]; // assign value to object
        }
        return $obj;
    }
}

?>