<?php
ini_set('display_errors','Off');
header('Content-Type: text/html; charset=utf-8');
session_start();
?>
<div class="btn-group"style="text-align: center;">
    <br>....UA<br><br>
    <form action="/test_rossvn/check_drugs.php" method="post"><input type="hidden" name="222" type="text" value="drug"><input type="submit"value="Обновить лекарства" ></form><br>
    <form action="/test_rossvn/check_vitam.php" method="post"><input type="hidden" name="222" type="text" value="vitam"><input type="submit"value="Обновить витамины и БАДы" ></form>

</div>
<?php ini_set('display_errors','On');
require_once 'simple_html_dom.php';
require_once 'function_vitam.php';
require_once 'function.php';

if ($_POST['222']=="vitam"||$_SESSION['ss']==1||!empty($_POST["num"])||$_POST["num"]=== "0"||$_POST['back']=="777"){

    sqlConn();

   if($_SESSION['drug']=1&&$_SESSION['vitam']!=1||$_POST['222']=="vitam") {
        translit2($str);
        translit7($str);
        translit($str);
        translit1($str);
        transForm($str);
        loadNewVitam();
        unset ($_SESSION['drug']);
        $_SESSION['vitam']=1;
  }
    $query4 = implode(mysql_fetch_assoc(mysql_query("SELECT id FROM `fds23ddsd_drug_notes_new`")));
    $count=@implode(mysql_fetch_assoc(mysql_query("SELECT COUNT(*) FROM `drugs_new`")));


    $id_dr=@implode(mysql_fetch_assoc(mysql_query("SELECT id FROM drugs_new ORDER BY id ASC LIMIT 1")));
    $id_dr1=@implode(mysql_fetch_assoc(mysql_query("SELECT id FROM drugs_new ORDER BY id DESC LIMIT 1")));
    ?>
    <?php if(!empty($query4)){?>
        <div class="qqq1" style="width: 1500px; margin-top:50px">
            <p><b>В категории "Витамины и БАДы" найдены новые препараты</b></p>
        </div>


        <?php
        // пагинация
        $pagin=20;                      // количество записей на странице
        if($count>$pagin) {
            $ii=1;
            $id_dr1++;
            if(!empty($_POST["num"])){

                $g= $id_dr + $_POST["num"];  // начальный препарат на новой странице
            }else {
                $g = $id_dr;
            }

            while ($g < $id_dr1):
                if($ii<($pagin+1)) {
                    $qqq = mysql_fetch_assoc(mysql_query("SELECT * FROM `fds23ddsd_drug_notes_new` WHERE id=$g "));
                    $res = $qqq;
                    unset($qqq);

                    ?>
                    <?php  if(!empty( $res)){   ?>
                        <div class="qqq" style="width: 1500px; margin-top:10px">

                            <form method="post" action="/test_rossvn/single_drug.php">
                                <input type="hidden" name="id" type="text" value="<?= $res['id']; ?>">
                                <input type="submit" value="Предварительный просмотр">&nbsp;&nbsp;&nbsp;&nbsp;<a
                                    href="http://www.....ua/medicine/vitamins/<?= $res['id'] ?>">http://www.....ua/medicine/vitamins/<?= $res['id'] ?></a>&nbsp;&nbsp;&nbsp;&nbsp; <?= $res['title'] ?><?php echo $_POST['a'] ?>
                            </form>

                        </div>
                    <?php  }  ?>
                    <?php
                    unset($res);
                    $g++;
                    $ii++;} else {$g = 20000;}
            endwhile;

            $page=substr(($count/$pagin)+1.5,0,1);
            $iii=2;            // номера страниц
            $ddd=$pagin;                     // идентификатор для начальной записи на новой странице
            ?>


            <div style="text-align: center;width: 200px; height: 30px; ">
                <form method="post" action="/test_rossvn/check_vitam.php" style="float: left">
                    <input type="hidden" name="num" type="text" value="0">

                    <h3><p><input type="submit" value="1"></p></h3>
                </form>
                <?php  while($iii<= $page):    ?>

                    <form method="post" action="/test_rossvn/check_vitam.php" style="float: left">
                        <input type="hidden" name="num" type="text" value="<?= $ddd?>">

                        <h3><p><input type="submit" value="<?= $iii?>"></p></h3>
                    </form>
                    <?php  $iii++;$ddd=$ddd+$pagin;endwhile; ?>

            </div>



        <?php }else {

           $id_dr = @implode(mysql_fetch_assoc(mysql_query("SELECT id FROM fds23ddsd_drug_notes_new ORDER BY id ASC LIMIT 1")));
           $id_dr1 = @implode(mysql_fetch_assoc(mysql_query("SELECT id FROM fds23ddsd_drug_notes_new ORDER BY id DESC LIMIT 1")));
            $id_dr1++;
            $g = $id_dr;

            while ($g < $id_dr1):
                $qqq = mysql_fetch_assoc(mysql_query("SELECT * FROM `fds23ddsd_drug_notes_new` WHERE id=$g"));

                $res = $qqq;
                unset($qqq);
                ?>
                <?php  if(!empty( $res)){   ?>
                <div class="qqq" style="width: 1500px; margin-top:10px">

                    <form method="post" action="/test_rossvn/single_drug.php">
                        <input type="hidden" name="id" type="text" value="<?= $res['id']; ?>">
                        <input type="submit" value="Предварительный просмотр">&nbsp;&nbsp;&nbsp;&nbsp;<a
                            href="http://www.....ua/medicine/vitamins/<?= $id_dr ?>">http://www.....ua/medicine/vitamins/<?= $id_dr ?></a>&nbsp;&nbsp;&nbsp;&nbsp; <?= $res['title'] ?><?php echo $_POST['a'] ?>
                    </form>

                </div>
            <?php  }  ?>
                <?php
                unset($res);
                $id_dr++;
                $g++;
            endwhile;

        } ?>




        <div  style="margin: 40px; ">

            <form method="post" action="/test_rossvn/load.php" >
                <input type="hidden" name="id_dr" type="text" value="444">
                <h3><p><input type="submit" value="Загрузить" >&nbsp;все препараты на сайт</p></h3></form>
        </div>
    <?php } else{?>
        <div class="qqq2" style="text-align: center;">
            <p><b><h3>Новых препаратов категории "витамины и БАДы" не найдено</h3></b></p>
        </div>
    <?php }?>
<?php } ?>




