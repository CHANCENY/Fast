<?php @session_start();
$users = \Datainterface\Selection::selectAll('users');

if(!empty(\GlobalsFunctions\Globals::get('q'))){
    $searchfor = \GlobalsFunctions\Globals::get('q');
   $users = \Datainterface\Searching::search($searchfor, 'users');
}

$sortedData = \UI\Pagination::pager($users,'users-pagination',5);
$users = $sortedData['data'];
$pager = $sortedData['html'];
?>

<form class="flex items-center" method="GET" action="<?php echo \GlobalsFunctions\Globals::url(); ?>">
    <label for="simple-search" class="sr-only">Search</label>
    <div class="relative w-full">
        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
            <svg aria-hidden="true" class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path></svg>
        </div>
        <input type="text" name="q" id="simple-search" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5  dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Search" required>
    </div>
    <button type="submit" name="search-user" value="search" class="p-2.5 ml-2 text-sm font-medium text-white bg-blue-700 rounded-lg border border-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
        <span class="sr-only">Search</span>
    </button>
</form>


<div id="alertbox"></div>
<table class="table table-hover">

    <thead>
    <tr>
        <th scope="col">#</th>
        <th scope="col">Firstname</th>
        <th scope="col">Lastname</th>
        <th scope="col">Email</th>
        <th scope="col">Phone</th>
        <th scope="col">Role</th>
        <th scope="col">Blocked</th>
        <th scope="col">Verified</th>
        <th scope="col">Action</th>
    </tr>
    </thead>

    <!--body-->
    <?php if(!empty($users)): ?>
    <tbody>
    <?php foreach ($users as $user): ?>
    <tr>
        <th scope="row"><?php echo $user['uid']; ?></th>
        <td><?php echo $user['firstname']; ?></td>
        <td><?php echo $user['lastname']; ?></td>
        <td><?php echo $user['mail']; ?></td>
        <td><?php echo $user['phone']; ?></td>
        <td>
            <?php

             $selectAdmin = false;
             if($user['role'] === "Admin"){
                 $selectAdmin = true;
             }
            ?>
            <select name="role" id="role<?php echo $user['uid']; ?>">
                <option value="admin-<?php echo $user['uid']; ?>" <?php echo $selectAdmin === true ? 'selected' : ""; ?> >admin</option>
                <option value="user-<?php echo $user['uid']; ?>" <?php echo $selectAdmin === false ? 'selected' : ""; ?> >user</option>
            </select>
        </td>
        <td>
            <?php
              $selectBlocked = false;
              if(!empty($user['blocked'])){
                 $selectBlocked = true;
              }
            ?>
            <select name="block" id="block<?php echo $user['uid']; ?>">
                <option value="block-<?php echo $user['uid']; ?>"  <?php echo $selectBlocked === true ? 'selected' : ""; ?>>unblock</option>
                <option value="unblock-<?php echo $user['uid']; ?>"  <?php echo $selectBlocked === false ? 'selected' : ""; ?>>block</option>
            </select>
        </td>
        <td>
            <?php
               $selectVerified = false;
               if(!empty($user['verified'])){
                   $selectVerified = true;
               }
            ?>
            <select name="verified" id="verified<?php echo $user['uid']; ?>">
                <option value="verified-<?php echo $user['uid']; ?>" <?php echo $selectVerified === true ? 'selected' : ""; ?>>un verify</option>
                <option value="unverified-<?php echo $user['uid']; ?>" <?php echo $selectVerified === false ? 'selected' : ""; ?>>verify</option>
            </select>
        </td>
        <td>
            <div class="inline-flex">
                <button name="delete-button-user" value="<?php echo $user['uid']; ?>" id="delete-button-user<?php echo $user['uid']; ?>" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded-l">
                    Delete
                </button>
            </div>
        </td>
    </tr>
    <?php endforeach; ?>
    </tbody>
    <?php endif; ?>
</table>
<div>
    <?php echo $pager; ?>
</div>
<div>
    <script type="application/javascript">

        const requestSender = (params)=>{
            const url = window.location.protocol+'//'+window.location.hostname+'/users-commands?'+params;
            let xhr = new XMLHttpRequest();
            xhr.open('GET',url, true);
            xhr.setRequestHeader('Content-Type', 'application/json');
            xhr.onload = function (){
                if(this.status === 200){
                    const data = JSON.parse(this.responseText);
                    if(data.status === 200){
                        const box =document.getElementById('alertbox');
                        let div = document.createElement('div');
                        div.className = "alert alert-success";
                        div.id = "current-alert";
                        div.appendChild(document.createTextNode(data.msg));
                        box.appendChild(div);
                        setTimeout(()=>{
                            document.getElementById('current-alert').remove();
                        },4000);
                    }
                }
                if(this.status === 404){
                    const data = JSON.parse(this.responseText);
                    if(data.status === 404){
                        const box =document.getElementById('alertbox');
                        let div = document.createElement('div');
                        div.className = "alert alert-danger";
                        div.id = "current-alert";
                        div.appendChild(document.createTextNode(data.msg));
                        box.appendChild(div);
                        setTimeout(()=>{
                            document.getElementById('current-alert').remove();
                        },4000);
                    }
                }
            }
            xhr.onerror = function (){
                console.log(this.error);
            }
            xhr.send();
        }

        const handlerChangeBlock = (e) =>{
            const valueRecieved = e.target.value;
            if(valueRecieved !== ""){
                let list = valueRecieved.split('-');
                const command = list[0];
                const userId = list[1];
                const parameter = `command=${command}&userid=${userId}`;
                requestSender(parameter);
            }
        }

        const handlerChangeRole = (e)=>{
            const valueRecieved = e.target.value;
            if(valueRecieved !== ""){
                let list = valueRecieved.split('-');
                const command = list[0];
                const userId = list[1];
                const parameter = `command=${command}&userid=${userId}`;
                requestSender(parameter);
            }
        }

        const handlerChangeVerified =(e)=>{
            const valueRecieved = e.target.value;
            if(valueRecieved !== ""){
                let list = valueRecieved.split('-');
                const command = list[0];
                const userId = list[1];
                const parameter = `command=${command}&userid=${userId}`;
                requestSender(parameter);
            }
        }

        const handlerDeleteUser = (e)=>{
            const valueRecieved = e.target.value;
            if(valueRecieved !== ""){
                let list = valueRecieved.split('-');
                const command = 'delete';
                const userId = e.target.value;
                const parameter = `command=${command}&userid=${userId}`;
                requestSender(parameter);
            }
        }

        let selects = document.getElementsByName('block');
        for(let i = 0; i < selects.length; i++){
            selects[i].addEventListener('change', (e)=> {
                handlerChangeBlock(e)
            });
        }

        const selectrole = document.getElementsByName('role');
        for(let i = 0; i < selectrole.length; i++){
            selectrole[i].addEventListener('change', (e)=> {
                handlerChangeRole(e)
            });
        }

        const selectVerified =document.getElementsByName('verified');
        for(let i = 0; i < selectVerified.length; i++){
            selectVerified[i].addEventListener('change', (e)=> {
                handlerChangeVerified(e)
            });
        }

        const deleteButton = document.getElementsByName('delete-button-user');
        for(let i = 0; i < deleteButton.length; i++){
            deleteButton[i].addEventListener('click',(e)=>{
                handlerDeleteUser(e);
            })
        }

    </script>
</div>
