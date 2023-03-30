<?php
namespace GlobalsFunctions;
use Core\Router;

class Globals
{
  public static function titleView(){
      return isset($_SESSION['public_data']['view']['view_name']) ? $_SESSION['public_data']['view']['view_name'] : "View not found";
  }

  public static function host(){
      return isset($_SESSION['public_data']['host']) ? $_SESSION['public_data']['host'] : "host not found";
  }

  public static function url(){
      return isset($_SESSION['public_data']['path']) ? $_SESSION['public_data']['path'] : "Path not found";
  }

  public static function view(){
      return isset($_SESSION['public_data']['view']) ? $_SESSION['public_data']['view'] : "View data not found";
  }

  public static function queryline(){
      return isset($_SESSION['public_data']['query']) ? $_SESSION['public_data']['query'] : "Querystring not found";
  }

  public static function params(){
      return isset($_SESSION['public_data']['params']) ? $_SESSION['public_data']['params'] : "Params not set";
  }

  public static function user(){
      return isset($_SESSION['private_data']['current_user']) ? $_SESSION['private_data']['current_user'] : [];
  }

  public static function menus(){

      $menus = [];
      if(isset($_SESSION['viewsstorage'])){
          foreach ($_SESSION['viewsstorage'] as $view){
              if($view['view_role_access'] === 'administrator'){
                  $menus[] = $view;
              }
          }
      }

     return $menus;
  }

  public static function privateMenus(){
      $menus = [];
      if(isset($_SESSION['viewsstorage'])){
          foreach ($_SESSION['viewsstorage'] as $view){
              if($view['view_role_access'] === 'private'){
                  $menus[] = $view;
              }
          }
      }

      return $menus;
  }

  public static function findViewByUrl($url){

      if(isset($_SESSION['viewsstorage'])){
          foreach ($_SESSION['viewsstorage'] as $view){
              if($view['view_url'] === $url){
                  return $view;
              }
          }
      }

  }

  public static function method(){
      return $_SERVER['REQUEST_METHOD'];
  }

  public static function uri(){
      return $_SERVER['REQUEST_URI'];
  }

  public static function root(){
      return $_SERVER['DOCUMENT_ROOT'];
  }

  public static function script(){
      return $_SERVER['SCRIPT_FILENAME'];
  }

  public static function serverName(){
      return $_SERVER['SERVER_NAME'];
  }

  public static function port(){
      return $_SERVER['SERVER_PORT'];
  }

  public static function address(){
      return $_SERVER['SERVER_ADDR'];
  }

  public static function protocal(){
      return isset($_SERVER['HTTPS']) ? 'https' : 'http';
  }

  public static function serverHost(){
      return $_SERVER['HTTP_HOST'];
  }

  public static function post($postKey){
    if(isset($_POST[$postKey])){
        return htmlspecialchars(strip_tags($_POST[$postKey]));
    }
    return false;
  }

  public static function get($getKey){
    if(isset($_GET[$getKey])){
        return htmlspecialchars(strip_tags($_GET[$getKey]));
    }
    return false;
  }

  public static function files($fileKey){
    return $_FILES[$fileKey];
  }

  public static function redirect($destination, $permanent = false){
     header("Location: {$destination}",true, $permanent ? 301 : 302);
     exit();
  }

  public static function home(){
      global $HOME;
      $home = $HOME[0] === '/' ? strlen($HOME) > 0 ? substr($HOME, 1) : $HOME : $HOME;
     return $home;
  }

  public static function urlComponents($url){
      return parse_url($url);
  }

  public static function viewTitleOnRequest(){
      $component = \GlobalsFunctions\Globals::urlComponents($_SERVER['REQUEST_URI']);
      $titlePage = "";
      if(!empty($component)){
          $list = explode('/', $component['path']);
          $path = end($list);
          $view = \GlobalsFunctions\Globals::findViewByUrl($path);
          $titlePage  = $view['view_name'] ?? $_SERVER['HTTP_HOST'];
      }
      return $titlePage;
  }

}