<?php

namespace Alias;
use Datainterface\Selection;
use GlobalsFunctions\Globals;
use RoutesManager\RoutesManager;

class Alias
{

    private string $endPoint;
    private array $routeCreate;

    /**
     * @param string $endPoint
     */
    public function setEndPoint(string $endPoint): Alias
    {
        $this->endPoint = $endPoint;
        return $this;
    }

    public function auto(): Alias
    {
        $url = Globals::uri();
        $this->endPoint = random_int(0, 100000000);
        $this->create();
        $this->save();
        return $this;
    }
    private function create():Alias
    {
        $data =[
            'view_name'=> ucfirst(str_replace('-',' ', $this->endPoint)),
            'view_url'=> str_replace(' ','-', $this->endPoint),
            'view_path_absolute'=>'Views/alias/'.str_replace(' ','.',$this->endPoint),
            'view_path_relative'=> 'Views/alias/'.str_replace(' ','.',$this->endPoint),
            'view_role_access'=> 'public',
            'view_description'=>'Created by Alias Class',
            'view_timestamp'=>time()
        ];
        $data['view_path_absolute'] = $data['view_path_absolute'].'.php';
        $data['view_path_relative'] = $data['view_path_relative'].'.php';
        if(!is_dir('Views/alias/')){
            mkdir('Views/alias/', 0777, true);
            chmod('Views/alias/', 0777);
        }
        file_put_contents($data['view_path_absolute'],"<?php");
        $this->routeCreate = $data;
        return $this;
    }

    public function alias(): string
    {
        if(empty($this->routeCreate)){
            return '';
        }
        return $this->routeCreate['view_url'];
    }

    public function save() : Alias
    {
        (new RoutesManager())->saveRoute($this->routeCreate);
        return $this;
    }

    public function generate(){
        if(empty($this->endPoint)){
            throw new \Exception('Endpoint not specified');
        }
        $this->create();
        if(Selection::selectById('routes',['view_url'=>$this->routeCreate['view_url']])){
            return $this;
        }
        $this->save();
        return $this;
    }

}