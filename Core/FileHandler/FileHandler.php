<?php

namespace FileHandler;

use Alerts\Alerts;
use Curls\Curls;
use Datainterface\Database;
use Datainterface\Delete;
use Datainterface\Insertion;
use Datainterface\MysqlDynamicTables;
use Datainterface\Updating;
use GlobalsFunctions\Globals;

class FileHandler
{
  public static function saveFile($filename, $data, $type = "tmp"){

      $path = $_SERVER['DOCUMENT_ROOT'].'/Files';
      if(!is_dir($path)){
         mkdir($path, 777);
      }

      $home = isset($_SERVER['HTTPS']) ? 'https://'.$_SERVER['HTTP_HOST'] : 'http://'.$_SERVER['HTTP_HOST'].'/'.
      Globals::home();
      switch ($type){
          case 'tmp':
              $content = file_get_contents($data);
              top:
              if(file_exists($path.'/'.$filename)){
                  $list = explode('.', $filename);
                  $filename = uniqid().'.'.end($list);
                  goto top;
              }else{
                  if(file_put_contents($path.'/'.$filename, $content)){
                      $data =[
                          'filename'=>$filename,
                          'filesize'=>filesize('Files/'.$filename),
                          'fileurl'=>$home.'/Files/'.$filename
                      ];
                      self::dbSavingFile($data);
                      return $home.'/Files/'.$filename;
                  }
              }
          case 'binary':
              tops:
              if(file_exists($path.'/'.$filename)){
                  $list = explode('.', $filename);
                  $filename = uniqid().'.'.end($list);
                  goto tops;
              }else{
                  if(file_put_contents($path.'/'.$filename, $data)){
                      $data =[
                          'filename'=>$filename,
                          'filesize'=>filesize('Files/'.$filename),
                          'fileurl'=>$home.'/Files/'.$filename
                      ];
                      self::dbSavingFile($data);
                    return $home.'/Files/'.$filename;
                  }
              }
          default:
              return $path;

      }
  }


  public static function deleteFile($filename){
      if(empty($filename)){
          return false;
      }

      $base = $_SERVER['DOCUMENT_ROOT'].'/Files';
      if(is_dir($base)){
          $fileList = scandir($base);

          foreach ($fileList as $file){
              if($file === $filename){
                 return unlink($base.'/'.$filename);
              }
          }
      }
  }

  public static function renameFile($oldname, $newname){
      if(empty($newname) || empty($oldname)){
          return false;
      }
      return rename($_SERVER['DOCUMENT_ROOT'].'/Files/'.$oldname, $_SERVER['DOCUMENT_ROOT'].'/Files/'.$newname);
}

  public static function findFile($filname, $type = 'absolute'){
      if(empty($filname)){
          return false;
      }
      $base = $_SERVER['DOCUMENT_ROOT'].'/Files';

      if(is_dir($base)){
          $fileList = scandir($base);
          foreach ($fileList as $file){
              if($file === $filname){
                  if($type === "absolute"){
                      return $_SERVER['DOCUMENT_ROOT'].'/Files/'.$filname;
                  }else{
                      return 'Files/'.$filname;
                  }
              }
          }
      }
  }

 public static function dbSavingFile($data){
        $con = Database::database();
        $columns = ['fid', 'filename','filesize','fileurl','target_id'];
        $attributes = [
            'fid'=>['INT(11)','AUTO_INCREMENT','PRIMARY KEY'],
            'filename'=>['VARCHAR(100)','NULL'],
            'filesize'=>['INT(11)', 'NULL'],
            'fileurl'=>['VARCHAR(250)','NULL'],
            'target_id'=>['INT(11)','NULL']
        ];
        $maker = new MysqlDynamicTables();
        $maker->resolver($con,$columns,$attributes,'file_managed',false);
  
        return Insertion::insertRow('file_managed', $data);
 }

 public static function dbdeleteFile($keyValue){
      return Delete::delete('file_managed', $keyValue);
 }

 public static function dbupdateFile($keyValue, $data){
      return Updating::update('file_managed',$data, $keyValue);
 }

 public static function zipFiles($filesCurrentDirectory, $nameZipFile, $destinationDirectory, $additionalFile = ""){
      $zipper = new \ZipArchive();

      if($zipper->open($nameZipFile, \ZipArchive::CREATE) === true){
          $files = scandir($filesCurrentDirectory);
          foreach ($files as $file){
              if(is_file($filesCurrentDirectory.'/'.$file)){
                  $zipper->addFile($filesCurrentDirectory.'/'.$file, $file);
              }
          }

          if(!empty($additionalFile)){
              $lists = explode('/',$additionalFile);
              $filename = end($lists);
              $zipper->addFile($additionalFile,$filename );
          }
          $zipper->close();
          return $_SERVER['DOCUMENT_ROOT'].'/'.$nameZipFile;
      }
 }

 public static function createBackUp($zipname){
      $storage = $_SERVER['DOCUMENT_ROOT'].'/Core/Router/Register/registered_path_available.json';

       if(file_exists($_SERVER['DOCUMENT_ROOT'].'/Backups/'.$zipname)){
           echo Alerts::alert('danger', "Please give new name this {$zipname} already exist");
           return false;
       }
      $current = $_SERVER['DOCUMENT_ROOT'].'/Views';
      $destination = $_SERVER['DOCUMENT_ROOT'].'/Backups';
      $zipfile = self::zipFiles($current, "backups.zip", $destination,  $storage);

      if(!is_dir($destination)){
          mkdir($destination,777,true);
      }

      if(rename($zipfile, $destination.'/'.$zipname)){
          echo Alerts::alert('info', "Back up of your view and current register for route created successfully with name {$zipname}");
      }else{
          echo Alerts::alert('warning', "Failed to move your backup file to Backup folder please check your back at root directory and move it into Backup directory for system to acknowledge its existence");
      }


 }


 public static function collectionsBackupFile(){
      $files = [];

      if(!is_dir($_SERVER['DOCUMENT_ROOT'].'/Backups')){
          return [];
      }

      $list = scandir($_SERVER['DOCUMENT_ROOT'].'/Backups');
      foreach ($list as $li){
          if($li !== '.' && $li !== '..'){
              $filename = $_SERVER['DOCUMENT_ROOT'].'/Backups/'.$li;
              $created = filemtime($filename);
              array_push($files, ['filename'=>$li, 'created'=>$created,'current'=>false]);
          }
      }

      $last = time();
      if(!empty($files)){
          $last = $files[0]['created'];
      }

      foreach ($files as $file){
         $times = $file['created'];
         if($last > $times){
             continue;
         }elseif($last < $times){
             $last = $times;
         }
      }

      $counter= 0;
      foreach ($files as $file){
          if($file['created'] == $last){
             $file['current'] = true;
             $files[$counter] = $file;
          }
          $counter += 1;
      }

    return $files;
 }

 public static function collectInfoFileLink($link){
     $curl = new Curls();
     $curl->setUrl($link);
     $curl->runCurl();
     print_r($curl->getResultBody());
 }
}