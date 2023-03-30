<?php

namespace Faker;

use Commerce\Commerce;
use Datainterface\Database;
use Datainterface\Insertion;
use Datainterface\MysqlDynamicTables;
use Datainterface\Selection;
use FileHandler\FileHandler;
use GlobalsFunctions\Globals;

class FakerProducts
{
    public static function generateFakeProducts($total = 50){
       self::tables();
        //products
        $products = self::fakerProducts($total);
        $feedBack['created']= [];
        $feedBack['existed']=0;
        $feedBack['totalNew'] = 0;
        if(!empty($products)){
            foreach ($products as $user=>$value){
                $alreadyUser = Selection::selectById('products',['pname'=>$value['pname']]);
                if(empty($alreadyUser)){
                    $id = Insertion::insertRow('products', $value);
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

    public static function fakerProducts($total = 50){
        $path = "";
        if($total < 100000){
            $path = Globals::root().'/Core/Faker/Store/products/product2.csv';
        }else{
            $path = Globals::root().'/Core/Faker/Store/products/product1.csv';
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
                throw new \Exception('Faker failed to generate products');
            }

            while(($row = fgetcsv($findlerHandler)) !== false){
                $lines[] = $row;
            }
            fclose($findlerHandler);
        }

        $products = [];
        $counter = 0;
        foreach ($lines as $key){
            if($counter < $total+2 && $counter !== 0){
                $products[]=$key;
            }
            $counter += 1;
        }

        $schema = self::productSchema();
        $col = $schema['col'];
        $finalProductsCopy = [];

        foreach ($products as $product=>$value){
            $item[$col[1]]=trim($value[1]);
            $item[$col[2]]=$value[5];
            $item[$col[3]]=self::saveNoImage(Globals::root().'/Files/no-image-product.png');
            $item[$col[4]]=self::fakeCategory(trim($value[8]));
            $item[$col[5]]=$value[9];
            $item[$col[6]]=$value[7];
            $item[$col[7]]=$value[3];
            $item[$col[8]]="Product by $value[2]";
            $finalProductsCopy[] = $item;
        }
        return $finalProductsCopy;
    }

    public static function productSchema(){
        $col = ["pid","pname", "pprice", "pimage","pcategory", "pquality", "pbrand", "pquantity", "pdescription"];
        $attr = [
            "pid"=>['INT(11)', 'AUTO_INCREMENT', 'PRIMARY KEY'],
            "pname"=>['VARCHAR(50)','NOT NULL'],
            "pprice"=>['VARCHAR(20)', 'NOT NULL'],
            "pimage"=>['VARCHAR(120)', 'NOT NULL'],
            "pcategory"=>['INT(11)', 'NULL'],
            "pquality"=>['VARCHAR(20)', 'NULL'],
            "pbrand"=>['VARCHAR(20)', 'NULL'],
            "pquantity"=>['INT(11)', 'NULL'],
            "pdescription"=>['TEXT', 'NULL']
        ];
        return ['col'=>$col,'att'=>$attr];
    }

    public static function saveNoImage($filepath){
        $default = Globals::root().'/Core/Faker/Store/products/no-image.png';

        $content = "";
        if(file_exists($default)){
            $content = file_get_contents($default);
        }

        if(file_exists($filepath)){
            return Globals::protocal().'://'.Globals::serverHost().'/Files/no-image-product.png';
        }

        return FileHandler::saveFile('no-image-product.png',$content,'binary');
    }

    public static function fakeCategory($name){
        self::tables();
        $already = Selection::selectById('categories',['cname'=>$name]);
        if(empty($already)){
            return Insertion::insertRow('categories',['cname'=>$name]);
        }else{
            return $already[0]['cid'];
        }

    }

    public static function tables(){
        Commerce::init();
    }
}