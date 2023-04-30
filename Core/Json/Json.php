<?php

namespace Json;

use Core\Router;
use ErrorLogger\ErrorLogger;
use GlobalsFunctions\Globals;
use Ramsey\Uuid\Uuid;

/**
 *
 */
class Json
{
    private string $type;

    /**
     * @param string $type
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }
    /**
     * @var string
     */
    private string $storeName;
    /**
     * @var true
     */
    private bool $error;
    /**
     * @var string
     */
    private string $message;
    /**
     * @var mixed
     */
    private mixed $dataInStorage;

    /**
     * @return string
     */
    public function getStoreName(): string
    {
        return $this->storeName;
    }

    /**
     * @param string $storeName
     */
    public function setStoreName(string $storeName): void
    {
        $file =Globals::root()."/config/storage/{$storeName}";
        if(!file_exists($file)){
            $list = explode('/', $file);
            mkdir(implode('/',array_slice($list, 0, count($list) - 1)), 0777, true);
            $handler = fopen($file,'x+');
            fclose($handler);
            if(chmod($file, 0777)){
               file_put_contents($file, '[]');
           }
        }
        $this->storeName = $file;
    }

    /**
     *
     */
    public function __construct()
  {
      $this->storeName = "";
      $this->type = 'Assoc';
  }

    /**
     * @param array $data
     * @return $this
     */
    public function save(array $data): Json{

      try {
          $storageContent = $this->getDataInStorage();
          $data['defaultKey'] = self::uuid();
          $storageContent[] = $data;
          $content = json_encode($storageContent);
          $result = file_put_contents($this->storeName, Router::clearUrl($content));
          if($result){
              $this->error = false;
              $this->message = "Saved data with key: {$data['defaultKey']}";
              $this->dataInStorage = $storageContent;
          }else{
              $this->error = true;
              $this->message = "Failed to save data";
          }
          return $this;
      }catch (\Throwable $e){
          $this->error = true;
          $this->message = $e->getMessage();
          ErrorLogger::log($e);
          return $this;
      }

  }

    /**
     * @return mixed
     */
    public function getDataInStorage(): mixed
    {
        if(empty($this->storeName)){
            $this->error = true;
            $this->message = "Storage path not specified";
            return $this;
        }
        $type = true;
        if($this->type === 'Std'){
            $type = false;
        }
        $this->dataInStorage = json_decode(file_get_contents($this->storeName), $type);
        return $this->dataInStorage;
    }

    /**
     * @return bool
     */
    public function isError(): bool
    {
        return $this->error;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @return string
     */
    public static function uuid(){
        $uuid1 = Uuid::uuid1();
        return $uuid1->toString();
    }

    /**
     * @param array $data
     * @param string $defaultKey
     * @return $this
     */
    public function upDate(array $data, string $defaultKey): Json{
        try {
            $contentStorage = $this->getDataInStorage();
            $flag = false;
            $keys = 0;
            $oldData = [];
            foreach ($contentStorage as$key=>$value){
                if(gettype($value) === 'array'){
                    if($value['defaultKey'] === $defaultKey){
                        $keys = $key;
                        $oldData = $value;
                        $flag = true;
                       break;
                    }
                }
            }

            if($flag){
                $arrayKeys = array_keys($oldData);
                foreach ($arrayKeys as $key=>$value){
                    $contentStorage[$keys][$value] = $data[$value] ?? $oldData[$value];
                }

                $result = file_put_contents($this->storeName, Router::clearUrl(json_encode($contentStorage)));
                if($result){
                    $this->error = false;
                    $this->message = "Updated data at: {$defaultKey}";
                    $this->dataInStorage = $contentStorage;
                }
            }else{
                $this->error = true;
                $this->message = "Failed to Updated data at: {$defaultKey}";
            }
            return $this;
        }catch (\Throwable $e){
            $this->error = true;
            $this->message = $e->getMessage();
            ErrorLogger::log($e);
            return $this;
        }
    }


    /**
     * @param $defaultKey
     * @return $this
     */
    public function delete($defaultKey):Json{
        try{
            $contentData = $this->getDataInStorage();
            $temp = [];
            $flag = false;
            foreach ($contentData as $key=>$value){
                if(gettype($value) === 'array'){
                    if($value['defaultKey'] !== $defaultKey){
                        $temp[] = $value;
                        $flag = true;
                    }
                }
            }
            if($flag){
                $result = file_put_contents($this->storeName, Router::clearUrl(json_encode($temp)));
                if($result){
                    $this->error = false;
                    $this->message = "Deleted data at: {$defaultKey}";
                    $this->dataInStorage = $temp;
                }else{
                    $this->error = true;
                    $this->message = "Failed to Delete data at: {$defaultKey}";
                }
                return $this;
            }
            throw new \Exception("Deleting failed no match found of key {$defaultKey}");
        }catch (\Throwable $e){
            $this->error = true;
            $this->message = $e->getMessage();
            ErrorLogger::log($e);
            return $this;
        }
   }
}