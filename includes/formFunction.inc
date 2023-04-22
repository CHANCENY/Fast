<?php 
/**
*Functions that handlers related Field added to any form using Content Type creation
*/
namespace formFunction;
use Datainterface\Query;

function sample(){ 
 $query = "SELECT * FROM users"; 

$result = Query::query($query); 

$arrayReturns = []; 

foreach ($result as $key=>$value){ 

    if(gettype($value) === 'array'){ 

        $keys = array_keys($value); 

        $arrayReturns[] = [ 

            'text' => $value['lastname'], 

            'value' => $value['uid'] 

        ]; 

    } 

} 

return $arrayReturns; 
 }

function sample2(){ 
 $query = "SELECT * FROM users"; 

$result = Query::query($query); 

$arrayReturns = []; 

foreach ($result as $key=>$value){ 

    if(gettype($value) === 'array'){ 

        $keys = array_keys($value); 

        $arrayReturns[] = [ 

            'text' => $value['lastname'], 

            'value' => $value['uid'] 

        ]; 

    } 

} 

return $arrayReturns; 
 }