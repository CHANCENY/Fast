<?php

namespace Core;

use Datainterface\Database;
use Datainterface\Insertion;
use Datainterface\MysqlDynamicTables;
use Datainterface\Selection;
use ErrorLogger\ErrorLogger;
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

   public static function metaTags($metaData, $page) : bool{
       $col = ['page_url', 'data', 'mid'];
       $attr =['page_url'=>['varchar(50)','not null'], 'data'=>['text','null'],'mid'=>['int(11)','auto_increment','primary key']];
       $maker = new MysqlDynamicTables();
       $maker->resolver(Database::database(),$col,$attr,'metatags',false);

       $content = json_encode($metaData);
       $data =['page_url'=>$page, 'data'=>$content];
       return Insertion::insertRow('metatags',$data);
   }

   public static function appendMetatags($page){
       try {
           $data = Selection::selectById('metatags',['page_url'=>$page]);
           if(!empty($data)){
              $lineOfMetaTags = "";
              $contentArray = [];
              foreach ($data as $key=>$value){
                  if(gettype($value) === 'array'){
                      $contentArray[] = json_decode($value['data'], true);
                  }
              }
              foreach ($contentArray as $key=>$value){
                  if(gettype($value) === 'array'){
                      extract($value);
                      $line = "<meta name='{$name}' content='{$content}' />";
                      $lineOfMetaTags .= "\n{$line}";
                  }
              }
              return $lineOfMetaTags;
           }
       }catch (\Exception $e){
           ErrorLogger::log($e);
       }
   }
}