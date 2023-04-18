<?php

namespace ContentType;

use Datainterface\Database;
use Datainterface\mysql\TablesLayer;
use Datainterface\MysqlDynamicTables;
use Datainterface\SecurityChecker;

class ContentType
{
    private string $contentTypeName;

    private array $definitionAttributes;

    public string $message;

    /**
     * @param mixed $definitionAttributes
     */
    public function setDefinitionAttributes(string $key, array $definitionAttributes): void
    {
        $this->definitionAttributes[$key] = $definitionAttributes;
    }

    /**
     * @param string $contentTypeName
     */
    public function setContentTypeName(string $contentTypeName): void
    {
        $this->contentTypeName = $contentTypeName;
    }

    /**
     * @param string $contentTypeNameLinked
     */
    public function setContentTypeNameLinked(string $contentTypeNameLinked): void
    {
        $this->contentTypeNameLinked = $contentTypeNameLinked;
    }

    /**
     * @param string $fields
     */
    public function setFields(string $fields): void
    {
        $this->fields[] = $fields;
    }

    private string $contentTypeNameLinked;

    private array $fields;

    private array $selectOptionContentTypeLinks;

    /**
     * @return array
     */
    public function getSelectOptionContentTypeLinks(): array
    {
        return $this->selectOptionContentTypeLinks;
    }

    public function __construct()
    {
        $this->contentTypeName = '';
        $this->contentTypeNameLinked = '';
        $this->fields = array();
        $this->selectOptionContentTypeLinks = array();
    }

    public function makeOptionLinker(){
        if(!SecurityChecker::isConfigExist()){
            return [];
        }
        if(Database::database() === null){
            return [];
        }

        $layer = new TablesLayer();
        $schemas = $layer->getSchemas()->schema();
        $tables = array_keys($schemas);
        for ($i = 0; $i < count($tables); $i++){
            $definitions = $schemas[$tables[$i]];
            if(gettype($definitions) == 'array'){
                for ($j = 0; $j < count($definitions); $j++){

                    if(isset($definitions[$j]['Field'])){
                        $option = "<option value='{$tables[$i]}@{$definitions[$j]['Field']}'
                              id='{$tables[$i]}-{$definitions[$j]['Field']}'>
                              {$tables[$i]} - {$definitions[$j]['Field']}</option>";
                        $this->selectOptionContentTypeLinks[] = $option;
                    }
                }
            }
        }
      return $this;
    }

    public function sortNewContentFieldsDefinitions($incomingData) {
        $this->setContentTypeName(htmlspecialchars(strip_tags($incomingData['content-type-name'])));
        $id = substr($incomingData['content-type-name'], 0, 2).'_id';
        $this->setFields("{$id}");
        $this->setDefinitionAttributes($id,['int(11)','primary key', 'auto_increment']);
        $total = $incomingData["total-fields"];
        for ($i = 1; $i <= intval($total); $i++){
           if(!in_array($incomingData["field-$i"], $this->fields)){
               $this->setFields(htmlspecialchars(strip_tags($incomingData["field-$i"])));
               $canBeNull = isset($incomingData["empty-$i"]) ? 'not null' : 'null';
               $this->setDefinitionAttributes(htmlspecialchars(strip_tags($incomingData["field-$i"])),
                   [htmlspecialchars(strip_tags($incomingData["select-$i"])), $canBeNull]);
           }else{
               $this->message =  "Failed Field name {$incomingData["field-$i"]} has been used more than one please make sure field names are unique";
               return $this;
           }
        }
        if(!empty($incomingData['related'])){
            $related = htmlspecialchars(strip_tags($incomingData['related']));
            $list = explode('@', $related);
            $layer = new TablesLayer();
            $schemas = $layer->getSchemas()->schema();
            $perTable = $schemas[$list[0]];

            $schema = [];
            foreach ($perTable as $key=>$value){
                if(gettype($value) == 'array'){
                    if($value['Field'] === end($list)){
                      $schema[] = $value['Type'];
                    }
                }
            }
            $this->setFields(end($list));
            $this->setDefinitionAttributes(end($list), $schema);
        }
        return $this;
    }

    public function saveContentTypeDefinitions() : bool {
        if(!SecurityChecker::isConfigExist()){
            return false;
        }
        if(Database::database() === null){
            return false;
        }
        if(empty($this->message)){
            $maker = new MysqlDynamicTables();
            return $maker->resolver(Database::database(), $this->fields, $this->definitionAttributes, $this->contentTypeName, false);
        }
        die($this->message);
        return false;
    }


}