<?php @session_start();
$content  = new \ContentType\ContentType();
$articleForm = $content->loadContentType('sample2');

if(\GlobalsFunctions\Globals::method() === 'POST'){
    print_r($_POST);
}
foreach ($articleForm as $key=>$value){
    echo $value['form_layout'] ?? "";
}
?>