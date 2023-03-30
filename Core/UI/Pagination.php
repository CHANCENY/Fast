<?php

namespace UI;

use GlobalsFunctions\Globals;
use Sessions\SessionManager;

class Pagination
{
   public static function pager(array $data, string $identity, int $max = 20){

       $position = 0;
       $currentindex = Globals::get('currentindex');
       if($currentindex == 0){
           SessionManager::setSession($identity, 0);
       }

       if(!empty(SessionManager::getSession($identity))){
           $position = intval(SessionManager::getSession($identity));
       }
       $returnData = [];
       $next = 0;
       $previous = 0;
       if(isset($_GET['nextindex'])){
           $next = htmlspecialchars(strip_tags($_GET['nextindex']));
           $returnData = array_slice($data,intval($next), $max);
           $next += $max;
           $previous = $next - $max;
       }

       if(isset($_GET['perviousindex'])){
           $previous = htmlspecialchars(strip_tags($_GET['perviousindex']));
           $returnData = array_slice($data,intval($previous), $max);
           $previous -= $max;
           $next = $previous + $max;
       }

       if(!isset($_GET['perviousindex']) && !isset($_GET['nextindex'])){
           $previous = 0;
           $next = $max;
           $returnData = array_slice($data,intval($previous), $max);
           $next = 0;
       }
       SessionManager::setSession($identity, count($data));
       $html = self::html($next, $previous, $identity);
       return ['data'=>$returnData, 'html'=>$html];
   }

   public static function html($nextstart, $previous, $identity){
       $formn = "<form method='GET'><input type='hidden' name='nextindex' value='{$nextstart}'>@buttons@</form>";
       $formp = "<form method='GET'><input type='hidden' name='perviousindex' value='{$previous}'>@buttons@</form>";

       $total = SessionManager::getSession($identity);

       if($nextstart < $total){
           $nextbutton = '<button type="submit" class="d-flex justify-content-start inline-flex items-center px-4 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">Next<svg aria-hidden="true" class="w-5 h-5 ml-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M12.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg></button>';
           $formn = str_replace('@buttons@',$nextbutton, $formn);
       }
       if($nextstart > 0){
           $previousbutton = '<button type="submit" class="d-flex justify-content-end inline-flex items-center px-4 py-2 mr-3 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white"><svg aria-hidden="true" class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M7.707 14.707a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l2.293 2.293a1 1 0 010 1.414z" clip-rule="evenodd"></path></svg>Previous</button>';
           $formp = str_replace('@buttons@',$previousbutton, $formp);
       }
       $formn = str_replace('@buttons@',' ', $formn);
       $formp = str_replace('@buttons@', ' ', $formp);
       return "<div class='flex items-center justify-content-md-between justify-between d-flex flex-row'>{$formp} {$formn}</div>";
   }


}