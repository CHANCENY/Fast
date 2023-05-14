<?php @session_start();
$msg = null;

$m = new \SiteMap\SiteMap();
$config = $m->siteMapConfigs();

if(\GlobalsFunctions\Globals::method() === 'POST'){
    if(isset($_POST['sitemapbtn'])){
       $site = \GlobalsFunctions\Globals::post('sitemap');
       $save = $site === 'on' ? 'enabled' : 'disabled';
       $default = \GlobalsFunctions\Globals::post('default') === 'on' ? "allowed" : "disallowed";
       $priority = \GlobalsFunctions\Globals::post('priority');
       $update = \GlobalsFunctions\Globals::post('update');
       $domains = \GlobalsFunctions\Globals::post('domains');
       $private = \GlobalsFunctions\Globals::post('private') === 'on' ? "allowed" : "disallowed";
       $map = new \SiteMap\SiteMap();
       if($map->config(['enabled'=>$save,'view_default'=>$default,'priority'=>$priority, 'update_check'=>$update,'skipped'=>$domains,'private'=>$private])){
           $msg = \Alerts\Alerts::alert('info', "Site map {$save}");
       }else{
           $msg = \Alerts\Alerts::alert('danger', "Site map {$save}");
       }
    }else{
        $urls = \ApiHandler\ApiHandlerClass::getPostBody();
        $m->savingSiteMapLocs($urls['links'] ?? [])->makeSiteMapFile()->saveSiteMap();
        echo \ApiHandler\ApiHandlerClass::stringfiyData(['status'=>200]);
        exit;
    }
}

$priorities = ['1.0', '0.9', '0.8', '0.7', '0.6', '0.5', '0.4', '0.3', '0.2', '0.1','0.0.'];
$priority = "";
foreach ($priorities as $key=>$pr){
    if($pr === $config[2]){
        $priority .= "<option value='$pr' selected>$pr</option>";
    }else{
        $priority .= "<option value='$pr'>$pr</option>";
    }
}

$updates = ['Always','Hourly','Weekly','Monthly','Yearly','Never'];
$update = "";

foreach ($updates as $key=>$pr){
    if($pr === $config[3]){
        $update .= "<option value='$pr' selected>$pr</option>";
    }else{
        $update .= "<option value='$pr'>$pr</option>";
    }
}

?>
<section class="container mt-3">
    <div class="container-md">
        <div class="m-auto">
           <div class="text-center">
               <h3 class="fs-4">Site Map</h3>
               <p class="lead">You can use this configuration to allow fast to make sitemap for you automatically</p>
           </div>
            <form method="POST" class="form mt-5 w-50 m-auto" action="<?php \GlobalsFunctions\Globals::uri(); ?>">
                <div class="form-group">
                    <?php echo $msg ?? null; ?>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="form-group">
                            <label for="site">Site map enabled/disabled</label>
                            <input type="checkbox" <?php echo $config[0] === 'enabled' ? 'checked' : null; ?> name="sitemap" id="site" class="form-check"/>
                        </div>
                        <div class="form-group">
                            <label>Allow default link/disallow</label>
                            <input type="checkbox" <?php echo $config[1] === 'allowed' ? 'checked' : null; ?> name="default" class="form-check"/>
                        </div>
                        <div class="form-group">
                            <label for="priority">Priority</label>
                            <select class="form-control" name="priority" id="priority">
                                <?php echo $priority; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="update">Priority</label>
                            <select class="form-control" name="update" id="update">
                                <?php echo $update; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col">
                        <div class="form-group">
                            <label for="domains">Domains to Skip (separated by ,)</label>
                            <textarea class="form-control" name="domains" cols="2" rows="2"><?php echo $config[4]; ?></textarea>
                        </div>
                        <div class="form-group mt-3">
                            <label for="private">Private Views allow/disallowed</label>
                            <input  type="checkbox" <?php echo $config[5] === 'allowed' ? 'checked' : null; ?> name="private" class="form-check" id="private">
                        </div>
                        <button name="sitemapbtn" type="submit" class="btn mt-4 d-block w-100 btn-primary bg-primary">Save</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>
