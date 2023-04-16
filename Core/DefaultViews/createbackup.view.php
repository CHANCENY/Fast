<?php @session_start();

$backupFiles = \FileHandler\FileHandler::collectionsBackupFile();

$currentcolor = '<svg class="w-4 h-4 mr-1.5 text-green-500 dark:text-green-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>';
$old = '<svg class="w-4 h-4 mr-1.5 text-gray-400 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>';


if($_SERVER['REQUEST_METHOD'] === 'POST'){
    if(isset($_POST['back-up-button-create'])){
        if(empty($_POST['backup-filename'])){
            echo \Alerts\Alerts::alert('danger', "Name is not given. please give you backup file name");
            goto outs;
        }

        if(strpos($_POST['backup-filename'], ' ')){
            echo \Alerts\Alerts::alert('danger',"Name of backup filename should not contain space");
            goto outs;
        }

        \FileHandler\FileHandler::createBackUp(htmlspecialchars(strip_tags($_POST['backup-filename'])).'.zip');
        outs:
    }
}
?>

<h2 class="mb-2 text-lg font-semibold text-2xl text-gray-900 dark:text-black">Backed up views collections</h2>
<div class="grid grid-cols-4 gap-4">
    <div class="w-80">
        <ul class="max-w-md space-y-1 text-gray-500 list-inside dark:text-gray-400">
            <?php if(!empty($backupFiles)): ?>
                <?php foreach ($backupFiles as $file): ?>
                    <li class="flex items-center">
                        <?php echo $file['current'] === true ? $currentcolor : $old; ?>
                        <?php echo $file['filename']; ?>
                    </li>
                <?php endforeach; ?>
            <?php endif; ?>
        </ul>
    </div>

    <div class="space-x-96">
     <div></div>
        <form class="w-full max-w-sm" method="POST" action="<?php echo \GlobalsFunctions\Globals::url(); ?>">
            <h1 class="text-2xl mb-8">Make view back up</h1>
            <div class="flex items-center border-b border-teal-500 py-2">
                <input name="backup-filename" class="appearance-none bg-transparent border-none w-full text-gray-700 mr-3 py-1 px-2 leading-tight focus:outline-none" type="text" placeholder="mybackup" aria-label="Full name">
                <button type="submit" name="back-up-button-create" class="flex-shrink-0 bg-teal-500 hover:bg-teal-700 border-teal-500 hover:border-teal-700 text-sm border-4 text-white py-1 px-2 rounded" type="button">
                    Create backup
                </button>
            </div>
        </form>
    </div>
</div>