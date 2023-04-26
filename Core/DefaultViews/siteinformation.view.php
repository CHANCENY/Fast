<?php @session_start();

if(\GlobalsFunctions\Globals::method() === "POST"){
    if(!empty(\GlobalsFunctions\Globals::post('site_configuration_edit'))){
        $site = new \Site\Site();
        $result = $site->saveSiteInformation([
                'site_name'=>\GlobalsFunctions\Globals::post('site_name'),
                'site_mail'=> \GlobalsFunctions\Globals::post('site_mail'),
            'site_logo'=> file_get_contents(fiterLogo($_FILES['site_logo'])),
            'site_phone'=>\GlobalsFunctions\Globals::post('site_phone'),
            'site_owner'=>\GlobalsFunctions\Globals::post('site_owner'),
            'site_slogan'=>\GlobalsFunctions\Globals::post('site_slogan')
        ]);

        echo \Alerts\Alerts::alert(
                $result ? 'info' : 'danger',
                $result ? "Site Information configuration saved" :
                    "Site Information saving failed"
        );
        unset($_FILES);
        unset($_POST);
    }
}

function fiterLogo($file){
    $size = $file['size'];
    $filename = $file['name'];
    $tmp = $file['tmp_name'];

    $list = explode('.', $filename);
    if(count($list) > 2) return null;
    if(!in_array(end($list),['jpeg','jpg', 'png', 'PNG', 'JPG', 'JPEG'])) return null;
    if($size > 200000) return null;
    return $tmp;
}

?>

<section class="container w-100 mt-5">
    <div class="constainer w-50 m-auto border rounded">
        <div class="text-center">
            <h2 class="fs-2">Site Information Configuration</h2>
        </div>
        <form action="<?php echo \GlobalsFunctions\Globals::url(); ?>" method="POST" enctype="multipart/form-data" class="p-3">

            <div class="form-group mb-2">
                <label for="site-name">Site Name</label>
                <input type="text" class="form-control" name="site_name" id="site-name"/>
            </div>

            <div class="form-group mb-2">
                <label for="site-logo">Site Logo</label>
                <input type="file" class="form-control" name="site_logo" id="site-logo"/>
            </div>

            <div class="form-group mb-2">
                <label for="site-mail">Site Mail</label>
                <input type="mail" class="form-control" name="site_mail" id="site-mail"/>
            </div>

            <div class="form-group mb-2">
                <label for="site-phone">Site Phone</label>
                <input type="tel" class="form-control" name="site_phone" id="site-phone"/>
            </div>

            <div class="form-group mb-2">
                <label for="site-owner">Site Owner</label>
                <input type="text" class="form-control" name="site_owner" id="site-owner"/>
            </div>

            <div class="form-group mb-2">
                <label for="site-slogan">Site Slogan</label>
                <textarea class="form-control" name="site_slogan" id="site-slogan"></textarea>
            </div>

            <div class="form-group mb-2">
                <input type="submit" class="btn btn-primary w-100" name="site_configuration_edit" id="site-configure" value="Save Edits"/>
            </div>

        </form>
    </div>
</section>
