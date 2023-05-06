<?php

namespace ApiHandler;

use Core\Router;
use ErrorLogger\ErrorLogger;
use GlobalsFunctions\Globals;

class ApiHandlerClass
{
    public static function headersRequest(){
        return getallheaders();

    }

    public static function findHeaderValue($headerType){
        $headers = getallheaders();

        if(empty($headers)){
            return NULL;
        }

        foreach ($headers as $headerss=>$value){
            if($headerss === $headerType){
                return $value;
            }
        }
    }

    public static function getPostBody($ssoc = true){
            return json_decode(file_get_contents('php://input'), $ssoc);
    }

    public static function paramsQuery(){
        $url = $_SERVER['REQUEST_URI'];
        $line = parse_url($url, PHP_URL_QUERY);
        parse_str($line, $query);

        if(isset($query)){
            return $query;
        }
        return [];
    }

    public static function createParams($data = []){
        $line = "";
        foreach ($data as $datum=>$value){
            $line .= $datum.'='.$value.'&';
        }

        if(empty($line)){
            return NULL;
        }

        $line = substr($line, 0, strlen($line) - 1);
        return trim($line);
    }

    public static function stringfiyData($data){
        return json_encode($data);
    }

   public static function isApiCall(){
       try{
           $content = self::findHeaderValue('Content-Type');
           if($content === "application/json"){
               $url = Router::clearUrl(Globals::uri());
               $parseurl = parse_url($url, PHP_URL_PATH);
               $parseurl = substr($parseurl, 1 , strlen($parseurl));
               $list = strpos($parseurl, '/') ? explode('/',$parseurl) : $parseurl;
               $parseurl = gettype($list) === "array" ? end($list) : $parseurl;
               $view = Globals::findViewByUrl($parseurl);
               if(!empty($view)){
                   Router::requiringFile($view);
               }
           }
           
       }catch(\Exception $e){
           ErrorLogger::log($e);
           Router::errorPages(500);
           exit;
       }
   }

}