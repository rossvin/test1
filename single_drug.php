// страница просмотра инструкции единичного препарата
<?php header('Content-Type: text/html; charset=utf-8');
require_once 'function.php';
session_start();

?>
<!DOCTYPE html>
<head>

</head>

<body>
<div style="margin: 20px; width: 1050px; height: 30px; margin-left: 100px ;">
    <form method="post" action="/test_rossvn/load.php"style=" width: 300px;float: left" >
        <input type="hidden" name="id_drug[id]" type="text" value="<?= $_POST['id'];?>">
        <input type="hidden" name="id_drug[category]" type="text" value="<?php if($_SESSION['drug']==1){echo "drug";}else { echo "vitam";} ?>">
<h4><input type="submit" value="Загрузить">&nbsp;препарат на сайт</h4></form>

    <form method="post" action="/test_rossvn/<?php if($_SESSION['drug']==1){echo "check_drugs.php";}else { echo "check_vitam.php";} ?>"style="float: left;width: 300px" >
        <input type="hidden" name="back" type="text" value="777">
                <h4><input type="submit" value="Назад">&nbsp;к списку препаратов</h4></form>


    <form method="post" action="/test_rossvn/del.php" style="float: left;width: 300px;">
        <input type="hidden" name="id_drug[id]" type="text" value="<?= $_POST['id'];?>">
        <input type="hidden" name="id_drug[category]" type="text" value="<?php if($_SESSION['drug']==1){echo "drug";}else { echo "vitam";} ?>">
        <h4><input type="submit" value="Удалить">&nbsp;препарат</h4></form>
    </div>
<?php

sqlConn();

$id= $_POST['id'];
$i=1;                            // поля с блоками информации инструкции препарата
while($i<35){
    $qqq  = @mysql_fetch_assoc(mysql_query("SELECT df_id,value FROM `fds23ddsd_drug_text_fields_notes_new` WHERE dn_id=$id AND df_id=$i"));
    if(!empty($qqq)){
    $res[]= $qqq;}
    unset($qqq);
    $i++;
}

?>



<?php if(!empty($res)){?>
<div class="container"style="margin-top: 60px; ">
    <?php foreach ($res as $res1):?><!--название -->
    <?php $jjj=$res1['df_id']?>

    <div style="margin-top:20px;"><h3><?php if(!empty($res1['value'])){echo @implode(mysql_fetch_assoc(mysql_query("SELECT title_ru FROM `fds23ddsd_drug_text_fields_list` WHERE id=$jjj")));}?></h3> </div>

    <div class="qqq" style="width: 1000px; height: 100px; 1000px; overflow: auto; border: 1px solid black; ">
     <?php  echo $res1['value']?>
    </div>
    <?php endforeach;?>
<?php } else {echo "Данные по препарату не загружены";}?>


</div>

<div style="margin: 20px; width: 1050px; height: 30px; margin-left: 100px ;">
    <form method="post" action="/test_rossvn/load.php"style=" width: 300px;float: left" >
        <input type="hidden" name="id_drug[id]" type="text" value="<?= $_POST['id'];?>">
        <input type="hidden" name="id_drug[category]" type="text" value="<?php if($_SESSION['drug']==1){echo "drug";}else { echo "vitam";} ?>">
        <h4><input type="submit" value="Загрузить">&nbsp;препарат на сайт</h4></form>

    <form method="post" action="/test_rossvn/<?php if($_SESSION['drug']==1){echo "check_drugs.php";}else { echo "check_vitam.php";} ?>"style="float: left;width: 300px" >
        <input type="hidden" name="back" type="text" value="777">
        <h4><input type="submit" value="Назад">&nbsp;к списку препаратов</h4></form>


    <form method="post" action="/test_rossvn/del.php" style="float: left;width: 300px;">
        <input type="hidden" name="id_drug[id]" type="text" value="<?= $_POST['id'];?>">
        <input type="hidden" name="id_drug[category]" type="text" value="<?php if($_SESSION['drug']==1){echo "drug";}else { echo "vitam";} ?>">
        <h4><input type="submit" value="Удалить">&nbsp;препарат</h4></form>
</div>



</div>
</body>