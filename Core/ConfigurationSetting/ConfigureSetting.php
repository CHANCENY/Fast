<?php

namespace ConfigurationSetting;

class ConfigureSetting
{
   private static $configs = [
          "mail"=>[
//              "smtp"=>"smtp.gmail.com",
//              "user"=>"chance.svinfotech@gmail.com",
//              "password"=>"neozobjecwvvrrct"
          ]
       ];

   public static function getConfig($name){
       return self::$configs[$name];
   }

   public static function getDatabaseConfig(){
       $base = $_SERVER['DOCUMENT_ROOT'].'/config/basesetting.json';
       if(file_exists($base)){
           return json_decode(file_get_contents($base), true);
       }
   }
}