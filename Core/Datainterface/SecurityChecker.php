<?php

namespace Datainterface;

use ConfigurationSetting\ConfigureSetting;

class SecurityChecker
{
  public static function isConfigExist(){
      if(!empty(ConfigureSetting::getDatabaseConfig())){
          return true;
      }
      return false;
  }
}