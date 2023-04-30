<?php @session_start();

$inDbRoutes = \Datainterface\Selection::selectAll('routes');
$defaults = (new \RoutesManager\RoutesManager())->tempReaderView();

$temps = [];
$ids = [];
foreach ($inDbRoutes as $key=>$value){
    if(gettype($value) === 'array'){
        if(str_contains($value['view_path_absolute'], 'DefaultViews')){
            continue;
        }else{
            $temps[] = $value;
            $ids[] = $value['rvid'];
        }
    }
}
if(\GlobalsFunctions\Globals::method() === 'POST'){
    $incoming = \ApiHandler\ApiHandlerClass::getPostBody();
    if(!empty($incoming) && (new User\User())->role() === 'Admin'){
        $data = \Datainterface\Selection::selectById('routes',['rvid'=>$incoming['id']]);
        if(!empty($data)){
            $data = $data[0];
            $defaults = (new \RoutesManager\RoutesManager())->tempReaderView();
            $url = $data['view_url'];
            $flag = false;
            foreach ($defaults as $item=>$value){
                if(gettype($value) == 'array'){
                    if($value['view_url'] === $url){
                        $flag = true;
                    }
                }
            }
            if($flag){
                echo \ApiHandler\ApiHandlerClass::stringfiyData(['result'=>true, 'msg'=>'Included Already']);
                exit;
            }
            echo \ApiHandler\ApiHandlerClass::stringfiyData(['result'=>(new \RoutesManager\RoutesManager())->writeInTemps($data), 'msg'=>'Included']);
            exit;
        }

    }
}



$host = \GlobalsFunctions\Globals::protocal().'://'.\GlobalsFunctions\Globals::serverHost().'/'.\GlobalsFunctions\Globals::home();
?>
<section class="container mt-4">
    <div class="m-auto bg-light border-white border">
        <ul class="list-group" id="url-listing" data-ids="<?php echo implode(',',array_values($ids)); ?>" data-host="<?php echo $host; ?>">
            <?php foreach ($temps as $key=>$value): ?>
                <li class="list-group-item mt-2 rounded" id="list-<?php echo $value['rvid']; ?>"><?php echo $value['view_name']; ?>
                    <button class="btn btn-primary text-center text-white float-lg-end" id="btn-<?php echo $value['rvid']; ?>" data-id="<?php echo $value['rvid']; ?>">Include In Production (<?php echo $value['rvid']; ?>)</button>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</section>
<div>
    <script type="application/javascript">
        const ids = document.getElementById('url-listing').getAttribute('data-ids');
        const host = document.getElementById('url-listing').getAttribute('data-host');
        const list = ids.split(',');

        const saveProduction = (data, tag) =>{
            const xhr = new XMLHttpRequest();
            const url = host+'/production?';
            xhr.open('POST',url, true);
            xhr.setRequestHeader('Content-Type','application/json');
            xhr.onload = function (){
                if(this.status === 200){
                  const data = JSON.parse(this.responseText);
                  if(data.result){
                      tag.textContent = data.msg;
                  }
                }
            }
            xhr.send(JSON.stringify(data));

        }

        for (let i = 0; i < list.length; i++){
            document.getElementById('btn-'+list[i]).addEventListener('click', (e)=>{
                let id = e.target.id;
                const idlist = id.split('-');
                id = idlist[idlist.length - 1];
                const data = {id};
                saveProduction(data,document.getElementById('btn-'+id));

            })
        }
    </script>
</div>
