<?php

namespace Modules;

use MiddlewareSecurity\Security;

class SettingWeb
{
  public $settingConfigs =[
        "home"=>[
            "view_name"=>'Home',
            "view_url"=>'/',
            "view_path_absolute"=>'/',
            "view_path_relative"=>'index.php',
            "view_timestamp"=>NULL,
            "view_description"=>'This is home',
            "view_role_access"=>'public'
        ]
      ];

  public function __construct(){

      $security = new Security();
      $user= $security->checkCurrentUser();
      if($user === "U-Admin"){
        if(isset($this->settingConfigs['home']['view_path_absolute'])){
          $this->settingConfigs['home']['view_path_absolute'] = $_SERVER['DOCUMENT_ROOT'].'/Views/DefaultViews/index.view.php';
        }
      }else{
          if(file_exists($_SERVER['DOCUMENT_ROOT'].'/Views/index.view.php')){
              $this->settingConfigs['home']['view_path_absolute'] = $_SERVER['DOCUMENT_ROOT'].'/Views/index.view.php';
          }
          elseif(isset($this->settingConfigs['home']['view_path_absolute'])){
              $this->settingConfigs['home']['view_path_absolute'] = $_SERVER['DOCUMENT_ROOT'].'/Views/DefaultViews/index.view.php';
          }
      }

  }

  public function setSettingConfig($settingName, $setting = []){
      if(!empty($setting)){
          $this->settingConfigs[$settingName] = $setting;
      }
  }

  public function getSettingConfig($settingName){
      return $this->settingConfigs[$settingName];
  }
}