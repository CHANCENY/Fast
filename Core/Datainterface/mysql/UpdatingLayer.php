<?php

namespace Datainterface\mysql;

use Datainterface\Updating;

class UpdatingLayer extends InsertionLayer
{
    private bool $result;

    public function keys(array $keyValue) : UpdatingLayer{
        $this->key = $keyValue;
        return $this;
    }

    public function result() : bool{
        return $this->result;
    }
   public function update() : UpdatingLayer {
       $this->result = Updating::update($this->getTable(),$this->getToSaveData(),$this->key);
       return $this;
   }
}