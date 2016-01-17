<?php	
//Подключаем библиотеки
require 'INC/my_lib.php';
require 'addons/zip_archive/pclzip.lib.php';

$provider      = $_POST['provider'];

if ($provider == "C5228354-E5AF-4946-B7B6-032B0B8DDEBD") $provider = "ИП Белкин Н.В.";
if ($provider == "8AC8DD0F-422F-4D87-8220-E2DD6B4A8FA1") $provider = "ООО \"Самсон-Опт\"";
if ($provider == "D2862286-2BCA-4A30-A155-2AD6D860C9F7") $provider = "ООО \"Рельеф-Центр\"";
if ($provider == "9D7AC28D-B420-47E3-ACD8-9A79DC6997B3") $provider = "ЗАО \"ФАРМ\"";
if ($provider == "B29CB545-C8B7-4C3A-9A9E-78F8682546C5") $provider = "ООО \"Микрос\"";
?>

<!DOCTYPE html>
<html lang="ru">

  <head>
    <meta charset="utf-8">
    <title><?=date('d.m.y H:i')?> Поставщик <?=$provider?></title>

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
  $date = date('Y-m-d');

  //Создаём имя для дирректории
  $dir_name = "uploads/1C/$date" . '_' . $id;

 /* Определяем переменные для хранения данных
---------------------------------------------- */
  $date             = '';
  $number_doc       = '';
  $provider         = '';
  $identifier       = '';

  $provider_info    = '';
  $identifier_info  = '';

  $author           = '';
  $title            = '';
  $barcode          = '';
  $quantity         = '';
  $price            = '';

  $no_Barcode       = '';
  $table_no_Barcode = '';

 /* Получаем данные из формы
---------------------------------------------- */
  $date           = date('Y-m-d', $_POST['date_doc']);
  $number_doc     = $_POST['number_doc'];
  $provider       = $_POST['provider'];
  $identifier     = $_POST['identifier'];

  $provider_info  = provider_info($provider);
  $identifier_info= identifier_info($identifier);

  $author         = $_POST['author'];
  $title          = $_POST['title'];
  $barcode        = $_POST['barcode'];
  $quantity       = $_POST['quantity'];
  $price          = $_POST['price'];

 /* Очищаем пришедшие данные
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

 /* Формируем строку, которая и будет файликом xml для 1С
---------------------------------------------- */
  //Начало строки - без изменений
  $str = "<?xml version=\"1.0\" encoding=\"windows-1251\"?>\n";

  //Информация о Каталоге и Владельце
  $str .= "<КоммерческаяИнформация>\n\t<Каталог $identifier_info Владелец=\"454FA02E-4E77-4684-83FC-AC5CC7B3FBC0\" Единица=\"шт\">\n\n\t\t";

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
  $str .= "<ПредприятиеВДокументе Контрагент=\"$provider\" Роль=\"Saler\"/>\n\n\t\t";

  //Кол-во Цена Сумма
  foreach ($new_Title as $key => $val) {
    //Проверяем есть ли в данной строке штрихкод
    if ( !empty($new_Barcode[$key]) ) $str .= '<ТоварнаяПозиция Каталог="' . $identifier . '" Товар="' . $new_Barcode[$key] . '" Единица="шт" Количество="' . $new_Quantity[$key] . '" Цена="'. $new_Price[$key] .'" Сумма="' . $new_Price[$key] * $new_Quantity[$key] . '"/>' . "\n\t\t";
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
  $str .= "<Контакт Идентификатор=\"ID__B___0___0___4014___0___0____________1_____\" Наименование=\"ИП Осипенко Олег Викторович\"/>\n\t";
  $str .= "</Контрагент>\n\t";

    //Реквизиты поставщика
    $str .= "<Контрагент $provider_info />\n\n";

 /* Конец строки xml для 1С
---------------------------------------------- */
  $str .= '</КоммерческаяИнформация>';

  //Создаём директорию для полученной накладной
  if ( !file_exists("$dir_name") ) {
    mkdir("$dir_name", 0700);
  }

  //Записываем получившуюся накладную формата 1С в файлик
  file_put_contents("$dir_name/$number_doc.xml", iconv('utf-8','windows-1251', $str));

  //Выводим информацию об обработанной накладной
  echo message_on_invoice($no_Barcode, $number_doc, $table_no_Barcode);

 /* Формируем архив с получившейся накладной
---------------------------------------------- */
    $archive = new PclZip("$dir_name/$number_doc.zip"); //Создаём объект и в качестве аргумента, указываем название архива, с которым работаем.    
    if($archive->add("$dir_name/$number_doc.xml", PCLZIP_OPT_REMOVE_PATH, "$dir_name", PCLZIP_OPT_COMMENT, 'Akliya') == 0) echo $archive->errorInfo(true);

  //Перезаписываем значение идентификатора в файл "id.txt"
  file_put_contents('id.txt', $id, FILE_USE_INCLUDE_PATH);

  //Остановливаем секундомер
  $time = microtime(true) - $start;
  printf('<p>На обработку ушло: %.4F сек.</p>', $time);
?>
      <div class="buttons" >
        <a class="btn res download" href=<?="$dir_name/$number_doc.zip"?>>Скачать</a>

        <?php if( !empty($table_no_Barcode[1]) ) {echo "<button class='btn download' onClick='window.print();'>Распечатать</button>";} ?>
      </div>
    </div>
  </main>

  <?php require 'INC/footer.php'; ?>

  </body>

</html>