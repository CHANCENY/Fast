<?php @session_start();

if(\GlobalsFunctions\Globals::method() === "POST"){
    $files = \GlobalsFunctions\Globals::files('content');
    $filenames = $files['name'];
    $sizes = $files['size'];
    $tmps = $files['tmp_name'];
    global $MAXFILESIZE;
    $collection = "";
    $flag = false;
    for($i = 0; $i < count($filenames); $i++){
        $filename = $filenames[$i];
        $size = $sizes[$i];
        $tmp_name = $tmps[$i];
        if($size < $MAXFILESIZE){
            $link = \FileHandler\FileHandler::saveFile($filename, $tmp_name);
            $flag = true;
            $collection .= "File name: {$filename}<br>File size: {$size}<br>system_link: {$link}<br><br>";
        }
    }
    if($flag === true){
        echo \Alerts\Alerts::alert('info', $collection);
    }else{
        echo \Alerts\Alerts::alert('warning', 'Failed to upload file or files');
    }
}
?>
<div class="container w-75 mt-5 float-md-end">
    <div class="mt-3 w-50 border rounded shadow p-5">
        <h1 class="fs-1 text-center mb-2">Files form</h1>
        <p class="lead mb-5">This form allows you to upload file into the system</p>
        <form method="POST" action="<?php echo \GlobalsFunctions\Globals::url(); ?>" class="form" enctype="multipart/form-data">
            <div class="form-group mt-4">
                <label for="name">Files: </label>
                <input type="file" name="content[]" class="form-control" multiple>
            </div>
            <button type="submit" class="btn-primary btn bg-primary mt-3 text-center" name="submit-upload-files">Upload now!</button>
        </form>
    </div>
</div>
