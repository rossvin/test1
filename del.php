//удаление препарата при просмотре в списке
<?php session_start();?>
<?php
require_once 'function.php';
sqlConn();
del($_POST['id_drug']['id'], $_POST['id_drug']['category']);

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    </head>
<div style="margin-top: 50px; text-align: center;">
    <h2>Препарат удален</h2>
</div>

