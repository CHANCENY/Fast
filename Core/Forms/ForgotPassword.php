<?php

namespace FormViewCreation;


use ConfigurationSetting\ConfigureSetting;
use Datainterface\Database;
use Datainterface\Delete;
use Datainterface\Insertion;
use Datainterface\MysqlDynamicTables;
use Datainterface\Selection;
use Mailling\Mails;

class ForgotPassword
{
    private $data;
    /**
     * @param $data
     * @return void
     *
     * This  function requires new password, email used to register account and possible old password
     */

  public static function forgotPassword($data =[]){
      if(empty($data)){
          return false;
      }

      //send me with code
      return Mails::send($data);

  }


  public static function changePassword($newpassword, $userid){
      $con = Database::database();
      $user = Selection::selectById('users', ['uid'=>$userid]);
      $stmt = $con->prepare('UPDATE users SET password = :password WHERE uid = :uid');
      $newpassword = password_hash($newpassword, PASSWORD_BCRYPT);
      $stmt->bindParam(':password', $newpassword);
      $stmt->bindParam(':uid', $userid);
      if($stmt->execute()){
          $user2 = Selection::selectById('users', ['uid'=>$userid]);
          if(!empty($user2) && !empty($user)){
              if($user2[0]['password'] !== $user[0]['password']){
                  return true;
              }
          }
      }
      return false;
  }

  public static function passwordInfomationTempStorage($data = []){
      if(empty($data)) {
          return false;
      }
      $serials = serialize($data);

      $columns = ['code','data'];
      $attributes = [
          "code"=>['VARCHAR(250)', 'NOT NULL'],
          "data"=>['TEXT']
      ];
      $con = Database::database();
      $maker = new MysqlDynamicTables();
      if($maker->resolver($con,$columns,$attributes,'forgotpasswordstorage',true)){
          if(Insertion::insertRow('forgotpasswordstorage', ['code'=>$data['code'], "data"=>$serials])){
            return true;
          }
      }
      return false;
  }

  public static function verifyCode($code){
      if(empty($code)){
          return false;
      }

      $data = Selection::selectById('forgotpasswordstorage',['code'=>$code]);

      if(!empty($data)){
          $userials = unserialize($data[0]['data']);

          $now = strval(date('Y-m-d H:i:s'));
          $start_date = new \DateTime( strval($data[0]['created']));
          $since_start = $start_date->diff(new \DateTime($now));

          if($since_start->d == 0 && intval($since_start->i) > 0) {
              if ($userials['code'] === $code) {

                  $userData = Selection::selectById('users', ['mail' => $userials['mail']]);
                  if (!empty($userData)) {
                      $userid = $userData[0]['uid'];
                      if (self::changePassword($userials['password'], $userid)) {
                          if (Delete::delete('forgotpasswordstorage', ['code' => $code])) {
                              return true;
                          }
                      }
                  }
              }
          }
      }
      return false;
  }
}