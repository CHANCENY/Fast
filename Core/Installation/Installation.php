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
       if(file_put_contents($file, json_encode($data))){
          if(self::viewsFilesInstallations()){
              return true;
          }
       }
       return false;
   }

   public static function viewsFilesInstallations(){

       //reading zip
       $base = $_SERVER['DOCUMENT_ROOT'].'/Core/Temps/viewsfiles.zip';

       //unzipping
       if(file_exists($base)){

           $zipper = new \ZipArchive();
           $result = $zipper->open($base);

           $location = $_SERVER['DOCUMENT_ROOT'].'/Views/DefaultViews';
           if(!is_dir($location)){
               mkdir($location, 777,true);
           }

           if($result === true){
               $zipper->extractTo($location);
               $zipper->close();

               $registry = $_SERVER['DOCUMENT_ROOT'].'/Core/Router/Register/registered_path_available.json';
               $newRegistry = [];
               if(file_exists($registry)){
                   $content = json_decode(file_get_contents($registry), true);
                   foreach ($content as $co){
                       $list = explode('/', $co['view_path_absolute']);
                       $filename = end($list);
                       $complete = $_SERVER['DOCUMENT_ROOT'].'/Views/DefaultViews/'.$filename;
                       $relative = '/Views/DefaultViews/'.$filename;
                       $co['view_path_absolute'] = $complete;
                       $co['view_path_relative'] = $relative;
                       array_push($newRegistry, $co);

                   }

                   if(count($content) === count($newRegistry)){
                       $toSave = json_encode($newRegistry);
                       $toSave = Router::clearUrl($toSave);
                       if(file_put_contents($registry, $toSave)){
                           return true;
                       }
                   }
               }
           }
       }
       return false;
   }
}