<?php

namespace Datainterface;

use ConfigurationSetting\ConfigureSetting;

class Query
{
  public static function query($query, $data = []){
      if(SecurityChecker::isConfigExist()){
          $con = Database::database();
          $stmt = $con->prepare($query);

          if(!empty($data)){
              $keys = array_keys($data);
              for ($i = 0; $i < count($keys); $i++){
                  $stmt->bindParam(":{$keys[$i]}", $data[$keys[$i]]);
              }
          }
          $stmt->execute();
          return $stmt->fetchAll(\PDO::FETCH_ASSOC);
      }
      return [];
  }
}