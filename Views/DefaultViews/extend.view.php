<?php @session_start();

if(\GlobalsFunctions\Globals::method() === "POST"){
    if(isset($_POST['install-module-custom'])){

        $namespaces = htmlspecialchars(strip_tags($_POST['classes']));
        $paths = htmlspecialchars(strip_tags($_POST['paths']));

        $files = [];

        for($i = 0; $i < count($_FILES['zip']['name']); $i++){
            array_push($files, $_FILES['zip']['tmp_name'][$i]);
        }

        if(\CustomInstallation\CustomInstallation::saveModules($files) === true){
            if(\CustomInstallation\CustomInstallation::writeComposerFile($namespaces, $paths)){
               echo \Alerts\Alerts::alert('info', "Your custom php files installed successfully please open terminal on root of your project and run composer dumpautoload command");
            }else{
                echo \Alerts\Alerts::alert('danger', "Failed to update your composer.json file");
            }
        }else{
            echo \Alerts\Alerts::alert('warning', "Files uploaded but not all of the please check manually");
        }

    }
}
?>
<form class="w-full max-w-sm text-center ml-48" method="POST" action="<?php echo \GlobalsFunctions\Globals::url(); ?>" enctype="multipart/form-data">
    <h1 class="text-3xl text-center mb-6">Install your modules here</h1>
    <div class="md:flex md:items-center mb-6">
        <div class="md:w-1/3">
            <label class="block text-gray-500 font-bold md:text-right mb-1 md:mb-0 pr-4" for="inline-full-name">
                Namespace
            </label>
        </div>
        <div class="md:w-2/3">
            <input name="classes" class="bg-gray-200 appearance-none border-2 border-gray-200 rounded w-full py-2 px-4 text-gray-700 leading-tight focus:outline-none focus:bg-white focus:border-purple-500" id="inline-full-name" type="text" placeholder="namespaces separated by @" value="">
        </div>
    </div>
    <div class="md:flex md:items-center mb-6">
        <div class="md:w-1/3">
            <label class="block text-gray-500 font-bold md:text-right mb-1 md:mb-0 pr-4" for="inline-password">
                Paths
            </label>
        </div>
        <div class="md:w-2/3">
            <input name="paths" class="bg-gray-200 appearance-none border-2 border-gray-200 rounded w-full py-2 px-4 text-gray-700 leading-tight focus:outline-none focus:bg-white focus:border-purple-500" id="inline-password" type="text" placeholder="Paths separator by @">
        </div>
    </div>

    <div class="md:flex md:items-center mb-6">
        <div class="md:w-1/3">
            <label class="block text-gray-500 font-bold md:text-right mb-1 md:mb-0 pr-4" for="inline-password">
                Zip files
            </label>
        </div>
        <div class="md:w-2/3">
            <input name="zip[]" class="bg-gray-200 appearance-none border-2 border-gray-200 rounded w-full py-2 px-4 text-gray-700 leading-tight focus:outline-none focus:bg-white focus:border-purple-500" id="inline-password" type="file" multiple>
        </div>
    </div>
    <div class="md:flex md:items-center">
        <div class="md:w-1/3"></div>
        <div class="md:w-2/3">
            <button  class="shadow bg-purple-500 hover:bg-purple-400 focus:shadow-outline focus:outline-none text-white font-bold py-2 px-4 rounded" type="submit" name="install-module-custom">
                Install module now!
            </button>
        </div>
    </div>
</form>
