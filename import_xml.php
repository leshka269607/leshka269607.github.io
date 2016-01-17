<?php	
//Подключаем библиотеки
require 'INC/my_lib.php';
require 'addons/zip_archive/pclzip.lib.php';
?>

<!DOCTYPE html>
<html lang="ru">

  <head>
    <meta charset="utf-8">
    <title><?=date('d.m.y H:i')?> Поставщик ИП Белкин Н.В.</title>

    <!-- Библиотеки -->
    <link rel="stylesheet" href="css/normalize.min.css">

    <!-- Аддоны -->

    <!-- Минифицированные -->
      <link rel="stylesheet" href="css/style.min.css">

    <!-- Мои стилевые файлы -->
      <!-- <link rel="stylesheet" href="css/style.css"> -->

    <!-- favicon -->
      <link rel="icon" href="favicon.ico" type="image/x-icon" />
      <link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
  </head>

  <body>

    <header class="main-header">
      <?php require 'INC/version_info.php'; ?>
    </header>

    <main class="layout-center">
      <div class="message_invoice">

  <?php

  //Запускаем секундомер выполнения скрипта
  $start = microtime(true);

 /* Формируем значение идентификатора
---------------------------------------------- */
    //Считываем идентификатор для товара
    $id = file_get_contents('id.txt');

 /* Формируем имя директории, в которую положим результат-архив
---------------------------------------------- */
  //Читаем сегодняшнюю дату
  $today = date('Y-m-d');

  //Создаём имя для дирректории
  $dir_name = "uploads/$today" . '_' . $id;

  /* Записываем все пришедшие файлики в один массив
---------------------------------------------- */
  //Полный путь до пришедшего/пришедших файлов
  $filelist = glob("$dir_name/*.xml");

  foreach ($filelist as $key => $val) {
    //Получаем данные
    $doc_out = file_get_contents($val);

    $file_name = str_replace("$dir_name/", '' , $val);
    if ( strpos($doc_out, '454FA02E-4E77-4684-83FC-AC5CC7B3FBC0') ) echo "<p class='error'>$file_name - уже готовая накладная!</p>";
      else
        $docs_out[$key] = $doc_out;
  }

/* ----------------------------------------------
  Начинается глобальный цикл. Через который проходит каждый пришедщий файлик
---------------------------------------------- */
if ( !empty($docs_out) ) {
  //Кидаем пришедший документ в переменную
  foreach ($docs_out as $val) {
    $xmlstr = <<<XML
$val
XML;

  //И создаём из него объект
  $stroka = new SimpleXMLElement($xmlstr);

 /* Определяем переменные для хранения данных
---------------------------------------------- */
    $date             = '';
    $number_doc       = '';

    $author           = '';
    $title            = '';
    $barcode          = '';
    $quantity         = '';
    $price            = '';

    $no_Barcode       = '';
    $table_no_Barcode = '';

 /* Получаем нужные нам данные из пришедшего документа (уже из объекта)
---------------------------------------------- */ 
  //Дату накладной сразу переделываем под формат 1С
    $originalDate = $stroka['DATDOC'];
    $date = date('Y-m-d', strtotime($originalDate));

  //Номер накладной
    $number_doc   = $stroka['NOMDOC'];

  //
  //Раскидываем Автора, Названия, ШтрихКоды, Количество и Цены по отдельным массивам с разделителем ";"

    //Автор
      foreach ($stroka->STROKA as $val) {
        $author .= $val->PARENT[0]['VIHOD'] . ';';
      }

    //Название
      foreach ($stroka->STROKA as $val) {
        $title .= $val->PARENT[0]['NAME'] . ';';
      }

    //ШтрихКод
      foreach ($stroka->STROKA as $val) {
        $barcode .= $val->PARENT[0]['SHIFR'] . ';';
      }

    //Количество
      foreach ($stroka->STROKA as $val) {
        $quantity .= $val['KOLVO'] . ';';
      }

    //Цена
      foreach ($stroka->STROKA as $val) {
        $price .= $val['CENA'] . ';';
      }

 /* Очищаем пришедшие данные и помещаем в массив (в основном для exel)
---------------------------------------------- */
    $new_Author   = myCleaning($author);
    $new_Title    = myCleaning($title);
    $new_Barcode  = myCleaning($barcode);
    $new_Quantity = myCleaning($quantity);
    $new_Price    = myCleaning($price);

    //Заменяем буквы "ё" и "Ё"
    foreach ($new_Author as $key => $val) {
      $new_Author[$key] = str_replace('Ё', 'е', $new_Author[$key]);
      $new_Author[$key] = str_replace('ё', 'е', $new_Author[$key]);
    }

    // А для цены запятые изменяем на точки
    $new_Price = str_replace(',', '.',  $new_Price);

    //Сумма накладной
    $sum = sum_invoice($new_Quantity, $new_Price, $new_Barcode);

    //Пропускаем Названия через функцию belkinRename(), как в JSK
    $til = $new_Title;
    unset($new_Title);

   $new_Title = belkinRename($til);

 /* Если в пришедшем документе вообще нет штрихКодов, не создаём накладную,
    переходим к обработке следующего файлика
---------------------------------------------- */

    //Переменная для проверки существования штрихКодов
    $control_barcode = '';
    $control_barcode = str_replace(';', '',  $barcode);

    if( empty($control_barcode) ){
      foreach ($new_Title as $key => $val) {
        $table_no_Barcode[] = $key+1;
        $table_no_Barcode[] = $new_Title[$key];
        $table_no_Barcode[] = $new_Quantity[$key];
        $table_no_Barcode[] = $new_Price[$key];
        $table_no_Barcode[] = $new_Price[$key] * $new_Quantity[$key];

        $no_Barcode .= $key+1 . ';';
      }

    //Выводим информацию о том, что в данная накладная не создана
      $message = "<p class='error good'>Ни одного штрихКода в накладной ";
      echo message_on_invoice($no_Barcode, $number_doc, $table_no_Barcode, $message);

    //Перезаписываем значение идентификатора в файл "id.txt"
      $id++;
      file_put_contents('id.txt', $id, FILE_USE_INCLUDE_PATH);

    //и переходим к обработке следующего файла
    continue;
}

 /* Формируем строку, которая и будет файликом xml для 1С
---------------------------------------------- */  
    //Начало строки - без изменений
    $str = "<?xml version=\"1.0\" encoding=\"windows-1251\"?>\n";

    //Информация о Каталоге и Владельце
    $str .= "<КоммерческаяИнформация>\n\t<Каталог Идентификатор=\"2014\" Наименование=\"По штрихкоду\" Владелец=\"454FA02E-4E77-4684-83FC-AC5CC7B3FBC0\" Единица=\"шт\">\n\n\t\t";

    foreach ($new_Title as $key => $val) {

      //Добавляем пробел, если есть Автор книги
      if ( !empty($new_Author[$key]) ) {
        $new_Title[$key] = ' ' . $new_Title[$key];

        //Вырезаем Автора книги из Названия (чтобы не повторялось два раза)
        $new_Title[$key] = str_replace($new_Author[$key] . ' ', '', $new_Title[$key]);

        //Объединяем Автора и Название
        $new_Title[$key] = $new_Author[$key] . $new_Title[$key];
      }

      //Проверяем есть ли в данной строке штрихкод
      //Формируем идентификатор 1С вида - ("ID__B___0___0___84___0_0_***___")
      if ( !empty($new_Barcode[$key]) ) $str .= '<Товар Идентификатор="ID__B___0___0___84___0_0_' . str_pad($id++, 16, '0', STR_PAD_LEFT) . '___" ИдентификаторВКаталоге="' . $new_Barcode[$key] . '" Наименование="'. $new_Title[$key] . '" Единица="шт"/>' . "\n\t\t";
    }

    $str .= "\n\t</Каталог>\n\n\t";

    //Дата, Номер, Время и Сумма документа
    $str .= "<Документ Дата=\"$date\" ";
    $str .= "Номер=\"$number_doc\" Время=\"01:00:00\" ХозОперация=\"Sale\" Сумма=\"$sum\" Валюта=\"РУБ\" Курс=\"1\" Кратность=\"1\" СрокПлатежа=\"$date\">\n\t\t";

    //Покупатель и Поставщик
    $str .= "<ПредприятиеВДокументе Контрагент=\"454FA02E-4E77-4684-83FC-AC5CC7B3FBC0\" Контакт=\"ID__B___0___0___4014___0___0____________1_____\" Роль=\"Buyer\"/>\n\t\t";
    $str .= "<ПредприятиеВДокументе Контрагент=\"C5228354-E5AF-4946-B7B6-032B0B8DDEBD\" Роль=\"Saler\"/>\n\n\t\t";

    //Кол-во Цена Сумма
    foreach ($new_Title as $key => $val) {
      //Проверяем есть ли в данной строке штрихкод
      if ( !empty($new_Barcode[$key]) ) $str .= '<ТоварнаяПозиция Каталог="2014" Товар="' . $new_Barcode[$key] . '" Единица="шт" Количество="' . $new_Quantity[$key] . '" Цена="'. $new_Price[$key] .'" Сумма="' . $new_Price[$key] * $new_Quantity[$key] . '"/>' . "\n\t\t";
        //Определям номера строк, которые потребуют ручного ввода
        else {
          $table_no_Barcode[] = $key+1;
          $table_no_Barcode[] = $new_Title[$key];
          $table_no_Barcode[] = $new_Quantity[$key];
          $table_no_Barcode[] = $new_Price[$key];
          $table_no_Barcode[] = $new_Price[$key] * $new_Quantity[$key];

          $no_Barcode .= $key+1 . ';';
        }
    }

    //Реквизиты покупателя
    $str .= "\n\t</Документ>\n\n\t";
    $str .= "<Контрагент Идентификатор=\"454FA02E-4E77-4684-83FC-AC5CC7B3FBC0\" Наименование=\"ИП Осипенко Олег Викторович\" ОтображаемоеНаименование=\"Осипенко Олег Викторович(Основной учет)\" Адрес=\",,Брянская область,,Клинцы,,Гоголя,22,,\" ЮридическийАдрес=\",,Брянская область,,Клинцы,,Гоголя,22,,\">\n\t\t";
    $str .= "<Контакт Идентификатор=\"ID__B___0___0___4014___0___0____________1_____\" Наименование=\"Осипенко Олег Викторович(Основной учет)\"/>\n\t";
    $str .= "</Контрагент>\n\t";

    //Реквизиты поставщика
    $str .= "<Контрагент Идентификатор=\"C5228354-E5AF-4946-B7B6-032B0B8DDEBD\" Наименование=\"ИП Белкин Н.В.\" ОтображаемоеНаименование=\"ИП Белкин Н.В.\"/>\n\n";

 /* Конец строки xml для 1С
---------------------------------------------- */
    $str .= '</КоммерческаяИнформация>';

    //Создаём директорию 1C в папке с пришедшими накладными
    if ( !file_exists("$dir_name/1C") ) {
      mkdir("$dir_name/1C", 0700);
    }

    //Записываем получившуюся накладную формата 1С в файлик
    file_put_contents( "$dir_name/1C/$number_doc.xml", iconv('utf-8','windows-1251', $str) );
 
    //Выводим информацию об обработанной накладной
    echo message_on_invoice($no_Barcode, $number_doc, $table_no_Barcode);
  }

    //Собираем все получившиеся накладные в один массив
    $filelist_in = glob("$dir_name/1C/*.xml");

  if ( !empty($filelist_in) ) {
    //Создаём объект и в качестве аргумента, указываем название архива, с которым работаем.
      $archive = new PclZip("$dir_name/1C/archive.zip");

    foreach ($filelist_in as $val) {
      if($archive->add($val, PCLZIP_OPT_REMOVE_PATH, "$dir_name/1C", PCLZIP_OPT_COMMENT, 'Akliya') == 0) echo $archive->errorInfo(true);
    }
  }
}

  //Перезаписываем значение идентификатора в файл "id.txt"
  file_put_contents('id.txt', $id, FILE_USE_INCLUDE_PATH);

  //Остановливаем секундомер
  $time = microtime(true) - $start;
  printf('<p>На обработку ушло: %.4F сек.</p>', $time);
?>
      <!-- <a onclick="setTimeout(function(){self.location='index.php'},2000)">Скачать</a> -->
      <div class="buttons" >
        <?php if ( file_exists("$dir_name/1C/archive.zip") ) {echo "<a class='btn res download' href=$dir_name/1C/archive.zip>Скачать</a>";} ?>

        <?php if( !empty($table_no_Barcode[1]) ) {echo "<button class='btn download' onClick='window.print();'>Распечатать</button>";} ?>
      </div>
    </div>
  </main>

  <?php require 'INC/footer.php'; ?>

  </body>

</html>