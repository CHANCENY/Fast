<?php

namespace SiteMap;

use Datainterface\Database;
use Datainterface\Insertion;
use Datainterface\mysql\InsertionLayer;
use Datainterface\mysql\SelectionLayer;
use Datainterface\mysql\UpdatingLayer;
use Datainterface\MysqlDynamicTables;
use Datainterface\Query;
use Datainterface\SecurityChecker;
use Datainterface\Updating;
use GlobalsFunctions\Globals;
use RoutesManager\RoutesManager;

class SiteMap
{
    private string $siteMap;

    public function __construct()
    {
        $brek = PHP_EOL;
        $this->siteMap = '<?xml version="1.0" encoding="UTF-8"?>'.$brek.'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'.$brek.'{{placeholder}}'.$brek.'</urlset>';
    }

    public function crawlerAttacher(): string
    {
        if(SecurityChecker::isConfigExist()){
            if(!is_null(Database::database())){
                $this->storage();
                $query = Query::query('select enabled from sitemap_config');
                if(!empty($query)){
                    $enabled = $query[0]['enabled'] ?? null;
                    if(!is_null($enabled) && $enabled === 'enabled'){
                        return 'Core/SiteMap/js/sitemap.js';
                    }
                }
            }
        }
        return "";
    }

    public function schema()
    {
      return ['col'=>['sid','url'],
              'attr'=> [
                  'sid'=>['int(11)', 'auto_increment', 'primary key'],
                  'url'=>['varchar(300)']
              ],
              'table'=>'site_map'
          ];
    }

    public function storage(): SiteMap
    {
      if(!SecurityChecker::isConfigExist()){
          return $this;
      }
      if(Database::database() === null){
          return $this;
      }
      $maker = new MysqlDynamicTables();
      $maker->resolver(Database::database(),$this->schema()['col'],$this->schema()['attr'],$this->schema()['table'],false);
      $maker->resolver(Database::database(),
          ['scid','enabled','view_default','priority','update_check','skipped','private'],
          ['scid'=>['int(11)','auto_increment','primary key'], 'enabled'=>['varchar(20)'],
          'view_default'=>['varchar(20)'],
              'priority'=>['varchar(5)'],
              'update_check'=>['varchar(50)'],
              'skipped'=>['text'],
              'private' =>['varchar(20)']
          ],
          'sitemap_config',false
      );
      return $this;
    }

    public function config($data){
        $this->storage();
        $query = Query::query("SELECT scid FROM sitemap_config");
        if(!empty($query)){
            $scid = $query[0]['scid'];
            return Updating::update('sitemap_config',$data,['scid'=>$scid]);
        }else{
            return Insertion::insertRow('sitemap_config',$data);
        }
    }

    public function siteMapConfigs(){
        $this->storage();
        $query = Query::query('select * from sitemap_config');
        return [
                $query[0]['enabled'] ?? null,
                $query[0]["view_default"] ?? null,
                $query[0]["priority"] ?? null,
                $query[0]["update_check"] ?? null,
                $query[0]['skipped'] ?? null,
                $query[0]['private'] ?? null
              ];
    }

    public function savingSiteMapLocs($data): SiteMap{
        if(SecurityChecker::isConfigExist()){
            if(Database::database() !== null){
                if(!empty($data)){
                    $config = $this->siteMapConfigs();
                    foreach ($data as $key=>$value){
                        if(gettype($value) === 'string'){
                            $already = (new SelectionLayer())
                                ->setTableName($this->schema()['table'])
                                ->setKeyValue(['url'=>$value])
                                ->selectBy()->rows();
                            if(empty($already)){
                                (new InsertionLayer())
                                    ->setTableName($this->schema()['table'])
                                    ->setData(['url'=>$value])
                                    ->insert();
                            }else{
                                (new UpdatingLayer())->setTableName($this->schema()['table'])
                                    ->setData(['url'=>$value])->keys(['sid'=>$already[0]['sid']])
                                    ->update();
                            }
                        }
                    }
                }
            }
        }
        return $this;
    }

    public function filterFromConfig($list): bool{
        $config = $this->siteMapConfigs();
        $forbidden  = $config[4];
        if(empty($forbidden)){
            return false;
        }
        $forbidden = strpos($forbidden, ',') ? $forbidden : $forbidden.',';
        $forbiddenList = array_filter(explode(',', $forbidden), 'strlen');
        $list = array_filter($list, 'strlen');
        foreach ($forbiddenList as $key=>$value){
            if(array_search($value, $list)){
                return true;
            }
        }
        return false;
    }

    public function makeSiteMapFile():SiteMap
    {
        if(!SecurityChecker::isConfigExist()){
            return $this;
        }
        if(Database::database() === null){
            return $this;
        }

        $data = (new SelectionLayer())->setTableName($this->schema()['table'])->selectAll()->rows();
        $config = $this->siteMapConfigs();
        $urlSet  = "";
        foreach ($data as $key=>$value){
            $loc = $value['url'];
            $list = explode('/', $loc);
            $this->isPrivate($loc);

            if($this->filterFromConfig($list)){
                continue;
            }elseif ($this->isPrivate($loc)){
               continue;
            }else{
                $url = parse_url($loc,PHP_URL_PATH);
                $url = str_starts_with($url, '/') ? substr($url,1) : $url;
                $list = explode('/',$url);
                $url = trim(str_replace('#','',end($list)));
                $loc = $loc[strlen($loc)-1] ?? "" === '#' ? substr($loc,0,strlen($loc)) : $loc;
                $r = $this->disallowed($url, $loc);
                $priority = $config[2];
                $timeUpdated = new \DateTime('now',);
                $time = $timeUpdated->format('c');
                $checking = $config[3];
                if(!empty($r)){
                    $urlSet .= str_replace(['{{link}}','{{time}}','{{pr}}','{{checking}}'],[$loc,$time,$priority,$checking], $this->templates());
                    $urlSet .= PHP_EOL;
                }
            }
        }
        $this->siteMap = str_replace('{{placeholder}}', $urlSet, $this->siteMap);
        return $this;
    }

    private function templates(){
        $b = PHP_EOL;
        return "<url>$b\t<loc>{{link}}</loc>$b\t<lastmod>{{time}}</lastmod>$b\t<changefreq>{{checking}}</changefreq>$b\t<priority>{{pr}}</priority>$b</url>";
    }

    public function saveSiteMap(){
        $root = Globals::root();
        restart:
        if(file_exists($root.'/sitemap.xml')  && chmod($root.'/sitemap.xml', 0777)){
            return file_put_contents($root.'/sitemap.xml', $this->siteMap);
        }else{
            file_put_contents($root.'/sitemap.xml','');
            goto restart;
        }
    }


    public function disallowed($url, $actual): string
    {
        $flag = false;
        $defaults = (new RoutesManager())->tempReaderView();
        foreach ($defaults as $key=>$value){
            if($value['view_url'] === $url){
                $flag = true;
                break;
            }
        }
        $config = $this->siteMapConfigs();
        if($flag === true && $config[1] === 'disallowed'){
            return "";
        }elseif ($flag === true && $config[1] === 'allowed'){
            return $actual;
        }elseif ($flag === true && $config[1] === null){
            return $actual;
        }else{
            return $actual;
        }
    }

    public function isPrivate($requestUrl): bool
    {
        if(!SecurityChecker::isConfigExist()){
          return false;
        }
        if(Database::database() === null){
            return false;
        }
        $list = explode('?', $requestUrl);
        $path = explode('/', $list[0]);
        $url = end($path);
        $view = Globals::findViewByUrl($url);

        $config = $this->siteMapConfigs();
        if(!empty($view)){
            if($view['view_role_access'] === 'private' ||
                $view['view_role_access'] === 'administrator' ||
                $view['view_role_access'] === 'moderator'){
                if(empty($config[5]) ||$config[5] === 'disallowed'){
                    return false;
                }else{
                    return true;
                }
            }
        }

        return false;
    }


}