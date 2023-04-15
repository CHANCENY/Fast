<?php
@session_start();
use Sessions\SessionManager;

/**
 * This file will run once for installations
 */
$form = "";

//handler post

if($_SERVER['REQUEST_METHOD'] === "POST"){

    if(isset($_POST['site-information-continue-button'])){
        $data = [$_POST, "file"=>$_FILES ];
       echo \Alerts\Alerts::alert('info', \Installer\Installer::collectSiteInformation($data));
       \GlobalsFunctions\Globals::redirect('installation');
    }

    if(isset($_POST['site-database-continue-button'])){
     echo \Alerts\Alerts::alert('info', \Installer\Installer::collectDatabaseInformation($_POST));
        \GlobalsFunctions\Globals::redirect('installation');
    }

    if(isset($_POST['site-mail-end-button'])){
       echo \Alerts\Alerts::alert('info', \Installer\Installer::collectEmailPassword($_POST));
        \GlobalsFunctions\Globals::redirect('installation');
    }

}

if(SessionManager::getSession('installer') == null){
    SessionManager::setSession('installer', 'site-info');
}

if(SessionManager::getSession('installer') === 'site-info'){

    $form = '<strong>Site Information</strong>
                <form action="#" method="POST" class="form mt-2" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col">
                            <input type="text" name="sitename" class="form-control" placeholder="Site name" aria-label="First name">
                        </div>
                        <div class="col">
                            <input type="email"  name="sitemail" class="form-control" placeholder="Site mail" aria-label="Last name">
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col">
                            <input type="tel" name="sitephone" class="form-control" placeholder="Site phone" aria-label="First name">
                        </div>
                         <div class="col">
                            <input type="file"  class="form-control" aria-label="Last name">
                            <span>logo</span>
                        </div>
                    </div>
                    <input type="submit" name="site-information-continue-button" class="btn btn-primary mt-3" value="Continue">
                </form>';
}


if (SessionManager::getSession('installer') === 'site-database'){
    $form = '<strong>Site database information</strong>
                <form action="installation" method="POST" class="form mt-2">
                    <div class="row">
                        <div class="col">
                            <input type="text" class="form-control" name="dbname" placeholder="Database name" aria-label="First name">
                        </div>
                        <div class="col">
                            <input type="text" class="form-control" name="dbuser" placeholder="Database user" aria-label="Last name">
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col">
                            <input type="tel" class="form-control" name="dbpassword" placeholder="Database password" aria-label="First name">
                        </div>
                       
                    </div>
                    <input type="submit" name="site-database-continue-button" class="btn btn-primary mt-3" value="Continue">
                </form>';
}


if(SessionManager::getSession('installer') === "site-mail"){
    $form = '<strong>Site Information</strong>
                <form action="installation" method="POST" class="form mt-2">
                    <div class="row">
                        <div class="col">
                            <input type="password" name="mailpassword" class="form-control" placeholder="Mail password" aria-label="Last name">
                        </div>
                    </div>
                    <input type="submit" name="site-mail-end-button" class="btn btn-primary mt-3" value="Installer now!">
                </form>';
}


if(SessionManager::getSession('installer') === "site-done"){
   \Installer\Installer::saveInformation();
   echo \Alerts\Alerts::alert('info', SessionManager::getSession('installationMessage'));
}
?>

<!doctype html>
<html lang="en" class="h-full bg-gray-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <title>Installer</title>
</head>
<body class="h-full" style="background-color: #4a76a8">
<section class="container mt-3">
    <div class="row">
        <div class="col-6 col-sm-4 w-25"></div>
        <div class="col-6 col-sm-4 w-50" style="background-color: whitesmoke; padding: 1em; border-radius: 2px;">
            <h2 class="text-center">Configuration form</h2>
            <p>This form collects all required information that is need to use this builder.</p>
            <ul>
                <li>Site Information</li>
                <li>Site database information</li>
                <li>Site mail configurations</li>
            </ul>
            <p>Fill all above mentioned list you will be able to use this builder without any problem</p>
            <div>
                <?php echo $form; ?>
            </div>
        </div>
        <div class="col-6 col-sm-4 w-auto" style="background-color: #4a76a8"></div>
    </div>
</section>
</body>
</html>
