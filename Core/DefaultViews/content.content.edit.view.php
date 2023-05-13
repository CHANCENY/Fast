<?php @session_start();

$form = "";
$keys = [];
if(!empty(\GlobalsFunctions\Globals::get('cid')) && !empty(\GlobalsFunctions\Globals::get('content'))){
    $table = \GlobalsFunctions\Globals::get('content');
    $id = substr($table, 0, 2).'_id';
    $cid = \GlobalsFunctions\Globals::get('cid');
    $data = [];
    try {
        $data = (new \Datainterface\mysql\SelectionLayer())->setTableName($table)->setKeyValue([$id=>$cid])->selectBy()->rows();
    }catch (\Throwable $e){
        if(empty($data)){
            $id = substr($table, 0, 2).'Id';
            $data = (new \Datainterface\mysql\SelectionLayer())->setTableName($table)->setKeyValue([$id=>$cid])->selectBy()->rows();
        }
    }
    if(empty($data)){
        echo \Alerts\Alerts::alert('warning',"Data with id $cid in $table does not exist");
        \GlobalsFunctions\Globals::redirect('content-table');
    }else{
        $keys = array_keys($data[0]);
        \Sessions\SessionManager::setSession('identity',$keys[0]);
        unset($keys[0]);
        unset($keys[count($keys)]);
        \Sessions\SessionManager::setSession('cid', $cid);
        \Sessions\SessionManager::setSession('keys',$keys);
        \Sessions\SessionManager::setSession('table',$table);
        \Sessions\SessionManager::setSession('update-data',$data[0]);
        $form = (new \Datainterface\mysql\SelectionLayer())->setTableName('content_type_form_storage')->setKeyValue(['content_type'=>$table])->selectBy()->rows();
    }
}else{
    if(\GlobalsFunctions\Globals::method() === 'POST'){
        if(isset($_POST[\Sessions\SessionManager::getSession('table').'-update-btn'])){
            $data = [];
            $keys = \Sessions\SessionManager::getSession('keys');
            $dataUpdate = \Sessions\SessionManager::getSession('update-data');
            foreach ($keys as $key=>$value){
                $result = handlerFiles($value);
                if($result === 'nofile'){
                    $data[$value] = $dataUpdate[$value];
                }elseif ($result === false){
                    $data[$value] = \GlobalsFunctions\Globals::post($value);
                    $data[$value] = empty($data[$value]) ? $dataUpdate[$value] : $data[$value];
                }else{
                    $data[$value] = $result;
                }
            }
            if(!empty($data)){
                $cid = \Sessions\SessionManager::getSession('cid');
                $result = \Datainterface\Updating::update(
                        \Sessions\SessionManager::getSession('table'),
                         $data,
                         [\Sessions\SessionManager::getSession('identity')=>$cid]
                );
                if($result){
                    \Sessions\SessionManager::setSession('msg',\Alerts\Alerts::alert('info','Content with id ('.$cid.') updated'));
                }else{
                   \Sessions\SessionManager::setSession('msg',\Alerts\Alerts::alert('warning', 'Failed to update content with id ('.$cid.')'));
                }
                \GlobalsFunctions\Globals::redirect('content-table');
            }
        }
        $form = (new \Datainterface\mysql\SelectionLayer())->setTableName('content_type_form_storage')->setKeyValue(['content_type'=>\Sessions\SessionManager::getSession('table')])->selectBy()->rows();
    }else{
        \GlobalsFunctions\Globals::redirect('content-table');
    }
}


foreach ($form as $key=>$value){
    echo $value['form_layout'];
}

function handlerFiles($value){
    if(empty($_FILES)){
        return false;
    }
    if(isset($_FILES[$value])){
        $filename = $_FILES[$value]['name'];
        $tmp = $_FILES[$value]['tmp_name'];

        $filename = gettype($filename) === 'array' ? $filename[0] : $filename;
        $tmp = gettype($tmp) === 'array' ? $tmp[0] : $tmp;

        if(!empty($filename) && !empty($tmp)){
            return \FileHandler\FileHandler::saveFile($filename,$tmp);
        }else{
            return 'nofile';
        }
    }else{
        return false;
    }
}
?>
<div id="t" data="<?php echo $table; ?>" data-title="<?php  echo $cid; ?>" data-ac="<?php echo \GlobalsFunctions\Globals::url(); ?>"></div>
<script type="application/javascript">
    const id = document.getElementById('t').getAttribute('data');
    const title = document.getElementById('t').getAttribute('data-title');
    const t = document.getElementById(id+'-title-id');
    t.textContent = "Update Content with id ("+title+")";
    t.className = "text-center fs-5 display-4";
    document.getElementById(id+'-btn-id').setAttribute('name',id+'-update-btn');
    document.getElementById('form-'+id).setAttribute('action',document.getElementById('t').getAttribute('data-ac'));

    const inputs = document.querySelectorAll('input');
    for (let i = 0; i < inputs.length; i++){
        if(inputs[i] !== null){
            inputs[i].removeAttribute('required');
        }
    }

    const inputsText = document.querySelectorAll('textarea');
    for (let i = 0; i < inputsText.length; i++){
        if(inputsText[i] !== null){
            inputsText[i].removeAttribute('required');
        }
    }
</script>
