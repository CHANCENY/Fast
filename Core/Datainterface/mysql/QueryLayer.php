<?php

namespace Datainterface\mysql;

use Datainterface\Query;
use ErrorLogger\ErrorLogger;

class QueryLayer
{
   private string $query;

   private array $placeHolders;

   public array $values;

   public $outPut;

   public function __construct(){
       $this->placeHolders = array();
       $this->values = array();
   }
   public function setQuery(string $query) : QueryLayer{
       $this->query = $query;
       return $this;
   }

   public function setPlaceHoldersNames($names) : QueryLayer{
       $this->placeHolders = $names;
       return $this;
   }

   public function setValues($values) : QueryLayer{
       $this->values = $values;
       return $this;
   }

   public function run() : QueryLayer{
       if(count($this->placeHolders) === count($this->values)){
          if(count($this->placeHolders) === 0 && count($this->values) === 0){
              $this->outPut = Query::query($this->query);
              return $this;
          } else{
              try{
                  $data = array_combine($this->placeHolders, $this->values);
                  $this->outPut = Query::query($this->query, $data);
                  return $this;
              }catch (\Exception $e){
                  ErrorLogger::log($e);
                  $this->outPut = false;
                  return $this;
              }
          }
       }
       throw new \Exception('Placeholders and Values have different size');
   }

   public function outPut(){
       return $this->outPut;
   }
}