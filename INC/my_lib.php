<?php

//1. Функция вычисляет сумму накладной (товары без штрихкода не суммируются) - Сумма накладной
  function sum_invoice($Quantity, $Price, $Barcode) {
    $sum = 0;
      foreach ($Quantity as $key => $val) {
        if ( !empty($Barcode[$key]) ) {
          $sum += $Quantity[$key] * $Price[$key];
        }
      }
    return $sum;
  }

//2. Очищает пришедшие данные и кидает их в массив - Моя очистка
  function myCleaning($mud) {
    //Массивы с символами для удаления
    $del_Symbol  = array("\r\n", '"', "'", "\r", "\n", "\t");
    $del_Symbol0 = array('     ', '    ', '   ', '  ');

    //Заменяем переносы строк на ;
    $clean = str_replace("\n", ';', $mud);

    //Удаляем лишние пробелы и табуляцию
    $clean = trim( str_replace($del_Symbol0, ' ',  $clean) );
    $clean = trim( str_replace($del_Symbol, '',  $clean) );

    //Создаём массив из пришедших данных
    $clean = explode(';', $clean);

    return $clean;
  }

//3. Присваиваем набор соответствующих реквизитов для поставщика - Информация о поставщике
  function provider_info($provider) {
    switch ($provider) {
      //Белкин
      case "C5228354-E5AF-4946-B7B6-032B0B8DDEBD":
        $provider_info = "Идентификатор=\"C5228354-E5AF-4946-B7B6-032B0B8DDEBD\" Наименование=\"ИП Белкин Н.В.\" ОтображаемоеНаименование=\"ИП Белкин Н.В.\"";
        break;

      //Самсон
      case "8AC8DD0F-422F-4D87-8220-E2DD6B4A8FA1":
        $provider_info = "Идентификатор=\"8AC8DD0F-422F-4D87-8220-E2DD6B4A8FA1\" Наименование=\"ООО &quot;САМСОН-Опт&quot;\" ОтображаемоеНаименование=\"ООО &quot;САМСОН-Опт&quot;\"";
        break;

      //Рельеф
      case "D2862286-2BCA-4A30-A155-2AD6D860C9F7":
        $provider_info = "Идентификатор=\"D2862286-2BCA-4A30-A155-2AD6D860C9F7\" Наименование=\"ООО &quot;Рельеф-Центр&quot;\" ОтображаемоеНаименование=\"ООО &quot;Рельеф-Центр&quot;\"";
        break;

      //Фарм
      case "9D7AC28D-B420-47E3-ACD8-9A79DC6997B3":
        $provider_info = "Идентификатор=\"9D7AC28D-B420-47E3-ACD8-9A79DC6997B3\" Наименование=\"ЗАО &quot;ФАРМ&quot;\" ОтображаемоеНаименование=\"ЗАО &quot;ФАРМ&quot;\"";
        break;

      //Микрос
      case "B29CB545-C8B7-4C3A-9A9E-78F8682546C5":
        $provider_info = "Идентификатор=\"B29CB545-C8B7-4C3A-9A9E-78F8682546C5\" Наименование=\"ООО &quot;Микрос&quot;\" ОтображаемоеНаименование=\"ООО &quot;Микрос&quot;\"";
        break;
        
      //Поумолчанию: Белкин
      default:
        $provider_info = "Идентификатор=\"C5228354-E5AF-4946-B7B6-032B0B8DDEBD\" Наименование=\"ИП Белкин Н.В.\" ОтображаемоеНаименование=\"ИП Белкин Н.В.\"";
    }
    return $provider_info;
  }

//4. Формируем сообщение об обработанных накладных (строки без ШК) - Сообщение о накладных 
  function message_on_invoice($no_Barcode, $number_doc, $table_no_Barcode, $message = "<p class='good'>Cоздана накладная ") {
    //Преобразуем строку в массив
    $no_Barcode = explode(';', $no_Barcode);

    //Удаляем последний элемент массива, так как он всегда получается пустой
    $last = array_pop($no_Barcode);

    //Если пользователь копирует-вставляет из excel, то удаляем ещё один элемент
    if ( empty($last) ){
      array_pop($no_Barcode);
    }

    $message .= "№ $number_doc.";

    //Если строк без штрихКода нет, то закрываем тег, выходим из функции
    if ( empty($no_Barcode) ) {$message .= '</p>'; return $message;}

    //Преобразуем обратно в строку
    $no_Barcode = implode('; ', $no_Barcode);
    if (!empty($no_Barcode)) $no_Barcode .= ';';

    //Правильное окончание для слова "Строки" или "Строку"
    if ( !empty($no_Barcode) ) {
      if (substr_count($no_Barcode, ';') <= 1) {
        $ending = ' Строку';
        $message .= "<b class='error'>" . $ending . " №: $no_Barcode придётся добавить вручную :(</b></p>";
      }
      else {
        $ending = ' Строки';
        $message .= "<b class='error'>" . $ending . " №: $no_Barcode придётся добавить вручную :(</b></p>";
      }
    }

    if (count($table_no_Barcode)>5) {
      //Добавляем в сообщение таблицу со строками без штрих-кода (для печати и последующего ввода руками)
      $message .= '<table>';

      $message .= '<tr><th>№ строки</th><th>Название</th><th>Кол-во</th><th>Цена</th><th>Сумма</th></tr>';

      for ($i=0; $i<(count($table_no_Barcode)-5); $i++) {
        $message .= '<tr><td>'. $table_no_Barcode[$i++] . '</td><td>' . $table_no_Barcode[$i++] . '</td><td>' . $table_no_Barcode[$i++] . '</td><td>' . $table_no_Barcode[$i++] . '</td><td>' . $table_no_Barcode[$i] . "</td></tr>\n";
      }

      $message .= '</table>';
    }

    return $message;
  }

//5. Присваиваем набор соответствующих характеристик об идентификаторе
  function identifier_info($identifier) {
    switch ($identifier) {    
      //Штрихкод
      case '2014':
        $identifier_info = "Идентификатор=\"2014\" Наименование=\"По штрихкоду\"";
        break;

      //Артикул
      case '63E072CC-B4E5-492F-A84E-3687EDE026A6':
        $identifier_info = "Идентификатор=\"63E072CC-B4E5-492F-A84E-3687EDE026A6\" Наименование=\"По артикулу\"";
        break;

      //Поумолчанию: Штрихкод
      default:
        $identifier_info = "Идентификатор=\"2014\" Наименование=\"По штрихкоду\"";
    }
    return $identifier_info;
  }

//6. Производим очистку наименований, аналог функции belkinRename() на JSk
  function belkinRename($title) {
  
    foreach ($title as $key => $val) {

/*  Частные случаи
---------------------------------------------- */
      $title[$key] = str_replace('Атлас Карт. с к/к 3-5 кл. Природоведение', 'Атлас с контурными картами 3-5кл Природоведение', $title[$key]);

      $title[$key] = str_replace('Букварь (Литур) Сред.', 'Букварь Литур (Средний)', $title[$key]);
      $title[$key] = str_replace('Биология Общие закон. 9 кл. (Дрофа) Р. т. к учеб. Мамонтова', 'Биология Общие закономерности 9кл Дрофа РТ Мамонтов', $title[$key]);
      $title[$key] = str_replace('Баранкин.. (Стрекоза)', 'Баранкин будь человеком Стрекоза', $title[$key]);

      $title[$key] = str_replace('Введ. в естественно-науч. предметы 5 кл. (Дрофа) Р. т.', 'Введение в естественно-научные предметы 5кл Дрофа РТ', $title[$key]);
      $title[$key] = str_replace('Все правила сов. рус. яз. (Баро-Пресс)', 'Все правила современного русского языка Баро-Пресс', $title[$key]);
      $title[$key] = str_replace('Всеобщая ист. к. XIX - н. XXI в. 11 кл. (РС)', 'Всеобщая история 11кл Конец 19-начало 21века Русское слово', $title[$key]);

      $title[$key] = str_replace('ГИА Матем. 3000 зад. с отв. Все Зад. ч.1 (Закрытый сегмент) Семёнова, Ященко (Экз)', 'ГИА Математика 3000 задач с ответами. Все задания части 1 Семенова, Ященко Экзамен (Закрытый сегмент)', $title[$key]);
      $title[$key] = str_replace('Глобус 210 мм физ. с рельефом', 'Глобус 210мм Физический С рельефом', $title[$key]);
      $title[$key] = str_replace('Глобус 250 мм полит. без подсветки', 'Глобус 250мм Политический Без подсветки', $title[$key]);

      $title[$key] = str_replace('ЕГЭ АСТ 2014 Обществознание Полный сп-к (Баранов)', 'ЕГЭ 2014 Обществознание Полный справочник Баранов АСТ', $title[$key]);

      $title[$key] = str_replace('Компьютер для людей стар. возраста (цвет.) (Питер)', 'Компьютер для людей старшего возраста Питер', $title[$key]);
      $title[$key] = str_replace('Кк Дрофа 8 кл. История России 19 в.', 'Контурная карта 8кл История России 19век Дрофа', $title[$key]);
      $title[$key] = str_replace('Кк Дрофа 7 кл. История России 17-18 в.', 'Контурная карта 7кл История России 17-18век Дрофа', $title[$key]);
      $title[$key] = str_replace('Кк Дрофа 8 кл. История Нового вр. 19 в.', 'Контурная карта 8кл История Нового времени 19век Дрофа', $title[$key]);
      $title[$key] = str_replace('Кк Дрофа 7 кл. История Нового вр. ХVI-XVIII в.', 'Контурная карта 7кл История Нового времени 16-18век Дрофа', $title[$key]);

      $title[$key] = str_replace('Мир и Россия (расклад.) (Администр.) (Атлас Принт)', 'Мир и Россия Административная Атлас-Принт (Складная)', $title[$key]);
      $title[$key] = str_replace('Мир и Россия (расклад.) (Физич.) (Атлас Принт)', 'Мир и Россия Физическая Атлас-Принт (Складная)', $title[$key]);
      $title[$key] = str_replace('МУС Занимательные упражнение рус. яз. 5-9 кл. (Вако)', 'МУС Занимательные упражнения Русский язык 5-9кл Вако', $title[$key]);

      $title[$key] = str_replace('НХ по лит-ре 3 кл. (Эксмо)', 'Новейшая хрестоматия по литературе 3кл Эксмо', $title[$key]);
      $title[$key] = str_replace('Новейший сп-к для поступ. в ВУЗы 2013-2014 г.г. (ДСК)', 'Новейший справочникк для поступающих в ВУЗы 2013-2014г ДСК', $title[$key]);

      $title[$key] = str_replace('Портфолио обуч. нач. школы (Планета)', 'Портфолио обучающегося начальной школы Планета', $title[$key]);
      $title[$key] = str_replace('Подарок. Рассказы и сказки (Эксмо)', 'Подарок Рассказы и сказки Эксмо', $title[$key]);
      $title[$key] = str_replace('Правила для нач. кл. (Стрекоза)', 'Правила для начальных классов Стрекоза', $title[$key]);
      $title[$key] = str_replace('ПХ для 1-4 кл. по рус. и зар. лит-ре (Фирма СТД)', 'Полная хрестоматия для 1-4кл по русской и зарубежной литературе Фирма СТД', $title[$key]);

      $title[$key] = str_replace('Справочник по рус. яз. и мат. 1-4 кл. (АСТ)', 'Справочник по русскому языку и математике 1-4кл АСТ', $title[$key]);
      $title[$key] = str_replace('Сочинения на 5+. Все темы шк. программы 5-11кл (Лада)', 'Сочинения на 5+ Все темы школьной программы 5-11кл Лада', $title[$key]);

      $title[$key] = str_replace('Универсальная хрестоматия 3 кл. (Эксмо)', 'Универсальная хрестоматия 3кл Эксмо', $title[$key]);
      $title[$key] = str_replace('УМК КР по матем. 5 кл. (Экз.) ко всем учебникам.', 'УМК КР по математике 5кл Экзамен Ко всем учебникам', $title[$key]);
      $title[$key] = str_replace('УМК Развивающие зад. 1 кл. Тесты, игры. упр. (Экз.)', 'УМК Развивающие задания 1кл Тесты, игры, упражнения Экзамен', $title[$key]);

      $title[$key] = str_replace('Физическая карта мира 1:34 000 000 (Атлас-Принт)', 'Физическая карта мира 1:34 000 000 Атлас-Принт', $title[$key]);
      $title[$key] = str_replace('Физическая карта России 1:8 800 000 (Атлас Принт)', 'Физическая карта России 1:8 800 000 Атлас Принт', $title[$key]);

      $title[$key] = str_replace('Энц ума (Олма)', 'Энциклопедия ума Олма', $title[$key]);

/*  Общие случаи
---------------------------------------------- */
      //Века
      $title[$key] = str_replace(' I ', ' 1 ', $title[$key]);
      $title[$key] = str_replace(' II ', ' 2 ', $title[$key]);
      $title[$key] = str_replace(' III ', ' 3 ', $title[$key]);
      $title[$key] = str_replace(' IV ', ' 4 ', $title[$key]);
      $title[$key] = str_replace(' V ', ' 5 ', $title[$key]);
      $title[$key] = str_replace(' VI ', ' 6 ', $title[$key]);
      $title[$key] = str_replace(' VII ', ' 7 ', $title[$key]);
      $title[$key] = str_replace(' VIII ', ' 8 ', $title[$key]);
      $title[$key] = str_replace(' IX ', ' 9 ', $title[$key]);
      $title[$key] = str_replace(' X ', ' 10 ', $title[$key]);
      $title[$key] = str_replace(' XI ', ' 11 ', $title[$key]);
      $title[$key] = str_replace(' XII ', ' 12 ', $title[$key]);
      $title[$key] = str_replace(' XIII ', ' 13 ', $title[$key]);
      $title[$key] = str_replace(' XIV ', ' 14 ', $title[$key]);
      $title[$key] = str_replace(' XV ', ' 15 ', $title[$key]);
      $title[$key] = str_replace(' XVI ', ' 16 ', $title[$key]);
      $title[$key] = str_replace(' XVII ', ' 17 ', $title[$key]);
      $title[$key] = str_replace(' XVIII ', ' 18 ', $title[$key]);
      $title[$key] = str_replace(' XIX ', ' 19 ', $title[$key]);
      $title[$key] = str_replace(' XX ', ' 20 ', $title[$key]);
      $title[$key] = str_replace(' XXI ', ' 21 ', $title[$key]);

      //Века русские "иксы"
      $title[$key] = str_replace(' IХ ', ' 9 ', $title[$key]);
      $title[$key] = str_replace(' Х ', ' 10 ', $title[$key]);
      $title[$key] = str_replace(' ХI ', ' 11 ', $title[$key]);
      $title[$key] = str_replace(' ХII ', ' 12 ', $title[$key]);
      $title[$key] = str_replace(' ХIII ', ' 13 ', $title[$key]);
      $title[$key] = str_replace(' ХIV ', ' 14 ', $title[$key]);
      $title[$key] = str_replace(' ХV ', ' 15 ', $title[$key]);
      $title[$key] = str_replace(' ХVI ', ' 16 ', $title[$key]);
      $title[$key] = str_replace(' ХVII ', ' 17 ', $title[$key]);
      $title[$key] = str_replace(' ХVIII ', ' 18 ', $title[$key]);
      $title[$key] = str_replace(' ХIХ ', ' 19 ', $title[$key]);
      $title[$key] = str_replace(' ХХ ', ' 20 ', $title[$key]);
      $title[$key] = str_replace(' ХХI ', ' 21 ', $title[$key]);

      //Века различные вариации
      $title[$key] = str_replace(' 1 в.', ' 1век', $title[$key]);
      $title[$key] = str_replace(' 2 в.', ' 2век', $title[$key]);
      $title[$key] = str_replace(' 3 в.', ' 3век', $title[$key]);
      $title[$key] = str_replace(' 4 в.', ' 4век', $title[$key]);
      $title[$key] = str_replace(' 5 в.', ' 5век', $title[$key]);
      $title[$key] = str_replace(' 6 в.', ' 6век', $title[$key]);
      $title[$key] = str_replace(' 7 в.', ' 7век', $title[$key]);
      $title[$key] = str_replace(' 8 в.', ' 8век', $title[$key]);
      $title[$key] = str_replace(' 9 в.', ' 9век', $title[$key]);
      $title[$key] = str_replace(' 10 в.', ' 10век', $title[$key]);
      $title[$key] = str_replace(' 11 в.', ' 11век', $title[$key]);
      $title[$key] = str_replace(' 12 в.', ' 12век', $title[$key]);
      $title[$key] = str_replace(' 13 в.', ' 13век', $title[$key]);
      $title[$key] = str_replace(' 14 в.', ' 14век', $title[$key]);
      $title[$key] = str_replace(' 15 в.', ' 15век', $title[$key]);
      $title[$key] = str_replace(' 16 в.', ' 16век', $title[$key]);
      $title[$key] = str_replace(' 17 в.', ' 17век', $title[$key]);
      $title[$key] = str_replace(' 18 в.', ' 18век', $title[$key]);
      $title[$key] = str_replace(' 19 в.', ' 19век', $title[$key]);
      $title[$key] = str_replace(' 20 в.', ' 20век', $title[$key]);
      $title[$key] = str_replace(' 21 в.', ' 21век', $title[$key]);

      $title[$key] = str_replace(' XVI-XVIII ', ' 16-18 ', $title[$key]);

      $title[$key] = str_replace('16-18 в.', '16-18век', $title[$key]);
      $title[$key] = str_replace('17-18 в.', '17-18век', $title[$key]);

      $title[$key] = str_replace('XX-нач. ', '20-начало ', $title[$key]);
      $title[$key] = str_replace('к. 19 - н. 21 в.', 'Конец 19-начало 21века', $title[$key]);

      $title[$key] = str_replace('начало 21век', 'начало 21века', $title[$key]);

      $title[$key] = str_replace('16 - 18век', '16-18век', $title[$key]);
      $title[$key] = str_replace('17 - 18век', '17-18век', $title[$key]);

      $title[$key] = str_replace('до 10век', 'до 10века', $title[$key]);
      $title[$key] = str_replace('до 11век', 'до 11века', $title[$key]);
      $title[$key] = str_replace('до 12век', 'до 12века', $title[$key]);
      $title[$key] = str_replace('до 13век', 'до 13века', $title[$key]);
      $title[$key] = str_replace('до 14век', 'до 14века', $title[$key]);
      $title[$key] = str_replace('до 15век', 'до 15века', $title[$key]);
      $title[$key] = str_replace('до 16век', 'до 16века', $title[$key]);
      $title[$key] = str_replace('до 17век', 'до 17века', $title[$key]);
      $title[$key] = str_replace('до 18век', 'до 18века', $title[$key]);
      $title[$key] = str_replace('до 19век', 'до 19века', $title[$key]);
      $title[$key] = str_replace('до 20век', 'до 20века', $title[$key]);
      $title[$key] = str_replace('до 21век', 'до 21века', $title[$key]);

      $title[$key] = str_replace('векаа', 'века', $title[$key]);

      $title[$key] = str_replace('1500-1800 г.', '1500-1800г', $title[$key]);
      $title[$key] = str_replace('1800-1913 г.', '1800-1913г', $title[$key]);

      //Возраст
      $title[$key] = str_replace(' 5-7 ', ' 5-7лет ', $title[$key]);

      //Классы
      $title[$key] = str_replace('-й кл. ', 'й класс ', $title[$key]);
      $title[$key] = str_replace('-й кл ', 'й класс ', $title[$key]);

      $title[$key] = str_replace(' кл. ', 'кл ', $title[$key]);
      $title[$key] = str_replace(' кл ', 'кл ', $title[$key]);
      $title[$key] = str_replace('кл. ', 'кл ', $title[$key]);

      $title[$key] = str_replace('1 кл.', '1кл', $title[$key]);
      $title[$key] = str_replace('2 кл.', '2кл', $title[$key]);
      $title[$key] = str_replace('3 кл.', '3кл', $title[$key]);
      $title[$key] = str_replace('4 кл.', '4кл', $title[$key]);
      $title[$key] = str_replace('5 кл.', '5кл', $title[$key]);
      $title[$key] = str_replace('6 кл.', '6кл', $title[$key]);
      $title[$key] = str_replace('7 кл.', '7кл', $title[$key]);
      $title[$key] = str_replace('8 кл.', '8кл', $title[$key]);
      $title[$key] = str_replace('9 кл.', '9кл', $title[$key]);
      $title[$key] = str_replace('10 кл.', '10кл', $title[$key]);
      $title[$key] = str_replace('11 кл.', '11кл', $title[$key]);

      $title[$key] = str_replace('1кл.', '1кл', $title[$key]);
      $title[$key] = str_replace('2кл.', '2кл', $title[$key]);
      $title[$key] = str_replace('3кл.', '3кл', $title[$key]);
      $title[$key] = str_replace('4кл.', '4кл', $title[$key]);
      $title[$key] = str_replace('5кл.', '5кл', $title[$key]);
      $title[$key] = str_replace('6кл.', '6кл', $title[$key]);
      $title[$key] = str_replace('7кл.', '7кл', $title[$key]);
      $title[$key] = str_replace('8кл.', '8кл', $title[$key]);
      $title[$key] = str_replace('9кл.', '9кл', $title[$key]);
      $title[$key] = str_replace('10кл.', '10кл', $title[$key]);
      $title[$key] = str_replace('11кл.', '11кл', $title[$key]);

      $title[$key] = str_replace('1-4кл.', '1-4кл', $title[$key]);
      $title[$key] = str_replace('1-4 кл.', '1-4кл', $title[$key]);
      $title[$key] = str_replace('5-11кл.', '5-11кл', $title[$key]);
      $title[$key] = str_replace('5-11 кл.', '5-11кл', $title[$key]);

      //Книга
      $title[$key] = str_replace('Книга 1', 'К1', $title[$key]);
      $title[$key] = str_replace('Книга 2', 'К2', $title[$key]);
      $title[$key] = str_replace('Книга 3', 'К3', $title[$key]);
      $title[$key] = str_replace('Книга 4', 'К4', $title[$key]);

      $title[$key] = str_replace('в 2-х кн.', '2К', $title[$key]);
      $title[$key] = str_replace('в 3-х кн.', '3К', $title[$key]);
      $title[$key] = str_replace('в 4-х кн.', '4К', $title[$key]);

      //Листы
      $title[$key] = str_replace(' л ', 'л ', $title[$key]);
      $title[$key] = str_replace(' л. ', 'л ', $title[$key]);
      $title[$key] = str_replace('л. ', 'л ', $title[$key]);

      //Миллиметры
      $title[$key] = str_replace(' мм ', 'мм ', $title[$key]);

      //Форматы А1 А2...
      $title[$key] = str_replace('(А-1)', 'А1', $title[$key]);
      $title[$key] = str_replace('(А-2)', 'А2', $title[$key]);
      $title[$key] = str_replace('(А-3)', 'А3', $title[$key]);
      $title[$key] = str_replace('(А-4)', 'А4', $title[$key]);
      $title[$key] = str_replace('(А-5)', 'А5', $title[$key]);
      $title[$key] = str_replace('(А-6)', 'А6', $title[$key]);

      $title[$key] = str_replace(' А-1', ' А1', $title[$key]);
      $title[$key] = str_replace(' А-2', ' А2', $title[$key]);
      $title[$key] = str_replace(' А-3', ' А3', $title[$key]);
      $title[$key] = str_replace(' А-4', ' А4', $title[$key]);
      $title[$key] = str_replace(' А-5', ' А5', $title[$key]);
      $title[$key] = str_replace(' А-6', ' А6', $title[$key]);

      $title[$key] = str_replace(' (А1)', ' А1', $title[$key]);
      $title[$key] = str_replace(' (А2)', ' А2', $title[$key]);
      $title[$key] = str_replace(' (А3)', ' А3', $title[$key]);
      $title[$key] = str_replace(' (А4)', ' А4', $title[$key]);
      $title[$key] = str_replace(' (А5)', ' А5', $title[$key]);
      $title[$key] = str_replace(' (А6)', ' А6', $title[$key]);

      //Форматы - разновидности
      $title[$key] = str_replace('70х90', '70*90', $title[$key]);

/*  Издательства
---------------------------------------------- */
      $title[$key] = str_replace('(АСТ)', 'АСТ', $title[$key]);
      $title[$key] = str_replace('(Ак/кн)', 'Академия', $title[$key]);
      $title[$key] = str_replace('(Аделант)', 'Аделант', $title[$key]);
      $title[$key] = str_replace('(Азбука-клас.)', 'Азбука', $title[$key]);
      $title[$key] = str_replace('(Азбука)', 'Азбука', $title[$key]);
      $title[$key] = str_replace('(Айрис)', 'Айрис', $title[$key]);
      $title[$key] = str_replace('(Аквилегия)', 'Аквилегия', $title[$key]);
      $title[$key] = str_replace('(Антураж)', 'Антураж', $title[$key]);
      $title[$key] = str_replace('(Астрель)', 'Астрель', $title[$key]);
      $title[$key] = str_replace('(Атберг 98)', 'Атберг98', $title[$key]);
      $title[$key] = str_replace('Атберг 98', 'Атберг98', $title[$key]);
      $title[$key] = str_replace('(Атлас-Принт)', 'Атлас-Принт', $title[$key]);
      $title[$key] = str_replace('(Атлас Принт)', 'Атлас-Принт', $title[$key]);

      $title[$key] = str_replace('(Баласс)', 'Баласс', $title[$key]);
      $title[$key] = str_replace('(БАО-Пресс)', 'Бао-Пресс', $title[$key]);
      $title[$key] = str_replace('(БАО)', 'Бао-Пресс', $title[$key]);
      $title[$key] = str_replace('(Бао)', 'Бао-Пресс', $title[$key]);
      $title[$key] = str_replace('(Бао-Пресс)', 'Бао-Пресс', $title[$key]);
      $title[$key] = str_replace('(Баро-Пресс)', 'Баро-Пресс', $title[$key]);
      $title[$key] = str_replace('(БАРО-Пресс)', 'Баро-Пресс', $title[$key]);
      $title[$key] = str_replace('(БИНОМ)', 'Бином', $title[$key]);

      $title[$key] = str_replace('(В.-Пресс)', 'Вита-Пресс', $title[$key]);
      $title[$key] = str_replace('(В-Граф)', 'В-Граф', $title[$key]);
      $title[$key] = str_replace('(Вако)', 'Вако', $title[$key]);
      $title[$key] = str_replace('(Вернон)', 'Вернон', $title[$key]);
      $title[$key] = str_replace('(Вече)', 'Вече', $title[$key]);
      $title[$key] = str_replace('(Виктория+)', 'Виктория+', $title[$key]);
      $title[$key] = str_replace('(Владис)', 'Владис', $title[$key]);

      $title[$key] = str_replace('(Гном)', 'Гном', $title[$key]);

      $title[$key] = str_replace('(ДСК)', 'ДСК', $title[$key]);
      $title[$key] = str_replace('(Дрофа)', 'Дрофа', $title[$key]);

      $title[$key] = str_replace('(Каро)', 'Каро', $title[$key]);
      $title[$key] = str_replace('(Кн. Дом)', 'Книжный дом', $title[$key]);
      $title[$key] = str_replace('(Кладезь-Букс)', 'Кладезь-букс', $title[$key]);

      $title[$key] = str_replace('(Лада)', 'Лада', $title[$key]);
      $title[$key] = str_replace('(ЛадКом)', 'ЛадКом', $title[$key]);
      $title[$key] = str_replace('(Легион)', 'Легион', $title[$key]);
      $title[$key] = str_replace('(Линг)', 'Линг', $title[$key]);
      $title[$key] = str_replace('(Литур)', 'Литур', $title[$key]);

      $title[$key] = str_replace('(Малыш)', 'Малыш', $title[$key]);
      $title[$key] = str_replace('(Махаон)', 'Махаон', $title[$key]);
      $title[$key] = str_replace('(Мир Автокниг)', 'Мир автокниг', $title[$key]);
      $title[$key] = str_replace('(Мнемозина)', 'Мнемозина', $title[$key]);

      $title[$key] = str_replace('(Олма)', 'Олма', $title[$key]);
      $title[$key] = str_replace('(Омега)', 'Омега', $title[$key]);
      $title[$key] = str_replace('(Омега-Л)', 'Омега', $title[$key]);

      $title[$key] = str_replace('(Панорама)', 'Панорама', $title[$key]);
      $title[$key] = str_replace('(Перспектива)', 'Перспектива', $title[$key]);
      $title[$key] = str_replace('(Питер)', 'Питер', $title[$key]);
      $title[$key] = str_replace('(Планета дет.)', 'Планета детства', $title[$key]);
      $title[$key] = str_replace('(Планета дет)', 'Планета детства', $title[$key]);
      $title[$key] = str_replace('(ПродТерминал)', 'ПродТерминал', $title[$key]);
      $title[$key] = str_replace('(Прос.)', 'Просвещение', $title[$key]);
      $title[$key] = str_replace('(Проспект)', 'Проспект', $title[$key]);
      $title[$key] = str_replace('(Проф-Пресс)', 'Проф-Пресс', $title[$key]);
      $title[$key] = str_replace('(Проф-пресс)', 'Проф-Пресс', $title[$key]);

      $title[$key] = str_replace('(РС)', 'Русское слово', $title[$key]);
      $title[$key] = str_replace('(РУЗ.Ко)', 'РУЗ Ко', $title[$key]);
      $title[$key] = str_replace('(Рецепт-Холдинг)', 'Рецепт-Холдинг', $title[$key]);
      $title[$key] = str_replace('(Росмэн)', 'Росмэн', $title[$key]);
      $title[$key] = str_replace('(Рост-Кн.)', 'РОСТкнига', $title[$key]);
      $title[$key] = str_replace('(Русич)', 'Русич', $title[$key]);
      $title[$key] = str_replace('(Рыжий Кот)', 'Рыжий кот', $title[$key]);

      $title[$key] = str_replace('(Самовар)', 'Самовар', $title[$key]);
      $title[$key] = str_replace('(Сов. шк.)', 'Современная школа', $title[$key]);
      $title[$key] = str_replace('(Сов.шк.)', 'Современная школа', $title[$key]);
      $title[$key] = str_replace('(Стрекоза)', 'Стрекоза', $title[$key]);
      $title[$key] = str_replace('(Сфера)', 'Сфера', $title[$key]);

      $title[$key] = str_replace('(Тимошка)', 'Тимошка', $title[$key]);
      $title[$key] = str_replace('(Титул)', 'Титул', $title[$key]);

      $title[$key] = str_replace('(Удача)', 'Удача', $title[$key]);
      $title[$key] = str_replace('(Учимся читать)', 'Учимся читать', $title[$key]);

      $title[$key] = str_replace('(Фирма СТД)', 'Фирма СТД', $title[$key]);
      $title[$key] = str_replace('(Фламинго)', 'Фламинго', $title[$key]);

      $title[$key] = str_replace('(Харвест)', 'Харвест', $title[$key]);

      $title[$key] = str_replace('(Цитадель-трейд)', 'Цитадель-трейд', $title[$key]);

      $title[$key] = str_replace('(Эгмонт)', 'Эгмонт', $title[$key]);
      $title[$key] = str_replace('(Эксмо)', 'Эксмо', $title[$key]);
      $title[$key] = str_replace('(Экз.)', 'Экзамен', $title[$key]);
      $title[$key] = str_replace('(Экз)', 'Экзамен', $title[$key]);

      $title[$key] = str_replace('(Ювента)', 'Ювента', $title[$key]);
      $title[$key] = str_replace('(Юнвес)', 'Юнвес', $title[$key]);

      $title[$key] = str_replace('(Winx)', 'Winx', $title[$key]);

/*  Словари и разговорники
---------------------------------------------- */
      //Словари
      $title[$key] = str_replace('Англо-рус. и рус. англ. слов.', 'Англо-Русский«««', $title[$key]);
      $title[$key] = str_replace('англо-рус. и рус. англ. слов.', 'Англо-Русский«««', $title[$key]);
      $title[$key] = str_replace('англо-рус. и рус.англ.', 'Англо-Русский«««', $title[$key]);
      $title[$key] = str_replace('Немецко-рус. и рус. нем.', 'Немецко-Русский«««', $title[$key]);
      $title[$key] = str_replace('немецко-рус. и рус. нем.', 'Немецко-Русский«««', $title[$key]);

      $title[$key] = str_replace('Новый шк.', 'Новый школьный', $title[$key]);

      //Разговорники
      $title[$key] = str_replace('Русско-англ. разговорник', 'Русско-Английский разговорник', $title[$key]);
      $title[$key] = str_replace('Русско-нем. разговорник', 'Русско-Немецкий разговорник', $title[$key]);

      //Количество слов
      $title[$key] = str_replace(' тыс.', 'т.', $title[$key]);

/*  Сокращения
---------------------------------------------- */
      //Альбомы
      $title[$key] = str_replace('1 альбом', '(1й альбом)', $title[$key]);
      $title[$key] = str_replace('2 альбом', '(2й альбом)', $title[$key]);
      $title[$key] = str_replace('3 альбом', '(3й альбом)', $title[$key]);
      $title[$key] = str_replace('4 альбом', '(4й альбом)', $title[$key]);
      $title[$key] = str_replace('5 альбом', '(5й альбом)', $title[$key]);
      $title[$key] = str_replace('6 альбом', '(6й альбом)', $title[$key]);

      //Год обучения
      $title[$key] = str_replace('(1-й год обуч.)', '(1й год обучения)', $title[$key]);
      $title[$key] = str_replace('(2-й год обуч.)', '(2й год обучения)', $title[$key]);
      $title[$key] = str_replace('(3-й год обуч.)', '(3й год обучения)', $title[$key]);
      $title[$key] = str_replace('(4-й год обуч.)', '(4й год обучения)', $title[$key]);
      $title[$key] = str_replace('(5-й год обуч.)', '(5й год обучения)', $title[$key]);
      $title[$key] = str_replace('(6-й год обуч.)', '(6й год обучения)', $title[$key]);

      $title[$key] = str_replace(' 1-й год обуч.', ' (1й год обучения)', $title[$key]);
      $title[$key] = str_replace(' 2-й год обуч.', ' (2й год обучения)', $title[$key]);
      $title[$key] = str_replace(' 3-й год обуч.', ' (3й год обучения)', $title[$key]);
      $title[$key] = str_replace(' 4-й год обуч.', ' (4й год обучения)', $title[$key]);
      $title[$key] = str_replace(' 5-й год обуч.', ' (5й год обучения)', $title[$key]);
      $title[$key] = str_replace(' 6-й год обуч.', ' (6й год обучения)', $title[$key]);

      $title[$key] = str_replace('1-й год об.', '(1й год обучения)', $title[$key]);
      $title[$key] = str_replace('2-й год об.', '(2й год обучения)', $title[$key]);
      $title[$key] = str_replace('3-й год об.', '(3й год обучения)', $title[$key]);
      $title[$key] = str_replace('4-й год об.', '(4й год обучения)', $title[$key]);
      $title[$key] = str_replace('5-й год об.', '(5й год обучения)', $title[$key]);
      $title[$key] = str_replace('6-й год об.', '(6й год обучения)', $title[$key]);

      //Номера
      $title[$key] = str_replace('№ 1', '№1', $title[$key]);
      $title[$key] = str_replace('№ 2', '№2', $title[$key]);
      $title[$key] = str_replace('№ 3', '№3', $title[$key]);
      $title[$key] = str_replace('№ 4', '№4', $title[$key]);
      $title[$key] = str_replace('№ 5', '№5', $title[$key]);
      $title[$key] = str_replace('№ 6', '№6', $title[$key]);

      //Предметы
      $title[$key] = str_replace('по англ. яз.', 'по английскому языку', $title[$key]);
      $title[$key] = str_replace('по англ яз.', 'по английскому языку', $title[$key]);
      $title[$key] = str_replace('по Матем.', 'по математике', $title[$key]);
      $title[$key] = str_replace('по матем.', 'по математике', $title[$key]);
      $title[$key] = str_replace('по нем. яз.', 'по немецкому языку', $title[$key]);
      $title[$key] = str_replace('по немец. яз.', 'по немецкому языку', $title[$key]);
      $title[$key] = str_replace('по рус. яз ', 'по русскому языку ', $title[$key]);
      $title[$key] = str_replace('по рус. яз.', 'по русскому языку', $title[$key]);

      $title[$key] = str_replace('Алгебра и нач. матем. анализа', 'Алгебра и начало анализа', $title[$key]);
      $title[$key] = str_replace('Алгебра и нач. анализа', 'Алгебра и начало анализа', $title[$key]);
      $title[$key] = str_replace('алгебра и нач. анализа', 'алгебра и начало анализа', $title[$key]);
      $title[$key] = str_replace('Англ. яз. ', 'Английский язык ', $title[$key]);
      $title[$key] = str_replace('англ. яз. ', 'английский язык ', $title[$key]);

      $title[$key] = str_replace('Введение в общ. биол. и экол.', 'Введение в общую биологию и экологию', $title[$key]);
      $title[$key] = str_replace('Внеклассное чт.', 'Внеклассное чтение', $title[$key]);    
      $title[$key] = str_replace('Для внекл.чтения', 'Для внеклассного чтения', $title[$key]);    
      $title[$key] = str_replace('Всеобщая ист.', 'Всеобщая история', $title[$key]);
      $title[$key] = str_replace('всеобщая ист.', 'всеобщая история', $title[$key]);

      $title[$key] = str_replace('История России с др. вр.', 'История России с древнейших времен', $title[$key]);

      $title[$key] = str_replace('Комплекс. анализ текста', 'Комплексный анализ текста', $title[$key]);

      $title[$key] = str_replace('Лит. чтение', 'Литературное чтение', $title[$key]);
      $title[$key] = str_replace('лит. чтение', 'литературное чтение', $title[$key]);
      $title[$key] = str_replace('Лит-ное чтение', 'Литературное чтение', $title[$key]);
      $title[$key] = str_replace('лит-ное чтение', 'литературное чтение', $title[$key]);
      $title[$key] = str_replace('Лит-ре', 'Литературе', $title[$key]);
      $title[$key] = str_replace('лит-ре', 'литературе', $title[$key]);
      $title[$key] = str_replace('Лит-ра', 'Литература', $title[$key]);
      $title[$key] = str_replace('лит-ра', 'литература', $title[$key]);

      $title[$key] = str_replace('Матем.', 'Математика', $title[$key]);

      $title[$key] = str_replace('Немец. яз.', 'Немецкий язык', $title[$key]);
      $title[$key] = str_replace('немец. яз.', 'немецкий язык', $title[$key]);
      $title[$key] = str_replace('Нов. ист. зар. стран', 'Новейшая история зарубежных стран', $title[$key]);

      $title[$key] = str_replace('История Древ. мира', 'История древнего мира', $title[$key]);
      $title[$key] = str_replace('История древ. мира', 'История древнего мира', $title[$key]);
      $title[$key] = str_replace('История Нового вр.', 'История нового времени', $title[$key]);
      $title[$key] = str_replace('История Нов. вр.', 'История нового времени', $title[$key]);
      $title[$key] = str_replace('Ист. сред. веков', 'История средних веков', $title[$key]);
      $title[$key] = str_replace('ист. сред. веков', 'история средних веков', $title[$key]);
      $title[$key] = str_replace('История сред. веков', 'История средних веков', $title[$key]);
      $title[$key] = str_replace('История Сред. веков', 'История средних веков', $title[$key]);

      $title[$key] = str_replace('Окруж. мир', 'Окружающий мир', $title[$key]);

      $title[$key] = str_replace('Рус. азбука', 'Русская азбука', $title[$key]);
      $title[$key] = str_replace('Рус. яз.', 'Русский язык', $title[$key]);
      $title[$key] = str_replace('Рус. язык', 'Русский язык', $title[$key]);

      $title[$key] = str_replace('Франц. яз.', 'Французский язык', $title[$key]);

      //Части
      $title[$key] = str_replace('ч. 1', 'Ч1', $title[$key]);
      $title[$key] = str_replace('ч. 2', 'Ч2', $title[$key]);
      $title[$key] = str_replace('ч. 3', 'Ч3', $title[$key]);
      $title[$key] = str_replace('ч. 4', 'Ч4', $title[$key]);
      $title[$key] = str_replace('ч. 5', 'Ч5', $title[$key]);
      $title[$key] = str_replace('ч. 6', 'Ч6', $title[$key]);

      $title[$key] = str_replace('в 2-х. ч.', '2Ч', $title[$key]);
      $title[$key] = str_replace('в 3-х. ч.', '3Ч', $title[$key]);
      $title[$key] = str_replace('в 4-х. ч.', '4Ч', $title[$key]);
      $title[$key] = str_replace('в 5-х. ч.', '5Ч', $title[$key]);
      $title[$key] = str_replace('в 5-ти. ч.', '5Ч', $title[$key]);
      $title[$key] = str_replace('в 6-x. ч.', '6Ч', $title[$key]);
      $title[$key] = str_replace('в 6-ти. ч.', '6Ч', $title[$key]);

      $title[$key] = str_replace('в 2-х ч.', '2Ч', $title[$key]);
      $title[$key] = str_replace('в 3-х ч.', '3Ч', $title[$key]);
      $title[$key] = str_replace('в 4-х ч.', '4Ч', $title[$key]);
      $title[$key] = str_replace('в 5-х ч.', '5Ч', $title[$key]);
      $title[$key] = str_replace('в 5-ти ч.', '5Ч', $title[$key]);
      $title[$key] = str_replace('в 6-х ч.', '6Ч', $title[$key]);
      $title[$key] = str_replace('в 6-ти ч.', '6Ч', $title[$key]);

      $title[$key] = str_replace(' 2-х ч.', ' 2Ч', $title[$key]);
      $title[$key] = str_replace(' 3-х ч.', ' 3Ч', $title[$key]);
      $title[$key] = str_replace(' 4-х ч.', ' 4Ч', $title[$key]);
      $title[$key] = str_replace(' 5-х ч.', ' 5Ч', $title[$key]);
      $title[$key] = str_replace(' 5-ти ч.', ' 5Ч', $title[$key]);
      $title[$key] = str_replace(' 6-х ч.', ' 6Ч', $title[$key]);
      $title[$key] = str_replace(' 6-ти ч.', ' 6Ч', $title[$key]);

      //Сокращения: Разные
      $title[$key] = str_replace(' +CD', ' + CD', $title[$key]);
      $title[$key] = str_replace(' + зад.', ' + Задачник', $title[$key]);
      $title[$key] = str_replace(' +зад.', ' + Задачник', $title[$key]);
      $title[$key] = str_replace(' - в ассорт.', ' Ассорти*', $title[$key]);
      $title[$key] = str_replace(' Зелёная обложка', ' (Зеленая)', $title[$key]);
      $title[$key] = str_replace(' Зеленая обложка', ' (Зеленая)', $title[$key]);
      $title[$key] = str_replace(' зелёная обложка', ' (Зеленая)', $title[$key]);
      $title[$key] = str_replace(' зеленая обложка', ' (Зеленая)', $title[$key]);
      $title[$key] = str_replace(' (зеленая)', ' (Зеленая)', $title[$key]);
      $title[$key] = str_replace(' (синяя)', ' (Синяя)', $title[$key]);
      $title[$key] = str_replace(' (Учебник)', ' Учебник', $title[$key]);
      $title[$key] = str_replace(' (карточки)', ' (Карточки)', $title[$key]);
      $title[$key] = str_replace(' (карм.)', ' (Карманный)', $title[$key]);
      $title[$key] = str_replace(' вар.', ' вариантов', $title[$key]);
      $title[$key] = str_replace(' для д/с', ' для детского сада', $title[$key]);
      $title[$key] = str_replace(' Зеленая', ' (Зеленая)', $title[$key]);
      $title[$key] = str_replace(' Зелёная', ' (Зеленая)', $title[$key]);
      $title[$key] = str_replace(' лаб. работ ', ' лабораторных работ ', $title[$key]);
      $title[$key] = str_replace(' Пров. раб.', ' ПР', $title[$key]);
      $title[$key] = str_replace(' пров. раб.', ' ПР', $title[$key]);
      $title[$key] = str_replace(' с ком.', ' с комментариями', $title[$key]);
      $title[$key] = str_replace(' слов. ', ' словарь ', $title[$key]);
      $title[$key] = str_replace(' Синяя', ' (Синяя)', $title[$key]);
      $title[$key] = str_replace(' сп-к для поступ. в ВУЗы', ' справочник для поступающих в ВУЗы', $title[$key]);
      $title[$key] = str_replace(' соч. медалистов', ' сочинений медалистов', $title[$key]);
      $title[$key] = str_replace(' Я.', ' Я', $title[$key]);

      $title[$key] = str_replace('+CD', ' + CD', $title[$key]);
      $title[$key] = str_replace('+ СД', '+ CD', $title[$key]);
      $title[$key] = str_replace('+ зад.', ' + Задачник', $title[$key]);
      $title[$key] = str_replace('+зад.', ' + Задачник', $title[$key]);

      $title[$key] = str_replace('- в ассорт.', 'Ассорти*', $title[$key]);

      $title[$key] = str_replace('(расклад.)', '(Складная)', $title[$key]);
      $title[$key] = str_replace('(Раскладная)', '(Складная)', $title[$key]);
      $title[$key] = str_replace('(лиса и журавль)', '(Лиса и журавль)', $title[$key]);
      $title[$key] = str_replace('(син.)', '(Синяя)', $title[$key]);
      $title[$key] = str_replace('(подар)', '(Подарочная)', $title[$key]);

      $title[$key] = str_replace('33 накл.', '33н.', $title[$key]);

      $title[$key] = str_replace('а/м', 'автомобили', $title[$key]);
      $title[$key] = str_replace('Альбом зад.', 'Альбом заданий', $title[$key]);
      $title[$key] = str_replace('Аудио СД', 'Аудио CD', $title[$key]);

      $title[$key] = str_replace('БК', 'Большая книга', $title[$key]);
      $title[$key] = str_replace('БИЭ', 'Большая иллюстрированная энциклопедия', $title[$key]);
      $title[$key] = str_replace('Баз. и проф. ур.', 'Базовый и профессиональный уровень', $title[$key]);
      $title[$key] = str_replace('Баз. уров.', 'Базовый уровень', $title[$key]);
      $title[$key] = str_replace('Баз. ур.', 'Базовый уровень', $title[$key]);
      $title[$key] = str_replace('Банк зад.', 'Банк заданий', $title[$key]);
      $title[$key] = str_replace('без подсветки', 'Без подсветки', $title[$key]);
      $title[$key] = str_replace('Брянская обл.', 'Брянская область', $title[$key]);

      $title[$key] = str_replace('Введ. ', 'Введение ', $title[$key]);
      $title[$key] = str_replace('в табл. и схемах', 'в таблицах и схемах', $title[$key]);

      $title[$key] = str_replace('Гов. бабушкины сказки', 'Говорящие бабушкины сказки', $title[$key]);
      $title[$key] = str_replace('Готовлюсь в шк.', 'Готовлюсь в школу', $title[$key]);
      $title[$key] = str_replace('газета', '(Газета)', $title[$key]);

      $title[$key] = str_replace('для нач. шк.', 'для начальной школы', $title[$key]);

      $title[$key] = str_replace('ЕГЭ Экз 20', 'ЕГЭ Экзамен 20', $title[$key]);
      $title[$key] = str_replace('ЕГЭ Экз. 20', 'ЕГЭ Экзамен 20', $title[$key]);

      $title[$key] = str_replace('Ё', 'Е', $title[$key]);
      $title[$key] = str_replace('ё', 'е', $title[$key]);

      $title[$key] = str_replace('Зачетная работа', 'ЗР', $title[$key]);
      $title[$key] = str_replace('Зачетные работы', 'ЗР', $title[$key]);
      $title[$key] = str_replace('Зачет. раб.', 'ЗР', $title[$key]);
      $title[$key] = str_replace('Зач. раб.', 'ЗР', $title[$key]);
      $title[$key] = str_replace('зелёная', '(Зеленая)', $title[$key]);
      $title[$key] = str_replace('зеленая', '(Зеленая)', $title[$key]);
      $title[$key] = str_replace('Зад. по физике', 'Задачник по физике', $title[$key]);
      $title[$key] = str_replace('За курс нач. шк.', 'За курс начальной школы', $title[$key]);
      $title[$key] = str_replace('за курс нач. шк.', 'За курс начальной школы', $title[$key]);

      $title[$key] = str_replace('Итог. тест.', 'Итоговое тестирование', $title[$key]);
      $title[$key] = str_replace('и др.', 'и другие', $title[$key]);

      $title[$key] = str_replace('Кк ', 'Контурная карта ', $title[$key]);
      $title[$key] = str_replace('Контрольные работы', 'КР', $title[$key]);
      $title[$key] = str_replace('Контр. раб.', 'КР', $title[$key]);
      $title[$key] = str_replace('Кн. для чт.', 'Книга для чтения', $title[$key]);
      $title[$key] = str_replace('книга + иллюстрированный материал', 'Книга + иллюстрированный материал', $title[$key]);

      $title[$key] = str_replace('Логопедические дом. зад.', 'Логопедические домашние задания', $title[$key]);

      $title[$key] = str_replace('Многообраз. живых орган.', 'Многообразие живых организмов', $title[$key]);
      $title[$key] = str_replace('Муфта, Полботинка и Мох. Борода', 'Муфта, Полботинка и Моховая Борода', $title[$key]);

      $title[$key] = str_replace('НХ', 'Новейшая хрестоматия', $title[$key]);
      $title[$key] = str_replace('Настольная кн.', 'Настольная книга', $title[$key]);
      $title[$key] = str_replace('Нац. образ.', 'Национальное образование', $title[$key]);

      $title[$key] = str_replace('ОГЭ Экз. 20', 'ОГЭ Экзамен 20', $title[$key]);
      $title[$key] = str_replace('Орфогр. словарь рус. яз.', 'Орфографический словарь русского языка', $title[$key]);
      $title[$key] = str_replace('Орфогр. словарь', 'Орфографический словарь', $title[$key]);
      $title[$key] = str_replace('Офсет', '(Офсет)', $title[$key]);
      $title[$key] = str_replace('офсет.', '(Офсет)', $title[$key]);

      $title[$key] = str_replace('ПХ', 'Полная хрестоматия', $title[$key]);
      $title[$key] = str_replace('ПДД с коммент.', 'ПДД с комментариями', $title[$key]);
      $title[$key] = str_replace('Пров. и контр. раб.', 'Проверочные и КР', $title[$key]);
      $title[$key] = str_replace('Проверочные работы', 'ПР', $title[$key]);
      $title[$key] = str_replace('Проф. ур.', 'Профессиональный уровень', $title[$key]);
      $title[$key] = str_replace('правила сов. рус. яз.', 'правила современного русского языка', $title[$key]);
      $title[$key] = str_replace('Полная иллюстр. хрест.', 'Полная иллюстрированная хрестоматия', $title[$key]);
      $title[$key] = str_replace('Полный курс рус. яз.', 'Полный курс русского языка', $title[$key]);    
      $title[$key] = str_replace('по рус. и зар. литературе', 'по русской и зарубежной литературе', $title[$key]);

      $title[$key] = str_replace('Р. т.', 'РТ', $title[$key]);
      $title[$key] = str_replace('Р.т.', 'РТ', $title[$key]);
      $title[$key] = str_replace('Развивающие зад.', 'Развивающие задания', $title[$key]);
      $title[$key] = str_replace('развивающие зад.', 'развивающие задания', $title[$key]);

      $title[$key] = str_replace('Сб. упр.', ' Сборник упражнений', $title[$key]);
      $title[$key] = str_replace('соч.-шпаргалок', 'сочинений-шпаргалок', $title[$key]);
      $title[$key] = str_replace('словарь рус. яз.', 'словарь русского языка', $title[$key]);
      $title[$key] = str_replace('с Древ. вр. ', 'с древнейших времен ', $title[$key]);
      $title[$key] = str_replace('Самостоятельные. и контрольные работы', 'Самостоятельные и КР', $title[$key]);
      $title[$key] = str_replace('Самост. и контр. работы', 'Самостоятельные и КР', $title[$key]);
      $title[$key] = str_replace('Сам. и контр. работы', 'Самостоятельные и КР', $title[$key]);
      $title[$key] = str_replace('Сам. и контр. раб.', 'Самостоятельные и КР', $title[$key]);
      $title[$key] = str_replace('сам. и контр. раб.', 'самостоятельные и КР', $title[$key]);
      $title[$key] = str_replace('Самостоятельная работа', 'СР', $title[$key]);
      $title[$key] = str_replace('Самостоятельные работы', 'СР', $title[$key]);
      $title[$key] = str_replace('Сам. раб.', 'СР', $title[$key]);

      $title[$key] = str_replace('Тет. по чистописанию', 'Тетрадь по чистописанию', $title[$key]);
      $title[$key] = str_replace('Тестовые зад.', 'Тестовые задания', $title[$key]);
      $title[$key] = str_replace('Тренир. задачи', 'Тренировочные задачи', $title[$key]);
      $title[$key] = str_replace('Табл. умножение...', 'Таблица умножения*', $title[$key]);
      $title[$key] = str_replace('Тренир. примеры', 'Тренировочные примеры', $title[$key]);
      $title[$key] = str_replace('Тренир. задания', 'Тренировочные задания', $title[$key]);
      $title[$key] = str_replace('Тесты, игры. упр.', 'Тесты, игры, упражнения', $title[$key]);

      $title[$key] = str_replace('Учеб.', 'Учебник', $title[$key]);

      $title[$key] = str_replace('шк. прог. в крат. из.', 'Школьная программа в кратком изложении', $title[$key]);
      $title[$key] = str_replace('Школьный словообр. словарь', 'Школьный словообразовательный словарь', $title[$key]);

      $title[$key] = str_replace('Экз билеты', 'Экзаменационные билеты', $title[$key]);
      $title[$key] = str_replace('Экз. билеты', 'Экзаменационные билеты', $title[$key]);
      $title[$key] = str_replace('Энц. занимательных наук', 'Энциклопедия занимательных наук', $title[$key]);

/*  Удаляем вообще
---------------------------------------------- */
      $title[$key] = str_replace(' !!!', '', $title[$key]);
      $title[$key] = str_replace(', вырубка...', '', $title[$key]);
      $title[$key] = str_replace(' (Под.) 70/90/16 ', '', $title[$key]);

      $title[$key] = str_replace(' к уч.', '', $title[$key]);
      $title[$key] = str_replace(' к учеб.', '', $title[$key]);  
      $title[$key] = str_replace(' газет.', '', $title[$key]);
      $title[$key] = str_replace(' мяг.', '', $title[$key]); 
      $title[$key] = str_replace(' покет ст10', '', $title[$key]); 
      $title[$key] = str_replace(' покет ст8', '', $title[$key]);
      $title[$key] = str_replace(' с наклейками', '', $title[$key]);
      $title[$key] = str_replace(' ст6', '', $title[$key]);
      $title[$key] = str_replace(' ст8', '', $title[$key]);
      $title[$key] = str_replace(' ст10', '', $title[$key]);
      $title[$key] = str_replace(' ст14', '', $title[$key]);
      $title[$key] = str_replace(' ст16', '', $title[$key]);
      $title[$key] = str_replace(' тверд.', '', $title[$key]);

/*  Знаки и пробелы
---------------------------------------------- */
    $title[$key] = str_replace('"', '', $title[$key]);
    $title[$key] = str_replace('   ', ' ', $title[$key]);
    $title[$key] = str_replace('  ', ' ', $title[$key]);

/* Исправляем неудачные замены
---------------------------------------------- */
    $title[$key] = str_replace('(Синяя) птица', 'Синяя птица', $title[$key]);
  }

    return $title;
  }
?>