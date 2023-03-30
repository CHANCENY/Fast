<?php

namespace Sessions;

use Datainterface\Database;
use Datainterface\Delete;
use Datainterface\Insertion;
use Datainterface\MysqlDynamicTables;
use Datainterface\Selection;
use Datainterface\Tables;
use Datainterface\Updating;

class Store
{
    private $store = [];

    private $valuesFound;

    public function __construct(){
        $this->storage();
    }
   public function get($property){
        $counter = 0;
        top:
       $this->load();
       if(!empty($this->store)) {
           foreach ($this->store as $key => $value) {
               if (gettype($value) === 'array') {
                   extract($value);
                   if ($sessionProperty === $property) {
                       $this->valuesFound[$property][] = empty($data) ? $data : unserialize($data);
                       break;
                   }
               }
           }
       }
       if(empty($this->valuesFound) && $counter === 0){
           $counter = 1;
           $this->loadFromDb();
           goto top;
       }
       return $this;
   }

   public function values(){
        return $this->valuesFound;
   }

   public function set($property, $value){
        $temp = "";
        if(gettype($value) === "array"){
            $temp = serialize($value);
        }else{
            $temp = serialize($value);
        }
        $this->store = $this->get($property)->values();
        $dataTemp = "";
        if(!empty($this->store)){
            $this->store[$property][] = unserialize($temp);
            Updating::update('sessions',['data'=>serialize($this->store)],['sessionProperty'=>$property]);
        }else{
            Insertion::insertRow('sessions',['sessionProperty'=>$property, 'data'=>$temp]);
        }
       $this->valuesFound = $this->get($property)->values();
       return $this;
   }

   public function storage(){
       $columns = ['sid','sessionProperty','data'];
       $attributes = [
         'sid'=>['int(11)','not null','primary key','auto_increment'],
         'sessionProperty'=>['varchar(255)','not null'],
         'data'=>['text', 'null']
       ];

       $maker = new MysqlDynamicTables();
       $maker->resolver(Database::database(),$columns,$attributes,'sessions',false);
   }

   private function load(){
       if(empty(SessionManager::getSession('store_session_1234567876543'))){
           $result = Selection::selectAll('sessions');
           SessionManager::setSession('store_session_1234567876543',$result);
       }
       $this->loadFromDb();
       $this->store = SessionManager::getSession('store_session_1234567876543');
   }

   private function loadFromDb(){
        $result = Selection::selectAll('sessions');
       if(!empty($result)){
           SessionManager::setSession('store_session_1234567876543', []);
           SessionManager::setSession('store_session_1234567876543',$result);
       }
       $this->store = SessionManager::getSession('store_session_1234567876543');
   }

   public function clear($property = ""){
        if(!empty($property)){
            Delete::delete('sessions',['sessionProperty'=>$property]);
        }else{
            Tables::deleteTable('sessions');
            $this->storage();
        }
        SessionManager::setSession('store_session_1234567876543',[]);
       $this->valuesFound = $this->get($property);
        return $this;
   }
}