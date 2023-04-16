<?php

namespace Installation;

use Core\Router;

class Installation
{
   public static function collectDatabaseInformation($data){

       if(empty($data['dbname'])){
           return "Database name is required";
       }
       if(empty($data['user'])){
           return "Database username is required";
       }
       if(empty($data['host'])){
         return "Database host name is required";
       }

       $data = [
           "host"=>htmlspecialchars(strip_tags($data['host'])),
           "user"=>htmlspecialchars(strip_tags($data['user'])),
           "password"=>empty(htmlspecialchars(strip_tags($data['password']))) ? NULL : htmlspecialchars(strip_tags($data['password'])),
           "dbname"=>htmlspecialchars(strip_tags($data['dbname']))
       ];

       $file = $_SERVER['DOCUMENT_ROOT'].'/Core/ConfigurationSetting/basesetting.json';
       return file_put_contents($file, json_encode($data)) !== null;
   }
}