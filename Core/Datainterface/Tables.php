<?php

namespace Datainterface;

use ConfigurationSetting\ConfigureSetting;
use ErrorLogger\ErrorLogger;

class Tables extends Database
{
  public static function installTableRequired() {

      $con = self::database();
      //lists needed
      $sqlTables = [
          "CREATE TABLE IF NOT EXISTS users (uid INT AUTO_INCREMENT PRIMARY KEY, 
           firstname VARCHAR(30) NOT NULL, lastname VARCHAR(30) NOT NULL, mail VARCHAR(40) NOT NULL, phone VARCHAR(20), 
           address VARCHAR(100), password VARCHAR(100) NOT NULL, role VARCHAR(10) NOT NULL, created TIMESTAMP DEFAULT CURRENT_TIMESTAMP)",
      ];

      foreach ($sqlTables as $table){
          $stmt = $con->prepare($table);
          $stmt->execute();
      }
  }

  public static function tablesExists($tables = []) : bool {
      if(empty(ConfigureSetting::getDatabaseConfig())){
          return [];
      }
      if(empty($tables)){
          $tables = [
              "users"
          ];
      }
      $con = self::database();

      $counter = 0;

      foreach ($tables as $table){
          $sql = "SHOW TABLES LIKE '".$table."'";
          $stmt = $con->prepare($sql);
          $stmt->execute();
          $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
          if(count($result) >= 1){
              $counter++;
          }
      }

      if(count($tables) === $counter){
          return true;
      }else{
          return false;
      }
  }

  public static function createTable($query ) : bool {
      $con = self::database();
      $stmt = $con->prepare($query);
      if($stmt->execute()){
          return true;
      }
      return false;
  }

  public static function deleteTable($tableName) : bool{
      $con  = self::database();
      $stmt = $con->prepare('DROP TABLE '.$tableName);
      return $stmt->execute();
  }

  public static function makeCopyTable($oldTable, $newTable, $data = true) : bool {
      $con = self::database();
      if($data === false){
          $stmt = $con->prepare("CREATE TABLE {$newTable} AS SELECT * FROM {$oldTable}");
      }else{
          $stmt = $con->prepare("CREATE TABLE {$newTable} LIKE {$oldTable}; INSERT INTO {$newTable} SELECT * FROM {$oldTable}");
      }
      return $stmt->execute();
  }

  public static function tableSchemaInfo($table) : array {
      $con = self::database();
      $stmt = $con->prepare("DESCRIBE {$table}");
      $stmt->execute();
      $columns = [];
      foreach ($stmt->fetchAll(\PDO::FETCH_ASSOC) as $key=>$value){
          $columns[] = $value;
      }
      return $columns;
  }

  public static function checkColumn($table, $column){
      try {
          $dbname = self::getDbname();
          $query = "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = :db AND TABLE_NAME = :t";
          $results = Query::query($query,['db'=>$dbname,'t'=>$table]);
          foreach ($results as $result=>$value){
              if($value['COLUMN_NAME'] === $column){
                 return true;
              }
          }
          return false;
      }catch (\Exception $exception){
         ErrorLogger::log($exception);
      }

  }

}