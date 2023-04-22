<?php @session_start();


if(\GlobalsFunctions\Globals::method() === "GET"){
    if(!empty(\GlobalsFunctions\Globals::get('call'))){
        try{
            $call = \GlobalsFunctions\Globals::get('call');
            $callback = "formFunction\\".$call;
            $result = $callback();
            echo \ApiHandler\ApiHandlerClass::stringfiyData($result);
            exit;
        }catch (\Throwable $throwable){
            echo \ApiHandler\ApiHandlerClass::stringfiyData(['status'=>'error', 'msg'=>$throwable->getMessage()]);
            exit;
        }
    }
}
exit;
?>