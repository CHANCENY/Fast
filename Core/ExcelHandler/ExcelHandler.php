<?php

namespace ExcelHandler;

use Datainterface\Database;
use Datainterface\Insertion;
use Datainterface\MysqlDynamicTables;
use Shuchkin\SimpleXLSX;

class ExcelHandler
{

   public static function getRows($excel_path){
        if(file_exists($excel_path)){
            if ( $xlsx = SimpleXLSX::parse($excel_path) ) {
                return $xlsx->rows();
            } else {
                return SimpleXLSX::parseError();
            }
        }
   }

   public static function getSheets($excelpath){
       if ( $xlsx = SimpleXLSX::parse( $excelpath ) ) {
           return $xlsx->sheetNames();
       }
   }

   public static function getActiveSheet($excelpath){
       if ( $xlsx = SimpleXLSX::parse( $excelpath ) ) {
           return $xlsx->sheetName( $xlsx->activeSheet );
       }
   }

   public static function makeDbTable($excelPath){

        if(empty($excelPath) && empty($tableName)){
            return NULL;
        }

        $dataRows = self::getRows($excelPath);

        $firstRow = $dataRows[0];
        if(empty($firstRow)){
            return NULL;
        }
       $returns = self::makeTableFromExcel($excelPath);
        if(!empty($returns)){
            $data = array_slice($dataRows, 1);
            $tablename = $returns['table'];
            $columns =array_slice($returns['columns'],1);
            $newData = [];
            for ($i = 0; $i < count($data); $i++){
                $item = array_combine($columns, $data[$i]);
                $newData[]=$item;
            }
            $ids = [];
            foreach ($newData as $row=>$rowValues) {
                $id = Insertion::insertRow($tablename,$rowValues);
                $ids[] = $id;
            }
            if(!empty($ids)){
                return $ids;
            }
            return [];
        }
   }

   public static function makeTableFromExcel($excelpath){
        if(empty($excelpath)){
            return NULL;
        }

        $list = explode('/',$excelpath);
        $filename = end($list);
        $flist = explode('.',$filename);
        $filename = strtolower($flist[0]);
        $filename = str_replace(' ','_', $filename);

        $dataExcel = self::getRows($excelpath);
        $columns = array_values($dataExcel[0]);
        $newColumns = [];
        $newColumns[] = $filename.'_id';
        foreach ($columns as $col=>$value){
            $newColumns[] = strtolower(str_replace(' ','_', $value));
        }
        $attributes = [];
        $attributes[$filename.'_id'] = ['INT(11)','AUTO_INCREMENT','PRIMARY KEY'];
        foreach ($newColumns as $col=>$value){
            if($value !== $filename.'_id'){
                $attributes[$value] = ['VARCHAR(250)','NULL'];
            }
        }

        $maker = new MysqlDynamicTables();
        if($maker->resolver(Database::database(),$newColumns,$attributes,$filename,false)){
            return ['table'=>$filename,'columns'=>$newColumns];
        }else{
            return FALSE;
        }
   }


}