<?php

namespace UI;

use Assest\Assest;
use GlobalsFunctions\Globals;
use Modules\CountriesModular;

class AddressField extends CountriesModular
{
    public static function addressFieldSet($defaultCountry){
        $countries = self::getAllCountries();
       $options = "";
        foreach ($countries as $countrydata){
            extract($countrydata);

            if($code === $defaultCountry){
                $options .= "<option value ='$code' selected>$country</option>";
            }else {
                $options .= "<option value ='$code'>$country</option>";
            }
        }

        $host = Globals::protocal().'://'.Globals::serverHost().'/'.Globals::home();

        $fields = "<div id='host-address' class='dragArea row' data-hosst='{$host}'>";

         $country = "<div id='country-div' class='col-md col-sm-12 form-group mb-3'><select class='form-control' name='country' id='country-edit'>
                  $options
                  </select></div>";

        $state = "<div id='state-div' class='col-md col-sm-12 form-group mb-3'><select class='form-control' name='states' id='state-edit' disabled>
                   <option value=''>--State--</option>
                   </select></div>";

        $city = "<div id='city-div' class='col-md col-sm-12 form-group mb-3'><select class='form-control' name='cities' id='city-edit' disabled>
                 <option value=''>--City--</option>
                 </select></div>";
        $address1 = "<div class='col-12 form-group mb-3'><input class='form-control' name='address1' id='address1-edit' placeholder='Address' type='text'></div>";

        $fields .= $country.$state.$city.$address1."</div>";

        $fields .= self::javaScriptAddress();



        return $fields;

    }

    public static function javaScriptAddress(){
        $file = $_SERVER['DOCUMENT_ROOT'].'/Core/UI/js/address.js';
        $content = "";
        if(file_exists($file)){
            $content = file_get_contents($file);
        }
        return "<script type='application/javascript'>$content</script>";
    }
}
?>

