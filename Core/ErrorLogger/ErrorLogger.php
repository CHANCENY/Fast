<?php

namespace ErrorLogger;

use Datainterface\Database;
use Datainterface\Delete;
use Datainterface\Insertion;
use Datainterface\MysqlDynamicTables;
use Datainterface\Query;
use Datainterface\Selection;
use Datainterface\Tables;

class ErrorLogger
{
   public static function severity($code){
       switch ($code){
           case 500:
               return 'Critical Error';
           case 404:
               return 'Warning';
           case 401:
               return 'Notice';
           case '403':
               return 'Danger';
           default:
               return 'Unknown';
       }
   }

   public static function errorsTableSchema(){
       $columns = ['eid','severity','code','message','location'];
       $attributes = [
           'eid'=>['int(11)','auto_increment','primary key'],
           'severity'=>['varchar(20)','not null'],
           'code'=>['varchar(11)','null'],
           'message'=>['text'],
           'location'=>['varchar(250)']
       ];
       return ['col'=>$columns,'att'=>$attributes];
   }

   public static function log($exceptionObject = NULL, $errorObject = []){
        $maker = new MysqlDynamicTables();
        $schema = self::errorsTableSchema();
        $maker->resolver(Database::database(),$schema['col'],$schema['att'],'errors_logs',false);
        if(!empty($errorObject)){

            $data = [
                'severity'=>self::severity($errorObject['code']),
                'code'=>$errorObject['code'],
                'message'=>$errorObject['message'],
                'location'=> $errorObject['location']
            ];
           Insertion::insertRow('errors_logs',$data);
        }else{
            $location = [
                'file'=>$exceptionObject->getFile(),
                'line'=>$exceptionObject->getLine(),
            ];
            $data = [
                'severity'=>self::severity($exceptionObject->getCode()),
                'code'=>$exceptionObject->getCode(),
                'message'=>$exceptionObject->getMessage(),
                'location'=> serialize($location)
            ];

            Insertion::insertRow('errors_logs',$data);
        }

   }

   public static function errors(){
       return Query::query("SELECT * FROM errors_logs ORDER BY eid DESC ");
   }

   public static function getDetails($eid){
       return Selection::selectById('errors_logs',['eid'=>$eid]);
   }

   public static function deleteErrors(){
       return Tables::deleteTable('errors_logs');
   }

   public static function deleteByErrorId($eid){
       return Delete::delete('errors_logs',['eid'=>$eid]);
   }
}