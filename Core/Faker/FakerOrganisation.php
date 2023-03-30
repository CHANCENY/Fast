<?php

namespace Faker;

use Datainterface\Database;
use Datainterface\Insertion;
use Datainterface\MysqlDynamicTables;
use Datainterface\Selection;
use GlobalsFunctions\Globals;

class FakerOrganisation
{
  public static function generateOrganisations($total = 50){
      $schema = self::tables();
      $maker = new MysqlDynamicTables();
      $maker->resolver(Database::database(),$schema['col'], $schema['att'],'organisations',true);

      //organisation
      $organisation = self::fakerOrganisation($total);
      $feedBack['created']= [];
      $feedBack['existed']=0;
      $feedBack['totalNew'] = 0;
      if(!empty($organisation)){
          foreach ($organisation as $user=>$value){
              $alreadyUser = Selection::selectById('organisations',['OrganizationId'=>$value['OrganizationId']]);
              if(empty($alreadyUser)){
                  $id = Insertion::insertRow('organisations', $value);
                  if(!empty($id)){
                      $feedBack['totalNew'] = $feedBack['totalNew'] + 1;
                      $feedBack['created'][] = $id;
                  }
              }else{
                  $feedBack['existed'] = $feedBack['existed'] + 1;
              }
          }
      }
      return $feedBack;

  }

  public static function fakerOrganisation($total = 50){
      $path = "";
      if($total < 100000){
          $path = Globals::root().'/Core/Faker/Store/organisation/organizations-1.csv';
      }else{
          $path = Globals::root().'/Core/Faker/Store/organisation/organizations-2.csv';
      }

      $content = "";
      if(file_exists($path)){
          $content = file_get_contents($path);
      }else{
          throw new \Exception('Faker failed to load csv file');
      }

      $lines = [];
      if(!empty($content)){
          $findlerHandler = fopen($path, 'r');
          if($findlerHandler === false){
              throw new \Exception('Faker failed to generate organisation');
          }

          while(($row = fgetcsv($findlerHandler)) !== false){
              $lines[] = $row;
          }
          fclose($findlerHandler);
      }

      $organisation = [];
      $counter = 0;
      foreach ($lines as $key){
          if($counter < $total+2 && $counter !== 0){
              $organisation[]=$key;
          }
          $counter += 1;
      }

      $schema = self::tables();
      $col = $schema['col'];
      $finalOrganisation = [];

      foreach ($organisation as $product=>$value){
          $item[$col[0]]=trim($value[1]);
          $item[$col[1]]=$value[2];
          $item[$col[2]]=$value[3];
          $item[$col[3]]=$value[4];
          $item[$col[4]]=$value[5];
          $item[$col[5]]=$value[6];
          $item[$col[6]]=$value[7];
          $item[$col[7]]=$value[8];
          $finalOrganisation[] = $item;
      }

      return $finalOrganisation;
  }

  public static function tables(){
      $column = ['OrganizationId','Name','Website','Country','Description','Founded','Industry','employees'];
      $attribute = [
          'OrganizationId'=>['VARCHAR(50)','NOT NULL'],
          'Name'=>['VARCHAR(100)','NOT NULL'],
          'Website'=>['VARCHAR(250)','NULL'],
          'Country'=>['VARCHAR(20)','NULL'],
          'Description'=>['TEXT'],
          'Founded'=>['VARCHAR(30)','NOT NULL'],
          'Industry'=>['VARCHAR(100)','NULL'],
          'employees'=>['INT(11)']
      ];

      return ['col'=>$column, 'att'=>$attribute];
  }

}