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
use Sessions\SessionManager;

@session_start();
class Router
{
    /**
     * @var $requestUrl
     */
    private $requestUrl;

    /**
     * @var
     */
    private $paramsInUrl;

    /**
     * @var
     */
    private $registeredUrl;

    /**
     * @return mixed
     */
    public function getRequestUrl()
    {
        return $this->requestUrl;
    }

    /**
     * @param mixed $requestUrl
     */
    public function setRequestUrl($requestUrl)
    {
        $this->requestUrl = $requestUrl;
    }

    /**
     * @return mixed
     */
    public function getParamsInUrl()
    {
        return $this->paramsInUrl;
    }

    /**
     * @param mixed $paramsInUrl
     */
    public function setParamsInUrl($paramsInUrl)
    {
        $this->paramsInUrl = $paramsInUrl;
    }

    /**
     * @return mixed
     */
    public function getRegisteredUrl()
    {
        return $this->registeredUrl;
    }

    /**
     * @param mixed $registeredUrl
     */
    public function setRegisteredUrl($registeredUrl)
    {
        $this->registeredUrl = $registeredUrl;
    }

    /**
     * @param $viewData
     * @return false|string|void
     */
    public static function addView($viewData = []){

        try {
            if(empty($viewData)){
                return false;
            }

            //start making the view here
            $baseRoot = $_SERVER['DOCUMENT_ROOT'];
            $additionalPath = '/Core/Router/Register/';
            $completePath = $baseRoot.'/Views';
            $completePath .= $viewData['path'][0] === '/' ? $viewData['path'] : "/".$viewData['path'];
            self::checkMultiDirectory($completePath);
            $relativePath = '/Views';
            $relativePath .= $viewData['path'][0] === '/' ? $viewData['path'] : "/".$viewData['path'];
            $storage = $baseRoot.'/'.$additionalPath.'registered_path_available.json';
            $storage = str_replace('//','/',$storage);

            $listAssoc = json_decode(is_file($storage) ? file_get_contents($storage) : json_encode(["status"=>"no-file"]),true);
            if(isset($listAssoc['status']) && $listAssoc['status'] === 'no-file'){
                return false;
            }

            $accessModifies = ['public', 'private'];

            if(!isset($viewData['access']) && in_array($viewData['access'], $accessModifies) === false){
                return "Please ensure access key is added and has public or private value";
            }

            $viewFormat =[
                "view_name"=>$viewData['name'],
                "view_url"=>$viewData['url'],
                "view_path_absolute"=>$completePath,
                "view_path_relative"=>$relativePath,
                "view_timestamp"=>time(),
                "view_description"=>$viewData['description'],
                "view_role_access"=>$viewData['access']
            ];

            foreach ($listAssoc as $item){
                if($item['view_url'] === $viewData['url']) {
                    return "Ensure url is unique and try again";
                }

                $listed = explode('/', $viewData['url']);
                $vlist = explode('/', $item['view_url']);
                if(in_array($vlist[0], $listed)){
                    return "Ensure that first from hostname in url is unique your clean url";
                }
            }

            array_push($listAssoc, $viewFormat);
            $content = json_encode($listAssoc);

            //clear up

            $content = Router::clearUrl($content);
            if(file_exists($completePath)){
                return "View file name already exist in view directory!";
            }

            if(file_put_contents($completePath, Router::boilerpulate($viewData['path']))){
                if(file_put_contents($storage, $content)){
                    return "View created";
                }else{
                    unlink($completePath);
                    return "Failed to create view";
                }
            }
        }catch (\Exception $e){
            return $e->getMessage();
        }

    }

    /**
     * @param $content
     * @return array|string|string[]|void
     */
    public static function clearUrl($content){

        if(!empty($content)){
            $content = str_replace("\\", "", $content);
            $content = str_replace('\/'," ",$content);
            $content = str_replace('/', '/', $content);
            return $content;
        }
    }


    public static function checkMultiDirectory(string $completeFilePath){
        if(!empty($completeFilePath)){

            $list = explode('/', $completeFilePath);
            $extra = count($list) - array_search('Views', $list);
            if($extra > 0){
                $subArray = array_slice($list, 0, count($list) - 1);
                $dir = implode('/',$subArray);
                mkdir($dir,7777,true);
            }
        }
    }

    /**
     * @param $view
     * @return string
     */
    public static function boilerpulate($view){

        $list = explode('.', $view);
        $extension = strtolower(end($list));
        switch ($extension){
            case 'html':
                return "<section>{$list[0]}</section>";
            case 'php':
                return "<?php @session_start(); ?>";
            default:
                return "add your code here .... valid for {$extension}";
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
            parse_str($query, $list);
            $queryList = $list ?? [];

            $path = $path[strlen($path) - 1] === '/' ? substr($path, 1, strlen($path) - 2) : substr($path, 1, strlen($path));
            if (SessionManager::getSession('site') === false) {
                $path = 'registration';
            }

            $storage = 'Core/Router/Register/registered_path_available.json';
            $foundView = [];
            if (file_exists($storage)) {
                $views = json_decode(file_get_contents($storage), true);
                $_SESSION['viewsstorage'] = $views;
                foreach ($views as $view) {
                    $line = "";
                    $pathList = explode('/', $path);
                    $rootList = explode('/',Globals::root());
                    if(in_array($pathList[0], $rootList)){
                        $path = str_replace($pathList[0],'/',$path);
                    }
                    $path = trim(str_replace('//',' ',$path));
                    if (strtolower($path) === strtolower($view['view_url'])) {
                        $foundView = $view;
                        break;
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
                if (!empty($foundView)) {

                    if ($restricstionLevel === true) {
                        self::viewAccessChecker($foundView);
                    } else {
                        self::requiringFile($foundView);
                    }
                } else {
                    if ($path === '/' || empty($path)) {
                        $foundView = self::findHomePage();
                        self::requiringFile($foundView);
                    } else {
                        $result = self::findParamsCleanUrl($path, $views);
                        if($result !== false){
                            $security = new Security();
                            self::accessAuthenticate($result, $security);
                        }else{
                            self::errorPages(404);
                        }
                    }
                }

            } else {
               self::errorPages(404);
                exit;
            }
        }else{
            $storage = 'Core/Router/Register/registered_path_available.json';
            $views = json_decode(file_get_contents($storage), true);
            $foundView=[];
            $_SESSION['viewsstorage'] = $views;
            foreach ($views as $view) {
                if (strtolower('installation') === strtolower($view['view_url'])) {
                    $foundView = $view;
                    break;
                }
            }

            if(!empty($foundView)){
                self::requiringFile($foundView);
            }
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
     * @return array|mixed
     */
    public static function findHomePage(){
          $setting = new SettingWeb();
          return $setting->getSettingConfig('home');

    }

    /**
     * @param $view
     * @param $url
     * @return false|string|void
     */
    public static function updateView($view, $url){
        $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $path = $path[strlen($path)-1] === '/' ? substr($path, 1, strlen($path) - 2) : substr($path, 1, strlen($path));
        $storage = 'Core/Router/Register/registered_path_available.json';
        $foundView = [];
        if(file_exists($storage)) {
            $views = json_decode(file_get_contents($storage), true);
            $remaining = [];
            $oldview = [];
            foreach ($views as $view) {
                if (strtolower($url) !== strtolower($view['view_url'])) {
                    array_push($remaining, $view);
                }else{
                  $oldview = $view;
                }
            }

            $list = explode('/', $oldview['view_path_absolute']);
            $completePath = "";
            $relativePath = "";
            if(end($list) !== $_POST['path-address']){
                $sub = array_slice($list, 0 , count($list) - 1);
                $line = implode('/',$sub).'/'.$_POST['path-address'];
                if(rename($oldview['view_path_absolute'], $line)){
                    $completePath = $line;

                    $newlist = explode('/', $oldview['view_path_relative']);
                    $sub = array_slice($newlist,0,count($newlist) - 1);
                    $line = implode('/', $newlist).'/'.$_POST['path-address'];
                    $relativePath = $line;
                }else{
                    return false;
                }
            }else{
                $completePath = $oldview['view_path_absolute'];
                $relativePath = $oldview['view_path_relative'];
            }


            $viewFormat =[
                "view_name"=>htmlspecialchars(strip_tags($_POST['view-name'])),
                "view_url"=>htmlspecialchars(strip_tags($_POST['view-url'])),
                "view_path_absolute"=>$completePath,
                "view_path_relative"=>$relativePath,
                "view_timestamp"=>time(),
                "view_description"=>$_POST['description'],
                "view_role_access"=>$_POST['accessible']
            ];
            array_push($remaining, $viewFormat);

            $content = json_encode($remaining);

            //clear up

            $content = Router::clearUrl($content);

                if(file_put_contents($storage, $content)){
                    return "View updated";
                }else {
                    unlink($completePath);
                    return "Failed to update view";
                }
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
     * @param $url
     * @param $views
     * @return array|false|mixed
     */
    public static function findParamsCleanUrl($url, $views){

        $data = [];
        $flag = false;
        $queryline = "";
        $foundView = [];
        foreach($views as $view){
            $list = explode('/', $view['view_url']);
            $urlList = explode('/', $url);

            $urlSize = count($urlList);
            if($urlSize === count($list)){
                $firstpart = $urlList[0];
                if(in_array($firstpart, $list)){
                    $list = array_slice($list, 1);
                    $prms = array_slice($urlList, 1);

                    $conter = 0;
                    foreach ($list as $li){
                        $element = str_replace('{',' ',$li);
                        $element = str_replace('}',' ', $element);
                        $element = trim($element);
                        $item = [$element=> $prms[$conter]];
                        $data = array_merge($data, $item);
                        $queryline .= $element.'='.$prms[$conter].'&';
                        $conter += 1;
                    }
                    $foundView = $view;
                    //$_SESSION['public_data'][]
                   $flag = true;
                }
            }

            if($flag === true){
                break;
            }
        }

        if($flag === true){
            $queryline = substr($queryline,0, strlen($queryline) - 1);
            $_SESSION['public_data']['query']=$queryline;
            $_SESSION['public_data']['params']=$data;
            $_SESSION['public_data']['view'] = $foundView;
            return $foundView;
        }else{
            return false;
        }
    }

    /**
     * @param $foundView
     * @param $securityClass
     * @return void
     */
    public static function accessAuthenticate($foundView, $securityClass){
        $user = $securityClass->checkCurrentUser();
        if ($user === "U-Admin") {
            $_SESSION['access']['role'] = 1;
            self::requiringFile($foundView);
        } elseif ($user === "U-BLOCK") {
            self::errorPages(403);
        } elseif ($user === "V-VERIFIED") {
            self::requiringFile($foundView);
        } elseif ($user === "V-NOT-VERIFIED"){
           self::errorPages(401);
        }
        else {
            self::errorPages(401);
        }
    }

    /**
     * @param $foundView
     * @return void
     */
    public static function viewAccessChecker($foundView){
        $security = new Security();
        $access = $security->checkViewAccess();
        if ($access === "V-NULL") {
           self::errorPages(500);
        } elseif ($access === "V-PRIVATE") {
            self::accessAuthenticate($foundView, $security);
        }elseif ($access === "V-MODERATOR"){
            self::accessAuthenticate($foundView, $security);
        }
        else {
            self::requiringFile($foundView);
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

    public static function navReader(){
        try{
            ApiHandlerClass::isApiCall();
            $security = new Security();
            $user= $security->checkCurrentUser();
            $base = $_SERVER['DOCUMENT_ROOT'];
            if($user === "U-Admin"){
                if(file_exists("{$base}/Views/DefaultViews/nav.php")){
                    require_once "{$base}/Views/DefaultViews/nav.php";
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

    public static function footerReader(){
        //Below handles footer section
        $security = new Security();
        $user= $security->checkCurrentUser();
        $base = $_SERVER['DOCUMENT_ROOT'];
        if($user === "U-ADMIN"){
            if(file_exists("{$base}/Views/DefaultViews/footer.php")){
                require_once "{$base}/Views/DefaultViews/footer.php";
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

    public static function removeView($url){
        if(!empty(Globals::user()) && Globals::user()[0]['role'] === 'Admin'){
            $storage = 'Core/Router/Register/registered_path_available.json';
            $views = json_decode(file_get_contents($storage),true);
            $remainingView = [];
            if(!empty($views)){
                foreach ($views as $view=>$value){
                    if($value['view_url'] !== $url){
                       $remainingView[] = $value;
                    }else{
                        if(file_exists($value['view_path_absolute'])){
                            unlink($value['view_path_absolute']);
                        }
                    }
                }
            }
            return file_put_contents($storage,Router::clearUrl(json_encode($remainingView)));
        }
        return false;
    }
}