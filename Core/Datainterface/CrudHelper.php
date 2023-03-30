<?php

namespace Datainterface;

class CrudHelper
{

    /**
     * connection variable
     */
    private $connection;

    /**
     * action variable
     */
    private $action;

    /**
     * fields columns
     */
    private $columns;

    /**
     * data variable
     */
    private $data;

    /**
     * table name
     */
    private $table;

    /**
     * constructor to initialize connection
     */
    public function __construct()
    {
        $this->connection = Database::database();
    }

    public function setConnection($con){
        $this->connection = $con;
    }
    /**
     * columns setter
     */
    public function setColumns($cols = []){
        $this->columns = $cols;
    }

    /**
     * action setter
     */
    public function setAction($action = ""){
        $this->action = $action;
    }

    /**
     * data setter
     */
    public function setData($data = []){
        $this->data = $data;
    }

    /**
     * table name setter
     */
    public function setTableName($table){
        $this->table = $table;
    }

    /**
     * splitter
     */
    public function putData($data = []){
        $this->setColumns(array_keys($data));
        $this->setData(array_values($data));
    }

    /**
     * actioner
     */
    public function decider(){

        switch ($this->action) {
            case 'value':
                # code...
                break;

            default:
                # code...
                break;
        }
    }

    /**
     * insertion
     */
    public function insertion(){

        $TABLE = $this->table;
        $queryLine = "";
        foreach($this->columns as $column){
            $queryLine .= $column." = :".$column." , ";
        }

        $queryLine = substr($queryLine, 0, strlen($queryLine) - 2);
        $queryLine = trim($queryLine);

        $queryLine = "INSERT INTO ".$TABLE." SET ".$queryLine;
        $stmt = $this->connection->prepare($queryLine);

        for($i = 0; $i < count($this->columns); $i++){
            $stmt->bindParam(":".$this->columns[$i], $this->data[$i]);
        }

        if($stmt->execute()){
            return $this->connection->lastInsertId();
        }
        return NULL;
    }

    /**
     * update
     */
    public function updates($colKey= [], $logic = ""){

        $TABLE = $this->table;
        $logic = empty($logic) ? "" : $logic;

        if(empty($this->columns)){
            return NULL;
        }

        if(empty($colKey)){
            return NULL;
        }


        $condition = "";
        $logic = empty($logic) ? "AND" : $logic;
        $col = array_keys($colKey);
        for($i = 0; $i < count($col); $i++){
            $condition .= $col[$i]. " = :".$col[$i]." ".$logic." ";
        }

        $condition = trim($condition);
        $condition = trim(substr($condition, 0, strlen($condition) - 3));


        $dataline = "";
        for($i = 0; $i < count($this->columns); $i++){
            $dataline .= $this->columns[$i]. " = :".$this->columns[$i]." , ";
        }
        $dataline = trim(substr($dataline, 0, strlen($dataline) - 2));
        $sqlline = "UPDATE ".$TABLE." SET ".$dataline." WHERE ".$condition;
        $sqlline = trim($sqlline);

        $stmt = $this->connection->prepare($sqlline);
        for($i = 0; $i < count($this->columns); $i++){

            $stmt->bindParam(":".$this->columns[$i]."", $this->data[$i]);
        }

        for($i = 0; $i < count($col); $i++){

            $stmt->bindParam(":".$col[$i]."", $colKey[$col[$i]]);
        }

        if($stmt->execute()){
            return TRUE;
        }

        return FALSE;
    }

    /**
     * delete
     */
    public function delete($colKey = []){

        $condition = "";
        $col = array_keys($colKey);
        for($i = 0; $i < count($col); $i++){
            $condition .= $col[$i]. " = :".$col[$i]." ";
        }

        if(empty($condition)){
            return NULL;
        }

        $condition = trim($condition);

        $sql = "DELETE FROM ".$this->table." WHERE ".$condition;
        $stmt = $this->connection->prepare($sql);

        for($i = 0; $i < count($col); $i++){

            $stmt->bindParam(":".$col[$i]."", $colKey[$col[$i]]);
        }

        if($stmt->execute()){
            return TRUE;
        }
        return FaLSE;

    }

    /**
     * select functions below
     */
    public function selectAll(){

        $sql = "SELECT * FROM ".$this->table;
        $stmt = $this->connection->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return $result;
    }

    /**
     * select by id
     */
    public function selectById($colKey = []){

        $condition = "";
        $col = array_keys($colKey);
        for($i = 0; $i < count($col); $i++){
            $condition .= $col[$i]. " = :".$col[$i]." ";
        }

        if(empty($condition)){
            return NULL;
        }

        $condition = trim($condition);

        $sql = "SELECT * FROM ".$this->table." WHERE ".$condition;
        $stmt = $this->connection->prepare($sql);

        for($i = 0; $i < count($col); $i++){

            $stmt->bindParam(":".$col[$i]."", $colKey[$col[$i]]);
        }

        if($stmt->execute()){
            $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            return $result;
        }
        return FaLSE;
    }

    /**
     * addition method
     */
    public function xmlMaker($data, $table){

        if(empty($data)){
            return NULL;
        }

        $xml = "<".$table.">";
        foreach($data as $collect){

            $row = "<row>";
            foreach($collect as $ink=>$value){
                $column = "<".$ink.">".$value."</".$ink.">";
                $row .= $column;
            }
            $row .= "</row>";
            $xml .= $row;
        }

        $xml .= "</".$table.">";
        return $xml;
    }

}