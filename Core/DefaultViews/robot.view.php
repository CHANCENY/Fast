<?php @session_start();

$view = new \Core\RouteConfiguration();

if(!empty(\GlobalsFunctions\Globals::get('action')) && !empty(\GlobalsFunctions\Globals::get('url'))){
  $action = \GlobalsFunctions\Globals::get('action');
  $url = \GlobalsFunctions\Globals::get('url');

  $view = \GlobalsFunctions\Globals::findViewByUrl($url);
  if(empty($view)){
      echo \ApiHandler\ApiHandlerClass::stringfiyData(['status'=>404, 'msg'=>'View not found']);
      exit;
  }

   if(trim($action) == "remove"){
       $result = \Robot\Robot::remove(trim($url));
       $message = $result ? "View: {$view['view_name']} has been removed from Robots.txt file." : "View: {$view['view_name']} failed to be removed from Robots.txt file.";
       echo \ApiHandler\ApiHandlerClass::stringfiyData(['status'=>200, 'action'=>$message]);
   }
   elseif (trim($action) == 'add'){
       $result = \Robot\Robot::add($url,'api-call');
       $message = $result ? "View: {$view['view_name']} has been added to Robots.txt file" : "View: {$view['view_name']} failed to be added to Robots.txt file.";
       echo \ApiHandler\ApiHandlerClass::stringfiyData(['status'=>200, 'action'=>$message]);
       exit;
   }
   elseif (trim($action) == 'disallow'){
       $result = \Robot\Robot::disAllowed($url);
       $message = $result ? "View: {$view['view_name']} has been added to disallowed section of Robots.txt file" : "View: {$view['view_name']} failed to be added to disallowed section of Robots.txt file.";
       echo \ApiHandler\ApiHandlerClass::stringfiyData(['status'=>200, 'action'=>$message]);
       exit;
   }elseif (trim($action) == 'allow'){
       $result = \Robot\Robot::allowed($url);
       $message = $result ? "View: {$view['view_name']} has been added to allowed section of Robots.txt file" : "View: {$view['view_name']} failed to be added to allowed section of Robots.txt file.";
       echo \ApiHandler\ApiHandlerClass::stringfiyData(['status'=>200, 'action'=>$message]);
       exit;
   }else{
       $result = \Robot\Robot::upDateRobotFile();
       $message = $result ? "Robots.txt file updated" : "Robots.txt file failed to update";
       echo \ApiHandler\ApiHandlerClass::stringfiyData(['status'=>200, 'action'=>$message]);
       exit;
   }
   exit;
}

$routes = \Robot\Robot::getAllInRobot();
$urls = [];
foreach ($routes as $key=>$value){
   $urls[] = $value['viewUrl'];
}
$host = \GlobalsFunctions\Globals::protocal().'://'.\GlobalsFunctions\Globals::serverHost().'/'.\GlobalsFunctions\Globals::home();
?>
<input type="hidden" id="urls" value="<?php echo implode(',', $urls); ?>">
<input type="hidden" id="host" value="<?php echo $host; ?>">
<div class="relative overflow-x-auto shadow-md sm:rounded-lg">
    <div id="alert-boxes"></div>
    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
        <tr>
            <th scope="col" class="px-6 py-3">
               View Name
            </th>
            <th scope="col" class="px-6 py-3">
                View Url
            </th>
            <th scope="col" class="px-6 py-3 dark:text-white">
                Add/Remove
            </th>
            <th scope="col" class="px-6 py-3 dark:text-white">
                Allowed/Disallowed
            </th>

        </tr>
        </thead>
        <tbody>
        <?php if(!empty($routes)): ?>
         <?php foreach ($routes as $key=>$value): ?>
        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
            <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-dark">
                <?php echo $value['viewName']; ?>
            </th>
            <td class="px-6 py-4 dark:text-dark">
                <?php echo $value['viewUrl']; ?>
            </td>
            <td class="px-6 py-4">
                <a href="#" id="<?php echo empty($value['status']) ? $value['viewUrl'].'*add' : $value['viewUrl'].'*remove'; ?>" class="font-medium text-blue-600 dark:text-blue-500 hover:underline">
                    <?php
                      echo  empty($value['status']) ? "Add to robot file" : "Remove from robot file";
                    ?>
                </a>
            </td>
            <td class="px-6 py-4">
                <a href="#" id="<?php echo $value['locationInRobot'] == "allowed" ? $value['viewUrl'].'*disallow' : $value['viewUrl'].'*allow'; ?>" class="font-medium text-blue-600 dark:text-blue-500 hover:underline">
                    <?php
                    echo $value['locationInRobot'] == 'allowed' ? "Change to disAllow section" : "Change to allow section";
                    ?>
                </a>
            </td>
        </tr>
         <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
</div>
<div>
    <script type="application/javascript">
        const urlAvailable = document.getElementById('urls').value;
        const listUrls = urlAvailable.split(',');

        const sendingRequest = (params)=>{
            const xhr  = new XMLHttpRequest();
            let url = document.getElementById('host').value+params;
            console.log(url)
            xhr.open('GET', url, true);
            xhr.setRequestHeader('Content-Type','application/json');
            xhr.onload = function (){
                if(this.status === 200){
                console.log(this.responseText);
                    const data = JSON.parse(this.responseText);
                    let div = document.createElement('div');
                    div.className = "bg-orange-100 border-l-4 border-orange-500 text-orange-700 p-4";
                    div.id = "alert-div";
                    div.setAttribute('role','alert');
                    let p1 = document.createElement('p');
                    p1.className = "font-bold";
                    p1.appendChild(document.createTextNode("Action happened alert"));
                    let p2 = document.createElement('p');
                    p2.appendChild(document.createTextNode(data.action));
                    div.appendChild(p1);
                    div.appendChild(p2)
                    const alertBox = document.getElementById('alert-boxes');
                    alertBox.appendChild(div);
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                    setTimeout(()=>{
                        document.getElementById('alert-div').remove();
                    },10000)
                }
            }
            xhr.send();

        }

        let docById = [];
        for (let i = 0; i < listUrls.length; i++){
            if(listUrls[i] !== null){
                if(document.getElementById(`${listUrls[i]}*add`) !== null){
                    docById.push(document.getElementById(`${listUrls[i]}*add`));
                }
                if(document.getElementById(`${listUrls[i]}*allow`) !== null){
                    docById.push(document.getElementById(`${listUrls[i]}*allow`));
                }
                if(document.getElementById(`${listUrls[i]}*disallow`) !== null){
                    docById.push(document.getElementById(`${listUrls[i]}*disallow`));
                }
                if(document.getElementById(`${listUrls[i]}*remove`) !== null){
                    docById.push(document.getElementById(`${listUrls[i]}*remove`));
                }
            }
        }

        for (let i = 0; i < docById.length; i++){
            docById[i].addEventListener('click', (e)=>{
                const id = e.target.id;
                let list = id.split('*');
                const action = list[list.length - 1];
                const url = list[0];
                console.log(action);
                sendingRequest(`/robot?action=${action}&url=${url}`);
            })
        }
    </script>
</div>



