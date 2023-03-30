<?php

namespace Json;

use Core\Router;
use GlobalsFunctions\Globals;


class CrudOperation
{
    private $inBoundData = array();
    private $outBoundData = array();

    private $storageName;

    private $storageData = array();

    /**
     * @return array
     */
    public function getInBoundData(): array
    {
        return $this->inBoundData;
    }

    /**
     * @param array $inBoundData
     */
    public function setInBoundData(array $inBoundData)
    {
        $this->inBoundData = $inBoundData;
        return $this;

    }

    /**
     * @return array
     */
    public function getOutBoundData(): array
    {
        return $this->outBoundData;
    }

    /**
     * @param array $outBoundData
     */
    public function setOutBoundData(array $outBoundData)
    {
        $this->outBoundData = $outBoundData;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getStorageData()
    {
        return $this->storageData;
    }

    /**
     * @param mixed $storageData
     */
    public function setStorageData($storageData)
    {
        $this->storageData = $storageData;
        return $this;
    }

    /**
     * @return string
     */
    public function getStoragePath(): string
    {
        return $this->storagePath;
    }

    /**
     * @param string $storagePath
     */
    public function setStoragePath(string $storagePath)
    {
        $this->storagePath = $storagePath;
        return $this;
    }

    private $storagePath = "";

    public function __construct(){
        $base = Globals::root();
        $this->storagePath = "{$base}/Json-store/store.json";
        $content = Router::clearUrl(file_get_contents($this->storagePath));
        $this->storageData = json_decode($content);
    }

    public function setLocationName($storageName){
        $this->storageName = $storageName;
        return $this;
    }

    public function select($key, $value){
        $store = JsonOperation::getByName($this->storageName);
        if(empty($store)){
            return "Storage not found";
        }
        $actualData = $store[$this->storageName];
        foreach ($actualData as $actual=>$valueArray){
            if($valueArray[$key] === $value){
               $this->outBoundData[] = $valueArray;
            }
        }
     return $this;
    }

    public function selectAll(){
        $this->outBoundData = JsonOperation::getByName($this->storageName);
        if(empty($this->outBoundData)){
            $this->outBoundData = JsonOperation::getByUniqueName($this->storageName);
        }
        return $this;
    }

    public static function selectAllData($storageName){
       $data = JsonOperation::getByName($storageName);
       if(empty($data)){
           $data = JsonOperation::getByUniqueName($storageName);
       }
       return $data;
    }

    public function save(){
        $store = Json::getStorageByName($this->storageName);
        $actual = JsonOperation::getByName($this->storageName);
        if(empty($store)){
            $store = Json::getStorageByUniqueName($this->storageName);
            $actual = JsonOperation::getByUniqueName($this->storageName);
        }
        $path = "";
        if(!empty($store)){
            $path = $store[0]['path'].'/'.$store[0]['uuid'].'.json';
            $actual = $actual[$this->storageName];
        }
        $toSave = $this->getInBoundData();
        $toSave['default_key'] = Json::uuid();
        $actual[] = $toSave;
        if(file_exists($path)){
            return $this->accessFile($path, $actual);
        }
    }

    private function accessFile($path, $actual){
        $content = Router::clearUrl(json_encode($actual));
        file_put_contents($path, $content);
        $this->storageData = $actual;
        return $this;
    }

    public function update($key, $value){
        $actual = JsonOperation::getByName($this->storageName);
        $store = Json::getStorageByName($this->storageName);
        $path = "";
        if(empty($actual)){
            $store = Json::getStorageByUniqueName($this->storageName);
            $actual = JsonOperation::getByUniqueName($this->storageName);
        }
        if(!empty($actual)){
            $path = $store[0]['path'].'/'.$store[0]['uuid'].'.json';
            $actual = $actual[$this->storageName];
        }
        foreach ($actual as $data=>$values){
            if($values[$key] == $value){
                $this->inBoundData['default_key'] = isset($values['default_key']) ? $values['default_key'] : Json::uuid();
                $actual[$data] = $this->inBoundData;
            }
        }
        if(file_exists($path)){
            return $this->accessFile($path, $actual);
        }
    }

    public function delete($value){
        $store = Json::getStorageByName($this->storageName);
        $actual = JsonOperation::getByName($this->storageName);
        if(empty($actual)){
            $store = Json::getStorageByUniqueName($this->storageName);
            $actual = JsonOperation::getByUniqueName($this->storageName);
        }
        $path = "";
        if(!empty($actual)){
            $path = $store[0]['path'].'/'.$store[0]['uuid'].'.json';
            $actual = $actual[$this->storageName];
        }
        $temp = [];
        foreach ($actual as $key=>$values){
            if(isset($values['default_key']) && $values['default_key'] == $value){
                continue;
            }else{
                $temp[] = $values;
            }
        }
        if(file_exists($path)){
            return $this->accessFile($path, $temp);
        }
    }
}