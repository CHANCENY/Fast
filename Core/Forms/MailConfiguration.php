<?php

namespace FormViewCreation;

use Datainterface\Database;
use Datainterface\Delete;
use Datainterface\Insertion;
use Datainterface\MysqlDynamicTables;
use Datainterface\Selection;
use Datainterface\Updating;

class MailConfiguration
{
  public static function saveMailConfiguration(array $mailConfig){

      if(empty($mailConfig)){
          return false;
      }
      $schema = self::mailConfigurationSchema();

      $maker = new MysqlDynamicTables();
      $maker->resolver(Database::database(), $schema['col'],$schema['att'],$schema['table'],true);

      $mail = htmlspecialchars(strip_tags($mailConfig['mail']));
      $name = htmlspecialchars(strip_tags($mailConfig['name']));
      $username = htmlspecialchars(strip_tags($mailConfig['mail']));
      $password = htmlspecialchars(strip_tags($mailConfig['password']));
      $smtp = htmlspecialchars(strip_tags($mailConfig['smtp']));

      $toSave = [
        'email'=>$mail,
        'name'=>$name,
        'user'=>$username,
        'password'=>$password,
        'smtp'=>$smtp
      ];
      if(Insertion::insertRow($schema['table'],$toSave)){
          return true;
      }
      return false;
  }

  public static function mailConfigurationSchema(){
      $columns = ['name','email','user','password','smtp'];
      $attributes = [
          'name'=>['varchar(100)','not null'],
          'email'=>['varchar(100)','not null'],
          'user'=>['varchar(50)','not null'],
          'password'=>['varchar(100)','not null'],
          'smtp'=>['varchar(50)','not null']
      ];
      return ['col'=>$columns,'att'=>$attributes,'table'=>'mailConfigurations'];
  }

  public static function getMailConfiguration($name){
      if(empty($name)){
          return [];
      }

      $config = Selection::selectById(self::mailConfigurationSchema()['table'],['name'=>$name]);
      if(!empty($config)){
          return $config[0];
      }
      return array();
  }

  public static function deleteMailConfiguration($name){
      return Delete::delete(self::mailConfigurationSchema()['table'],['name'=>$name]);
  }

  public function updateMailConfiguration($name, $data){
      return Updating::update(self::mailConfigurationSchema()['table'],$data,['name'=>$name]);
  }
}