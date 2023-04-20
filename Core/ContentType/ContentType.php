<?php

namespace ContentType;

use Datainterface\Database;
use Datainterface\Insertion;
use Datainterface\mysql\TablesLayer;
use Datainterface\MysqlDynamicTables;
use Datainterface\SecurityChecker;
use Datainterface\Selection;

class ContentType
{
    private string $contentTypeName;

    private array $definitionAttributes;

    public string $message;

    private array $formLayout;

    /**
     * @return array
     */
    public function getFormLayout(): array
    {
        return $this->formLayout;
    }

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
        $this->message = "";
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
        $this->setContentTypeName(str_replace(' ','_', htmlspecialchars(strip_tags($incomingData['content-type-name']))));
        $id = substr($incomingData['content-type-name'], 0, 2).'_id';
        $this->setFields("{$id}");
        $this->setDefinitionAttributes($id,['int(11)','primary key', 'auto_increment']);
        $total = $incomingData["total-fields"];
        for ($i = 1; $i <= intval($total); $i++){
           if(!in_array($incomingData["field-$i"], $this->fields)){
               $this->setFields(str_replace(' ','_',htmlspecialchars(strip_tags($incomingData["field-$i"]))));
               $canBeNull = isset($incomingData["empty-$i"]) ? 'not null' : 'null';
               $this->setDefinitionAttributes(str_replace(' ','_',htmlspecialchars(strip_tags($incomingData["field-$i"]))),
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
            if($this->createContentTypeForm()){
                $this->message = "Content type created";
                return $maker->resolver(Database::database(), $this->fields, $this->definitionAttributes, $this->contentTypeName, false);
            }
        }
        return false;
    }

    public function createContentTypeForm(){
        if(!empty($this->definitionAttributes)){
            $form = [];
            foreach ($this->definitionAttributes as $column=>$attribute){
                $div = "<div class='form-group mt-3'><label>".ucfirst(str_replace('_', ' ', $column))."</label>@input</div>";
                if(gettype($attribute)){
                    if(in_array('auto_increment', $attribute) || in_array('primary key', $attribute)){
                        continue;
                    }
                    if (strstr($attribute[0], 'int')){
                        $required = isset($attribute[1]) ? $attribute[1] === "not null" ? "required" : "" : "";
                        $line = "<input type='number' name='$column' class='form-control' id='{$column}-id' $required/>";
                        $form[] = str_replace('@input',$line, $div);
                    }
                    if (strstr($attribute[0], 'varchar')){
                        $required = isset($attribute[1]) ? $attribute[1] === "not null" ? "required" : "" : "";
                        $line = "<input type='text' name='$column' class='form-control' id='{$column}-id' $required/>";
                        $form[] = str_replace('@input',$line, $div);
                    }
                    if (strstr($attribute[0], 'text')){
                        $required = isset($attribute[1]) ? $attribute[1] === "not null" ? "required" : "" : "";
                        $line  = "<textarea type='text' name='$column' cols='10' rows='10' class='form-control' id='{$column}-id' $required></textarea>";
                        $form[] = str_replace('@input',$line, $div);
                    }
                    if (strstr($attribute[0], 'bool')){
                        $required = isset($attribute[1]) ? $attribute[1] === "not null" ? "required" : "" : "";
                        $line = "<input type='checkbox' name='$column' class='{$column}-class' id='{$column}-id' $required/>";
                        $form[] = str_replace('@input',$line, $div);
                    }
                }
            }

            $line = implode(' ',   $form);
            $this->formLayout = $form;
            $formLayout = "<div class='mt-5 w-100'>
                              <div class='bg-light rounded shadow border'>
                                 <form action='#' method='POST' class='forms p-5' id='form-$this->contentTypeName'>
                                  $line
                                  <button class='btn btn-primary bg-primary border-primary d-block mt-3' type='submit' id='{$this->contentTypeName}-btn-id'> Submit</button>
                                 </form>
                              </div>
                            </div>";

            $columns = ['coid', 'content_type','form_layout'];
            $attributes = ['coid'=>['int(11)', 'auto_increment','primary key'],
                'form_layout'=>['text'],
                'content_type'=>['varchar(100)', 'not null']];

            if(SecurityChecker::isConfigExist()){
                if(Database::database() !== null){
                    $maker = new MysqlDynamicTables();
                    $maker->resolver(Database::database(), $columns, $attributes, 'content_type_form_storage',false);

                     if(empty(Selection::selectById('content_type_form_storage',['content_type'=>$this->contentTypeName])) &&
                         Insertion::insertRow('content_type_form_storage',[
                       'form_layout'=>$formLayout,
                         'content_type'=>$this->contentTypeName
                    ])){
                        return true;
                     }
                }
            }
            return $this;
        }
    }

    public function loadContentType(string $contentTypeName){
        if(!SecurityChecker::isConfigExist()){
            return;
        }
        if(Database::database() === null){
            return;
        }
        return Selection::selectById('content_type_form_storage',['content_type'=>str_replace(' ','_', $contentTypeName)]);

    }


}