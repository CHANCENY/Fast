<?php

namespace Datainterface;

class Updating
{
    public static function update($tableName, $data = [], $keyValue = []): bool{
        $helper = new CrudHelper();
        $helper->putData($data);
        $helper->setTableName($tableName);
        return $helper->updates($keyValue);

    }

}