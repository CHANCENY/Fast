<?php
namespace Manipulator;
@session_start();

use GlobalsFunctions\Globals;
use Modules\CountriesModular;

$url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY);

parse_str($url, $action);

switch ($action['action']){
    case 'pagetitle':
        $url = Globals::get('url');
        //$title = Globals::titleView();
        echo $url;
        exit;
    case 'states':
        echo json_encode(CountriesModular::getStateByCountry($action['country']));
        exit;
    default:
        echo "not found";
}
?>