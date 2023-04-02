<?php

namespace Robot;

use Core\RouteConfiguration;
use Datainterface\Database;
use Datainterface\Delete;
use Datainterface\Insertion;
use Datainterface\MysqlDynamicTables;
use Datainterface\SecurityChecker;
use Datainterface\Selection;
use Datainterface\Tables;
use Datainterface\Updating;
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
      return [
          'allowed'=>Selection::selectById(self::schema()['table'],['locationInRobot'=>'allowed']),
          'disallowed'=> Selection::selectById(self::schema()['table'],['locationInRobot'=>'disallowed'])
      ];
  }

  public static function runSchema(){
      $maker = new MysqlDynamicTables();
      $maker->resolver(Database::database(), self::schema()['col'], self::schema()['att'],self::schema()['table'], false);
  }

  public static function remove($url){
      if(!SecurityChecker::isConfigExist()){
          return false;
      }
      self::runSchema();
      $data = [
        'status'=>false
      ];
      $result = Updating::update(self::schema()['table'],$data,['viewUrl'=>$url]);
      if($result === true){
          return self::upDateRobotFile();
      }
      return false;

  }

  public static function add($url){
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
      }
      return false;
  }

  public static function disAllowed($url){
      if(!SecurityChecker::isConfigExist()){
          return false;
      }
      self::runSchema();
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
      $groups = self::grouping();
      $host = Globals::protocal().'://'.Globals::serverHost().'/'.Globals::home();
      $sitemap = Globals::sitemap();

      $allowedSection = [];
      $disAllowedSection = [];

      foreach ($groups['allowed'] as $key=>$value){
          if(gettype($value) === 'array'){
              $allowedSection[] = trim('Allow: '.$host.'/'.$value['viewUrl'] ?? 'Allow: '.$host);
          }
      }
      foreach ($groups['disallowed'] as $key=>$value){
          if(gettype($value) === 'array'){
              $disAllowedSection[] = trim('Disallow: '.$host.'/'.$value['viewUrl'] ?? 'Disallow: '.$host);
          }
      }

      $content = "# This applies that all client need to follows these rules\nUser-agent: *\n# This is disallowed section that means directories and files that dont need to be crawled\nDisallow: /Core/\nDisallow: /Backups/\nDisallow: /Files/\nDisallow: /Json-store/\nDisallow: /vendor/\nDisallow: /Views/\nDisallow: /Views/DefaultViews/\nDisallow: /Js/\nDisallow: /assets/\nDisallow: /Backups/\nDisallow: /settings.php\nDisallow: /index.php\nDisallow: /composer.json\nDisallow: /composer.lock\nDisallow: /README.md\nDisallow: /.gitIgnore\nDisallow: /.htaccess\n";

      if(file_exists($_SERVER['DOCUMENT_ROOT'].'/robot.txt')){
          unlink($_SERVER['DOCUMENT_ROOT'].'/robot.txt');
      }
      file_put_contents($_SERVER['DOCUMENT_ROOT'].'/robot.txt', $content);
      $handler = fopen($_SERVER['DOCUMENT_ROOT'].'/robot.txt','a');

      foreach ($disAllowedSection as $key=>$value){
          fwrite($handler,"{$value}\n");
      }
      fclose($handler);

      $content = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/robot.txt');
      $content .= "\n# This is allowed section which might override above disallowed\n";
      file_put_contents($_SERVER['DOCUMENT_ROOT'].'/robot.txt', $content);

      $handler = fopen($_SERVER['DOCUMENT_ROOT'].'/robot.txt','a');
      foreach ($allowedSection as $key=>$value){
          fwrite($handler,"{$value}\n");
      }
      fclose($handler);

      $content = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/robot.txt');
      $content .= "\n# This is sitemap location indicator\nSitemap: {$host}/{$sitemap}\n";
      return file_put_contents($_SERVER['DOCUMENT_ROOT'].'/robot.txt', $content);
  }

  public static function robotFileCreation($privateDefault = false){
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