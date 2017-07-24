//функции по работе с категорией лекарства
<?php
session_start();
ini_set('display_errors','Off');
function sqlConn(){   // соединение с БД

  //$link = mysql_connect("localhost", "root", "","base");mysql_select_db('base'); // локальное соединение
 $link = mysql_connect("localhost", "", "r1ZobUST",);mysql_select_db('');

    if (!$link) {
        die('Ошибка соединения: ' . mysql_error());
    }
    mysql_set_charset("UTF8");
    mysql_query ("set_client='utf8'");
    mysql_query ("set character_set_results='utf8'");
    mysql_query ("set collation_connection='utf8_general_ci'");
    mysql_query ("SET NAMES utf8");
    return $link;
}

function translit2($str) {
    $ua = array('І', 'Ї', 'і', 'ї', 'и', 'є', 'е','Е','Є');
    $rus = array('И', 'Й', 'и', 'й', 'ы', 'е','э', 'Э','Е');
    return str_replace($rus, $ua, $str);
}
function translit7($str) {
    $rus = array('А', 'Б', 'В', 'Г', 'Д', 'Е', 'Ё', 'Ж', 'З', 'И', 'Й', 'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф', 'Х', 'Ц', 'Ч', 'Ш', 'Щ', 'Ъ', 'Ы', '', 'Э', 'Ю', 'Я' );
    $lat = array( 'а', 'б', 'в', 'г', 'д', 'е', 'ё', 'ж', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ч', 'ш', 'щ', 'ъ', 'ы', 'ь', 'э', 'ю', 'я');
    return str_replace($rus, $lat, $str);
}

function translit($str) {
    $rus = array( 'а', 'б', 'в', 'г', 'д', 'е', 'ё', 'ж', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ч', 'ш', 'щ', 'ъ', 'ы', 'ь', 'э', 'ю', 'я');
    $lat = array( 'a', 'b', 'v', 'g', 'd', 'e', 'e', 'zh', 'z', 'i', 'y', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'h', 'c', 'ch', 'sh', 'sch', 'y', 'y', '', 'e', 'yu', 'ya');
    return str_replace($rus, $lat, $str);
}
function translit1($str){
    $rus = array( 'А', 'Б', 'В', 'Г', 'Д', 'Е', 'Ё', 'Ж', 'З', 'И', 'Й', 'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф', 'Х', 'Ц', 'Ч', 'Ш', 'Щ', 'Ъ', 'Ы', '', 'Э', 'Ю', 'Я');
    $lat = array( 'a', 'b', 'v', 'g', 'd', 'e', 'e', 'zh', 'z', 'i', 'y', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'h', 'c', 'ch', 'sh', 'sch', 'y', 'y', 'y', 'e', 'yu', 'ya');
    return str_replace($rus, $lat, $str);
}

function transForm($str){
    $forms = array( 'мазь', 'раствор', 'гель', 'таблетки', 'лиофилизат', 'крем', 'капсулы', 'субстанция-порошок', 'капли', 'спрей', 'порошок', 'суспензия', 'гранулы', 'сироп','суппозитории', 'концентрат', 'настойка', 'аэрозоль', 'сырье', 'фиточай', 'субстанция-настойка', 'масло', 'полуфабрикат-порошок', 'драже', 'сбор', 'микрогранулы', 'эмульсия',
        'субстанция-масса', 'субстанция-жидкость', 'имплантаты', 'пудра', 'лак', 'мыло','эликсир', 'пластырь', 'пластины', 'пастилки', 'линимент', 'лосьон', 'полуфабрикат-раствор',
        'полуфабрикат', 'субстанция-смесь','субстанция','брикеты','экстракт');
    $val = array( 30, 3, 31, 25, 32, 2, 26, 27, 1, 29, 27, 34, 35, 28, 36, 37, 38, 4, 39, 40, 38, 42, 27, 44, 45, 46, 47, 48, 49, 62, 50, 51, 52,53, 54, 55, 56, 57, 61, 58, 59, 60, 60, 90, 91);
    return str_replace($forms, $val, $str);
}
function clean(){



}
function loadNewDrugs()    // загрузка новых препаратов с сайта-донора во временные таблицы БД сайта
{
    // выборка+ разбивка на группы  ******************************************
    $prec='';$term='';$storage='';$drug_inter='';$overdose='';$dose=''; $collat='';$contr='';$cautions=''; $farm_group1=''; $storage1=''; $manufac1=''; // определение переменных
    $pregn='';$farm_kinet='';$farm_dynamic='';$form='';$indicat_applic='';$farm_act_descr='';$substance='';  $farm_group=[];  $farm_act='';$formExist=0;$violations='';
    $form_name='';$form_z='';
    $form_list= explode( ' ' ,'мазь раствор гель таблетки лиофилизат крем капсулы субстанция-порошок капли спрей порошок суспензия гранулы сироп суппозитории концентрат настойка аэрозоль сырье фиточай субстанция-настойка масло полуфабрикат-порошок драже сбор микрогранулы эмульсия субстанция-масса субстанция-жидкость имплантаты пудра лак мыло эликсир пластырь пластины пастилки линимент лосьон полуфабрикат-раствор полуфабрикат субстанция-смесь субстанция экстракт');

  //********************* очищение временных таблиц
    mysql_query("TRUNCATE TABLE `fds23ddsd_drugs_new`");
    mysql_query("TRUNCATE TABLE `drugs_new`");
    mysql_query("TRUNCATE TABLE `fds23ddsd_drug_notes_new`");
    mysql_query("TRUNCATE TABLE `fds23ddsd_drug_text_fields_notes_new`");

    //********************* поиск и загрузка нового препарата в таблицу drugs_new

    $i = 0;
    $count = 1;
    while ($i < 20000):
        $query1=@implode(mysql_fetch_assoc(mysql_query("SELECT text FROM drugs_instr_orig WHERE id=$i")));//  общая база по лекарствам
        if (empty($query1)) {
            $html = @file_get_html("http://...{$i}/");
            if (!empty($html)) {
                $count=0;
                $e = $html->find("div.l-main", 0);
                $ddd = stripos($e->outertext, "і"); // украинский текст
                $tttw = mysql_real_escape_string($e->outertext);
                if (empty($ddd)) {
                    mysql_query("INSERT INTO drugs_new (`id`,`text`) VALUES ('$i','$tttw')");//запись сырого HTML во временную таблицу
                }
            } else {
               $count++;
            }
            unset($html);
            if ($count == 100) { $i = 20000; }

        }
//***********************  загрузка блоков инструкции препарата во временные таблицы _new
        $sql="SELECT * FROM `drugs_new` where id=$i";
        $result= mysql_query($sql);
        if(!$result) exit("Ошибка - ".mysql_error().", ".$sql);

        $row = implode(mysql_fetch_assoc($result));

        if(!empty($row)):
            $html = str_get_html($row);
//*****************************************************************************

            foreach ($html->find(".medicine__manufacturer a") as $element2):
                if($element2->itemprop=="vendor")
                {
                    $manufac1 = $element2->plaintext;
                }
            endforeach;
            unset ($element2);

            $country=0;
            preg_match ("/\(.*\)/i",$manufac1,$matches);
            $country= trim($matches[0],"(,)");                                                  // страна-производитель

            $manufac= str_replace("Производители:"," ",preg_replace ("/\(.*\)/i" ," ",$manufac1));// производитель 3
            unset($manufac1);
            unset($matches);


            foreach($html->find("div.medicine__drug-effect__col a") as $element2):
                if($element2->itemprop=="active-ingredient")  {                               // действующее вещество 1
                    $substance = $element2->plaintext;

                }
                if($element2->itemprop=="pharmacology-action") {                           // фармакологическое действие 111
                    $farm_act = $element2->plaintext;
                }

            endforeach;

            foreach($html->find("div.medicine__drug-effect__col li") as $element1):
                if($element1->itemprop=="pharmacology-index") {                               // фармакологическая группа 8
                    $farm_group[] = $element1->plaintext;
                }
            endforeach;
            $farm_group1=@implode(',',$farm_group)  ;
            unset($farm_group);
            unset ($element1);

            foreach($html->find("div.announce") as $element):
                if($element->itemprop=="pharmacologyActionDescription"){          // описание фармакологического действия 222
                    $farm_act_descr= $element->plaintext;
                }
                if($element->itemprop=="indications"){                            // показания к применению 12
                    $indicat_applic= $element->innertext;
                }
                if($element->itemprop=="release-form"){                          // форма выпуска 9
                    $form= $element->innertext;
                    $form_z=trim($element->plaintext);
                }

                if($element->itemprop=="pharmacodynamics"){                     // фармакодинамика 21
                    $farm_dynamic= $element->innertext;
                }
                if($element->itemprop=="pharmacokinetics"){                     // фармокинетика 22
                    $farm_kinet= $element->innertext;
                }
                if($element->itemprop=="use-during-pregnancy"){             // использование во время беременности 29
                    $pregn= $element->innertext;
                }
                if($element->itemprop=="contraindications"){                //противопоказания к применению 13
                    $contr= $element->innertext;
                }
                if($element->itemprop=="side-effects"){                     // побочные действия 15
                    $collat= $element->innertext;
                }
                if($element->itemprop=="dosing"){                           // способ применения и дозы 14
                    $dose= $element->innertext;
                }
                if($element->itemprop=="drug-overdose"){                    // передозировка 16
                    $overdose= $element->innertext;
                }
                if($element->itemprop=="drug-interactions"){                // взаимодействие с другими препаратами 17
                    $drug_inter= $element->innertext;
                }
                if($element->itemprop=="precautions"){                     // меры предосторожности при приеме 25
                    $prec= $element->innertext;
                }
                if($element->itemprop=="storage-conditions"){           // условия хранения 32
                    $storage1= $element->innertext;
                }
                if($element->itemprop=="bbd"){                          // срок годности 32
                    $term= $element->innertext;
                }
                if($element->itemprop=="cautions"){                          // особые указания 24
                    $cautions= $element->innertext;
                }
                if($element->itemprop=="use-in-impaired-renal-function"){        // при нарушении функции почек  35
                    $violations= $element->innertext;
                }

            endforeach;

            $compound=str_replace("Состав"," ",stristr( $form ,"Состав" ));   // отделение состава от формы (в некоторых препаратах)
            if(!empty($compound)){
                $form=  stristr( $form ,"Состав",true );
            }

            $form_id=0;
            foreach ($form_list as $list):                                                 // получение id формы выпуска
                preg_match("#(?i)(\b|^)($list)(\B|$)#", translit7($form_z),$matches );
                if (!empty($matches)) {
                    $form_id = transForm($matches[0]);
                    $form_name=$matches[0];
                }
            endforeach;
            unset($matches);



            $e = $html->find("div.l-main h1", 0);
            $name = str_replace(" – описание препарата, инструкция по применению, отзывы", " ", stripcslashes($e->plaintext));  // название препарата
            if($form_id!=0) {
                $name = $name . "(" . $form_name . ")";

            }

            //*************************************************************блок  проверки на пустое значение и наличие украинского текста
            if (strlen($row)> 100):

                if (strripos( $row,"і") == false):


                    //*********************************************************


                    $symbol= array(" ","«","»","+","(",")",",",".","%","#",";","/","№",":","--","--");
                    $alias = trim(str_replace($symbol,"-",translit(translit7($name))),"-");                       // алиас


                    $rrr= array( 'ch', 'sh', 'sch', 'yu', 'ya','zh'); // литерал
                    $lit1=substr($alias, 0, 2);
                    if(in_array ( $lit1 ,$rrr,false)==false){
                        $lit= substr($alias, 0, 1);
                    }else {$lit= substr($alias, 0, 2);}



                    //****************************************************************** запросы к БД на поиск уже имеющихся значений

                    $countries = mysql_fetch_assoc(mysql_query("SELECT id FROM fds23ddsd_countries WHERE title_ru= '$country'"));



//  ********************** запросы на получение последних id
                    $id_drugss= $i;
                    $id_drug_text_fields_notes=@implode(mysql_fetch_assoc(mysql_query("SELECT id FROM fds23ddsd_drug_text_fields_notes_new ORDER BY id DESC LIMIT 1")));   // получение последнего ID конечной таблицы

                        $id_drug_text_fields_notes++;

                    if(!empty($countries)){
                        $countries =implode($countries);
                    }else{ $countries=0;}


                    $jjj=[];                               // проверка на наличие препаратов с одинаковой формой в русскоязычной
                    foreach ($formExist as $list2):

                        $jjj[]=implode($list2);
                    endforeach;
                    $search  =  in_array ( $form_id ,$jjj,false);

                    $storage= $term." ".$storage1;                                                      // обьединение срока и условий хранения


//******************************************************************************* загрузка в БД имедез






                            mysql_query( "INSERT INTO `fds23ddsd_drug_notes_new`(`id`,`drug_id`,`lang_id`,`country_id`,`form_id`,`title`,`literal`) VALUES('$id_drugss','$id_drugss',1,'$countries',$form_id,'$name','$lit')");//
                            mysql_query( "INSERT INTO `fds23ddsd_drugs_new`(`id`,`alias`) VALUES('$id_drugss','$alias')");








                        if(!empty($manufac)){
                            mysql_query( "INSERT INTO `fds23ddsd_drug_text_fields_notes_new`(`id`,`dn_id`,`df_id`,`value`) VALUES('$id_drug_text_fields_notes','$i',3,'$manufac')"); $id_drug_text_fields_notes++;
                            unset ($manufac);
                        }
                        if(!empty($substance)){
                            mysql_query( "INSERT INTO `fds23ddsd_drug_text_fields_notes_new`(`id`,`dn_id`,`df_id`,`value`) VALUES('$id_drug_text_fields_notes','$i',1,'$substance')");$id_drug_text_fields_notes++;
                            unset ($substance);
                        }
                        if(!empty($farm_group1)){
                            mysql_query( "INSERT INTO `fds23ddsd_drug_text_fields_notes_new`(`id`,`dn_id`,`df_id`,`value`) VALUES('$id_drug_text_fields_notes','$i',8,'$farm_group1')");$id_drug_text_fields_notes++;
                            unset($farm_group1);
                        }
                        if(!empty($indicat_applic)){
                            mysql_query( "INSERT INTO `fds23ddsd_drug_text_fields_notes_new`(`id`,`dn_id`,`df_id`,`value`) VALUES('$id_drug_text_fields_notes','$i',12,'$indicat_applic')"); $id_drug_text_fields_notes++;
                            unset ($indicat_applic);
                        }
                        if(!empty($farm_dynamic)||!empty($farm_kinet)){
                            mysql_query( "INSERT INTO `fds23ddsd_drug_text_fields_notes_new`(`id`,`dn_id`,`df_id`,`value`) VALUES('$id_drug_text_fields_notes','$i',20,'')"); $id_drug_text_fields_notes++;
                            unset ($substance);
                        }
                        if(!empty($cautions)){
                            mysql_query( "INSERT INTO `fds23ddsd_drug_text_fields_notes_new`(`id`,`dn_id`,`df_id`,`value`) VALUES('$id_drug_text_fields_notes','$i',24,'$cautions')"); $id_drug_text_fields_notes++;
                            unset ($cautions);
                        }
                        if(!empty($form)){
                            mysql_query( "INSERT INTO `fds23ddsd_drug_text_fields_notes_new`(`id`,`dn_id`,`df_id`,`value`) VALUES('$id_drug_text_fields_notes','$i',9,'$form')");$id_drug_text_fields_notes++;
                            unset ($form);
                        }
                        if(!empty($farm_dynamic)){
                            mysql_query( "INSERT INTO `fds23ddsd_drug_text_fields_notes_new`(`id`,`dn_id`,`df_id`,`value`) VALUES('$id_drug_text_fields_notes','$i',21,'$farm_dynamic')");$id_drug_text_fields_notes++;
                            unset ($farm_dynamic);
                        }
                        if(!empty($farm_kinet)){
                            mysql_query( "INSERT INTO `fds23ddsd_drug_text_fields_notes_new`(`id`,`dn_id`,`df_id`,`value`) VALUES('$id_drug_text_fields_notes','$i',22,'$farm_kinet')"); $id_drug_text_fields_notes++;
                            unset ($farm_kinet);
                        }
                        if(!empty($pregn)){
                            mysql_query( "INSERT INTO `fds23ddsd_drug_text_fields_notes_new`(`id`,`dn_id`,`df_id`,`value`) VALUES('$id_drug_text_fields_notes','$i',29,'$pregn')");$id_drug_text_fields_notes++;
                            unset ($pregn);
                        }
                        if(!empty($contr)){
                            mysql_query( "INSERT INTO `fds23ddsd_drug_text_fields_notes_new`(`id`,`dn_id`,`df_id`,`value`) VALUES('$id_drug_text_fields_notes','$i',13,'$contr')");$id_drug_text_fields_notes++;
                            unset ($contr);
                        }
                        if(!empty($collat)){
                            mysql_query( "INSERT INTO `fds23ddsd_drug_text_fields_notes_new`(`id`,`dn_id`,`df_id`,`value`) VALUES('$id_drug_text_fields_notes','$i',15,'$collat')"); $id_drug_text_fields_notes++;
                            unset ($collat);
                        }
                        if(!empty($dose)){
                            mysql_query( "INSERT INTO `fds23ddsd_drug_text_fields_notes_new`(`id`,`dn_id`,`df_id`,`value`) VALUES('$id_drug_text_fields_notes','$i',14,'$dose')");$id_drug_text_fields_notes++;
                            unset ($dose);
                        }
                        if(!empty($overdose)){
                            mysql_query( "INSERT INTO `fds23ddsd_drug_text_fields_notes_new`(`id`,`dn_id`,`df_id`,`value`) VALUES('$id_drug_text_fields_notes','$i',16,'$overdose')");$id_drug_text_fields_notes++;
                            unset ($overdose);
                        }
                        if(!empty($drug_inter)){
                            mysql_query( "INSERT INTO `fds23ddsd_drug_text_fields_notes_new`(`id`,`dn_id`,`df_id`,`value`) VALUES('$id_drug_text_fields_notes','$i',17,'$drug_inter')");$id_drug_text_fields_notes++;
                            unset ($drug_inter);
                        }
                        if(!empty($storage)){
                            mysql_query( "INSERT INTO `fds23ddsd_drug_text_fields_notes_new`(`id`,`dn_id`,`df_id`,`value`) VALUES('$id_drug_text_fields_notes','$i',32,'$storage')");$id_drug_text_fields_notes++;
                            unset ($storage);
                        }
                        if(!empty($prec)) {
                            mysql_query( "INSERT INTO `fds23ddsd_drug_text_fields_notes_new`(`id`,`dn_id`,`df_id`,`value`) VALUES('$id_drug_text_fields_notes','$i',25,'$prec')");
                            $id_drug_text_fields_notes++;
                            unset ($prec);
                        }
                        if(!empty($violations)){
                            mysql_query( "INSERT INTO `fds23ddsd_drug_text_fields_notes_new`(`id`,`dn_id`,`df_id`,`value`) VALUES('$id_drug_text_fields_notes','$i',35,'$violations')");$id_drug_text_fields_notes++;
                            unset ($violations);

                        }
                        if(!empty($compound)){
                            mysql_query( "INSERT INTO `fds23ddsd_drug_text_fields_notes_new`(`id`,`dn_id`,`df_id`,`value`) VALUES('$id_drug_text_fields_notes','$i',11,'$compound')");$id_drug_text_fields_notes++;
                            unset ($compound);

                        }




                endif;
            endif;

        endif;
        unset($term);
        unset($html);
        unset($form);
        unset($form_id);
        unset($formExist);
        unset($search);
        unset($country);
        unset($countries);
        unset($jjj);

        $i++;

    endwhile;

}

function drugsLoad($id_dr)               // загрузка препаратов на сайт
{

    //  ****************************************** переменные
    $prec = '';
    $term = '';
    $storage = '';
    $drug_inter = '';
    $overdose = '';
    $dose = '';
    $collat = '';
    $contr = '';
    $cautions = '';
    $farm_group1 = '';
    $storage1 = '';
    $manufac1 = ''; // определение переменных
    $pregn = '';
    $farm_kinet = '';
    $farm_dynamic = '';
    $form = '';
    $indicat_applic = '';
    $farm_act_descr = '';
    $substance = '';
    $farm_group = [];
    $farm_act = '';
    $formExist = 0;
    $violations = '';
    $form_name = '';
    $form_z = '';
    $form_list = explode(' ', 'мазь раствор гель таблетки лиофилизат крем капсулы субстанция-порошок капли спрей порошок суспензия гранулы сироп суппозитории концентрат настойка аэрозоль сырье фиточай субстанция-настойка масло полуфабрикат-порошок драже сбор микрогранулы эмульсия субстанция-масса субстанция-жидкость имплантаты пудра лак мыло эликсир пластырь пластины пастилки линимент лосьон полуфабрикат-раствор полуфабрикат субстанция-смесь субстанция экстракт');


    if ($id_dr == 0) {
        $id_dr = @implode(mysql_fetch_assoc(mysql_query("SELECT id FROM drugs_new ORDER BY id ASC LIMIT 1")));
        $id_dr1 = @implode(mysql_fetch_assoc(mysql_query("SELECT id FROM drugs_new ORDER BY id DESC LIMIT 1")));
        $id_dr1++;
    } else {
        $id_dr1 = $id_dr + 1;
    }

//*********************** загрузка препарата на тестовый сайт
    $i = $id_dr;         // значение стартового ID первичной БД
    while ($i < $id_dr1):  // значение заключительного ID первичной БД

        $sql = "SELECT * FROM `drugs_new` where id=$i";//test_rossvn_drugs

        $result = mysql_query($sql);
        if (!$result) exit("Ошибка - " . mysql_error() . ", " . $sql);




        $row = implode(mysql_fetch_assoc($result));
        $res5 = mysql_real_escape_string($row);
        if (!empty($row)):
            $html = str_get_html($row);
//*****************************************************************************

            foreach ($html->find(".medicine__manufacturer a") as $element2):
                if ($element2->itemprop == "vendor") {
                    $manufac1 = $element2->plaintext;
                }
            endforeach;
            unset ($element2);

            $country = 0;
            preg_match("/\(.*\)/i", $manufac1, $matches);
            $country = trim($matches[0], "(,)");                                                  // страна-производитель

            $manufac = str_replace("Производители:", " ", preg_replace("/\(.*\)/i", " ", $manufac1));// производитель 3
            unset($manufac1);
            unset($matches);


            foreach ($html->find("div.medicine__drug-effect__col a") as $element2):
                if ($element2->itemprop == "active-ingredient") {                               // действующее вещество 1
                    $substance = $element2->plaintext;

                }
                if ($element2->itemprop == "pharmacology-action") {                           // фармакологическое действие 111
                    $farm_act = $element2->plaintext;
                }

            endforeach;

            foreach ($html->find("div.medicine__drug-effect__col li") as $element1):
                if ($element1->itemprop == "pharmacology-index") {                               // фармакологическая группа 8
                    $farm_group[] = $element1->plaintext;
                }
            endforeach;
            $farm_group1 = @implode(',', $farm_group);
            unset($farm_group);
            unset ($element1);

            foreach ($html->find("div.announce") as $element):
                if ($element->itemprop == "pharmacologyActionDescription") {          // описание фармакологического действия 222
                    $farm_act_descr = $element->plaintext;
                }
                if ($element->itemprop == "indications") {                            // показания к применению 12
                    $indicat_applic = $element->innertext;
                }
                if ($element->itemprop == "release-form") {                          // форма выпуска 9
                    $form = $element->innertext;
                    $form_z = trim($element->plaintext);
                }
                if ($element->itemprop == "pharmacodynamics") {                     // фармакодинамика 21
                    $farm_dynamic = $element->innertext;
                }
                if ($element->itemprop == "pharmacokinetics") {                     // фармокинетика 22
                    $farm_kinet = $element->innertext;
                }
                if ($element->itemprop == "use-during-pregnancy") {             // использование во время беременности 29
                    $pregn = $element->innertext;
                }
                if ($element->itemprop == "contraindications") {                //противопоказания к применению 13
                    $contr = $element->innertext;
                }
                if ($element->itemprop == "side-effects") {                     // побочные действия 15
                    $collat = $element->innertext;
                }
                if ($element->itemprop == "dosing") {                           // способ применения и дозы 14
                    $dose = $element->innertext;
                }
                if ($element->itemprop == "drug-overdose") {                    // передозировка 16
                    $overdose = $element->innertext;
                }
                if ($element->itemprop == "drug-interactions") {                // взаимодействие с другими препаратами 17
                    $drug_inter = $element->innertext;
                }
                if ($element->itemprop == "precautions") {                     // меры предосторожности при приеме 25
                    $prec = $element->innertext;
                }
                if ($element->itemprop == "storage-conditions") {           // условия хранения 32
                    $storage1 = $element->innertext;
                }
                if ($element->itemprop == "bbd") {                          // срок годности 32
                    $term = $element->innertext;
                }
                if ($element->itemprop == "cautions") {                          // особые указания 24
                    $cautions = $element->innertext;
                }
                if ($element->itemprop == "use-in-impaired-renal-function") {        // при нарушении функции почек  35
                    $violations = $element->innertext;
                }

            endforeach;

            $compound = str_replace("Состав", " ", stristr($form, "Состав"));   // отделение состава от формы (в некоторых препаратах)
            if (!empty($compound)) {
                $form = stristr($form, "Состав", true);
            }

            $form_id = 0;
            foreach ($form_list as $list):                                                 // получение id формы выпуска
                preg_match("#(?i)(\b|^)($list)(\B|$)#", translit7($form_z), $matches);
                if (!empty($matches)) {
                    $form_id = transForm($matches[0]);
                    $form_name = $matches[0];
                }
            endforeach;
            unset($matches);


            $e = $html->find("div.l-main h1", 0);
            $name = str_replace(" – описание препарата, инструкция по применению, отзывы", " ", stripcslashes($e->plaintext));  // название препарата
            if ($form_id != 0) {
                $name = $name . "(" . $form_name . ")";

            }


            //*************************************************************блок  проверки
            if (strlen($row) > 100):

                if (strripos($row, "і") == false):


                    //*********************************************************


                    $symbol = array(" ", "«", "»", "+", "(", ")", ",", ".", "%", "#", ";", "/", "№", ":", "--", "--");
                    $alias = trim(str_replace($symbol, "-", translit(translit7($name))), "-");   // алиас


                    $rrr = array('ch', 'sh', 'sch', 'yu', 'ya', 'zh'); // литерал
                    $lit1 = substr($alias, 0, 2);
                    if (in_array($lit1, $rrr, false) == false) {
                        $lit = substr($alias, 0, 1);
                    } else {
                        $lit = substr($alias, 0, 2);
                    }


                    //****************************************************************** запросы к БД на поиск уже имеющихся значений

                    $countries = mysql_fetch_assoc(mysql_query("SELECT id FROM fds23ddsd_countries WHERE title_ru= '$country'"));
                    $nameExists = @implode(mysql_fetch_assoc(mysql_query("SELECT * FROM `fds23ddsd_drug_notes` WHERE title='$name'")));
                  //  ********************** запросы на получение последних id
                    $id_drugss = @implode(mysql_fetch_assoc(mysql_query("SELECT id FROM fds23ddsd_drugs ORDER BY id DESC LIMIT 1")));         // получение последнего ID конечной таблицы
                    $id_drug_notes = @implode(mysql_fetch_assoc(mysql_query("SELECT id FROM fds23ddsd_drug_notes ORDER BY id DESC LIMIT 1")));  // получение последнего ID конечной таблицы
                    $id_drug_text_fields_notes = @implode(mysql_fetch_assoc(mysql_query("SELECT id FROM fds23ddsd_drug_text_fields_notes ORDER BY id DESC LIMIT 1")));   // получение последнего ID конечной таблицы
                    $id_drug_notes_drug = @implode(mysql_fetch_assoc(mysql_query("SELECT id FROM drugs_instr_orig ORDER BY id DESC LIMIT 1")));// получение последнего ID конечной таблицы

                   $id_drugss++;
                    $id_drug_notes++;
                    $id_drug_text_fields_notes++;
                    $id_drug_notes_drug++;


                    if (!empty($countries)) {
                        $countries = implode($countries);
                    } else {
                        $countries = 0;
                    }



                    $storage = $term . " " . $storage1;                                                      // обьединение срока и условий хранения


//******************************************************************************* загрузка в БД имедез


                  if (empty($nameExists))
                    {

                        mysql_query("INSERT INTO `fds23ddsd_drug_notes`(`id`,`drug_id`,`lang_id`,`country_id`,`form_id`,`title`,`literal`) VALUES('$id_drug_notes','$id_drugss',1,'$countries',$form_id,'$name','$lit')");//
                        mysql_query("INSERT INTO `fds23ddsd_drugs`(`id`,`alias`) VALUES('$id_drugss','$alias')");
                         mysql_query("INSERT INTO `drugs_instr_orig`(`id`,`text`) VALUES('$i','$res5')");






                        if (!empty($manufac)) {
                            mysql_query("INSERT INTO `fds23ddsd_drug_text_fields_notes`(`id`,`dn_id`,`df_id`,`value`) VALUES('$id_drug_text_fields_notes','$id_drug_notes',3,'$manufac')");
                            $id_drug_text_fields_notes++;
                            unset ($manufac);
                        }
                        if (!empty($substance)) {
                            mysql_query("INSERT INTO `fds23ddsd_drug_text_fields_notes`(`id`,`dn_id`,`df_id`,`value`) VALUES('$id_drug_text_fields_notes','$id_drug_notes',1,'$substance')");
                            $id_drug_text_fields_notes++;
                            unset ($substance);
                        }
                        if (!empty($farm_group1)) {
                            mysql_query("INSERT INTO `fds23ddsd_drug_text_fields_notes`(`id`,`dn_id`,`df_id`,`value`) VALUES('$id_drug_text_fields_notes','$id_drug_notes',8,'$farm_group1')");
                            $id_drug_text_fields_notes++;
                            unset($farm_group1);
                        }
                        if (!empty($indicat_applic)) {
                            mysql_query("INSERT INTO `fds23ddsd_drug_text_fields_notes`(`id`,`dn_id`,`df_id`,`value`) VALUES('$id_drug_text_fields_notes','$id_drug_notes',12,'$indicat_applic')");
                            $id_drug_text_fields_notes++;
                            unset ($indicat_applic);
                        }
                        if (!empty($farm_dynamic) || !empty($farm_kinet)) {
                            mysql_query("INSERT INTO `fds23ddsd_drug_text_fields_notes`(`id`,`dn_id`,`df_id`,`value`) VALUES('$id_drug_text_fields_notes','$id_drug_notes',20,'')");
                            $id_drug_text_fields_notes++;
                            unset ($substance);
                        }
                        if (!empty($cautions)) {
                            mysql_query("INSERT INTO `fds23ddsd_drug_text_fields_notes`(`id`,`dn_id`,`df_id`,`value`) VALUES('$id_drug_text_fields_notes','$id_drug_notes',24,'$cautions')");
                            $id_drug_text_fields_notes++;
                            unset ($cautions);
                        }
                        if (!empty($form)) {
                            mysql_query("INSERT INTO `fds23ddsd_drug_text_fields_notes`(`id`,`dn_id`,`df_id`,`value`) VALUES('$id_drug_text_fields_notes','$id_drug_notes',9,'$form')");
                            $id_drug_text_fields_notes++;
                            unset ($form);
                        }
                        if (!empty($farm_dynamic)) {
                            mysql_query("INSERT INTO `fds23ddsd_drug_text_fields_notes`(`id`,`dn_id`,`df_id`,`value`) VALUES('$id_drug_text_fields_notes','$id_drug_notes',21,'$farm_dynamic')");
                            $id_drug_text_fields_notes++;
                            unset ($farm_dynamic);
                        }
                        if (!empty($farm_kinet)) {
                            mysql_query("INSERT INTO `fds23ddsd_drug_text_fields_notes`(`id`,`dn_id`,`df_id`,`value`) VALUES('$id_drug_text_fields_notes','$id_drug_notes',22,'$farm_kinet')");
                            $id_drug_text_fields_notes++;
                            unset ($farm_kinet);
                        }
                        if (!empty($pregn)) {
                            mysql_query("INSERT INTO `fds23ddsd_drug_text_fields_notes`(`id`,`dn_id`,`df_id`,`value`) VALUES('$id_drug_text_fields_notes','$id_drug_notes',29,'$pregn')");
                            $id_drug_text_fields_notes++;
                            unset ($pregn);
                        }
                        if (!empty($contr)) {
                            mysql_query("INSERT INTO `fds23ddsd_drug_text_fields_notes`(`id`,`dn_id`,`df_id`,`value`) VALUES('$id_drug_text_fields_notes','$id_drug_notes',13,'$contr')");
                            $id_drug_text_fields_notes++;
                            unset ($contr);
                        }
                        if (!empty($collat)) {
                            mysql_query("INSERT INTO `fds23ddsd_drug_text_fields_notes`(`id`,`dn_id`,`df_id`,`value`) VALUES('$id_drug_text_fields_notes','$id_drug_notes',15,'$collat')");
                            $id_drug_text_fields_notes++;
                            unset ($collat);
                        }
                        if (!empty($dose)) {
                            mysql_query("INSERT INTO `fds23ddsd_drug_text_fields_notes`(`id`,`dn_id`,`df_id`,`value`) VALUES('$id_drug_text_fields_notes','$id_drug_notes',14,'$dose')");
                            $id_drug_text_fields_notes++;
                            unset ($dose);
                        }
                        if (!empty($overdose)) {
                            mysql_query("INSERT INTO `fds23ddsd_drug_text_fields_notes`(`id`,`dn_id`,`df_id`,`value`) VALUES('$id_drug_text_fields_notes','$id_drug_notes',16,'$overdose')");
                            $id_drug_text_fields_notes++;
                            unset ($overdose);
                        }
                        if (!empty($drug_inter)) {
                            mysql_query("INSERT INTO `fds23ddsd_drug_text_fields_notes`(`id`,`dn_id`,`df_id`,`value`) VALUES('$id_drug_text_fields_notes','$id_drug_notes',17,'$drug_inter')");
                            $id_drug_text_fields_notes++;
                            unset ($drug_inter);
                        }
                        if (!empty($storage)) {
                            mysql_query("INSERT INTO `fds23ddsd_drug_text_fields_notes`(`id`,`dn_id`,`df_id`,`value`) VALUES('$id_drug_text_fields_notes','$id_drug_notes',32,'$storage')");
                            $id_drug_text_fields_notes++;
                            unset ($storage);
                        }
                        if (!empty($prec)) {
                            mysql_query("INSERT INTO `fds23ddsd_drug_text_fields_notes`(`id`,`dn_id`,`df_id`,`value`) VALUES('$id_drug_text_fields_notes','$id_drug_notes',25,'$prec')");
                            $id_drug_text_fields_notes++;
                            unset ($prec);
                        }
                        if (!empty($violations)) {
                            mysql_query("INSERT INTO `fds23ddsd_drug_text_fields_notes`(`id`,`dn_id`,`df_id`,`value`) VALUES('$id_drug_text_fields_notes','$id_drug_notes',35,'$violations')");
                            $id_drug_text_fields_notes++;
                            unset ($violations);

                        }
                        if (!empty($compound)) {
                            mysql_query("INSERT INTO `fds23ddsd_drug_text_fields_notes`(`id`,`dn_id`,`df_id`,`value`) VALUES('$id_drug_text_fields_notes','$id_drug_notes',11,'$compound')");
                            $id_drug_text_fields_notes++;
                            unset ($compound);

                        }
                    }
                    endif;

              endif;

        endif;
        unset($term);
        unset($html);
        unset($form);
        unset($form_id);
        unset($formExist);
        unset($search);
        unset($country);
        unset($countries);
        unset($jjj);
//*****************  удалени препарата со списка поиска после загрузки на сайт
        mysql_query(" DELETE FROM `fds23ddsd_drug_notes_new` WHERE id=$i ");
        mysql_query(" DELETE FROM `drugs_new`  WHERE id=$i ");
        mysql_query(" DELETE FROM `fds23ddsd_drugs_new` WHERE id=$i ");
        mysql_query(" DELETE FROM `fds23ddsd_drug_text_fields_notes_new` WHERE id=$i ");
        $i++;
    endwhile;

    $_SESSION['ss'] = 1;
    header( "refresh:2;url=/test_rossvn/check_drugs.php" );// возврат на страницу списка
}

function del($del_id,$category){    //   удаление отдельного препарата

    mysql_query( "INSERT INTO `drugs_instr_orig`(`id`,`text`) VALUES('$del_id','User deleted')");
    mysql_query("DELETE FROM  `fds23ddsd_drug_notes_new` WHERE  id=$del_id");
    mysql_query("DELETE FROM  `drugs_new` WHERE  id=$del_id");
    mysql_query("DELETE FROM  `fds23ddsd_drugs_new` WHERE  id=$del_id");
    mysql_query("DELETE FROM  `fds23ddsd_drug_text_fields_notes_new` WHERE  dn_id=$del_id");
    $query2  = mysql_fetch_assoc(mysql_query("SELECT * FROM `fds23ddsd_drug_notes_new`"));// наличие незагруженных препаратов для определения сессии
    if(!empty($query2)) {
        $_SESSION['ss'] = 1;
    } else {$_SESSION['ss'] = 0;}
    if($category=='vitam') {
        header("refresh:2;url=/test_rossvn/check_vitam.php");
    } else {header("refresh:2;url=/test_rossvn/check_drugs.php");}
}