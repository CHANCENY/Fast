<?php

namespace CustomInstallation;


class CustomInstallation
{
  public static function writeComposerFile( $className, $directoryName){
      if(empty($className) || empty($directoryName)){
          return NULL;
      }

      $composer = json_decode(file_get_contents($_SERVER['DOCUMENT_ROOT'].'/composer.json'), true);
      $pr4 = [];
      if(isset($composer['autoload']['psr-4'])){
          $pr4 = $composer['autoload']['psr-4'];
      }

      $listClass = explode(',', $className);
      $listPath = explode(',', $directoryName);

      if(count($listPath) !== count($listClass)){
          return FALSE;
      }

      $items = [];

      for ($i = 0; $i < count($listClass); $i++){
          $item = [$listClass[$i].'\\'=>$listPath[$i].'/'];
          $items = array_merge($items, $item);
      }

      if(empty($pr4)){
          $pr4 = array_merge($pr4, $items);
          $composer['autoload']['psr-4'] = $pr4;
      }else{
         $pr4 = array_merge($pr4, $items);
         $composer['autoload']['psr-4'] = $pr4;
      }

      if(file_put_contents($_SERVER['DOCUMENT_ROOT'].'/composer.json', self::cleardata(json_encode($composer)))){
          return true;
      }
      return false;
  }

  public static function cleardata($content){

        if(!empty($content)){
            $content = str_replace('\/',"/",$content);
            $content = str_replace('/', '/', $content);
            return $content;
        }
  }


  public static function saveModules($zips ){

      if(empty($zips)){
          return false;
      }

      $base = $_SERVER['DOCUMENT_ROOT'].'/Customs';

      if(!is_dir($base)){
          mkdir($base, 777, true);
      }

      $counter = 0;
      foreach ($zips as $zip){
          $zipper = new \ZipArchive();
          $res = $zipper->open($zip);
          if($res === true){
              $zipper->extractTo($base);
              $counter += 1;
          }
          $zipper->close();
      }

      if($counter === count($zips)){
          return true;
      }
      return false;
  }

}