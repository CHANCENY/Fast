<?php

namespace Datainterface;

class MysqlDynamicTables
{

    /**
     * columnNames holds names of columns to be made in tablr
     */
    private $columnNames = [];

    /**
     * tableName holds table name to be made
     */
    private $tableName = "";

    /**
     * attributes holds columns attributes specifications
     */
    private $attributes = [];

    /**
     * useDefaultPrimary holds true or false if table has to be made with
     * default column name rowid as primary and AI
     */
    private $useDefauiltPrimary = TRUE;

    /**
     * connection holds the database connection can be pdo or mysql
     * prefeble is pdo
     */
    private $connection;


    /**
     * constructor randomly give name of table to tableName attribute
     * in case table name wont be manually provide by programmer
     * this name can be override by manually setting table name
     * by using setTableName method or using resolver with takes table name
     * as one of its arguments
     */
    public function __construct(){

        $this->setTableName("table_".strval(random_int(0, 1000)));
    }

    /**
     * Set columns
     */
    public function setColumnNames($cols = []){
        $this->columnNames = $cols;
    }

    /**
     * Set table name
     */
    public function setTableName($tableName = ""){
        $this->tableName = empty($tableName) ? uniqid() : $tableName;
    }

    /**
     * Set columns attributes
     */
    public function setAttributes($attributes = []){
        $this->attributes = $attributes;
    }

    /**
     * set to override default primary column
     */
    public function setPrimaryColumn($default = true){
        $this->useDefauiltPrimary = $default;
    }

    /**
     * set PDO connection or mysqli connection
     * prefeble pdo
     */
    public function setConnection($con){
        $this->connection = $con;
    }

    /**
     * checker for empty values
     */
    private function runChecker(){

        if(count($this->columnNames) !== count($this->attributes)){
            return false;
        }
        return true;
    }

    /**
     * Making columns attribute and sql query line
     */
    private function creation(){

        $columnString = $this->useDefauiltPrimary === true ? "(rowid INT(10) UNSIGNED AUTO_INCREMENT PRIMARY KEY, "  : "(";
        $counter = 0;
        foreach($this->columnNames as $col){
            $columnString .= $col." ".$this->lineAttributes($col).", ";
        }
        $columnString .= 'created TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP)';

        $completeSql = "CREATE TABLE IF NOT EXISTS ".$this->tableName." ".$columnString;
        return $completeSql;

    }

    /**
     * Making attribute line to append on column name
     */
    private function lineAttributes($col){

        $attributeLine = "";
        $thisColumn = $this->attributes[$col];

        foreach($thisColumn as $attr){
            $attributeLine .= $attr." ";
        }
        return $attributeLine;
    }

    /**
     * Making table in databses
     */
    private function runSql($sql){
        //Code to run sql and make table in db
        $stmtss = $this->connection->prepare($sql);
        if($stmtss->execute()){
            return true;
        }
            return false;

    }

    /**
     * Creation of table lapper this to be called after all required
     * columns and attribute have been set
     * this function will run runchecker, creation, runSql method to
     * give out table in db and will return true if table created or false
     * if not
     */
    public function create(){

        if($this->runChecker()){
            $sql = $this->creation();

            $result = $this->runSql($sql);
            if($result === "EXIST"){
                return false;
            }

            if($this->runSql($sql)){
                //send feedback of successfully
                return true;
            }else{
                //send feedback of failure
                return false;
            }
        }else{
            return "Not all column have attribute required to make table";
        }
    }

    /**
     * lapper around all required to be set and  calls for create method
     * to make table in database all at once
     * NOTE this is shortcut of you called all setter own your own
     * return TRUE if table made or FALSE if not made.
     */
    public function resolver($connection, $cols = [], $attr = [], $tName = "", $defaultprimary = true){
        if(SecurityChecker::isConfigExist()){
            $this->setColumnNames($cols);
            $this->setAttributes($attr);
            $this->setTableName($tName);
            $this->setPrimaryColumn($defaultprimary);
            $this->setConnection($connection);
            return $this->create();
        }
        return false;
    }

}