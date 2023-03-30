<?php

namespace Sessions;

class SessionManager
{
   public static function setSession($sessionName, $data){
       $_SESSION[$sessionName] = $data;
   }

   public static function getSession($sessionName){
       return isset($_SESSION[$sessionName]) ? $_SESSION[$sessionName] : NULL;
   }

   public static function clearSession($sessionName){
       $type = gettype($_SESSION[$sessionName]);
       switch ($type){
           case 'string':
               $_SESSION[$sessionName] = " ";
           case 'array':
               $_SESSION[$sessionName] = array();
           case 'integer':
               $_SESSION[$sessionName] = 0;
           case 'boolean':
               $_SESSION[$sessionName] = false;
           default:
               $_SESSION[$sessionName] = NULL;
       }
   }

   public static function sessions(){
       return $_SESSION;
   }

   public static function setNamespacedSession($data, $namespace, $identity){
       $_SESSION[$namespace][$identity] = $data;
   }

   public static function getNamespacedSession($namespace , $identity){
       return $_SESSION[$namespace][$identity];
   }

}