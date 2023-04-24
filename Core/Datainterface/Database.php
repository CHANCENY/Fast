<?php

namespace Datainterface;

use Alerts\Alerts;
use ConfigurationSetting\ConfigureSetting;
use Core\Router;
use Installation\Installation;
use RoutesManager\RoutesManager;
use Sessions\SessionManager;

class Database
{
   /**
    * Mysql is being used please change these variables value to you database crenditial
    */
   private static $user = "root";

    /**
     * @return string
     */
    public static function getUser()
    {
        return self::$user;
    }

    /**
     * @param string $user
     */
    public static function setUser($user)
    {
        self::$user = $user;
    }

    /**
     * @return null
     */
    public static function getPassword()
    {
        return self::$password;
    }

    /**
     * @param null $password
     */
    public static function setPassword($password)
    {
        self::$password = $password;
    }

    /**
     * @return string
     */
    public static function getDbname()
    {
        $dbobj = ConfigureSetting::getDatabaseConfig();
        self::setDbname($dbobj['dbname']);
        return self::$dbname;
    }

    /**
     * @param string $dbname
     */
    public static function setDbname($dbname)
    {
        self::$dbname = $dbname;
    }

    /**
     * @return string
     */
    public static function getHost()
    {
        return self::$host;
    }

    /**
     * @param string $host
     */
    public static function setHost($host)
    {
        self::$host = $host;
    }
   private static $password = NULL;
   private static $dbname = "blogdb";
   private static $host = "localhost";

   public static function database(){
       $dbobj = ConfigureSetting::getDatabaseConfig();

       try {
           if(empty($dbobj)){
               throw new \Exception("not db data", 100);
           }
           self::setHost($dbobj['host']);
           self::setDbname($dbobj['dbname']);
           self::setUser($dbobj['user']);
           self::setPassword($dbobj['password']);
           $dsn  = "mysql:host=".self::$host.";dbname=".self::$dbname;
           return new \PDO($dsn, self::$user, self::$password);
       }catch (\PDOException $e){

           if($e->getCode() === 1049){
             self::makeDatabase();
           }else{
               echo Alerts::alert('info',$e->getMessage());
               die();
           }
       }catch (\Exception $ee){
           if($ee->getCode() === 100){

           }
       }
   }

   public static function installer(){
       if(empty(ConfigureSetting::getDatabaseConfig())){
           SessionManager::setSession('sitenew', true);
           return;
       }
       $con = self::database();

       if(empty($con)){
           $con = Database::database();
       }
       //installation of tables
       $routes = new RoutesManager();
       $routes->installerViewDefaults();

       $maker = new MysqlDynamicTables();
       $columns = ['uid','firstname','lastname','mail','phone','password','address','role','verified','blocked','image'];
       $attributes = [
         'uid'=>['INT(11)','AUTO_INCREMENT','PRIMARY KEY'],
         'firstname'=>['VARCHAR(100)','NOT NULL'],
         'lastname'=>['VARCHAR(100)', 'NOT NULL'],
         'mail'=>['VARCHAR(100)','NOT NULL'],
         'phone'=>['VARCHAR(50)', 'NULL'],
           'password'=>['VARCHAR(100)', 'NOT NULL'],
         'address'=>['TEXT','NULL'],
         'role'=>['VARCHAR(20)','NOT NULL'],
           'verified'=>['BOOLEAN'],
           'blocked'=>['BOOLEAN'],
           'image'=>['varchar(250)', 'null']
       ];

       $maker->resolver($con,$columns,$attributes,'users',false);

       try{
           $conn = self::database();
           $stmt = $conn->prepare("SELECT 1 FROM cities LIMIT 1");
           $stmt->execute();
       }catch (\Exception $e){

         $path = $_SERVER["DOCUMENT_ROOT"].'/Core/Temps/cities.sql';
         self::importTable($path);
       }

       try{
           $conn = self::database();
           $stmt = $conn->prepare("SELECT 1 FROM countries LIMIT 1");
           $stmt->execute();
       }catch (\Exception $e){

           $path = $_SERVER["DOCUMENT_ROOT"].'/Core/Temps/countries.sql';
           self::importTable($path);
       }

       try{
           $conn = self::database();
           $stmt = $conn->prepare("SELECT 1 FROM states LIMIT 1");
           $stmt->execute();
       }catch (\Exception $e){

           $path = $_SERVER["DOCUMENT_ROOT"].'/Core/Temps/states.sql';
           self::importTable($path);
       }

       $user = Selection::selectById('users', ['role'=>'Admin']);
       if(empty($user)){
           SessionManager::setSession('site', false);
       }else{
           SessionManager::setSession('site', true);
       }

   }

   public static function importTable($file){
       $con = self::database();
       $query = file_get_contents($file);
       if(!empty($query)){
          $stmt = $con->prepare($query);
           return $stmt->execute();
       }
   }

   public static function makeDatabase(){
       try {
           $pdo = new \PDO("mysql:host=".self::$host, self::$user, self::$password);
           $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

           self::$dbname = "`".str_replace("`","``",self::$dbname)."`";
           $pdo->query("CREATE DATABASE IF NOT EXISTS ".self::$dbname);
           $pdo->query("use ".self::$dbname);
       }
       catch (\PDOException $e) {
           die("DB ERROR: " . $e->getMessage());
       }
   }
}