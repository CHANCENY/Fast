<?php

namespace ContentType;


use Datainterface\mysql\QueryLayer;
use Datainterface\mysql\SelectionLayer;

class Content
{
  private array $contentTypeTables;
  private string $storage;
    private int $limit;

    /**
     * @return array
     */
    public function getContentTypeTables(): array
    {
        return $this->contentTypeTables;
    }

    /**
     * @return array
     */
    public function getContentTypeTableData(): array
    {
        return $this->contentTypeTableData;
    }
  private array $contentTypeTableData;
  private array $orderTables;

    /**
     * @param array $orderTables
     */
    public function setOrderTables(array $orderTables): Content
    {
        $this->orderTables = $orderTables;
        return $this;
    }

  public function __construct()
  {
      $this->contentTypeTableData = [];
      $this->contentTypeTables = [];
      $this->orderTables = [];
      $this->limit = -1;
      $this->storage = "content_type_form_storage";
  }

  public function tablesFinder(): Content
  {
      $inStore = (new SelectionLayer())->setTableName($this->storage)->selectAll()->rows();
      $tables = [];
      $ids = [];
      foreach ($inStore as $key=>$value){
          $tables[$value['coid']] = $value['content_type'];
          $ids[] = $value['coid'];
      }

      $max = max($ids);
      for ($i = 0; $i <= $max; $i++){
          if(in_array($i, $ids)){
              $this->contentTypeTables[] = $tables[$i];
              $this->orderTables[] = $i;
          }
      }
      return $this;
  }

  public function query(): Content
  {
        foreach ($this->contentTypeTables as $key=>$table){
            if($this->limit != -1){
                $query = "SELECT * FROM {$this->storage} LIMIT {$this->limit}";
                $this->contentTypeTableData[$table] = (new QueryLayer())->setQuery($query)->run()->outPut();
            }else{
                $this->contentTypeTableData[$table] = (new SelectionLayer())->setTableName($table)->selectAll()->rows();
            }
        }
        return $this;
  }

  public function sortReverse(): Content
  {
      $this->contentTypeTables = array_reverse($this->getContentTypeTables());
      return $this;
  }

  public function find($contentTypeKey): Content
  {
        $index = array_search($contentTypeKey, $this->contentTypeTables);
        if(gettype($index) === 'integer') unset($this->contentTypeTables[$index]);
        $this->contentTypeTables[] = $contentTypeKey; $this->contentTypeTables = array_reverse($this->contentTypeTables);
        return $this;
  }

  public function limit(int $limit):Content
  {
      $this->limit = $limit;
      return $this;
  }







}