<?php

use MiddlewareSecurity\Security;

@session_start();

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers,Access-Control-Allow-Methods,Content-Type,Authorization,X-Requested-With');

$security = new Security();
$user= $security->checkCurrentUser();

if($user === "U-Admin"){
    echo \ApiHandler\ApiHandlerClass::stringfiyData(callingActions(\GlobalsFunctions\Globals::get('command'), \GlobalsFunctions\Globals::get('userid')));
}else{
   echo \ApiHandler\ApiHandlerClass::stringfiyData(['msg'=>"not unthorized"]);
}

function callingActions($command, $userid){
    switch ($command){
        case 'block':
           if(\Datainterface\Updating::update('users',['blocked'=>true], ['uid'=>$userid])){
               return ['status'=>200, 'msg'=>'blocked'];
           }else{
               return ['status'=>404, 'msg'=>'failed'];
           }
        case 'unblock':
            if(\Datainterface\Updating::update('users',['blocked'=>false], ['uid'=>$userid])){
                return ['status'=>200, 'msg'=>'unblocked'];
            }else{
                return ['status'=>404, 'msg'=>'failed'];
            }
        case 'admin':
            if(\Datainterface\Updating::update('users',['role'=>"Admin"], ['uid'=>$userid])){
                return ['status'=>200, 'msg'=>'Role changed to admin'];
            }else{
                return ['status'=>404, 'msg'=>'failed'];
            }
        case 'user':
            if(\Datainterface\Updating::update('users',['role'=>'user'], ['uid'=>$userid])){
                return ['status'=>200, 'msg'=>'Role changed to normal user'];
            }else{
                return ['status'=>404, 'msg'=>'failed'];
            }
        case 'verified':
            if(\Datainterface\Updating::update('users',['verified'=>true], ['uid'=>$userid])){
                return ['status'=>200, 'msg'=>'User is marked verified now'];
            }else{
                return ['status'=>404, 'msg'=>'failed'];
            }
        case 'unverified':
            if(\Datainterface\Updating::update('users',['verified'=>false], ['uid'=>$userid])){
                return ['status'=>200, 'msg'=>'User is marked un verified now'];
            }else{
                return ['status'=>404, 'msg'=>'failed'];
            }
        case 'delete':
            if(\Datainterface\Delete::delete('users',['uid'=>$userid])){
                return ['status'=>200,'msg'=>'User deleted successfully'];
            }else{
                return ['status'=>404,'msg'=>'User failed to delete'];
            }
        default:
            return ['status'=>404, 'msg'=>'default reached action not found'];
    }
}
?>