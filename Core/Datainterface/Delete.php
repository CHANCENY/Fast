<?php

namespace Datainterface;

class Delete
{
    public static function delete($tableName, $keyValue = []): bool{
        if(SecurityChecker::isConfigExist()){
            $helper = new CrudHelper();
            $helper->setTableName($tableName);
            return $helper->delete($keyValue);
        }
        return false;
    }

}