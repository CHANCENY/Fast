<?php

namespace Core;

use Alerts\Alerts;
use ApiHandler\ApiHandlerClass;
use ConfigurationSetting\ConfigureSetting;
use Datainterface\Database;
use Datainterface\Tables;
use ErrorLogger\ErrorLogger;
use GlobalsFunctions\Globals;
use MiddlewareSecurity\Security;
use Modules\SettingWeb;
use RoutesManager\RoutesManager;
use Sessions\SessionManager;

@session_start();
class Router
{
    /**
     * @param $content
     * @return array|string|string[]|void
     */
    public static function clearUrl($content){

        if(!empty($content)){
            $content = str_replace("\\", "/", $content);
            $content = str_replace('\/'," ",$content);
            $content = str_replace('//', '/', $content);
            return $content;
        }
    }

    /**
     * @param $restricstionLevel
     * @return void
     */
    public static function router($restricstionLevel = false){

        if(!empty(ConfigureSetting::getDatabaseConfig())) {

            $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            $host = parse_url($_SERVER['REQUEST_URI'], PHP_URL_HOST);
            $query = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY);
            $query = empty($query) ? "page={$_SERVER['PHP_SELF']}" : $query;
            parse_str($query ?? "none", $list);
            $queryList = $list ?? [];

            if (SessionManager::getSession('site') === false) {
                $path = 'registration';
            }

            //find url
            $routes = new RoutesManager();
            $routes->installerViewDefaults();

            $routers = $routes->loadRoutes()->getRoutes();
            $_SESSION['viewsstorage'] = $routers;
            $foundView = [];

            //looking if home /
            if(trim(str_replace('/','', $path)) === '' ||
                trim(str_replace('/','', $path)) === trim(str_replace('/','',Globals::home()))){
                $path = self::homeUrl();
            }

            foreach ($routers as $key=>$value){
                if(preg_match("~\b{$value['view_url']}\b~",$path)){
                   $foundView = $value;
                }
            }
            $data = [
                "host" => $host,
                "path" => $path,
                "query" => $query,
                "params" => $queryList,
                "view" => $foundView
            ];
            $_SESSION['public_data'] = $data;

            if($restricstionLevel === true){
                $error = [
                    'code'=>404,
                    'message'=>"View not found on url {$path}",
                    'location'=> __FILE__
                ];

                $security = new Security();
                $result = $security->securityView($foundView);
                $foundView = $routes->loadViewByUrl($result)->getRoutes();
                if(!empty($foundView)){
                    self::requiringFile($foundView[0]);
                }else {
                    ErrorLogger::log(NULL, $error);
                    Router::errorPages(404);
                }
            }else{
                if(empty($foundView)){
                    ErrorLogger::log(NULL, [
                        'code'=>404,
                        'message'=>"View not found on url {$path}",
                        'location'=> __FILE__
                    ]);
                    Router::errorPages(404);
                }else{
                    self::requiringFile($foundView);
                }
            }
        }else{

            //install framework
            $viewFormat =[
                "view_name"=>'Site configuration',
                "view_url"=>'install',
                "view_path_absolute"=>Globals::root().'/install.php',
                "view_path_relative"=>'install.php',
                "view_timestamp"=>time(),
                "view_description"=>'installing website',
                "view_role_access"=>'public'
            ];
            self::requiringFile($viewFormat);
        }
    }

    /**
     * @param $foundView
     * @return void
     */
    public static function requiringFile($foundView = []){
        $list = explode('.', $foundView['view_path_absolute']);
        $contetType = Router::headerContentType(end($list));

        if(file_exists($foundView['view_path_absolute'])){
            http_response_code(200);
            global $THIS_SITE_ACCESS_LOCK;
            if($THIS_SITE_ACCESS_LOCK === true){
                require_once $foundView['view_path_absolute'];
            }else{
               die('Access denied!');
            }
        }else{
            http_response_code(404);
            self::errorPages(404);
        }

    }

    /**
     * @param $extension
     * @return string
     */
    public static function headerContentType($extension){
        switch ($extension){
            case 'html':
                return 'text/html';
            case 'php':
                return 'txt/html';
            case 'json':
                return 'application/json';
            case 'xml':
                return 'application/xml';
            case 'js':
                return 'application/javascript';
            default:
                return 'plain/text';

        }
    }

    /**
     * @param $code
     * @return void
     */
    public static function errorPages($code){
        $storage = $_SERVER['DOCUMENT_ROOT'].'/Core/Router/Register/registered_path_available.json';
        $views = json_decode(file_get_contents($storage), true);
        switch ($code){
            case 404:
                $error = [
                    'code'=>404,
                    'message'=>"Requested Page not found",
                    'location'=>$_SERVER['PHP_SELF']
                ];
                ErrorLogger::log(null,$error);
                $foundViews = [];
                foreach ($views as $view){
                    if($view['view_url'] === '404'){
                        $foundViews = $view;
                        break;
                    }
                }
                self::requiringFile($foundViews);
                break;
            case 500:
                $error = [
                    'code'=>500,
                    'message'=>"Server error occured",
                    'location'=>$_SERVER['PHP_SELF']
                ];
                ErrorLogger::log(null,$error);
                $foundViews = [];
                foreach ($views as $view){
                    if($view['view_url'] === '500'){
                        $foundViews = $view;
                        break;
                    }
                }
                self::requiringFile($foundViews);
                break;
            case 401:
                $error = [
                    'code'=>401,
                    'message'=>"Unauthorized user trying to access private view",
                    'location'=>$_SERVER['PHP_SELF']
                ];
                ErrorLogger::log(null,$error);
                $foundViews = [];
                foreach ($views as $view){
                    if($view['view_url'] === '401'){
                        $foundViews = $view;
                        break;
                    }
                }
                self::requiringFile($foundViews);
                break;
            case 403:
                $error = [
                    'code'=>403,
                    'message'=>"Blocked user trying to use the  site",
                    'location'=>$_SERVER['PHP_SELF']
                ];
                ErrorLogger::log(null,$error);
                $foundViews = [];
                foreach ($views as $view){
                    if($view['view_url'] === '403'){
                        $foundViews = $view;
                        break;
                    }
                }
                self::requiringFile($foundViews);
                break;
            default:
                echo "here";
        }
    }

    /**
     * @param string $view_url
     * @param array $options
     * @return void
     */
    public static function attachView(string $view_url, array $options = array()){
        $view = Globals::findViewByUrl($view_url);
        if(!empty($view)){
            extract($options);
            extract($view);
            if(file_exists($view_path_absolute)){
                require_once $view_path_absolute;
            }
        }
    }

    /**
     * @return void
     */
    public static function navReader(){
        try{
            ApiHandlerClass::isApiCall();
            $security = new Security();
            $user= $security->checkCurrentUser();
            $base = $_SERVER['DOCUMENT_ROOT'];
            if($user === "U-Admin"){
                if(file_exists("{$base}/Core/DefaultViews/nav.php")){
                    require_once "{$base}/Core/DefaultViews/nav.php";
                }
            }else{
                /*
                 * Your nav will load from here if exist in Views directory
                 */
                if(file_exists($base.'/Views/nav.view.php')){
                    require_once $base.'/Views/nav.view.php';
                }else{
                    //default nav will load here with menus that are not admin based
                    if(file_exists("{$base}/Views/DefaultViews/nav.php")){
                        require_once 'Views/DefaultViews/nav.php';
                    }
                }
                global $connection;
                $connection = Database::database();
                if(!empty(ConfigureSetting::getDatabaseConfig())){
                    if(!Tables::tablesExists()){
                        Tables::installTableRequired();
                    }
                }
            }
        }catch (\Exception $e){
            Alerts::alert('danger', 'Sorry views files does not exist');
        }
    }

    /**
     * @return void
     */
    public static function footerReader(){
        //Below handles footer section
        $security = new Security();
        $user= $security->checkCurrentUser();
        $base = $_SERVER['DOCUMENT_ROOT'];
        if($user === "U-ADMIN"){
            if(file_exists("{$base}/Core/DefaultViews/footer.php")){
                require_once "{$base}/Core/DefaultViews/footer.php";
            }else{
               // @todo creating footer.php file and require it
            }
        }else{
            /*
            * Your nav will load from here if exist in Views directory
            */
            if(file_exists($base.'Views/footer.view.php')){
                require_once $base.'/Views/footer.view.php';
            }else{
                //default nav will load here with menus that are not admin based
                if(file_exists($base.'/Views/DefaultViews/footer.php')){
                    require_once $base.'/Views/DefaultViews/footer.php';
                }else{
                    // @todo creating footer.php file and require it
                }
            }
        }
    }

    public static function homeUrl(){
        $security = new Security();
        $user = $security->checkCurrentUser();
        if($user === "U-Admin"){
            return 'default';
        }else{
            global $HOMEPAGE;
            return $HOMEPAGE ?? 'index';
        }
    }

}