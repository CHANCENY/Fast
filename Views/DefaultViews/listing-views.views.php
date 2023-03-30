<?php
@session_start();
$row = "";

$counter = 1;
foreach ($_SESSION['viewsstorage'] as $viewdata){
    $created = date('m-d-Y', intval($viewdata['view_timestamp']));
    $rowCode = " <tr>
        <th scope='row'>{$counter}</th>
        <td>{$viewdata['view_name']}</td>
        <td>{$viewdata['view_url']}</td>
        <td>{$viewdata['view_role_access']}</td>
        <td>{$created}</td>
        <td>{$viewdata['view_description']}</td>
        <td><a href='configuration-view?view={$viewdata['view_url']}'>Edit</a></td>
    </tr>";
    $row .= $rowCode;
    $counter++;
}
?>
<table class="table table-dark table-striped">
    <caption>List of views</caption>
    <thead>
    <tr>
        <th scope="col">#</th>
        <th scope="col">View Name</th>
        <th scope="col">View Url</th>
        <th scope="col">View Access</th>
        <th scope="col">View Created</th>
        <th scope="col">View Description</th>
        <th scope="col">Configuration</th>

    </tr>
    </thead>
    <tbody>
    <?php echo $row; ?>
    </tbody>
</table>
