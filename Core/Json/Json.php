<?php

namespace Json;

use Core\Router;
use GlobalsFunctions\Globals;
use Ramsey\Uuid\Uuid;

/**
 *
 */
class Json
{
    /**
     * @param $storageName this is group name (folder name)
     * @param $description this description of the store
     * @param $uniqueName this unique name if you have two or more file storageName then provide value to unigueName
     * @return string|null return null if two mandatory field not provide or return path of storage
     */
    public static function createStorage($storageName, $description, $uniqueName = ""){
      if(empty($storageName)){
          return null;
      }
      $counter = 0;
      regenerateUid:
      $data = [
          'storage'=>$storageName,
          'timestamp'=>time(),
          'createdby'=>Globals::user()[0]['firstname'].'-'.Globals::user()[0]['lastname'].'-'.Globals::user()[0]['uid'],
          'description'=>$description,
          'path'=>Globals::root().'/Json-store/'.$storageName,
          'alias'=>$uniqueName
      ];

      $base = Globals::root();
      $path = "{$base}/Json-store/{$storageName}";
      if(is_dir($path) === false){
          mkdir($path,777,true);
      }else{
          if(empty($uniqueName)){
              throw new \Exception("{$storageName} is already occurred if you want to create new document in this storage then set unique parameter too",979);
          }else{
              $st = self::getStorageByUniqueName($uniqueName);
              if(!empty($st)){
                  throw new \Exception("{$storageName} is already in use",7685);
              }
          }
      }
      $uuid = self::store($data,$counter);
      $path .= "/{$uuid}.json";
      if(file_exists($path)){
          $counter += 1;
          goto regenerateUid;
      }
      file_put_contents($path, json_encode([]));
      return $path;

  }

    /**
     * @param $data
     * @param $counter
     * @return string|null
     */
    private static function store($data, $counter){
      if(gettype($data) !== 'array' || empty($data)){
          return null;
      }

      $base =Globals::root();
      $path = "{$base}/Json-store/";
      if(is_dir($path) === false){
          mkdir($path,777,true);
      }
      $store = $path.'store.json';
      if(file_exists($store) === false){
          file_put_contents($store, json_encode([]));
      }
      $stores = json_decode(Router::clearUrl(file_get_contents($store)),true);

      $data['uuid'] = self::uuid();

      if($counter === 0){
          $stores[] = $data;
      }else{
          array_pop($stores);
          $stores[] = $data;
      }

      file_put_contents($store, Router::clearUrl(json_encode($stores)));
      return $data['uuid'];
  }

    /**
     * @return string
     */
    public static function uuid(){
      $uuid1 = Uuid::uuid1();
     return $uuid1->toString();
  }

    /**
     * @return array|mixed
     */
    public static function getStorages(){
        $base = Globals::root();
        $path = "{$base}/Json-store";
        if(is_dir($path)){
            $path .= '/store.json';
            return json_decode(Router::clearUrl(file_get_contents($path)),true);
        }
        return [];
  }


    /**
     * @param $name
     * @return array|null
     */
    public static function getStorageByName($name){
        $path = Globals::root().'/Json-store/store.json';
        if(file_exists($path)){
            $lists = json_decode(Router::clearUrl(file_get_contents($path)), true);
            $tempList = [];
            foreach ($lists as $list=>$value){
                if(gettype($value) === 'array'){
                    extract($value);
                    if($name === $storage){
                        $tempList[]= $value;
                    }
                }
            }
            return $tempList;
        }
        return null;
  }

    /**
     * @param $unique
     * @return array|null
     */
    public static function getStorageByUniqueName($unique){
       $path = Globals::root().'/Json-store/store.json';
       if(file_exists($path)){
           $lists = json_decode(Router::clearUrl(file_get_contents($path)), true);
           $tempList = [];
           foreach ($lists as $list=>$value){
               if(gettype($value) === 'array' && isset($value['alias'])){
                   extract($value);
                   if($unique === $alias){
                       $tempList[]= $value;
                   }
               }
           }
           return $tempList;
       }
       return null;
   }

    /**
     * @param $name
     * @param $unique
     * @return array|null
     */
    public static function strictGetStorage($name, $unique){
       $path = Globals::root().'/Json-store/store.json';
       if(file_exists($path)){
           $lists = json_decode(Router::clearUrl(file_get_contents($path)), true);
           $tempList = [];
           foreach ($lists as $list=>$value){
               if(gettype($value) === 'array' && isset($value['alias'])){
                   extract($value);
                   if($unique === $alias && $name === $storage){
                       $tempList[]= $value;
                   }
               }
           }
           return $tempList;
       }
       return null;
   }

    /**
     * @param $uuid
     * @return array|null
     */
    public static function getStorageByUuid($uuid){
       $path = Globals::root().'/Json-store/store.json';
       if(file_exists($path)){
           $lists = json_decode(Router::clearUrl(file_get_contents($path)), true);
           $tempList = [];
           foreach ($lists as $list=>$value){
               if(gettype($value) === 'array'){
                   if($uuid === $value['uuid']){
                       $tempList[]= $value;
                   }
               }
           }
           return $tempList;
       }
       return null;
   }

   public static function removeStore($uuid){
        $stores = self::getStorages();
        $temp = [];
        foreach ($stores as $store=>$value){
           if(isset($value['uuid']) && $value['uuid'] !== $uuid){
               $temp[] = $value;
           }
        }
        $path = Globals::root().'/Json-store/store.json';
        return file_put_contents($path, json_encode($temp));
   }
}

