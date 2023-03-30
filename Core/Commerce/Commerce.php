<?php

namespace Commerce;

use Datainterface\Database;
use Datainterface\Insertion;
use Datainterface\MysqlDynamicTables;
use Datainterface\Selection;
use Datainterface\Updating;
use UI\Pagination;

class Commerce
{
    public static function init(){

        /**
         * columns in tables required for this to Class to work
         */
        $columns =[
            "products"=>["pid","pname", "pprice", "pimage","pcategory", "pquality", "pbrand", "pquantity", "pdescription"],
            "categories"=>["cid","cname", "cdescription", "cimage"],
            "cart"=>["ctid", "ctpid", "ctcid","ctuid", "cttotal"],
            "orders"=>["oid","orderdate","ctid","ouid"],
            "payments"=>["pyid", "pyoid", "amount", "referrencecode"]
        ];

        /**
         * attributes of columns in tables
         */
        $attributes = [
            "products"=>[
                "pid"=>['INT(11)', 'AUTO_INCREMENT', 'PRIMARY KEY'],
                "pname"=>['VARCHAR(50)','NOT NULL'],
                "pprice"=>['VARCHAR(20)', 'NOT NULL'],
                "pimage"=>['VARCHAR(120)', 'NOT NULL'],
                "pcategory"=>['INT(11)', 'NULL'],
                "pquality"=>['VARCHAR(20)', 'NULL'],
                "pbrand"=>['VARCHAR(20)', 'NULL'],
                "pquantity"=>['INT(11)', 'NULL'],
                "pdescription"=>['TEXT', 'NULL']
            ],
            "categories"=>[
                "cid"=>['INT(11)','AUTO_INCREMENT','PRIMARY KEY'],
                "cname"=>['VARCHAR(50)', 'NOT NULL'],
                "cdescription"=>['TEXT', 'NULL'],
                "cimage"=>["VARCHAR(120)","NULL"]
            ],
            "cart"=>[
                "ctid"=>['INT(11)','AUTO_INCREMENT', 'PRIMARY KEY'],
                "ctpid"=>['INT(11)','NOT NULL'],
                "ctcid"=>['INT(11)','NULL'],
                "ctuid"=>['INT(11)', 'NOT NULL'],
                "cttotal"=>['INT(11)', 'NULL']
            ],
            "orders"=>[
                "oid"=>['INT(11)','AUTO_INCREMENT','PRIMARY KEY'],
                "orderdate"=>['VARCHAR(20)', 'NOT NULL'],
                "ctid"=>['INT(11)','NOT NULL'],
                "ouid"=>['INT(11)', 'NOT NULL']
            ],
            "payments"=>[
                "pyid"=>['INT(11)','AUTO_INCREMENT','PRIMARY KEY'],
                "pyoid"=>['INT(11)', 'NOT NULL'],
                "amount"=>['INT(11)', 'NOT NULL'],
                "referrencecode"=>['VARCHAR(60)', 'NOT NULL']
            ]
        ];

        /**
         * tables
         */
        $tables = ['products','categories','cart','orders', 'payments'];

        $maker = new MysqlDynamicTables();

        foreach($tables as $table){

            $columnsOfTable = $columns[$table];
            $attributesOfTableColumns = $attributes[$table];

            /**
             * make table in database
             */
            $conn = Database::database();
            $maker->resolver($conn, $columnsOfTable,$attributesOfTableColumns, $table, false);
        }
    }

    public static function products(){
        return Selection::selectAll('products');
    }

    public static function categories(){
        return Selection::selectAll('categories');
    }

    public static function cart(){
        return Selection::selectAll('cart');
    }

    public static function orders(){
        return Selection::selectAll('orders');
    }

    public static function payments(){
        return Selection::selectAll('payments');
    }

    public static function product($pid){
        return Selection::selectById('products', ['pid'=>$pid]);
    }

    public static function category($cid){
        return Selection::selectById('categories',['cid'=>$cid]);
    }

    public static function order($oid){
        return Selection::selectById('orders', ['oid'=>$oid]);
    }

    public static function cartitem($ctuid){
        return Selection::selectById('cart', ['ctuid'=>$ctuid]);
    }

    public static function payment($pyid){
        return Selection::selectById('payments', ['pyid'=>$pyid]);
    }

    public static function userCommerceData($uid){

        $sql = "SELECT * FROM products AS PR LEFT JOIN categories AS CA ON PR.pcategory = CA.cid LEFT JOIN cart AS CR ON 
                PR.pid = CR.ctpid LEFT JOIN orders AS ORD ON ORD.ctid = CR.ctid LEFT JOIN payments AS PY ON PY.pyoid = ORD.oid WHERE ctuid = :uid";
        $con = Database::database();

        $stmt = $con->prepare($sql);
        $stmt->bindParam(':uid', $uid);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public static function addProduct($product = []){
        return Insertion::insertRow('products',$product);
    }

    public static function addCategory($category = []){
        return Insertion::insertRow('categories', $category);
    }

    public static function addOrder($order = []){
        return Insertion::insertRow('orders', $order);
    }

    public static function addPayment($payment = []){
        return Insertion::insertRow('payments', $payment);
    }

    public static function addCartItem($item){
        return Insertion::insertRow('cart', $item);
    }

    public static function updateOrder($keyValue = [],$order=[]){
        return Updating::update('orders',$order,$keyValue);
    }

    public static function updateProduct($keyValue = [], $product = []){
        return Updating::update('products', $product, $keyValue);
    }

    public static function updateCart($keyvalue = [], $item =[]){
        return Updating::update('cart', $item, $keyvalue);
    }

    public static function updateCategory($keyValue = [], $category = []){
        return Updating::update('categories', $category, $keyValue);
    }

    public static function getProductsByCategoryId($cid){
        return Selection::selectById('products',['pcategory'=>$cid]);
    }
}