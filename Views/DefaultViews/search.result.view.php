<?php @session_start();
$columns = [];
if(!empty($results[0])){
    $columns = array_keys($results[0]);
    $view['view_name'] = $view['view_name'] . $searchfor;
}
?>

<?php if(isset($passing)): ?>
<div class="relative overflow-x-auto shadow-md sm:rounded-lg">
    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
        <tr>
            <?php foreach ($columns as $key=>$value): ?>
            <th scope="col" class="px-6 py-3">
               <?php echo $value; ?>
            </th>
            <?php endforeach; ?>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($results as $key=>$value): ?>
        <tr class="bg-white border-b dark:bg-gray-900 dark:border-gray-700">
            <?php foreach ($columns as $keyc=>$valuec): ?>
            <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-dark">
                <?php echo $value[$valuec]; ?>
            </th>
            <?php endforeach; ?>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php else: ?>
<p class="fs-5 text-center mt-5 p-5 border-bottom rounded shadow">
    This view can only be functional properly by calling attachview function in Router class<br>
    With options data array having passing key set to true and result of database output of search result
</p>
<?php endif; ?>

