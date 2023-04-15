<?php
@session_start();

$user = \GlobalsFunctions\Globals::user();
if(isset($user) && $user[0]['role'] === 'Admin'){
    $action = \GlobalsFunctions\Globals::get('action');
    $eid = \GlobalsFunctions\Globals::get('eid');
    if($action === 'details'){
        $result = \ErrorLogger\ErrorLogger::getDetails($eid);
        $list = explode('.',$result[0]['location']);
        if(end($list) === 'php'){
            $location = $result[0]['location'];
        }else{
            $location = unserialize($result[0]['location']);
        }
        $result[0]['location'] = $location;
        echo \Core\Router::clearUrl(\ApiHandler\ApiHandlerClass::stringfiyData($result));
       exit;
    }
    if($action === "delete"){
        $result = \ErrorLogger\ErrorLogger::deleteByErrorId($eid);
        $message = $result === true ? "Deleted this error log" : "Failed to delete this error log";
        echo \ApiHandler\ApiHandlerClass::stringfiyData(['msg'=>$message,'status'=>200]);
        exit;
    }
    if($action === "all-delete"){
        $user = \GlobalsFunctions\Globals::user();
        if(!empty($user)){
            $userEmail = $user[0]['mail'];
            $password = $user[0]['password'];
            $pass  =\GlobalsFunctions\Globals::get('password');
            $useremail = \GlobalsFunctions\Globals::get('username');
            if($userEmail === $useremail && password_verify($pass, $password) && $user[0]['role'] === "Admin"){
                $result = \ErrorLogger\ErrorLogger::deleteErrors();
                if($result){
                    echo \ApiHandler\ApiHandlerClass::stringfiyData(['status'=>200,'msg'=>'Cleared']);
                    exit;
                }
            }
        }
        echo \ApiHandler\ApiHandlerClass::stringfiyData(['status'=>404,'msg'=>'failed']);
        exit;
    }

}


