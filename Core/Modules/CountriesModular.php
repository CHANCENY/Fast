<?php

namespace Modules;

use Datainterface\Database;

class CountriesModular
{
   public static function getAllCountries(){
       $con = Database::database();
        $stmt = $con->prepare("SELECT iso3 as code3, 
       name as country, 
       numeric_code as hashcode, 
       id as rowid, 
       iso2 as code, currency as currencyused, capital as capitalcity FROM countries");
       $stmt->execute();
       return $stmt->fetchAll(\PDO::FETCH_ASSOC);

   }

   public static function getAllStates(){
       $con = Database::database();
       $stmt = $con->prepare("SELECT name as state, country_id as country, country_code as countrycode, id as state_id FROM states");
       $stmt->execute();
       return $stmt->fetchAll(\PDO::FETCH_ASSOC);
   }

   public static function getAllCities(){
       $con = Database::database();
       $stmt = $con->prepare("SELECT name as city, state_id as state, state_code as statecode, id as city_id FROM cities");
       $stmt->execute();
       return $stmt->fetchAll(\PDO::FETCH_ASSOC);
   }

   public static function getStateByCountry($country){
       $con = Database::database();
       $stmt = $con->prepare("SELECT name as state, country_id as country, country_code as countrycode, id as rowid FROM states WHERE country_code = :id");
       $country = htmlspecialchars(strip_tags($country));
       $stmt->bindParam(':id', $country);
       $stmt->execute();
       return $stmt->fetchAll(\PDO::FETCH_ASSOC);
   }

   public static function getCitiesByStates($state){
       $con = Database::database();
       $stmt = $con->prepare("SELECT name as city, state_id as state, state_code as statecode, id as rowid FROM cities WHERE state_id = :id");
       $state = htmlspecialchars(strip_tags($state));
       $stmt->bindParam(':id', $state);
       $stmt->execute();
       return $stmt->fetchAll(\PDO::FETCH_ASSOC);

   }

   public static function getCountryName($code){
       $con = Database::database();
       $stmt = $con->prepare("SELECT name as country FROM countries WHERE iso2 = :code OR iso3 = :code1");
       $stmt->bindParam(':code', $code);
       $stmt->bindParam(':code1', $code);
       $stmt->execute();

      return $stmt->fetchAll(\PDO::FETCH_ASSOC)[0]['country'];
   }
}