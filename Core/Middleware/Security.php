<?php

namespace MiddlewareSecurity;

use GlobalsFunctions\Globals;

class Security extends Globals
{
   private $currentUser;

   private $currentView;

   public function __construct(){

       $this->currentView = self::view();
       $this->currentUser = self::user();
   }

   public function checkViewAccess(){
       if(empty($this->currentView)){
           return "V-NULL";
       }

       //check access

       $values = array_values($this->currentView);
       if(in_array('public', $values)){
           return "V-PUBLIC";
       }

       if(in_array('private', $values)){
           return "V-PRIVATE";
       }

       if(in_array('administrator', $values)){
           return "V-PRIVATE";
       }

       if(in_array('moderator', $values)){
           return "V-MODERATOR";
       }
   }

   public function checkCurrentUser(){

       if(empty($this->currentUser)){
           return "U-NULL";
       }

       elseif($this->currentUser[0]['blocked'] === 1){
           return "U-BLOCK";
       }

       //check role
       elseif($this->currentUser[0]['role'] === "Admin"){
           return "U-Admin";
       }

       elseif($this->currentUser[0]['verified'] === 1){
           return "V-VERIFIED";
       }

       elseif(empty($this->currentUser[0]['verified'])){
           return "V-NOT-VERIFIED";
       }

      elseif (isset($this->currentView['view_role_access']) && $this->currentView['view_role_access'] === "moderator"){
           return "V-VERIFIED";
      }

       return "U-COMMON";
   }
}