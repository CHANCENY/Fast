<?php

namespace Datainterface;

use Modules\MakerModular;

class HelperClass extends Database
{

    public static function checkColumnsMatch($tableName = [], $data = []) : bool{
        $cols = Tables::tableSchemaInfo($tableName);

       // $moduleDefinitons = MakerModular::$tableSchemas[$tableName];
        return true;

        $flag = 0;
       print_r($cols);
        foreach ($cols as $key=>$v){

            foreach ($v as $inner){
               echo $inner.'<br>';
            }
            if(in_array($v, $moduleDefinitons)){
                $flag++;
            }
        }

        if(count($moduleDefinitons) !== $flag){
            return false;
        }
        print_r($moduleDefinitons);
        //check key in data
        $keys = array_keys($data);
        foreach ($keys as $key=>$value){
            if(in_array($value, $moduleDefinitons) === false){
                return false;
            }
        }
        return true;
    }

    public static function lineSetQuery($data = []) : string {
        $line = "";
        foreach ($data as $key=>$value){
            $line .= $key.' = :'.$key.' , ';
        }
        $line = substr($line, 0 , strlen($line) - 2);
        return trim($line);
    }

    public static function binding($sqlLine ,  $con, $data= []){
        if(empty($data)){
            return $sqlLine;
        }

        $stmt = $con->prepare($sqlLine);
        $col = array_keys($data);
        $values = array_values($data);

        for($i = 0; $i < count($col); $i++){
            $stmt->bindParam(':'.$col[$i], $values[$i]);
        }
        return $stmt;
    }

    public static function runQuery($statement){
        return $statement->execute();
    }

}