<?php

namespace Datainterface;

use ConfigurationSetting\ConfigureSetting;

class Insertion extends Database
{
  public static function insertRow($tableName, $data=[]) : int{
     if(SecurityChecker::isConfigExist()) {
         $con = self::database();
         $query = "INSERT INTO {$tableName} SET ". HelperClass::lineSetQuery($data);
         $stmt = HelperClass::binding($query, $con, $data);
         if(HelperClass::runQuery($stmt)){
             return $con->lastInsertId();
         }
     }
      return false;
  }
}