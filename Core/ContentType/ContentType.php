<?php

namespace ContentType;

use Datainterface\Database;
use Datainterface\Insertion;
use Datainterface\mysql\TablesLayer;
use Datainterface\MysqlDynamicTables;
use Datainterface\Query;
use Datainterface\SecurityChecker;
use Datainterface\Selection;
use GlobalsFunctions\Globals;

class ContentType
{
    private string $contentTypeName;

    private array $definitionAttributes;

    public string $message;

    private array $formLayout;

    private bool $relatedAdded;

    private string $relatedId;

    private string $relatedFiled;

    private array $relatedDefinition;

    /**
     * @return bool
     */
    public function isRelatedAdded(): bool
    {
        return $this->relatedAdded;
    }

    /**
     * @param bool $relatedAdded
     */
    public function setRelatedAdded(bool $relatedAdded): void
    {
        $this->relatedAdded = $relatedAdded;
    }

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
        $this->relatedAdded = false;
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
        $id = substr($incomingData['content-type-name'], 0, 2).'Id';
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
            $this->relatedFiled = str_replace('@', '_', $related);
            $this->relatedDefinition = [str_replace('@', '_', $related) => $schema];
            $this->setRelatedAdded(true);
            $this->relatedId = $this->contentTypeName.'_'.$this->relatedFiled;
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

        $columns = ['coid', 'content_type','form_layout'];
        $attributes = ['coid'=>['int(11)', 'auto_increment','primary key'],
            'form_layout'=>['text'],
            'content_type'=>['varchar(100)', 'not null']];

        $maker = new MysqlDynamicTables();
        $maker->resolver(Database::database(), $columns, $attributes,'content_type_form_storage',false );
        if(!empty(Selection::selectById('content_type_form_storage',['content_type'=>$this->contentTypeName]))){
            $this->message = "Content type $this->contentTypeName already exist";
            return false;
        }
        if(empty($this->message)){
            $maker = new MysqlDynamicTables();
            if($this->createContentTypeForm()){
                $this->addRelatedCallBacks();
                $this->message = "Content type created";
                return $maker->resolver(Database::database(), $this->fields, $this->definitionAttributes, $this->contentTypeName, false);
            }
        }
        return false;
    }

    public function createContentTypeForm(){
        if(!empty($this->definitionAttributes)){
            $form = [];
            $fileFlag = false;
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
                    if ($attribute[0] === 'varchar(100)'){
                        $required = isset($attribute[1]) ? $attribute[1] === "not null" ? "required" : "" : "";
                        $line = "<input type='text' name='$column' class='form-control {$column}-bool-class' id='{$column}-short-id' $required/>";
                        $form[] = str_replace('@input',$line, $div);
                    }
                    if (strstr($attribute[0], 'text')){
                        $required = isset($attribute[1]) ? $attribute[1] === "not null" ? "required" : "" : "";
                        $line  = "<textarea type='text' name='$column' cols='10' rows='10' class='form-control {$column}-bool-class' id='{$column}-text-id' $required></textarea>";
                        $form[] = str_replace('@input',$line, $div);
                    }
                    if (strstr($attribute[0], 'bool')){
                        $required = isset($attribute[1]) ? $attribute[1] === "not null" ? "required" : "" : "";
                        $line = "<input type='checkbox' name='$column' class='{$column}-bool-class' id='{$column}-bool-id' $required/>";
                        $form[] = str_replace('@input',$line, $div);
                    }
                    if (strstr($attribute[0], 'LONGBLOB')){
                        $fileFlag = true;
                        $required = isset($attribute[1]) ? $attribute[1] === "not null" ? "required" : "" : "";
                        $line = "<input type='file' name='{$column}[]' class='form-control {$column}-file-class' id='{$column}-file-id' $required multiple/>";
                        $form[] = str_replace('@input',$line, $div);
                    }
                    if ($attribute[0] === 'varchar(50)'){
                        $required = isset($attribute[1]) ? $attribute[1] === "not null" ? "required" : "" : "";
                        $sel = "<select name='$column' id='{$column}-1' class='form-control {$column}-select-class'>
                                 <option value=''>--Select {$column}--</option>
                               </select>";
                        $form[] = str_replace('@input',$sel, $div);
                    }
                }
            }

            $line = implode(' ',   $form);
            $this->formLayout = $form;
            $base = Globals::protocal().'://'.Globals::serverHost().'/'.Globals::home();
            $enctype = $fileFlag === true ? '"enctype=multipart/form-data"' : "";
            $formLayout = "<div class='mt-5 w-100' id='layout-content-form' data-call='{$base}'>
                              <div class='bg-light rounded shadow border'>
                                 <h2 id='{$this->contentTypeName}-title-id'></h2>
                                 <form action='#' method='POST' class='forms p-5' id='form-$this->contentTypeName' {$enctype}>
                                  $line
                                  @related
                                  <button name='{$this->contentTypeName}-btn-submit' class='btn btn-primary bg-primary border-primary d-block mt-3' type='submit' id='{$this->contentTypeName}-btn-id'> Submit</button>
                                 </form>
                              </div>
                            </div><div><script type='application/javascript'>@js</script></div>";
            if($this->relatedAdded === true){
                $label = ucfirst(str_replace('_',' ',$this->relatedFiled));
                $relatedTag = "<div class='form-group mt-3'> <label for='$this->relatedId'>$label</label><select class='form-control' id='{$this->relatedId}' name='{$this->relatedFiled}'>@option</select></div>";
                $option = "<option value=''>--Select--</option>";
                $relatedTag  = str_replace("@option",$option, $relatedTag);
                $formLayout = str_replace("@related", $relatedTag, $formLayout);
                $this->setFields($this->relatedFiled);
                $this->setDefinitionAttributes($this->relatedFiled, $this->relatedDefinition[$this->relatedFiled]);
                $js = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/Core/ContentType/example2.js');
                if(!empty($js)){
                    $js = str_replace('@id',$this->relatedId, $js);
                    $js = str_replace('@contentType',$this->contentTypeName, $js);
                    $formLayout = str_replace('@js', $js, $formLayout);
                }else{
                    $formLayout = str_replace('@js', "let i = 0;", $formLayout);
                }
            }else{
                $formLayout = str_replace('@js', "let i = 0;", $formLayout);
                $formLayout = str_replace('@related', "&nbsp;", $formLayout);
            }

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

    private function addRelatedCallBacks(){
        if($this->relatedAdded === true){
            $tableColumn = end($this->fields);
            $definition = $this->definitionAttributes[$tableColumn];
            $breakLine = PHP_EOL;
            $base = $_SERVER['DOCUMENT_ROOT'].'/includes/formFunction.inc';
            $content = "<?php {$breakLine}/**{$breakLine}*Functions that handlers related Field added to any form using Content Type creation{$breakLine}*/{$breakLine}namespace formFunction;{$breakLine}use Datainterface\Query;";
            if(!file_exists($base)){
                $list = explode('/', $base);
                $dir = implode('/', array_slice($list, 0, count($list) - 1));
                if(mkdir($dir,0777, true)){
                   file_put_contents($base, $content);
                }
            }else{
                $d = file_get_contents($base);
                if(empty($d)){
                    file_put_contents($base, $content);
                }
            }

            $content = file_get_contents($base);

            $list = explode('_', $tableColumn);
            $tableLayer = new TablesLayer();
            $achemas = $tableLayer->getSchemas()->schema();
            $thisTableSchema = $achemas[$list[0]];
            $varcharFieldFound = "";

            if(!empty($thisTableSchema)){
                $to = ['100','50', '200','250'];
                $counter = 0;
                top:
                foreach ($thisTableSchema as $key=>$value){
                    if(gettype($value) === 'array'){
                        if($value['Type'] === "varchar({$to[$counter]})" &&
                            !str_contains(strtolower($value['Field']), 'password')
                            && !str_contains(strtolower($value['Field']), 'username')
                            && !str_contains(strtolower($value['Field']), 'mail')){
                           $varcharFieldFound = $value['Field'];
                        }
                    }
                }
                if(empty($varcharFieldFound)){
                    $counter = $counter + 1;
                    goto top;
                }
            }
            $functionName = strtolower($this->contentTypeName);
            $contentPHP = "function {$functionName}(){ {$breakLine} @code {$breakLine} }";
            $code = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/Core/ContentType/example.txt');
            $replace = ["@table","@varcharString","@relatedColumn", "@breakLine"];
            $values = [$list[0], $varcharFieldFound, end($list), $breakLine];
            $code = str_replace($replace,$values,$code);
            $contentPHP = str_replace("@code",$code, $contentPHP);

            $oldFunction = file_get_contents($base);
            return file_put_contents($base, $oldFunction.$breakLine.$breakLine.$contentPHP);
        }
    }


}