<?php @session_start();

if(!empty(\GlobalsFunctions\Globals::get('searchfor'))){
    $searchFor = \GlobalsFunctions\Globals::get('searchfor');
    if(!empty(\GlobalsFunctions\Globals::get('q'))){
        $q = \GlobalsFunctions\Globals::get('q');
        $searchResult = \Datainterface\Searching::search($q, $searchFor);
        $data = [
            'results'=>$searchResult,
            'passing'=>true,
            'term'=>$q,
            'searchfor'=>$searchFor
        ];
        \Core\Router::attachView('results',$data);
    }
}else{
    \GlobalsFunctions\Globals::redirect(\GlobalsFunctions\Globals::home());
}
?>