<?php @session_start();
$url = "";

$select = "";
if(!empty(\GlobalsFunctions\Globals::get('url'))){
    $url = \GlobalsFunctions\Globals::get('url');
}else{
    $routes = new \Core\RouteConfiguration();
    $views = $routes->getAllViews();
    $option = "";
    foreach ($views as $key=>$value){
        $option .= "<option value='{$value['view_url']}'>{$value['view_name']}</option>";
    }
    $select = "<div class='form-group mt-4'>
             <label for='name'>Views</label>
             <select name='views-collection' class='form-control'>
                <option value=''>-Select view-</option>
              @option
             </select>
           </div>";
    $select = str_replace('@option',$option,$select);
}

if(\GlobalsFunctions\Globals::method() === 'POST'){
    $data = [
        'page_url'=>empty(\GlobalsFunctions\Globals::post('url')) ?
            \GlobalsFunctions\Globals::post('views-collection') : \GlobalsFunctions\Globals::post('url'),
        'name'=>\GlobalsFunctions\Globals::post('name'),
        'content'=>\GlobalsFunctions\Globals::post('content')
    ];
    $result = \Core\RouteConfiguration::metaTags($data, $data['page_url']);
    if($result){
        echo \Alerts\Alerts::alert('info', 'Meta tag created');
    }else{
      echo \Alerts\Alerts::alert('danger', 'Failed to create meta tags');
    }
}

?>
<div class="container w-75 mt-5 float-md-end">
    <div class="mt-3 w-50 border rounded shadow p-5">
        <h1 class="fs-1 text-center mb-2">Meta tags form</h1>
        <p class="lead mb-5">This form allows you to set meta tag of the page you can add images as links</p>
        <form method="POST" action="<?php echo \GlobalsFunctions\Globals::url(); ?>" class="form">
            <div class="form-group">
                <label for="name">Meta Name: </label>
                <input type="text" name="name" class="form-control">
            </div>
            <?php echo $select ?>
            <div class="form-group mt-4">
                <label for="name">Meta Content: </label>
                <textarea cols="10" rows="10" name="content" class="form-control"></textarea>
            </div>
            <input type="hidden" value="<?php echo $url; ?>" name="url">
            <button type="submit" class="btn-primary btn bg-primary mt-3 text-center" name="submit-meta-details">Save Meta tag details!</button>
        </form>
    </div>
</div>

