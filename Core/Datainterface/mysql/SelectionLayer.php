<?php

namespace Datainterface\mysql;

use Datainterface\Selection;

class SelectionLayer
{
  private array $dataFromDatabase;

  private array $keysValuePair;

  private bool $sessioned;
    private string $table;

    public function setSession(string $key = "")  : SelectionLayer{
      if(!empty($key)){
          $this->sessioned = $key;
      }
      return $this;
  }

  public function setTableName(string $table) : SelectionLayer{
      $this->table = $table;
      return $this;
  }

  public  function setKeyValue(array $data) : SelectionLayer{
      $this->keysValuePair = $data;
      return $this;
  }

  public function selectAll() : SelectionLayer{
      $this->dataFromDatabase = Selection::selectAll($this->table);
      return $this;
  }

  public function selectBy() : SelectionLayer{
      $this->dataFromDatabase = Selection::selectById($this->table,$this->keysValuePair);
      return $this;
  }

  public function rows()  : array{
      return $this->dataFromDatabase;
  }
}