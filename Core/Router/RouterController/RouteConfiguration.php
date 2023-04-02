<?php

namespace Core;

use Sessions\SessionManager;

class RouteConfiguration
{
   private array $views;

   public function __construct($refresh = true){
       $base = $_SERVER['DOCUMENT_ROOT'].'/Core/Router/Register/registered_path_available.json';
       if(isset($_SESSION['site.manager']['views'])){
           $this->views = SessionManager::getNamespacedSession('site.manager','views');
       }else{
           $this->views = json_decode(file_get_contents($base), true);
           SessionManager::setNamespacedSession($this->views,'site.manager','views');
       }
       if($refresh){
           $this->views = json_decode(file_get_contents($base), true);
       }
   }

   public function getAllViews(){
       return $this->views;
   }

   public function printViews(){
       echo "<pre>";
       print_r($this->views);
       echo "</pre>";
   }
}