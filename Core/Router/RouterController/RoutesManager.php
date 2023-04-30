<?php

namespace RoutesManager;

use Core\Router;
use Datainterface\Database;
use Datainterface\Delete;
use Datainterface\Insertion;
use Datainterface\MysqlDynamicTables;
use Datainterface\SecurityChecker;
use Datainterface\Selection;
use Datainterface\Updating;
use GlobalsFunctions\Globals;

class RoutesManager
{

    private array $schema;

    private string $message;

    /**
     * @return array
     */


    /**
     * @return array
     */
    public function getSchema(): array
    {
        return $this->schema;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @return bool
     */
    public function isError(): bool
    {
        return $this->error;
    }

    /**
     * @return array
     */
    public function getRoutes(): array
    {
        return $this->routes;
    }

    public bool $error;

    private array $routes;

    public function __construct(){
        $col = ['rvid', 'view_name', 'view_url', 'view_description', 'view_timestamp', 'view_role_access', 'view_path_absolute','view_path_relative'];
        $attr =[
            'rvid'=>['int(11)', 'auto_increment', 'primary key'],
            'view_name'=>['varchar(100)', 'not null'],
            'view_url'=>['varchar(250)','not null'],
            'view_description'=>['text'],
            'view_timestamp'=>['varchar(50)'],
            'view_role_access'=>['varchar(50)'],
            'view_path_absolute'=>['varchar(250)', 'not null'],
            'view_path_relative'=>['varchar(250)', 'not null']
        ];
        $this->schema = [
            'col'=>$col,
            'attr'=>$attr,
            'table'=>'routes'
        ];

        if(SecurityChecker::isConfigExist()){
            $maker = new MysqlDynamicTables();
            $maker->resolver(Database::database(),
                             $col,
                             $attr,
                             $this->schema['table'],
                             false);
        }
    }

    public function saveRoute(array $routeViewData) : RoutesManager{
        if(!empty($routeViewData)){
            $arrayKeys = array_keys($routeViewData);
            for($i = 1; $i < count($this->schema['col']); $i++){
                if(in_array($this->schema['col'][$i], $arrayKeys) === false){
                    $this->message = "Field {$this->schema['col'][$i]} not found in data passed";
                    $this->error = true;
                    return $this;
                }
            }

            if(!empty(Selection::selectById($this->schema['table'], ['view_url'=>$routeViewData['view_url']]))){
                $this->error = true;
                $this->message = "Url already exist";
                return $this;
            }
            //insert into db
            if(Insertion::insertRow($this->schema['table'], $routeViewData)){
                $this->message = "View created and save successfully";
                $this->error = false;
                return $this;
            }else{
                $this->message = "View failed to save";
                $this->error = true;
            }
        }
        return $this;
    }

    public function updateRoute(array $routeViewData, string $url) : RoutesManager{
        if(!empty($routeViewData) && !empty($url)){
            //update route
            if(Updating::update($this->schema['table'],$routeViewData,['view_url'=>$url])){
                $this->error = false;
                $this->message = "View updated successfully";
                return $this;
            }
        }
        return $this;
    }

    public function deleteRoute(string $url) : RoutesManager{
        if(!empty($url)){
            if(Delete::delete($this->schema['table'], ['view_url'=>$url])){
                $this->error = false;
                $this->message = "View deleted successfully";
            }
        }
        return $this;
    }

    public function loadRoutes() : RoutesManager{
        $this->routes = Selection::selectAll($this->schema['table']);
        return $this;
    }

    public function loadViewByUrl($url) : RoutesManager{
        $this->routes = Selection::selectById($this->schema['table'], ['view_url'=>$url]);
        return $this;
    }

    public function tempReaderView(){
        $base = Globals::root()."/Core/Router/Register/registered_path_available.json";
        if(file_exists($base)){
            return json_decode(file_get_contents($base), true);
        }
        return [];
    }

    public function installerViewDefaults(){
        $flag = false;
        $temp = [];
        if(SecurityChecker::isConfigExist()){
          $list = $this->tempReaderView();
          foreach ($list as $key=>$value){
              $filename = "";
              if(!empty( $value['view_path_absolute'])){
                  $list = explode('/', $value['view_path_absolute']);
                  $filename = end($list);
              }
              $value['view_path_absolute'] = "Core/DefaultViews/{$filename}";
              $value['view_path_relative'] = "Core/DefaultViews/{$filename}";
              $this->saveRoute($value);
              $temp[] = $value;
              $flag = true;
          }
        }

        if($flag === false){
            $list = $this->tempReaderView();
            foreach ($list as $key=>$value){
                $filename = "";
                if(!empty( $value['view_path_absolute'])){
                    $list = explode('/', $value['view_path_absolute']);
                    $filename = end($list);
                }
                $value['view_path_absolute'] = "Core/DefaultViews/{$filename}";
                $value['view_path_relative'] = "Core/DefaultViews/{$filename}";
                $temp[] = $value;
            }
            $_SESSION['viewsstorage'] = $temp;
        }
    }

    public function writeInTemps($data){
        $base = $_SERVER['DOCUMENT_ROOT'].'/Core/Router/Register/registered_path_available.json';
        if(!chmod($base, 0777)){
            return false;
        }
        $content = json_decode(Router::clearUrl(file_get_contents($base)),true);
        $content[] = $data;
        return file_put_contents($base, json_encode($content));
    }


}