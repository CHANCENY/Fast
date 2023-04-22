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
       if(!is_dir($_SERVER['DOCUMENT_ROOT'].'/config/')){
           mkdir($_SERVER['DOCUMENT_ROOT'].'/config/', 0777, true);
       }
       if(chmod($_SERVER['DOCUMENT_ROOT'].'/config/', 0777)){
           return file_put_contents($_SERVER['DOCUMENT_ROOT'].'/config/basesetting.json', json_encode($data)) !== null;
       }
   }
}