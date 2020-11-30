<?php
$array = array(0 => array('a' => 1), 1 => array('a' => 8));

$new_array = flatten($array, 'a');   
print_r($new_array); 

function flatten($array, $index='a') {
     $return = array();

     if (is_array($array)) {
        foreach ($array as $row) {
             $return[] = $row[$index];
        }
     }

     return $return;}