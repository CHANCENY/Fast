<?php

namespace User;

use Datainterface\Selection;
use GlobalsFunctions\Globals;

class User
{
  private bool $error;

  private string $message;

  private array $currentUser;

  public function __construct()
  {
      if(empty(Globals::user())){
          $this->error = true;
          $this->message = "User not found";
      }else{
          $this->error = false;
      }
  }

  public function firstName(): string
  {
      if(!$this->error){
        return Globals::user()[0]['firstname'];
      }
      return "anonymous";
  }

    public function lastName(): string
    {
        if(!$this->error){
            return Globals::user()[0]['lastname'];
        }
        return "anonymous";
    }

    public function email(): string
    {
        if(!$this->error){
            return Globals::user()[0]['mail'];
        }
        return "anonymous";
    }

    public function password(): string
    {
        if(!$this->error){
            return Globals::user()[0]['password'];
        }
        return "anonymous";
    }

    public function blocked(): string
    {
        if(!$this->error){
            return Globals::user()[0]['blocked'];
        }
        return "anonymous";
    }

    public function verified(): string
    {
        if(!$this->error){
            return Globals::user()[0]['verified'];
        }
        return "anonymous";
    }

    public function profileImage(): string
    {
        if(!$this->error){
            return Globals::user()[0]['image'];
        }
        return "anonymous";
    }

    public function role(): string
    {
        if(!$this->error){
            return Globals::user()[0]['role'];
        }
        return "anonymous";
    }

    public function address(): string
    {
        if(!$this->error){
            return Globals::user()[0]['address'];
        }
        return "anonymous";
    }

    public function registeredOn(): string
    {
        if(!$this->error){
            return Globals::user()[0]['created'];
        }
        return "anoynomus";
    }

    public static function loadUser($uid){
        $user = Selection::selectById('users',['uid'=>$uid]);
        return [
            'firstname'=>$user[0]['firstname'] ?? "anoynomus",
            'lastname'=>$user[0]['lastname'] ?? "anoynomus",
            'mail'=>$user[0]['mail'] ?? "anynomus",
            'password'=>$user[0]['password'] ?? "anoymonus",
            'blocked'=>$user[0]['blocked'] ?? "anoynomus",
            'verified'=>$user[0]['verified'] ?? "anoynomus",
            'profileImage'=>$user[0]['image'] ?? "anoynomus",
            'role'=>$user[0]['role'] ?? "anoynommus"
        ];
    }

}