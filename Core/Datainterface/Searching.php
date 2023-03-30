<?php

namespace Datainterface;

use Datainterface\mysql\TablesLayer;

class Searching
{
   public static function search(string $term, string $dbTable): array {
       if(empty($term) || empty($dbTable)){
           return [];
       }

       $t = new TablesLayer();
       $tables = $t->getSchemas()->schema();
       $tableFound = $tables[$dbTable] ?? [];

       $columns = [];
       foreach ($tableFound as $key=>$value){
           if(!empty($value)){
               $columns[] = $value['Field'];
           }
       }
       $searchString = self::buildQueryLine(self::buildPlaceHolders($columns));
       $query = "SELECT * FROM {$dbTable} WHERE {$searchString}";

       $placeholders = array_values($columns);
       $dataToSearch = [];
       foreach ($placeholders as $key=>$value){
           if(!empty($value)){
               $dataToSearch[$value] = $term;
           }
       }
       $query = self::bindParams($query, $dataToSearch);
       return Query::query($query);
   }

   public static function buildPlaceHolders(array $data): array{
       $line = [];
       foreach ($data as $key=>$value){
           if(!empty($value)){
               $line[] = "{$value} LIKE '%:{$value}%'";
           }
       }
       return $line;
   }

   public static function buildQueryLine(array $placeholderedArray) : string{
       $line = "";
       foreach ($placeholderedArray as $key=>$value){
           if(!empty($value)){
               $line .= "{$value} OR ";
           }
       }

       $line = trim(substr($line, 0, strlen($line) - 3));
       return  $line;
   }

   public static function bindParams(string $query, array $placeholder) : string {

       $keys = array_keys($placeholder);
       for ($i = 0; $i < count($keys); $i++){
           $query = str_replace(":$keys[$i]", $placeholder[$keys[$i]], $query);
       }
       return $query;
   }
}