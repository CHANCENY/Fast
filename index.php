<?php
namespace index;
require_once  __DIR__.'/vendor/autoload.php';
require_once __DIR__.'/settings.php';


use Core\Router;
use Datainterface\Database;
use ErrorLogger\ErrorLogger;

@session_start();
$router = Router::class;

global $THIS_SITE_ACCESS_LOCK;

if($THIS_SITE_ACCESS_LOCK === false){
    $router::errorPages(401);
}

try{
    Database::installer();
}catch (\Exception $e){
    ErrorLogger::log($e);
    $router::errorPages(500);
    exit;
}
$router::navReader();
?>
<main>
    <div class="mx-auto max-w-7xl py-6 sm:px-6 lg:px-8"><?php $router::router(true); ?></div>
</main>
<?php
$router::footerReader();
?>
