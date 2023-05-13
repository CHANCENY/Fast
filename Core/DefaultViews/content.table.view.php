<?php @session_start();

$content = new \ContentType\Content();
$data = $content->tablesFinder()->limit(3)->query()->getContentTypeTableData();
$dataInTable = [];
$table = [];
if(!empty(\GlobalsFunctions\Globals::get('content'))){
    $table = \GlobalsFunctions\Globals::get('content');
    $dataInTable = (new \Datainterface\mysql\SelectionLayer())->setTableName($table)->selectAll()->rows();

}
$keys = [];
$html = "";
if(!empty($dataInTable)){
    $keys = array_keys($dataInTable[0]);
}
?>
<section class="container mt-2">
    <div class="mt-2"><?php echo \Sessions\SessionManager::getSession('msg') ?? null; \Sessions\SessionManager::setSession('msg',null); ?></div>
    <div class="row">
        <div class="col-2 w-auto border-end">
            <ul class="list-group border-0">
                <?php
                  foreach ($data as $key=>$value){
                      echo "<li class='list-group-item mt-1'><a id='{$key}' href='?content={$key}'>{$key}</a></li>";
                  }
                ?>
            </ul>
        </div>
        <div class="col-lg-8">
         <table class="table">
             <thead>
             <tr><?php foreach ($keys as $key=>$value): ?>
                 <th><?php echo $value; ?></th>
                 <?php endforeach; ?>
                 <td>Action</td>
             </tr>
             </thead>
             <tbody>
              <?php foreach($dataInTable as $key=>$value): ?>
                  <?php $id = $value[substr($table,0,2).'_id'] ?? $value[substr($table,0,2).'Id']; ?>
                <tr>
                    <?php for ($j = 0; $j < count($value); $j++): ?>
                        <td><?php echo  htmlentities(substr($value[$keys[$j]], 0, 50)); ?></td>
                    <?php endfor; ?>
                    <td><a href="content-content-edit?cid=<?php echo $id; ?>&content=<?php echo $table; ?>">Edit</a></td>
                </tr>
              <?php endforeach; ?>
             </tbody>
         </table>
        </div>
    </div>
</section>
