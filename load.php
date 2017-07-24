<?php
header('Content-Type: text/html; charset=utf-8');
session_start();
ini_set('display_errors','On');
require_once 'simple_html_dom.php';
require_once 'function.php';
require_once 'function_vitam.php';
sqlConn();
translit2($str);
translit7($str);
translit($str);
translit1($str);
transForm($str);
if($_POST['id_drug']['category']=='drug'){

    drugsLoad($_POST['id_drug']['id']);
  }
if($_POST['id_dr']== 333){
    drugsLoad(0);

    }

if($_POST['id_drug']['category']=='vitam') {


    vitamLoad($_POST['id_drug']['id']);
}
if($_POST['id_dr']== 444) {
    vitamLoad(0);

}
echo $_POST['id_drug']['category'];
echo $_POST['id_drug']['id'];
?>
<?php if ($_POST['id_drug']['id']==true):?>
<div style="margin-top: 50px; text-align: center;">
   <h2>Препарат загружен на сайт</h2>
    </div>
<?php endif;?>
<?php if ($_POST['id_dr']==true):?>
    <div style="margin-top: 50px; text-align: center;">
        <h2>Препараты загружены на сайт</h2>
    </div>
<?php endif;?>

