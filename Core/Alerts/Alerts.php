<?php

namespace Alerts;

class Alerts
{
 public static function alert($type, $message){
     try{
         if(empty($type) || empty($message)){
             throw new \Exception("Both Type and Message need to have value to use alert");
         }else{
             $listUri = explode('/', $_SERVER['REQUEST_URI']);
             $midder = "";
             if(in_array('Views', $listUri) === false){
                 $midder = "Views";
             }else{
                 $midder= $listUri[1];
             }
             $completePath = "{$_SERVER['DOCUMENT_ROOT']}/{$midder}/Defaultviews/{$type}.html";

             if(is_file($completePath)){
                 if(file_exists($completePath)){
                     $cont = file_get_contents($completePath);
                     $cont = str_replace('@MESSAGE@', $message,$cont);
                     return $cont;
                 }else{
                     throw new \Exception("Alert file {$type} not found in DefaultViews directory");
                 }
             }else{
                 throw new \Exception("Alert type does exist in system please add in DefaultViews directory");
             }

         }
     }catch (\Exception $e){
         die($e->getMessage());
     }
 }
}