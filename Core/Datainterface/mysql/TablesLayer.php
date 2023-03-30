<?php

namespace Datainterface\mysql;

use Datainterface\Database;
use Datainterface\Query;
use Datainterface\SecurityChecker;

class TablesLayer
{

   private array $schemas;

   private array $tables;

   public function getTables() : TablesLayer {
       if(SecurityChecker::isConfigExist() === false){
           return $this;
       }
       $query = "SHOW TABLES";
       $dbname = Database::getDbname();
       $tables = Query::query($query);
       foreach ($tables as $table=>$value){
           $this->tables[] = $value["Tables_in_{$dbname}"];
       }
       return $this;
   }

   public function tables() : array
   {
       if(SecurityChecker::isConfigExist() === false){
           return [];
       }
       return $this->tables;
   }

   public function schema() : array{
       if(SecurityChecker::isConfigExist() === false){
           return [];
       }
       return $this->schemas;
   }

   public function getSchemas() : TablesLayer{
       if(SecurityChecker::isConfigExist() === false){
           return $this;
       }
       $tables = $this->getTables()->tables();
       if(!empty($tables)){
           foreach ($tables as $key=>$value){
               $query = "DESCRIBE {$value}";
               $this->schemas[$value] = Query::query($query);
           }
       }
       return $this;
   }


}