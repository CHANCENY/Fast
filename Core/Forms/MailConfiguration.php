<?php

namespace FormViewCreation;

use Datainterface\Database;
use Datainterface\Delete;
use Datainterface\Insertion;
use Datainterface\MysqlDynamicTables;
use Datainterface\Query;
use Datainterface\SecurityChecker;
use Datainterface\Selection;
use Datainterface\Updating;
use GlobalsFunctions\Globals;
use Mailling\Mails;

class MailConfiguration
{
  public static function saveMailConfiguration(array $mailConfig){

      if(empty($mailConfig)){
          return false;
      }
      $schema = self::mailConfigurationSchema();
      self::createMilConfigurationTable();
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
          self::testMail();
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
      if(!SecurityChecker::isConfigExist()){
          return [];
      }
      self::createMilConfigurationTable();
      $config = Selection::selectById(self::mailConfigurationSchema()['table'],['name'=>$name]);
      if(!empty($config)){
          return $config[0];
      }
      return array();
  }

  public static function deleteMailConfiguration($name){
      return Delete::delete(self::mailConfigurationSchema()['table'],['name'=>$name]);
  }

  public static function updateMailConfiguration($name, $data){
      return Updating::update(self::mailConfigurationSchema()['table'],$data,['name'=>$name]);
  }

  public static function testMail(){
      self::createMilConfigurationTable();
      $query = "SELECT * FROM ".self::mailConfigurationSchema()['table']." ORDER BY rowid DESC LIMIT 1";
      $configures = Query::query($query);
      $name = "";
      $to = "";
      $message = "";
      if(!empty($configures)){
          $name = $configures[0]['name'];
          $to = $configures[0]['email'];
          extract($configures[0]);
          $message = "<h2>Mail Configurations</h2>";
          $message .="<p>Config name: {$name}<br>Config user: {$user}<br>Config mail: {$email}<br>";
          $message .="Config smtp: {$smtp}<br>Config password: {$password}<br>Config created: {$created}</p>";
      }
      if($name !== ""){
          $data = [
              'subject'=>"Test mail from ".Globals::titleView(),
              'message'=>"{$message}<p>This is testing mail. If you have received this mail which means you have successfully configured your site mailing system</p>",
              'user'=>[$to],
              'reply'=>false,
              'altbody'=>"",
              'attached'=>false
          ];
          return Mails::send($data, $name);
      }
      return false;
  }

  private static function createMilConfigurationTable(){
      $schema = self::mailConfigurationSchema();
      $maker = new MysqlDynamicTables();
      $maker->resolver(Database::database(), $schema['col'],$schema['att'],$schema['table'],true);
  }
}