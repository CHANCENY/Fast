<?php @session_start();
$errors = \ErrorLogger\ErrorLogger::errors();
if(!empty(\GlobalsFunctions\Globals::get('q'))){
    $term = \GlobalsFunctions\Globals::get('q');
    $errors = \Datainterface\Searching::search($term, 'errors_logs');
}
$pager = \UI\Pagination::pager($errors,'error-errors-display');
global $HOME;

$hostName = \GlobalsFunctions\Globals::protocal().'://'.\GlobalsFunctions\Globals::serverHost().$HOME;
?>
<input type="hidden" id="input-total" name="input" value="<?php echo count($pager['data']);?>">
<input type="hidden" value="<?php echo $hostName; ?>" id="hostnames">
<div class="relative overflow-x-auto shadow-md sm:rounded-lg">
    <div id="popdiv"></div>
    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
        <tr>
            <th scope="col" class="px-6 py-3">
                Message
            </th>
            <th scope="col" class="px-6 py-3">
                Severity
            </th>
            <th scope="col" class="px-6 py-3">
                EID
            </th>
            <th scope="col" class="px-6 py-3">
                Date
            </th>
            <th scope="col" class="px-6 py-3">
                <span class="sr-only">Details</span>
            </th>
        </tr>
        </thead>
        <tbody id="t-body-data">
        <?php $counter = 0; ?>
        <?php foreach ($pager['data'] as $error=>$value):  ?>
        <tr id="td-<?php echo $value['eid']; ?>" class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
            <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-dark">
                <?php echo substr($value['message'], 0 ,30).'...'; ?>
            </th>
            <td class="px-6 py-4">
                <?php echo $value['severity']; ?>
            </td>
            <td class="px-6 py-4">
                <?php echo $value['eid']; ?>
            </td>
            <td class="px-6 py-4">
                <?php echo date("Y-m-d",$value['eid']); ?>
            </td>
            <td class="px-6 py-4 text-right">
                <a href="<?php echo "#{$value['eid']}"; ?>" id="details-<?php echo $counter; $counter += 1;?>" class="font-medium text-blue-600 dark:text-blue-500 hover:underline">details...</a>
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <div>
        <?php echo $pager['html']; ?>
    </div>
    <div>
        <button id="delete-all-error-logs" class="btn btn-danger w-100 mt-5 mb-5">Clear all error logs!</button>
    </div>
</div>

<div>
    <script type="application/javascript">
        const inputTotal = document.getElementById('input-total');
        const total = inputTotal.value;
        const details = [];

        const popUpWindowBuilder =(data)=>{
            const divs = document.createElement('div');
            divs.className = "container";
            let html = "";
            if(typeof data.location == 'object'){
               html = `<br><strong>Location: </strong>${data.location.file}
                 <br><strong>Line: </strong>&nbsp;&nbsp;${data.location.line}
               `;
            }else{
                html = `<br><strong>Location: </strong>&nbsp;&nbsp;${data.location}`;
            }
            const innerhtml = `<div class="text-center border bg-light rounded p-3"><p><strong>Type: </strong>&nbsp;&nbsp;${data.severity}<br>
                                  <strong>Message:</strong>&nbsp;&nbsp;${data.message}<br>
                                  <strong>Code:</strong>&nbsp;&nbsp;${data.code}<br>
                                   ${html}<br>
                                   <strong>Registered on: </strong>&nbsp;&nbsp;${data.created}
                               </p></div>
                               <div class="text-center mt-3 mb-3 rounded">
                                  <button id="delete-error-log" value="${data.eid}" class="btn btn-primary w-100">Delete</button>
                               </div>`;
           divs.innerHTML = innerhtml;
           const temp = document.getElementById('windows-popping');
           if(temp !== null){
               temp.remove();
           }
            divs.id = 'windows-popping';
            document.getElementById('popdiv').appendChild(divs);
            window.scrollTo({ top: 0, behavior: 'smooth' });
           setTimeout(()=>{
               document.getElementById('windows-popping').remove();
           },20000);

        }
        const requestSender = (eid, action, extras ="") =>{
            const xhr = new XMLHttpRequest();
            let hostName = document.getElementById('hostnames').value;
            if(hostName.endsWith('/')){
                hostName = hostName.slice(0,hostName.length - 1);
            }
            const url = hostName+'/errorhandler?action='+action+'&eid='+eid+"&"+extras;
            xhr.open('GET',url, true);
            xhr.setRequestHeader('Content-Type', 'application/json');
            xhr.onload = function (){
               if(this.status === 200){
                   const data = JSON.parse(this.responseText);
                   if(action === "details"){
                       popUpWindowBuilder(data[0]);
                   }
                   if(action === 'delete'){
                       if(data.status === 200){
                          const td = document.getElementById('td-'+eid);
                          if(td !== null){
                              td.remove();
                          }
                       }
                   }
                   if(action === 'all-delete'){
                       const data = JSON.parse(this.responseText);
                       console.log(data);
                       if(data.status === 200){
                           document.getElementById('t-body-data').remove();
                       }
                   }
               }
            }
            xhr.send();
        }

        const callingDetailsHandler = (url) =>{
            const listing = url.split('#');
            const errorId = listing[listing.length - 1].trim();
             requestSender(errorId, 'details');
        }

        for(let i = 0; i < parseInt(total); i++){
            document.getElementById(`details-${i}`).addEventListener('click',(e)=>{
                callingDetailsHandler(e.target.href);
            })
        }

        const deleteError = ()=>{
            const deleteButton = document.getElementById('delete-error-log');
            if(deleteButton !== null){
                deleteButton.addEventListener('click', (e)=>{
                    requestSender(e.target.value, 'delete');
                })
            }
        }

        setInterval(deleteError, 1000);

        const clearing = document.getElementById('delete-all-error-logs');
        clearing.addEventListener('click',(e)=>{
            let username = prompt('Enter your username:','xxx@gmail.com');
            let password = prompt('Enter your password:','..........');
            if(username !== "xxx@gmail.com" && password !== ".........."){
                const params = `username=${username}&password=${password}`;
                requestSender('none','all-delete',params);
            }
        })

    </script>
</div>

