<?php @session_start();

$inDbRoutes = \Datainterface\Selection::selectAll('routes');
$defaults = (new \RoutesManager\RoutesManager())->tempReaderView();
$msg = null;
$ids = [];

if(\GlobalsFunctions\Globals::method() === 'POST'){
  if(isset($_POST['submit-production'])){
      $id = \GlobalsFunctions\Globals::post('add');
      $data = \Datainterface\Selection::selectById('routes',['rvid'=>$id]);
      $view = $data[0] ?? ['rvid'=>0];
      unset($view['rvid']);
      $list = \Sessions\SessionManager::getSession('added') ?? [];
      if(in_array($view['view_url'], $list)){
          $msg = \Alerts\Alerts::alert('danger', 'View already added');
          goto out;
      }
      $result = (new \RoutesManager\RoutesManager())->production($view);
      if($result){
          $l = \Sessions\SessionManager::getSession('added') ?? [];
          $l[] = $view['view_url'];
          \Sessions\SessionManager::setSession('added', $l);
          $msg = \Alerts\Alerts::alert('info',"View ({$view['view_name']}) added to production views");
      }else{
          $msg = \Alerts\Alerts::alert('warning',"Failed to add view ({$view['view_name']}) to production views");
      }
  }

  if(isset($_POST['config-production'])){
      $result = (new \RoutesManager\RoutesManager())->installerViewProduction();
      if($result){
          $msg = \Alerts\Alerts::alert('info','View imported successfully');
      }else{
          $msg = \Alerts\Alerts::alert('warning','Failed to import views');
      }
  }
}
out:
$added = \Sessions\SessionManager::getSession('added');
$host = \GlobalsFunctions\Globals::protocal().'://'.\GlobalsFunctions\Globals::serverHost().'/'.\GlobalsFunctions\Globals::home();
?>
<section class="container mt-4">
    <div class="m-auto bg-light border-white border">
        <?php echo $msg ?? null ?>
        <ul class="list-group" id="url-listing" data-ids="<?php echo implode(',',array_values($ids)); ?>" data-host="<?php echo $host; ?>">
            <?php foreach ($inDbRoutes as $key=>$value): ?>
               <?php if(!empty($added) && !in_array($value['view_url'], $added)): ?>
                <li class="list-group-item mt-2 rounded" id="list-<?php echo $value['rvid']; ?>"><?php echo strstr($value['view_path_absolute'], 'Core/DefaultViews') ? $value['view_name'].' (Default)' : $value['view_name']; ?>
                    <form method="POST" action="#">
                        <input type="hidden" name="add" value="<?php echo $value['rvid']; ?>">
                        <button type="submit" name="submit-production" class="btn btn-primary bg-primary text-center text-white float-lg-end" id="btn-<?php echo $value['rvid']; ?>" data-id="<?php echo $value['rvid']; ?>">Include In Production (<?php echo $value['rvid']; ?>)</button>
                    </form>
                </li>
            <?php else: ?>
                    <li class="list-group-item mt-2 rounded" id="list-<?php echo $value['rvid']; ?>"><?php echo strstr($value['view_path_absolute'], 'Core/DefaultViews') ? $value['view_name'].' (Default)' : $value['view_name']; ?>
                        <form method="POST" action="#">
                            <input type="hidden" name="add" value="<?php echo $value['rvid']; ?>">
                            <button type="submit" name="submit-production" class="btn btn-primary bg-primary text-center text-white float-lg-end" id="btn-<?php echo $value['rvid']; ?>" data-id="<?php echo $value['rvid']; ?>">Include In Production (<?php echo $value['rvid']; ?>)</button>
                        </form>
                    </li>
            <?php endif; ?>
            <?php endforeach; ?>
        </ul>
        <div class="container mt-5">
            <form method="POST" action="#">
                <input type="hidden" name="config" value="<?php echo uniqid(); ?>">
                <button type="submit" id="config" name="config-production" class="btn btn-danger bg-danger">Update Configuration </button>
            </form>
        </div>
    </div>
</section>