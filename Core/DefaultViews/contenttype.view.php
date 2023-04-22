<?php @session_start();
\Sessions\SessionManager::setSession('flag', false);
$inputs = [];
$relateds = [];

if(!empty(\GlobalsFunctions\Globals::get('total-fields')) && !empty(\GlobalsFunctions\Globals::get('total-field-required'))){
    for($i = 1; $i <= intval(\GlobalsFunctions\Globals::get('total-fields')); $i++){
       $inputs[] = "<div class='border rounded p-3 shadow bg-white text-dark mt-3'>
                        <label for='field-$i'>Enter Field Name ($i)</label>
                        <input type='text' name='field-{$i}' class='form-control'/>
                        <label for='setting-$i'>Choose field type</label>
                        <select name='select-$i' class='form-control' id='setting-$i' required>
                         <option value=''>-- Select Field Setting --</option>
                          <option value='int(11)'>Number</option>
                          <option value='varchar(100)'>Short Text</option>
                          <option value='text'>Long Text</option>
                           <option value='LONGBLOB'>File</option>
                           <option value='varchar(50)'>Select</option>
                          <option value='bool'>True/False</option>
                        </select>
                        <div class='form-group mt-3'>
                           <label for='empty'>This field can not be left empty ?</label>
                           <input type='checkbox' checked name='empty-$i' id='empty'>
                        </div>
                    </div>";
    }
    if(!empty($inputs)){
        \Sessions\SessionManager::setSession('flag', true);
        $content = new \ContentType\ContentType();
        $relateds = $content->makeOptionLinker()->getSelectOptionContentTypeLinks();

    }
}

if(\GlobalsFunctions\Globals::method() === 'POST'){
    if(isset($_POST['save-definition-content-type'])){
        $content = new \ContentType\ContentType();
        $content->sortNewContentFieldsDefinitions($_POST)->saveContentTypeDefinitions();
        echo \Alerts\Alerts::alert('info', $content->message);
    }
}
?>
<?php if(\Sessions\SessionManager::getSession('flag')): ?>
<section class="container w-100 mt-5">
    <div class="container-md bg-light p-5 border rounded shadow">
        <form action="<?php echo \GlobalsFunctions\Globals::url(); ?>" method="POST" class="forms">
            <input type="hidden" name="total-fields"  value="<?php echo \GlobalsFunctions\Globals::get('total-fields'); ?>">
            <div class="form-group">
                <label for="content-type-name">Content Type Name</label>
                <input type="text" name="content-type-name" id="content-type-name" class="form-control"/>
            </div>
            <?php if(!empty($inputs)):?>
                <?php foreach ($inputs as $key=>$value): ?>
                    <?php  echo $value; ?>
                <?php endforeach; ?>
            <?php endif; ?>
            <div class="form-group mt-3">
                <label>This content type related to this (table - column)</label>
                <select name="related" class="form-control">
                    <option value="">-- Select Table - Column Related --</option>
                    <?php if(!empty($relateds)):?>
                        <?php foreach ($relateds as $key=>$value): ?>
                            <?php  echo $value; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            <div class="form-group">
                <button class="btn btn-primary bg-primary border-primary mt-3" value="set" type="submit" name="save-definition-content-type">Save ContentType</button>
            </div>
        </form>
    </div>
</section>
<?php else: ?>
<section class="container w-100 mt-5">
    <div class="container-md bg-light p-5 border rounded shadow">
        <form method="Get" action="#">
            <div class="form-group">
                <label for="total">How many field will be required ?</label>
                <input type="number" name="total-fields" class="form-control w-auto"/>
            </div>
            <div class="form-group">
                <button class="btn btn-warning bg-warning border-warning mt-3" value="set" type="submit" name="total-field-required">Continue</button>
            </div>
        </form>
    </div>
</section>
<?php endif; ?>
