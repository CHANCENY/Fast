<?php

namespace Json;

use Core\Router;
use Datainterface\Database;
use Datainterface\Selection;

class JsonOperation extends Json
{
   public static function getAll(){
       $stores = self::getStorages();

       $dataInStorages = [];

       foreach ($stores as $store=>$value){
           if(gettype($value) === 'array' && !empty($value)){
               extract($value);
               $location = $path.'/'.$uuid.'.json';
               $thisStore = json_decode(Router::clearUrl(file_get_contents($location)), true);
               if(isset($dataInStorages[$storage])){
                   $key = $storage.'-'.$alias;
                   $dataInStorages[$key] = $thisStore;
               }else{
                   $dataInStorages[$storage] = $thisStore;
               }

           }
       }
       return $dataInStorages;
   }

   public static function getByName($name){
       $stores = self::getStorageByName($name);
       $data = [];
       foreach ($stores as $store=>$value){
           if(gettype($value) === 'array' && !empty($value)){
               extract($value);
               $location = "{$path}/{$uuid}.json";
               $thisStore = json_decode(Router::clearUrl(file_get_contents($location)), true);
               if(isset($data[$storage])){
                   $key = "{$storage}-{$alias}";
                   $data[$key] = $thisStore;
               }else{
                   $data[$storage] = $thisStore;
               }
           }
       }
       return $data;
   }

   public static function getByUniqueName($name){
       $store = self::getStorageByUniqueName($name);
       if(!empty($store)){
         $path = $store[0]['path'].'/'.$store[0]['uuid'].'.json';
         return json_decode(Router::clearUrl(file_get_contents($path)), true);
       }
       return [];
   }

   public static function deleteByName($name, $userId){
       $stores = self::getStorageByName($name);
       $database = Database::database();
       $dir = "";
       $uids = [];

       foreach ($stores as $store=>$value){
           if(gettype($value) === "array"){
               extract($value);
               $list = explode('-',$createdby);
               $uid = end($list);

               $user = Selection::selectById('users',['uid'=>$userId]);
               $user2 =  Selection::selectById('users',['uid'=>$uid]);
               if(isset($user2[0]['uid']) && isset($user[0]['uid']) && $user2[0]['uid'] === $user[0]['uid']){
                   $dir = $path;
                   $uids[] = $uuid;
                   unlink($path.'/'.$uuid.'.json');
               }
           }
       }
       foreach ($uids as $uuid=>$value){
           self::removeStore($value);
       }
       if(!empty($dir)){
           return rmdir($dir);
       }
       return false;
   }

   public static function deleteByUnigue($name, $userId){
       $stores = self::getStorageByUniqueName($name);
       $database = Database::database();
       $dir = "";
       $uids = [];

       foreach ($stores as $store=>$value){
           if(gettype($value) === "array" && isset($value['alias'])){
               extract($value);
               $list = explode('-',$createdby);
               $uid = end($list);

               $user = Selection::selectById('users',['uid'=>$userId]);
               $user2 =  Selection::selectById('users',['uid'=>$uid]);
               if(isset($user2[0]['uid']) && isset($user[0]['uid']) && $user2[0]['uid'] === $user[0]['uid']){
                   $dir = $path;
                   $uids[] = $uuid;
                   unlink($path.'/'.$uuid.'.json');
               }
           }
       }
       foreach ($uids as $uuid=>$value){
           self::removeStore($value);
       }
       if(!empty($dir)){
           return rmdir($dir);
       }
       return false;
   }
}