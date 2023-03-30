<?php

namespace Datainterface;

use ConfigurationSetting\ConfigureSetting;

class Selection extends Database
{
  public static function selectById($tableName, $keyValue = []) : array{
      if(SecurityChecker::isConfigExist()){
          $con = self::database();
          $query = "SELECT * FROM {$tableName} WHERE ".HelperClass::lineSetQuery($keyValue);
          $stmt = $con->prepare($query);
          foreach ($keyValue as $key=>$value){
              $stmt->bindParam(':'.$key, $value);
          }
          $stmt->execute();
          return $stmt->fetchAll(\PDO::FETCH_ASSOC);
      }
      return [];
  }

  public static function selectAll($tableName) : array{
      if(SecurityChecker::isConfigExist()){
          $con = self::database();
          $stmt = $con->prepare("SELECT * FROM {$tableName}");
          $stmt->execute();
          return $stmt->fetchAll(\PDO::FETCH_ASSOC);
      }
      return [];
  }
}