<?php

namespace Robot;

use Core\RouteConfiguration;
use Datainterface\Database;
use Datainterface\Insertion;
use Datainterface\MysqlDynamicTables;
use Datainterface\Query;
use Datainterface\SecurityChecker;
use Datainterface\Selection;
use Datainterface\Updating;
use ErrorLogger\ErrorLogger;
use GlobalsFunctions\Globals;

class Robot
{
  public static function schema() : array{
      $columns = ['rid','viewUrl','viewName', 'locationInRobot','status'];
      $attributes = [
          'rid'=>['int(11)','auto_increment','primary key'],
          'viewUrl'=>['varchar(50)','not null'],
          'viewName'=>['varchar(100)','null'],
          'locationInRobot'=>['varchar(20)','not null'],
          'status'=>['boolean','null']
      ];
      return [
          'col'=>$columns,
          'att'=>$attributes,
          'table'=>'robot'
      ];
  }

  public static function grouping() : array{
      $t = self::schema()['table'];
      $allowedQuery = "SELECT * FROM {$t} WHERE locationInRobot = :allowed AND status = :status";
      $disallowedQuery = "SELECT * FROM {$t} WHERE locationInRobot = :disallowed AND status = :status";
      return [
          'allowed'=>Query::query($allowedQuery, ['allowed'=>'allowed', 'status'=>1]),
          'disallowed'=> Query::query($disallowedQuery, ['disallowed'=>'disallowed', 'status'=>1])
      ];
  }

  public static function runSchema(){
      if(SecurityChecker::isConfigExist()){
          $maker = new MysqlDynamicTables();
          $maker->resolver(Database::database(), self::schema()['col'], self::schema()['att'],self::schema()['table'], false);
      }
  }

  public static function remove($url){
      if(!SecurityChecker::isConfigExist()){
          return false;
      }
      self::runSchema();
      $data = [
        'status'=>0
      ];
      $result = Updating::update(self::schema()['table'],$data,['viewUrl'=>$url]);
      if($result === true){
          return self::upDateRobotFile();
      }
      return false;

  }

  public static function isAdded($url){
      $isAddeQuery = "SELECT * FROM ".self::schema()['table']." WHERE viewUrl = :url AND status = :s";
      if(!empty(Query::query($isAddeQuery,['url'=>$url,'s'=>1]))){
          return true;
      }
      return false;
  }

  public static function add($url, $op = 'default-call'){
      if(!SecurityChecker::isConfigExist()){
          return false;
      }
      $viewFound = Globals::findViewByUrl($url);
      if(empty($viewFound)){
          return false;
      }

      $data = [
          'viewUrl'=>  $viewFound['view_url'],
          'viewName'=> $viewFound['view_name'],
          'locationInRobot'=> 'allowed',
          'status'=>true
      ];
      self::runSchema();
      $exist = Selection::selectById(self::schema()['table'],['viewUrl'=>$url]);
      if(empty($exist)){
          $result = Insertion::insertRow(self::schema()['table'], $data);
          if(!empty($result)){
              return self::upDateRobotFile();
          }
      }else{
          if($op === 'api-call'){
              $result = Updating::update(self::schema()['table'],['status'=>1], ['viewUrl'=>$url]);
               if(!empty($result)){
                  return self::upDateRobotFile();
               }
          }else{
               return self::upDateRobotFile();
          }
      }
      return false;
  }

  public static function disAllowed($url){
      if(!SecurityChecker::isConfigExist()){
          return false;
      }
      self::runSchema();
      if(!self::isAdded($url)){
        self::add($url, 'api-call');
      }
      $data =['locationInRobot'=>'disallowed'];
      if(Updating::update(self::schema()['table'], $data,['viewUrl'=>$url])){
          return self::upDateRobotFile();
      }
      return false;
  }

    public static function allowed($url){
        if(!SecurityChecker::isConfigExist()){
            return false;
        }
        self::runSchema();
        if(!self::isAdded($url)){
            self::add($url, 'api-call');
        }
        $data =['locationInRobot'=>'allowed'];
        if(Updating::update(self::schema()['table'], $data,['viewUrl'=>$url])){
            return self::upDateRobotFile();
        }
        return false;
    }

  public static function upDateRobotFile() : bool {
      if(!SecurityChecker::isConfigExist()){
          return false;
      }
      try {
          $groups = self::grouping();
          $host = Globals::protocal().'://'.Globals::serverHost().'/'.Globals::home();
          $sitemap = Globals::sitemap();

          $allowedSection = [];
          $disAllowedSection = [];

          foreach ($groups['allowed'] as $key=>$value){
              if(gettype($value) === 'array'){
                  $allowedSection[] = trim('Allow: /'.$value['viewUrl'] ?? 'Allow: '.$host);
              }
          }
          foreach ($groups['disallowed'] as $key=>$value){
              if(gettype($value) === 'array'){
                  $disAllowedSection[] = trim('Disallow: /'.$value['viewUrl'] ?? 'Disallow: '.$host);
              }
          }

          $content = "# This applies that all client need to follows these rules\nUser-agent: *\n# This is disallowed section that means directories and files that dont need to be crawled\nDisallow: /Core/\nDisallow: /Backups/\nDisallow: /Files/\nDisallow: /Json-store/\nDisallow: /vendor/\nDisallow: /Views/\nDisallow: /Views/DefaultViews/\nDisallow: /Js/\nDisallow: /assets/\nDisallow: /Backups/\nDisallow: /settings.php\nDisallow: /index.php\nDisallow: /composer.json\nDisallow: /composer.lock\nDisallow: /README.md\nDisallow: /.gitIgnore\nDisallow: /.htaccess\n";

          if(file_exists($_SERVER['DOCUMENT_ROOT'].'/robots.txt')){
              file_put_contents($_SERVER['DOCUMENT_ROOT'].'/robots.txt', '');
              chmod($_SERVER['DOCUMENT_ROOT'].'/robots.txt', 0777);
          }
          file_put_contents($_SERVER['DOCUMENT_ROOT'].'/robots.txt', $content);
          $handler = fopen($_SERVER['DOCUMENT_ROOT'].'/robots.txt','a');

          foreach ($disAllowedSection as $key=>$value){
              fwrite($handler,"{$value}\n");
          }
          fclose($handler);

          $content = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/robots.txt');
          $content .= "\n# This is allowed section which might override above disallowed\n";
          file_put_contents($_SERVER['DOCUMENT_ROOT'].'/robots.txt', $content);

          $handler = fopen($_SERVER['DOCUMENT_ROOT'].'/robots.txt','a');
          foreach ($allowedSection as $key=>$value){
              fwrite($handler,"{$value}\n");
          }
          fclose($handler);

          $content = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/robots.txt');
          $content .= "\n# This is sitemap location indicator\nSitemap: {$host}/{$sitemap}\n";
          return file_put_contents($_SERVER['DOCUMENT_ROOT'].'/robots.txt', $content);
      }catch (\TypeError $e){
          ErrorLogger::log($e);
      }
  }

  public static function robotFileCreation($privateDefault = false){
      if(!SecurityChecker::isConfigExist()){
          return;
      }
      if(Database::database() === null){
          return;
      }
      $views = new RouteConfiguration();
      $allViews = $views->getAllViews();
     
      foreach ($allViews as $key=>$value){
          if(gettype($value) == 'array'){
              if($value['view_role_access'] !== "administrator"){
                  if($value['view_role_access'] === "private" && $privateDefault === true){
                      self::add($value['view_url']);
                  }elseif($value['view_role_access'] === "public" || $value['view_role_access'] === "moderator"){
                      self::add($value['view_url']);
                  }
              }
          }
      }
  }

  public static function getAllInRobot(){
      self::runSchema();
      return Selection::selectAll(self::schema()['table']);
  }

}