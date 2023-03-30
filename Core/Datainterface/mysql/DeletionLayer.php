<?php

namespace Datainterface\mysql;

use Datainterface\Delete;

class DeletionLayer
{
  private array $deleteBy;

  public bool $result;

  public function setKeys(array $keyValue) : DeletionLayer{
      $this->deleteBy = $keyValue;
      return $this;
  }

  public function setTable(string $table) : DeletionLayer{
      $this->table = $table;
      return $this;
  }

  public function delete() : DeletionLayer{
      $this->result = Delete::delete($this->table,$this->deleteBy);
      return $this;
  }

  public function result() : bool{
      return $this->result;
  }
}