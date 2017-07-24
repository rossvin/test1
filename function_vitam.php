//функции по работе с категорией витамины
<?php
session_start();
ini_set('display_errors','Off');
require_once 'simple_html_dom.php';


function loadNewVitam()  // загрузка новых препаратов во временные таблицы
{
//   ****************************************** переменные
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
    $manufac1 = '';
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
    $form_list = explode(' ', 'мазь раствор гель таблетки лиофилизат крем капсулы субстанция-порошок капли спрей порошок суспензия гранулы сироп суппозитории концентрат настойка аэрозоль сырье фиточай субстанция-настойка масло полуфабрикат-порошок драже сбор микрогранулы эмульсия субстанция-масса субстанция-жидкость имплантаты пудра лак мыло эликсир пластырь пластины пастилки линимент лосьон полуфабрикат-раствор полуфабрикат субстанция-смесь субстанция экстракт');
//******************** очистка временных таблиц
    mysql_query("TRUNCATE TABLE `fds23ddsd_drugs_new`");
    mysql_query("TRUNCATE TABLE `drugs_new`");
    mysql_query("TRUNCATE TABLE `fds23ddsd_drug_notes_new`");
    mysql_query("TRUNCATE TABLE `fds23ddsd_drug_text_fields_notes_new`");


    $i = 0;         // значение стартового ID первичной БД
    $count = 1;
    while ($i < 22000):  // значение заключительного ID первичной БД
        $query1=@implode(mysql_fetch_assoc(mysql_query("SELECT text FROM vitamins_instr_orig WHERE id=$i")));//  изменить таблицу на vitamins_instr_orig не было ли таких препаратов раньше

      //*************************************поиск и загрузка новых препаратов во временную таблицу
       if ( empty($query1)) {
            $html = @file_get_html("http://....ua/medicine/vitamins/{$i}/");
            if (!empty($html)) {
                $count=0;
                $e = $html->find("div.l-main", 0);
                $ddd = stripos($e->outertext, "і");
                $tttw = mysql_real_escape_string($e->outertext);
                if (empty($ddd)) {
                    mysql_query("INSERT INTO drugs_new (`id`,`text`) VALUES ('$i','$tttw')");
                }
            } else {
                $count++;
            }
            unset($html);
            if ($count == 100) {
                $i = 22000;
            }
        }


//***************************************************** загрузка блоков инструкции во временные таблицы

    $sql="SELECT * FROM `drugs_new` where id=$i";
    $result= mysql_query($sql);
    if(!$result) exit("Ошибка - ".mysql_error().", ".$sql);

    $row = implode(mysql_fetch_assoc($result));

    if(!empty($row)):
        $html = str_get_html($row);
//*****************************************************************************
        foreach ($html->find('div[style]',3) as $element1) {
            $substance[] = $element1->outertext;
        }

        preg_match("/<a name=\"sideEffects\">(.+?)<\/div>/i",implode($substance),$matches); // побочные действия
        $collat = trim(strip_tags(str_replace("Побочные действия"," ",$matches[0]),'<br>'));
        unset ($matches);
        preg_match("/<a name=\"contraindications\">(.+?)<\/div>/i",implode($substance),$matches); // противопоказания
        $contr = trim(strip_tags(str_replace("Противопоказания к применению"," ",$matches[0]),'<br>'));
        unset ($matches);
        preg_match("/<a name=\"dosing\">(.+?)<\/div>/i",implode($substance),$matches); // способ применения и дозы
        $dose = trim(strip_tags(str_replace("Способ применения и дозы"," ",$matches[0]),'<br>'));
        unset ($matches);
        preg_match("/<a name=\"storageConditions\">(.+?)<\/div>/i",implode($substance),$matches); // условия хранения
        $storage1 = trim(strip_tags(str_replace("Условия хранения"," ",$matches[0]),'<br>'));
        unset ($matches);
        preg_match("/<a name=\"bbd\">(.+?)<\/div>/i",implode($substance),$matches); // срок хранения
        $term = trim(strip_tags(str_replace("Срок годности"," ",$matches[0]),'<br>'));
        unset ($matches);
        preg_match("/<a name=\"compound\">(.+?)<\/div>/i",implode($substance),$matches); // состав  11
        $compound = trim(strip_tags(str_replace("Состав"," ",$matches[0]),'<br>'));
        unset ($matches);
        preg_match("/<a name=\"releaseForm\">(.+?)<\/div>/i",implode($substance),$matches); // форма выпуска
        $form = trim(strip_tags(str_replace("Форма выпуска"," ",$matches[0]),'<br>'));
        unset ($matches);
        preg_match("/<a name=\"indications\">(.+?)<\/div>/i",implode($substance),$matches); // показания к применению
        $indicat_applic = trim(strip_tags(str_replace("Показания к применению"," ",$matches[0]),'<br>'));
        unset ($matches);
        preg_match("/<a name=\"useDuringPregnancy\">(.+?)<\/div>/i",implode($substance),$matches); // прием при беременности
        $pregn = trim(strip_tags(str_replace("Использование во время беременности"," ",$matches[0]),'<br>'));
        unset ($matches);
        preg_match("/<a name=\"drugOverdose\">(.+?)<\/div>/i",implode($substance),$matches); // передозировка
        $overdose = trim(strip_tags(str_replace("Передозировка"," ",$matches[0]),'<br>'));
        unset ($matches);
        preg_match("/<a name=\"drugInteractions\">(.+?)<\/div>/i",implode($substance),$matches);                    // взаимодействие с другими препаратами
        $prec = trim(strip_tags (str_replace("Взаимодействия витамина с другими препаратами"," ",$matches[0]),'<br>'));
        unset ($matches);
        preg_match("/<a name=\"precautions\">(.+?)<\/div>/i",implode($substance),$matches);                        // меры предосторожности при приеме 25
        $drug_inter = trim(strip_tags (str_replace("Меры предосторожности при приеме"," ",$matches[0]),'<br>'));
        unset ($matches);
        unset ($substance);


        foreach ($html->find("div.l-main p") as $element2):
            if($element2->class=="medicine__manufacturer indent")
            {
                $manufac1 = $element2->plaintext;
            }
        endforeach;
        unset ($element2);

        $country=0;
        preg_match ("/\(.*\)/i",$manufac1,$matches);
        $country= trim($matches[0],"(,)");                                                  // страна-производитель

        $form_id=0;
        foreach ($form_list as $list):                                                 // получение id формы выпуска
            preg_match("#(?i)(\b|^)($list)(\B|$)#", translit7($form),$matches );
            if (!empty($matches)) {
                $form_id = transForm($matches[0]);
                $form_name=$matches[0];
            }
        endforeach;
        unset($matches);


        $manufac= trim(str_replace("Производитель:"," ",preg_replace ("/\(.*\)/i" ," ",$manufac1)));// производитель 3
        unset($manufac1);
        unset($matches);
        $e = $html->find("div.l-main h1", 0);
        $name = str_replace(" - описание и инструкция по применению", " ", $e->plaintext);  // название препарата
        if($form_id!=0) {
            $name = $name . "(" . $form_name . ")";

        }

        //*************************************************************блок  проверки (не пустой и язык)
        if (strlen($row)> 100):

            if (strripos($row,"і") == false):


                //*********************************************************


                $symbol= array(" ","«","»","+","(",")",",",".","%","#",";","/","№",":","--","--");
                $alias = trim(str_replace($symbol,"-",translit(translit7($name))),"-");   // алиас
                $rrr= array( 'ch', 'sh', 'sch', 'yu', 'ya','zh'); // литерал
                $lit1=substr($alias, 0, 2);
                if(in_array ( $lit1 ,$rrr,false)==false){
                    $lit= substr($alias, 0, 1);
                }else {$lit= substr($alias, 0, 2);}


                //****************************************************************** запросы к БД на поиск уже имеющихся значений

                $countries = mysql_fetch_assoc(mysql_query("SELECT id FROM fds23ddsd_countries WHERE title_ru= '$country'"));
                $nameExists  = @implode(mysql_fetch_assoc(mysql_query("SELECT id FROM `vitamins_instr_orig` WHERE id='$i'")));// проверка наличея препарата на сайте(дубля)
                 $formExist = mysql_num_rows(mysql_query( "SELECT form_id FROM fds23ddsd_drug_notes_new WHERE title='$name'AND form_id='$form_id'"));

//  ********************** запросы на получение последних id
                $id_drugss=$i;
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


                    if (empty($nameExists))
                    {

                       mysql_query( "INSERT INTO `fds23ddsd_drug_notes_new`(`id`,`drug_id`,`lang_id`,`country_id`,`form_id`,`title`,`literal`) VALUES('$id_drugss','$id_drugss',1,'$countries',$form_id,'$name','$lit')");//
                        mysql_query( "INSERT INTO `fds23ddsd_drugs_new`(`id`,`alias`) VALUES('$id_drugss','$alias')");

                    }



                if($i==implode(mysql_fetch_assoc(mysql_query("SELECT id FROM fds23ddsd_drug_notes_new ORDER BY id DESC LIMIT 1")))):


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

function vitamLoad($id_dr){  // загрузка препаратов на сайт





// выборка+ разбивка на группы  ******************************************
$prec='';$term='';$storage='';$drug_inter='';$overdose='';$dose=''; $collat='';$contr='';$cautions=''; $farm_group1=''; $storage1=''; $manufac1=''; // определение переменных
$pregn='';$farm_kinet='';$farm_dynamic='';$form='';$indicat_applic='';$farm_act_descr='';$substance='';  $farm_group=[];  $farm_act='';$formExist=0;$violations='';
$form_name='';
$form_list= explode( ' ' ,'мазь раствор гель таблетки лиофилизат крем капсулы субстанция-порошок капли спрей порошок суспензия гранулы сироп суппозитории концентрат настойка аэрозоль сырье фиточай субстанция-настойка масло полуфабрикат-порошок драже сбор микрогранулы эмульсия субстанция-масса субстанция-жидкость имплантаты пудра лак мыло эликсир пластырь пластины пастилки линимент лосьон полуфабрикат-раствор полуфабрикат субстанция-смесь субстанция экстракт');

    if   ($id_dr==0){  // загрузка всех препаратов
        $id_dr=@implode(mysql_fetch_assoc(mysql_query("SELECT id FROM drugs_new ORDER BY id ASC LIMIT 1")));
        $id_dr1=@implode(mysql_fetch_assoc(mysql_query("SELECT id FROM drugs_new ORDER BY id DESC LIMIT 1")));
        $id_dr1++;}
    else{    $id_dr1=$id_dr+1;} // загрузка одщиночного препарата


$i= $id_dr;         // значение стартового ID первичной БД
while ($i<$id_dr1):  // значение заключительного ID первичной БД


    $sql="SELECT * FROM `drugs_new` where id=$i";
    $result= mysql_query($sql);
    if(!$result) exit("Ошибка - ".mysql_error().", ".$sql);




    $row = implode(mysql_fetch_assoc($result));
    $res6 = mysql_real_escape_string($row);
    if(!empty($row)):
        $html = str_get_html($row);
//*****************************************************************************
        foreach ($html->find('div[style]',3) as $element1) {
            $substance[] = $element1->outertext;
        }

        preg_match("/<a name=\"sideEffects\">(.+?)<\/div>/i",implode($substance),$matches); // побочные действия
        $collat = trim(strip_tags(str_replace("Побочные действия"," ",$matches[0]),'<br>'));
        unset ($matches);
        preg_match("/<a name=\"contraindications\">(.+?)<\/div>/i",implode($substance),$matches); // противопоказания
        $contr = trim(strip_tags(str_replace("Противопоказания к применению"," ",$matches[0]),'<br>'));
        unset ($matches);
        preg_match("/<a name=\"dosing\">(.+?)<\/div>/i",implode($substance),$matches); // способ применения и дозы
        $dose = trim(strip_tags(str_replace("Способ применения и дозы"," ",$matches[0]),'<br>'));
        unset ($matches);
        preg_match("/<a name=\"storageConditions\">(.+?)<\/div>/i",implode($substance),$matches); // условия хранения
        $storage1 = trim(strip_tags(str_replace("Условия хранения"," ",$matches[0]),'<br>'));
        unset ($matches);
        preg_match("/<a name=\"bbd\">(.+?)<\/div>/i",implode($substance),$matches); // срок хранения
        $term = trim(strip_tags(str_replace("Срок годности"," ",$matches[0]),'<br>'));
        unset ($matches);
        preg_match("/<a name=\"compound\">(.+?)<\/div>/i",implode($substance),$matches); // состав  11
        $compound = trim(strip_tags(str_replace("Состав"," ",$matches[0]),'<br>'));
        unset ($matches);
        preg_match("/<a name=\"releaseForm\">(.+?)<\/div>/i",implode($substance),$matches); // форма выпуска
        $form = trim(strip_tags(str_replace("Форма выпуска"," ",$matches[0]),'<br>'));
        unset ($matches);
        preg_match("/<a name=\"indications\">(.+?)<\/div>/i",implode($substance),$matches); // показания к применению
        $indicat_applic = trim(strip_tags(str_replace("Показания к применению"," ",$matches[0]),'<br>'));
        unset ($matches);
        preg_match("/<a name=\"useDuringPregnancy\">(.+?)<\/div>/i",implode($substance),$matches); // прием при беременности
        $pregn = trim(strip_tags(str_replace("Использование во время беременности"," ",$matches[0]),'<br>'));
        unset ($matches);
        preg_match("/<a name=\"drugOverdose\">(.+?)<\/div>/i",implode($substance),$matches); // передозировка
        $overdose = trim(strip_tags(str_replace("Передозировка"," ",$matches[0]),'<br>'));
        unset ($matches);
        preg_match("/<a name=\"drugInteractions\">(.+?)<\/div>/i",implode($substance),$matches);                    // взаимодействие с другими препаратами
        $prec = trim(strip_tags (str_replace("Взаимодействия витамина с другими препаратами"," ",$matches[0]),'<br>'));
        unset ($matches);
        preg_match("/<a name=\"precautions\">(.+?)<\/div>/i",implode($substance),$matches);                        // меры предосторожности при приеме 25
        $drug_inter = trim(strip_tags (str_replace("Меры предосторожности при приеме"," ",$matches[0]),'<br>'));
        unset ($matches);
        unset ($substance);


        foreach ($html->find("div.l-main p") as $element2):
            if($element2->class=="medicine__manufacturer indent")
            {
                $manufac1 = $element2->plaintext;
            }
        endforeach;
        unset ($element2);

        $country=0;
        preg_match ("/\(.*\)/i",$manufac1,$matches);
        $country= trim($matches[0],"(,)");                                                  // страна-производитель

        $form_id=0;
        foreach ($form_list as $list):                                                 // получение id формы выпуска
            preg_match("#(?i)(\b|^)($list)(\B|$)#", translit7($form),$matches );
            if (!empty($matches)) {
                $form_id = transForm($matches[0]);
                $form_name=$matches[0];
            }
        endforeach;
        unset($matches);


        $manufac= trim(str_replace("Производитель:"," ",preg_replace ("/\(.*\)/i" ," ",$manufac1)));// производитель 3
        unset($manufac1);
        unset($matches);
        $e = $html->find("div.l-main h1", 0);
        $name = str_replace(" - описание и инструкция по применению", " ", $e->plaintext);  // название препарата
        if($form_id!=0) {
            $name = $name . "(" . $form_name . ")";

        }

        //*************************************************************блок  проверки
        if (strlen($row)> 100):

            if (strripos($row,"і") == false):


                //*********************************************************


                $symbol= array(" ","«","»","+","(",")",",",".","%","#",";","/","№",":","--","--");
                $alias = trim(str_replace($symbol,"-",translit(translit7($name))),"-");   // алиас
                $rrr= array( 'ch', 'sh', 'sch', 'yu', 'ya','zh'); // литерал
                $lit1=substr($alias, 0, 2);
                if(in_array ( $lit1 ,$rrr,false)==false){
                    $lit= substr($alias, 0, 1);
                }else {$lit= substr($alias, 0, 2);}

                //****************************************************************** запросы к БД на поиск уже имеющихся значений

                $countries = mysql_fetch_assoc(mysql_query("SELECT id FROM fds23ddsd_countries WHERE title_ru= '$country'"));
                $nameExists  = @implode(mysql_fetch_assoc(mysql_query("SELECT id FROM `fds23ddsd_drug_notes` WHERE title='$name'")));// поиск по русскоязычной базе на совбадение
                 $formExist = mysql_fetch_assoc(mysql_query( "SELECT form_id FROM fds23ddsd_drug_notes WHERE title='$name'AND form_id='$form_id'"));

//  ********************** запросы на получение последних id
                $id_drugss=@implode(mysql_fetch_assoc(mysql_query("SELECT id FROM fds23ddsd_drugs ORDER BY id DESC LIMIT 1")));         // получение последнего ID конечной таблицы
                $id_drug_notes=@implode(mysql_fetch_assoc(mysql_query("SELECT id FROM fds23ddsd_drug_notes ORDER BY id DESC LIMIT 1")));  // получение последнего ID конечной таблицы
                $id_drug_text_fields_notes=@implode(mysql_fetch_assoc(mysql_query("SELECT id FROM fds23ddsd_drug_text_fields_notes ORDER BY id DESC LIMIT 1")));   // получение последнего ID конечной таблицы

                $id_drugss++;$id_drug_notes++;$id_drug_text_fields_notes++;

                if(!empty($countries)){
                    $countries =implode($countries);
                }else{ $countries=0;}



                $storage= $term." ".$storage1;                                                      // обьединение срока и условий хранения


//******************************************************************************* загрузка в БД имедез


                    if (empty($nameExists)&&empty($nameExists))
                    {

                        mysql_query("INSERT INTO `fds23ddsd_drug_notes`(`id`,`drug_id`,`lang_id`,`country_id`,`form_id`,`title`,`literal`) VALUES('$id_drug_notes','$id_drugss',1,'$countries',$form_id,'$name','$lit')");//
                        mysql_query("INSERT INTO `fds23ddsd_drugs`(`id`,`alias`) VALUES('$id_drugss','$alias')");
                        mysql_query("INSERT INTO `vitamins_instr_orig`(`id`,`text`) VALUES('$i','$res6')");


                        //  if($id_drug_notes==implode(mysql_fetch_assoc(mysql_query("SELECT id FROM fds23ddsd_drug_notes ORDER BY id DESC LIMIT 1")))):


                        if (!empty($manufac)) {
                            mysql_query("INSERT INTO `fds23ddsd_drug_text_fields_notes`(`id`,`dn_id`,`df_id`,`value`) VALUES('$id_drug_text_fields_notes',' $id_drug_notes',3,'$manufac')");
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

    // ************** удаление информации о препарате с временных таблиц
   mysql_query(" DELETE FROM `fds23ddsd_drug_notes_new` WHERE id=$i ");
    mysql_query(" DELETE FROM `drugs_new`  WHERE id=$i ");
    mysql_query(" DELETE FROM `fds23ddsd_drugs_new` WHERE id=$i ");
    mysql_query(" DELETE FROM `fds23ddsd_drug_text_fields_notes_new` WHERE id=$i ");
    $i++;

endwhile;

    $_SESSION['ss'] = 1;
    header( "refresh:2;url=/test_rossvn/check_vitam.php" );
}

