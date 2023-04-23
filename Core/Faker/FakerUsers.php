<?php

namespace Faker;

use Datainterface\Database;
use Datainterface\Insertion;
use Datainterface\MysqlDynamicTables;
use Datainterface\Selection;
use GlobalsFunctions\Globals;

class FakerUsers
{
  public static function generateFakeUsers($total = 50){

      //making user we have table
     $userSchema = self::userSchema();
     $maker = new MysqlDynamicTables();
     $maker->resolver(Database::database(),$userSchema['col'], $userSchema['att'], 'users', false);

     //bring in users
      $usersData = self::fakerUsers($total);

      //save now
      $counter = 0;

      $feedBack['created']= [];
      $feedBack['existed']=0;
      $feedBack['totalNew'] = 0;
      if(!empty($usersData)){
          foreach ($usersData as $user=>$value){
              $alreadyUser = Selection::selectById('users',['mail'=>$value['mail']]);
              if(empty($alreadyUser)){
                  $id = Insertion::insertRow('users', $value);
                  if(!empty($id)){
                      $feedBack['totalNew'] = $feedBack['totalNew'] + 1;
                      $feedBack['created'][] = $id;
                  }
              }else{
                  $feedBack['existed'] = $feedBack['existed'] + 1;
              }
          }
      }
      return $feedBack;
  }

  public static function fakerUsers($total = 50){
      $path = "";
      if($total < 100000){
          $path = Globals::root().'/Core/Faker/Store/people/people-100000.csv';
      }else{
          $path = Globals::root().'/Core/Faker/Store/people/people-2000000.csv';
      }

      //Index,User Id,First Name,Last Name,Sex,Email,Phone,Date of birth,Job Title
      $content = "";
      if(file_exists($path)){
          $content = file_get_contents($path);
      }else{
          throw new \Exception('Faker failed to load csv file');
      }

      $lines = [];
      if(!empty($content)){
          $findlerHandler = fopen($path, 'r');
          if($findlerHandler === false){
              throw new \Exception('Faker failed to generate users');
          }

          while(($row = fgetcsv($findlerHandler)) !== false){
           $lines[] = $row;
          }
          fclose($findlerHandler);
      }

      $users = [];
      $counter = 0;
      foreach ($lines as $key){
          if($counter < $total+2 && $counter !== 0){
              $users[]=$key;
          }
          $counter += 1;
      }

      $schema = self::userSchema();
      $col = $schema['col'];
      $finalUserCopy = [];

      $defaultImage[]  = Globals::protocal().'://'.Globals::serverHost().
          '/'.Globals::home().'/Files/profile_default2.jpg';
      $defaultImage[]  = Globals::protocal().'://'.Globals::serverHost().
          '/'.Globals::home().'/Files/profile_default.avif';

      $blocked = [false, true];
      foreach ($users as $user=>$value){
          $item[$col[1]]=$value[2];
          $item[$col[2]]=$value[3];
          $item[$col[3]]=$value[5];
          $item[$col[4]]=$value[6];
          $item[$col[5]]= password_hash('secret@123',  PASSWORD_BCRYPT);
          $item[$col[6]]='no address';
          $item[$col[7]]='user';
          $item[$col[8]]= true;
          $item[$col[9]]= $blocked[random_int(0,1)];
          $item[$col[10]] = $defaultImage[random_int(0,1)];
          $finalUserCopy[] = $item;
      }
      return $finalUserCopy;
  }

  public static function userSchema(){
      $columns = ['uid','firstname','lastname','mail','phone','password','address','role','verified','blocked','image'];
      $attributes = [
          'uid'=>['INT(11)','AUTO_INCREMENT','PRIMARY KEY'],
          'firstname'=>['VARCHAR(100)','NOT NULL'],
          'lastname'=>['VARCHAR(100)', 'NOT NULL'],
          'mail'=>['VARCHAR(100)','NOT NULL'],
          'phone'=>['VARCHAR(20)', 'NULL'],
          'password'=>['VARCHAR(100)', 'NOT NULL'],
          'address'=>['TEXT','NULL'],
          'role'=>['VARCHAR(20)','NOT NULL'],
          'verified'=>['BOOLEAN'],
          'blocked'=>['BOOLEAN'],
          'image'=>['varchar(250)', 'null']
      ];
      return ['col'=>$columns, 'att'=>$attributes];
  }
}