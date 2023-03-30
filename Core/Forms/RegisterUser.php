<?php

namespace FormViewCreation;

use Datainterface\Database;
use Datainterface\Insertion;
use Datainterface\MysqlDynamicTables;
use Datainterface\Selection;
use GlobalsFunctions\Globals;
use Mailling\Mails;

class RegisterUser
{
  public static function registerUser($data, $verifycallbackurl = 'verify-new-account'){
      if(gettype($data) !== "array"){
          return FALSE;
      }

      $required = ['firstname','lastname','mail','password'];
      $keys =array_keys($data);

      $counter = 0;
      foreach ($keys as $key=>$value){
          if(in_array($value, $required)){
              if(empty($data[$value])){
                  return FALSE;
              }
              $counter += 1;
          }
      }

      if($counter !== count($required)){
          return  FALSE;
      }

      $columns = ['token', 'tempuser', 'expectedurl'];
      $attribute = [
          "token"=>['VARCHAR(100)','NOT NULL'],
          "tempuser"=>['TEXT','NOT NULL'],
          "expectedurl"=>['VARCHAR(100)', 'NOT NULL']
      ];
      $maker = new MysqlDynamicTables();
      if($maker->resolver(Database::database(),$columns,$attribute,'unverifiedusers',true)){
          $token = md5(uniqid().uniqid().uniqid());
          $url = Globals::protocal().'://'.Globals::serverHost().'/'.$verifycallbackurl.'?token='.$token;
          $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
          $tempdata = [
              'token'=>$token,
              'tempuser'=>serialize($data),
              'expectedurl'=>$url
          ];

          if(Insertion::insertRow('unverifiedusers',$tempdata)){
             $body = "<div style='text-align: center; position: center; margin: auto;'>";
              $body .=  "<p>Thank you for registering with us at <a href='".Globals::protocal().'://'.Globals::serverHost()."'>".Globals::serverHost()."</a> but one more step to finish this registration.<br>Please click on the below to verify your account.</p>
                      <a style='text-decoration: none; width: 100%; height: auto; border: 1px solid orange; background-color: orange; border-radius: 5px; text-align: center; color: white; padding: 1.2%; margin: auto;' href='".$url."'>Click to verify account</a>";
              $body .= "<p>Thank you!</p></div>";
              $mail = [
                  "subject" => "Verify your account: " . \GlobalsFunctions\Globals::titleView(),
                  "message" => $body,
                  "attached" => false,
                  "reply" => false,
                  "altbody" => \GlobalsFunctions\Globals::titleView(),
                  "user" => array($data['mail']),
              ];
              if(Mails::send($mail)){
                  return TRUE;
              }
          }
          return FALSE;
      }


  }

  public static function saveVerifiedUser($token){
      //get data from temp
      $data = Selection::selectById('unverifiedusers', ['token'=> htmlspecialchars(strip_tags($token))]);
      if(!empty($data)){
          $tempData = $data[0]['tempuser'];
          $expectedUrl = $data[0]['expectedurl'];

          if(Globals::protocal().'://'.Globals::serverHost().Globals::uri() === $expectedUrl){
              $arrayUser = unserialize($tempData);
              $arrayUser['verified'] = true;
              if(Insertion::insertRow('users', $arrayUser)){
                  return true;
              }else{
                  throw new \Exception('Failed to save user data',07071);
              }
          }else{
              throw new \Exception('Invalid url',0707);
          }
      }
  }
}