<?php

namespace Core;

use Datainterface\Database;
use Datainterface\Insertion;
use Datainterface\mysql\TablesLayer;
use Datainterface\MysqlDynamicTables;
use Datainterface\SecurityChecker;
use Datainterface\Selection;
use ErrorLogger\ErrorLogger;
use GlobalsFunctions\Globals;
use RoutesManager\RoutesManager;
use Sessions\SessionManager;

class RouteConfiguration
{
   private array $views;

   private RoutesManager $manager;

   public function __construct($refresh = true){
       $this->manager = new RoutesManager();
       $this->views = $this->manager->loadRoutes()->getRoutes();
       $this->runStorage();
   }

   private function runStorage() {
       $maker = new MysqlDynamicTables();
      $schema = $this->manager->getSchema();
       $maker->resolver(
         Database::database(),
         $schema['col'],
         $schema['attr'],
         $schema['table'],
         false
       );
   }

   public function getAllViews(){
       return $this->views;
   }

   public function printViews(){
       echo "<pre>";
       print_r($this->views);
       echo "</pre>";
   }

   public static function metaTags($metaData, $page) : bool{
       $col = ['page_url', 'data', 'mid'];
       $attr =['page_url'=>['varchar(50)','not null'], 'data'=>['text','null'],'mid'=>['int(11)','auto_increment','primary key']];
       $maker = new MysqlDynamicTables();
       $maker->resolver(Database::database(),$col,$attr,'metatags',false);

       $content = json_encode($metaData);
       $data =['page_url'=>$page, 'data'=>$content];
       return Insertion::insertRow('metatags',$data);
   }

   public static function appendMetatags($page){
       try {
           if(!SecurityChecker::isConfigExist()){
               return "";
           }
           $layer = new TablesLayer();
           $tables = $layer->getTables()->tables();
           if(!in_array('metatags', $tables)){
               return "";
           }
           $data = Selection::selectById('metatags',['page_url'=>$page]);
           if(!empty($data)){
              $lineOfMetaTags = "";
              $contentArray = [];
              foreach ($data as $key=>$value){
                  if(gettype($value) === 'array'){
                      $contentArray[] = json_decode($value['data'], true);
                  }
              }
              foreach ($contentArray as $key=>$value){
                  if(gettype($value) === 'array'){
                      extract($value);
                      $line = "<meta name='{$name}' content='{$content}' />";
                      $lineOfMetaTags .= "\n{$line}";
                  }
              }
              return $lineOfMetaTags;
           }
       }catch (\Exception $e){
           ErrorLogger::log($e);
       }
   }

    public function addView($viewData){

        try {
            //check unique
            $urls = [];
            $files = [];
            foreach ($this->views as $key=>$value){
                if(gettype($value) === 'array'){
                    $urls[] = $value['view_url'];
                    $files[] = $value['view_path_absolute'];
                }
            }

            if(in_array($viewData['url'], $urls)){
                return "View must have unique url";
            }

            foreach ($urls as $key=>$value){
                if(preg_match("~\b{$value}\b~",$viewData['url'])){
                    return "View must have unique url (caught by regex match)";
                }
            }

            if(substr_count($viewData['url'], '/') >= 1){
                return "This url {$viewData['url']} not allowed only type url allowed are of single word (index, home-page etc..)
                 url like (index/page, page/apply/user etc..) are not allowed. Inshort url must only  be one word after hostname eg 
                 https://www.example.com/home";
            }

            $paths = $this->viewPath($viewData['path'], $viewData['default']);
            $fileToCreate = $paths['absolute'];

            if(in_array($this->correctPaths($fileToCreate), $files)){
                return "File already exist you cant override any file please provide new filename .(extension)";
            }

            $list = explode('/', $fileToCreate);
            $dir = implode('/',array_slice($list, 0, count($list) - 1));
            if(mkdir($dir)){
                chmod($dir, 0777);
            }

            //create file
            $contentWrite = $this->boilerpulate($fileToCreate);
            $handler = fopen($fileToCreate, 'w+');
            fwrite($handler, $contentWrite);
            fclose($handler);

            //save view

            $viewFormat =[
                "view_name"=>$viewData['name'],
                "view_url"=>$viewData['url'],
                "view_path_absolute"=>$this->correctPaths($fileToCreate),
                "view_path_relative"=>$this->correctPaths($paths['relative']),
                "view_timestamp"=>time(),
                "view_description"=>$viewData['description'],
                "view_role_access"=>$viewData['access']
            ];

            if(!$this->manager->saveRoute($viewFormat)->error){
                if(isset($viewData['default'])){
                    $this->manager->writeInTemps($viewFormat);
                }
                 return $this->manager->getMessage();
            }
            return $this->manager->getMessage();

        }catch (\Exception $e){
            return $e->getMessage();
        }

    }

    public function boilerpulate($view){

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
     * @param $content
     * @return array|string|string[]|void
     */
    public function correctPaths($content){

        if(!empty($content)){
            $content = str_replace("\\", "/", $content);
            $content = str_replace('\/'," ",$content);
            $content = str_replace('/', '/', $content);
            return $content;
        }
    }

    /**
     * @param $view
     * @param $url
     * @return false|string|void
     */
    public function updateView($view, $url){

        $paths = $this->viewPath($view['path-address'], isset($view['default']));
        $viewFound = $this->manager->loadViewByUrl($url)->getRoutes();

        $renamed = false;
        if(!empty($viewFound)){
             $viewFound = $viewFound[0];
            if($this->correctPaths($paths['absolute']) !== $viewFound['view_path_absolute'] && !isset($view['default'])){
               $content = file_get_contents($viewFound['view_path_absolute']);
               $handler = fopen($paths['absolute'], 'w+');
               fwrite($handler, $content);
               fclose($handler);
               unlink($viewFound['view_path_absolute']);
               $renamed = true;
            }

            if(!isset($view['default'])){
                $viewFormat =[
                    "view_name"=> $view['view-name'] ?? $viewFound['view_name'],
                    "view_url"=> $view['view-url'] ?? $viewFound['view_url'],
                    "view_path_absolute"=> $renamed ? $this->correctPaths($paths['absolute']) : $viewFound['view_path_absolute'],
                    "view_path_relative"=>$renamed ? $this->correctPaths($paths['relative']) : $viewFound['view_path_relative'],
                    "view_timestamp"=>time(),
                    "view_description"=> $view['description'] ?? $viewFound['view_description'],
                    "view_role_access"=> $view['accessible'] ?? $viewFound['view_role_access']
                ];

                if(!$this->manager->updateRoute($viewFormat, $viewFound['view_url'])->error){
                    return $this->manager->getMessage();
                }
                return $this->manager->getMessage();
            }
           return "Default view can not be changed";
        }

    }

    public function viewPath($filename, $default){
        $s = DIRECTORY_SEPARATOR;
        $fileToCreate = $default === true ?
            Globals::root().DIRECTORY_SEPARATOR."Core{$s}DefaultViews{$s}{$filename}" :
            Globals::root().DIRECTORY_SEPARATOR."Views{$s}{$filename}";
        $relative = $default === true ?
            "Core{$s}DefaultViews{$s}{$filename}" :
            "Views{$s}{$filename}";
        return ['absolute'=>$fileToCreate, 'relative'=>$relative];
    }

    /**
     * @param $url
     * @return false|int
     */
    public function removeView($url){
        if(!empty(Globals::user()) && Globals::user()[0]['role'] === 'Admin'){
            $view = $this->manager->loadViewByUrl($url)->getRoutes();
            if(!empty($view)){
                unlink($view[0]['view_path_absolute']);
                return $this->manager->deleteRoute($url)->getMessage();
            }
        }
        return "Failed to delete view";
    }


}