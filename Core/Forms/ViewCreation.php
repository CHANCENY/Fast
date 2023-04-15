<?php
namespace FormViewCreation;

use Alerts\Alerts;
use Core\RouteConfiguration;
use Core\Router;

@session_start();

class ViewCreation
{
   public static function valiadateForm($FormState){

       if(empty($FormState)){

           $alert = Alerts::alert('danger', "Error: form submitted is empty!");
           $_SESSION['message']['creationviewform'] = $alert;
           return false;
       }

       if(empty($FormState['view-name']) || empty($FormState['view-url']) || empty($FormState['path-address']) || empty($FormState['accessible']))
       {
           $alert = Alerts::alert('danger', "Error: Some Fields are empty fill in all fields");
           $_SESSION['message']['creationviewform'] = $alert;
           return false;
       }

       if(strpos($FormState['view-url'],' ')){
           $lert = Alerts::alert('danger', "Url should not have space");
           $_SESSION['message']['creationviewform'] = $lert;
           return false;
       }

       $data = [
           'name'=>htmlspecialchars(strip_tags($FormState['view-name'])),
           'url'=>htmlspecialchars(strip_tags($FormState['view-url'])),
           'path'=> htmlspecialchars(strip_tags($FormState['path-address'])),
           'access'=>htmlspecialchars(strip_tags($FormState['accessible'])),
           'default'=> isset( $FormState['default']),
           'description'=> empty($FormState['description']) ? "No description" : htmlspecialchars(strip_tags($FormState['description']))
       ];
       $_SESSION['forms']['view-creation-form-data-storage'] = $data;
       return true;
   }

   public static function submitForm($FormState){

       $routes = new RouteConfiguration();
       $result = $routes->addView($FormState);
       $_SESSION['message']['creationviewform'] = Alerts::alert("info", $result);
   }
}