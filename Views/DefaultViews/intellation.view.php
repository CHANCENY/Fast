<?php @session_start();

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    if(isset($_POST['installation-button'])){
      $result = \Installation\Installation::collectDatabaseInformation($_POST);
      if($result === true){
          echo \Alerts\Alerts::alert('info', "Database configured successfully");
          \Sessions\SessionManager::setSession('site', false);
          \Sessions\SessionManager::setSession('sitenew', false);
          \GlobalsFunctions\Globals::redirect('registration');
      }else{
          echo \Alerts\Alerts::alert('warning', $result !== false ? $result : "Failed to installed database");
      }
    }
}
?>
<div class="w-full max-w-xs">
    <form method="POST" action="<?php echo \GlobalsFunctions\Globals::url(); ?>" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="username">
                Database name
            </label>
            <input name="dbname" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" id="username" type="text" placeholder="builder">
        </div>
        <div class="mb-6">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="password">
                Database user
            </label>
            <input name="user" class="shadow appearance-none border border-red-500 rounded w-full py-2 px-3 text-gray-700 mb-3 leading-tight focus:outline-none focus:shadow-outline" id="password" type="text" placeholder="root">
            <p class="text-red-500 text-xs italic">Please choose a user.</p>
        </div>
        <div class="mb-6">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="password">
                Database password
            </label>
            <input name="password" class="shadow appearance-none border border-red-500 rounded w-full py-2 px-3 text-gray-700 mb-3 leading-tight focus:outline-none focus:shadow-outline" id="password" type="password" placeholder="******************">
            <p class="text-red-500 text-xs italic">Please choose a password.</p>
        </div>
        <div class="mb-6">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="password">
                Database host
            </label>
            <input name="host" class="shadow appearance-none border border-red-500 rounded w-full py-2 px-3 text-gray-700 mb-3 leading-tight focus:outline-none focus:shadow-outline" id="password" type="text" placeholder="localhost">
            <p class="text-red-500 text-xs italic">Please choose a host.</p>
        </div>
        <div class="flex items-center justify-between">
            <button  name="installation-button" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="submit">
                Install Now!
            </button>
        </div>
    </form>
    <p class="text-center text-gray-500 text-xs">
        &copy;2020 Acme Corp. All rights reserved.
    </p>
</div>
