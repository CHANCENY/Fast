<?php

namespace Datainterface\mysql;

use Datainterface\Insertion;

class InsertionLayer
{
   private array $toSaveData;

    /**
     * @return array
     */
    public function getToSaveData(): array
    {
        return $this->toSaveData;
    }

    /**
     * @return string
     */
    public function getTable(): string
    {
        return $this->table;
    }

   private int $newRowId;

   private string $table;



   public function setData(array $data) : InsertionLayer{
       $this->toSaveData = $data;
       return $this;
   }

   public function setTableName(string $table) : InsertionLayer{
       $this->table = $table;
       return $this;
   }

   public function insert() : InsertionLayer{
       $this->newRowId = Insertion::insertRow($this->table, $this->toSaveData);
       return $this;
   }

   public function id(){
       return $this->newRowId;
   }
}