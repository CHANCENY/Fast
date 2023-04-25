<?php

namespace Site;

use Datainterface\Database;
use Datainterface\Delete;
use Datainterface\Insertion;
use Datainterface\MysqlDynamicTables;
use Datainterface\SecurityChecker;
use Datainterface\Selection;
use Datainterface\Updating;

class Site
{
    private array $site;

    /**
     * @return array
     */
    public function getSite(): array
    {
        return $this->site;
    }

    /**
     * @param array $site
     */
    public function setSite(array $site): void
    {
        $this->site = $site;
    }

    /**
     * @return bool
     */
    public function isError(): bool
    {
        return $this->error;
    }

    /**
     * @param bool $error
     */
    public function setError(bool $error): void
    {
        $this->error = $error;
    }

    private bool $error;

    public function __construct(){
        $this->error = !(($this->runSchema() === true));
        if(!$this->error){
            $this->site = $this->siteLoading();
        }
    }

    public function schema(){
        $column = ['sid', 'site_name','site_mail','site_phone','site_owner','site_slogan','site_logo'];
        $attributes =[
            'sid'=>['int(11)','auto_increment','primary key'],
            'site_name'=>['varchar(100)', 'null'],
            'site_mail'=>['varchar(100)','null'],
            'site_phone'=>['varchar(50)','null'],
            'site_owner'=>['varchar(250)', 'null'],
            'site_slogan'=>['text', 'null'],
            'site_logo'=>['longblob', 'null']
        ];
        return ['col'=>$column, 'attr'=>$attributes, 'table'=>'site_information_configuration'];
    }

    public function runSchema(){
        if(SecurityChecker::isConfigExist()){
            if(Database::database() !== null){
                $maker = new MysqlDynamicTables();
                return $maker->resolver(Database::database(), $this->schema()['col'], $this->schema()['attr'], $this->schema()["table"], false);
            }
        }
        return false;
    }

    public function siteLoading(){
        if(SecurityChecker::isConfigExist()){
            if(Database::database() !== null){
                return Selection::selectAll($this->schema()['table']);
            }
        }
        $this->error = true;
    }

    public function saveSiteInformation(array $data): bool{
        if($this->error){
            print_r($this->error);
            return false;
        }
        return Insertion::insertRow($this->schema()['table'], $data);
    }

    public function updateSiteInformation(array $data): bool{
        if(!$this->error){
            $temp = $data;
            unset($data['sid']);
            return Updating::update($this->schema()['table'],$temp, ['sid'=>$data['sid']]);
        }
        return false;
    }

    public function deleteSiteInformation(int $sid): bool{
        if(!$this->error){
            return Delete::delete($this->schema()['table'],['sid'=>$sid]);
        }
        return false;
    }


}