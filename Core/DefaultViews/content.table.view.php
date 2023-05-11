<?php @session_start();

$content = new \ContentType\Content();
$data = $content->tablesFinder()->limit(3)->query()->getContentTypeTableData();

$viewUrl = 'content-content';
$viewUrl = (new \Alias\Alias())->setEndPoint($viewUrl)->generate()->alias();

?>
<section class="container mt-2">
    <div class="row">
        <div class="col-4 border-end">
            <ul class="list-group border-0">
                <?php
                  foreach ($data as $key=>$value){
                      echo "<li class='list-group-item mt-1'><a id='{$key}' href='?content={$key}'>{$key}</a></li>";
                  }
                ?>
            </ul>
        </div>
    </div>
</section>
