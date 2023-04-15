<?php @session_start();
$routerObject = [];
$passing = false;

if(\GlobalsFunctions\Globals::method() === 'POST'){
    $data = \ApiHandler\ApiHandlerClass::getPostBody();
    $routes = new \Core\RouteConfiguration();
    $urlkey = htmlspecialchars(strip_tags($data['view_url'] ?? " "));
    $result = ['status'=>200, 'msg'=>$routes->removeView($data['view_url'])];
    echo \ApiHandler\ApiHandlerClass::stringfiyData($result);
    exit;
}

$base = \GlobalsFunctions\Globals::protocal().'://'.
    \GlobalsFunctions\Globals::serverHost().'/'.
    \GlobalsFunctions\Globals::home();
$backup = $base.'/backup-views';
$restore = $base.'/Core/Temps/viewsfiles.zip';
$url = "";

if(!empty(\GlobalsFunctions\Globals::get('url'))){
    $routes = new \RoutesManager\RoutesManager();
    $url = \GlobalsFunctions\Globals::get('url');
    $routerObject = $routes->loadViewByUrl($url)->getRoutes()[0];
    $passing = true;

}
?>
<?php if(!isset($passing) || $passing === false):?>
<div class="mt-5 p-2 bg-white border-bottom border-dark">
    <h1 class="lead text-danger p-3">
        Access blocked
    </h1>
    <p class="p-3 border-1 bg-danger text-center text-white">This view can only be used via view configuration by clicking delete button</p>
</div>
<?php elseif(\GlobalsFunctions\Globals::method() === 'GET'): ?>
<div class="mt-2 ms-3 p-5 bg-white border-1 rounded bg-light">
    <h2 class="fs-4 text-dark text-center">View information</h2>
    <ul class="list-group list-group-flush list-unstyled">
        <?php foreach ($routerObject as $key=>$value): ?>
          <li class="list-group-item p-1 fs-5">
              <?php $val = $key === "view_timestamp" ? date('d-m-Y H:i:s', $value) : $value; ?>
              <?php echo "{$key}&nbsp;:&nbsp;&nbsp;{$val}" ?>
          </li>
        <?php endforeach; ?>
    </ul>
    <div class="mt-5 p-1 border-1">
        <p class="lead mb-3">Please note that deleting with view may read to system error if view is default view.<br>
            You can restore this view by downloading this backup <a class="text-decoration-underline" href="<?php echo $restore; ?>">Restore views</a><br>
            If this is custom view please make backup first <a class="text-decoration-underline" href="<?php echo $backup; ?>">Backup creation</a><br>
        </p>
        <h2 class="text-danger mt-5 fs-5 border-top border-dark">Are you sure you want to delete this view ?</h2>
        <div id="info-box"></div>
        <div class="mt-5 mb-5">
            <input type="hidden" id="view-url" value="<?php echo $url; ?>">
            <input type="hidden" id="base" value="<?php echo $base; ?>">
            <button id="button-delete-view" class="btn btn-danger text-white text-center">Yes delete this view</button>
            <button id="button-keep-view" class="btn btn-primary text-center text-white">No keep this view</button>
        </div>
    </div>
</div>
<div>
    <script type="application/javascript">
        const buttonDelete = document.getElementById('button-delete-view');
        const buttonKeep = document.getElementById('button-keep-view');
        const urlDelete = document.getElementById('view-url').value;
        const basePath = document.getElementById('base').value;

        if(buttonDelete !== null){
            buttonDelete.addEventListener('click', (e)=>{
                const callUrl = `${basePath}/deleting-views`;
                const ahr = new XMLHttpRequest();
                ahr.open('POST',callUrl, true);
                ahr.setRequestHeader('Content-Type', 'application/json');

                const data = {view_url: urlDelete};
                ahr.onload = function (){
                    if(this.status === 200){
                        const d = JSON.parse(this.responseText);
                        let div = document.createElement('div');
                        if(d.status === 200){
                            div.className = "alert alert-success";
                            div.id = "alert-box";
                        }else{
                            div.className = "alert alert-danger";
                            div.id = "alert-box";
                        }
                        div.appendChild(document.createTextNode(d.msg));
                        document.getElementById('info-box').appendChild(div);
                        setTimeout(()=>{
                            document.getElementById('alert-box').remove();
                            window.location.replace(basePath);
                        },2000);
                    }
                }
                ahr.send(JSON.stringify(data));
            })

        }

        if(buttonKeep !== null){
            buttonKeep.addEventListener('click', (e)=>{
                window.location.replace('my-views');
            })
        }
    </script>
</div>
<?php endif; ?>
