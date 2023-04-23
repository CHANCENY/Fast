<?php @session_start();

if(\GlobalsFunctions\Globals::method() === "POST"){
    $data = \ApiHandler\ApiHandlerClass::getPostBody();
    if($data['action'] === 'likes'){
        $owner = $data['owner'];
        $post = $data['post'];
        \UI\Comments::saveLikes($owner, $post);
        $count = \UI\Comments::getLikeCount($post);
        echo \ApiHandler\ApiHandlerClass::stringfiyData(['count'=>$count,'action'=>$data['action']]);
        exit;
    }
    exit;
}
if(\GlobalsFunctions\Globals::method() === 'GET'){
    $action = \GlobalsFunctions\Globals::get('action');

    if($action === 'comments'){
        $post = \GlobalsFunctions\Globals::get('post');
        echo \ApiHandler\ApiHandlerClass::stringfiyData(['data'=> \UI\Comments::loadAllCommentByPostId($post),'action'=>
            \GlobalsFunctions\Globals::get('action')]);
        exit;
    }
}
?>