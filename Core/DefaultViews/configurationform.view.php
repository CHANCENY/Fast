<?php
@session_start();


if($_SERVER['REQUEST_METHOD'] === "GET"){
    $routes = new RoutesManager\RoutesManager();
    \Sessions\SessionManager::setSession('configview',  \GlobalsFunctions\Globals::findViewByUrl($_GET['view']));
    \Sessions\SessionManager::setSession('configview',  $routes->loadViewByUrl($_GET['view'])->getRoutes()[0]);
}
$view = \Sessions\SessionManager::getSession('configview');

if($_SERVER['REQUEST_METHOD'] === "POST"){
    if(isset($_POST['view-changes'])){
        $routes = new \Core\RouteConfiguration();
       echo \Alerts\Alerts::alert('info', $routes->updateView($_POST, $view['view_url']));
    }
}
if(empty($view)){
    \GlobalsFunctions\Globals::redirect(\GlobalsFunctions\Globals::home());
    exit;
}
?>
<div class="hidden sm:block" aria-hidden="true">
    <div class="py-5">
        <div class="border-t border-gray-200"></div>
    </div>
</div>

<div class="mt-10 sm:mt-0">
    <div class="md:grid md:grid-cols-3 md:gap-6">
        <div class="md:col-span-1">
            <div class="px-4 sm:px-0">
                <h3 class="text-lg font-medium leading-6 text-gray-900"><?php  echo $_SESSION['public_data']['view']['view_name']; ?></h3>
                <p class="mt-1 text-sm text-gray-600"><?php  echo $_SESSION['public_data']['view']['view_description']; ?></p>
                <?php
                if(isset($_SESSION['message']['creationviewform'])){
                    echo $_SESSION['message']['creationviewform'];
                }
                ?>
            </div>
        </div>
        <div class="mt-5 md:col-span-2 md:mt-0">
            <form action="<?php echo $_SESSION['public_data']['view']['view_url']; ?>" method="POST">
                <div class="overflow-hidden shadow sm:rounded-md">
                    <div class="bg-white px-4 py-5 sm:p-6">
                        <div class="grid grid-cols-6 gap-6">
                            <div class="col-span-6 sm:col-span-3">
                                <label for="view-name" class="block text-sm font-medium text-gray-700">View name</label>
                                <input type="text" name="view-name" value="<?php echo $view['view_name']; ?>" id="view-name" autocomplete="given-name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>

                            <div class="col-span-6 sm:col-span-3">
                                <label for="view-url" class="block text-sm font-medium text-gray-700">View url</label>
                                <input type="text" name="view-url" value="<?php echo $view['view_url']; ?>" id="view-url" autocomplete="family-name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>

                            <div class="col-span-6 sm:col-span-4">
                                <label for="path-address" class="block text-sm font-medium text-gray-700">View path (.extension)</label>
                                <?php
                                $list =explode('/', $view['view_path_relative']);
                                $path = end($list);
                                ?>
                                <input type="text" name="path-address" value="<?php echo $path;  ?>" id="path-address" autocomplete="email" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>
                            <div class="col-span-6 sm:col-span-4">
                                <label for="path-address" class="block text-sm font-medium text-gray-700">Is default view</label>
                                <input type="checkbox" name="default" id="path-address" autocomplete="email" class="mt-1">
                            </div>
                            <div class="col-span-6 sm:col-span-3">
                                <label for="accessible" class="block text-sm font-medium text-gray-700">Accessibility</label>
                                <select id="accessible"  name="accessible" autocomplete="country-name" class="mt-1 block w-full rounded-md border border-gray-300 bg-white py-2 px-3 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 sm:text-sm">
                                    <?php
                                    $option = "";
                                     if($view['view_role_access'] === 'public'){
                                         $option = "<option value='public'>Public</option>
                                                    <option value='private'>Private</option>
                                                    <option value='moderator'>Moderator</option>
                                                    <option value='administrator'>Administrator</option>";
                                     }elseif ($view['view_role_access'] === 'private'){
                                         $option = "<option value='private'>Private</option>
                                                   <option value='public'>Public</option>
                                                    <option value='moderator'>Moderator</option>
                                                   <option value='administrator'>Administrator</option>";
                                     }elseif($view['view_role_access'] === "moderator"){
                                         $option = "<option value='moderator'>Moderator</option>
                                                    <option value='private'>Private</option>
                                                   <option value='public'>Public</option>                                        
                                                   <option value='administrator'>Administrator</option>";
                                     }
                                     else{
                                         $option = "<option value='administrator'>Administrator</option>
                                                    <option value='private'>Private</option>
                                                   <option value='public'>Public</option>
                                                   <option value='moderator'>Moderator</option>
                                                   ";
                                     }
                                    ?>
                                    <?php echo $option; ?>
                                </select>
                            </div>

                            <div class="col-span-6">
                                <label for="description" class="block text-sm font-medium text-gray-700">View description</label>
                                <textarea cols="9" rows="9"  name="description" id="description" autocomplete="street-address" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"><?php echo $view['view_description']; ?></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 text-right sm:px-6">
                        <a class="focus:outline-none text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2 dark:bg-red-600 dark:hover:bg-orange-700 dark:focus:ring-orange-900" href="metatags?url=<?php echo $view['view_url']; ?>">Edit Meta tags</a>
                        <a class="focus:outline-none text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 mr-2 mb-2 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-900" href="deleting-views?url=<?php echo $view['view_url']; ?>">Delete</a>
                        <button type="submit" name="view-changes" class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">Save</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="hidden sm:block" aria-hidden="true">
    <div class="py-5">
        <div class="border-t border-gray-200"></div>
    </div>
</div>

