// Сразу после загрузки документа
// ==========================================================================

  //
  //Скрываем блок с подсчётом колонок в накладной (если там нечего выводить!)
  //Скрываем кнопку "Продолжить" (если хотя бы одно поле пустое)
  function winLoad() {
    res();
  }

// Модальные окна
// ==========================================================================

// Настройки модального окна для выбора даты
$(function(){
    $('.date_doc').will_pickdate({

      format: 'd.m.Y',

      //Русификация
      days: ['Понедельник', 'Вторник', 'Среда', 'Четверг','Пятница', 'Суббота', 'Воскресенье'],
      months: ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'],
    });
});

// Различные функции
// ==========================================================================

 /* Определим общие переменные
---------------------------------------------- */
var number_doc   = document.querySelector('#number_doc');

var provider     = document.querySelector('#provider');
var identifier   = document.querySelector('#identifier');

var strNum       = document.querySelector('#strNum');
var author       = document.querySelector('#author');
var title        = document.querySelector('#title');
var barcode      = document.querySelector('#barcode');
var quantity     = document.querySelector('#quantity');
var price        = document.querySelector('#price');

var verification = document.querySelector('.verification');

/* Функция
    Количество строк в исходной накладной определяем по колонке "Цена" (предполагается,
     что именно колонка "Цена" заполняется последней и потому по ней сверяем
     количество строк с накладной)
---------------------------------------------- */
function colomnLineReal(){

  var str     = price.value;
  var message = 'Количество строк в накладной - ';
 
  if (str != '') {

    //Очищаем колонку с номерами строк
    strNum.value = '';

    //Определяем количество строк в колонке "Цена"
    qtStr = str.split('\n').length-1;

    verification.classList.add('show-object');
    verification.innerHTML = message + qtStr + ' ?';

    for( i=1; i <= qtStr; i++ ) {strNum.value += i + '.\n';}
  } 
  else {
    document.querySelector('.verification').classList.remove('show-object');
    strNumbering();
  }
}

/* Функция
    Своя кнопка reset - "Очистить" (Для того, чтобы не трогать поле с датой)
---------------------------------------------- */
function res() {
    number_doc.value = '';

    provider.selectedIndex    = '0';
    identifier.selectedIndex  = '0';

    strNum.value     = '';
    author.value     = '';
    title.value      = '';
    barcode.value    = '';
    quantity.value   = '';
    price.value      = '';

    number_doc.focus();

    btnShow();
    colomnLineReal();
    columnAuthorShow();
    strNumbering();
  return
}


/* Функция
    Поведение кнопки "Продолжить" - Показываем/Скрываем
---------------------------------------------- */
function btnShow() {
  if (number_doc.value != '' &&
      //author.value     != '' &&
      title.value      != '' &&
      barcode.value    != '' &&
      quantity.value   != '' &&
      price.value      != ''
     )
        {
          document.querySelector('.btn-continue').classList.add('show-object');
        }
        else {
          document.querySelector('.btn-continue').classList.remove('show-object');
        }
}

/* Функция
    Убираем колонку "Автор" для всех поставщиков кроме "Белкина"
      и выставляем идентификацию по артиклу
---------------------------------------------- */
function columnAuthorHidden() {
  var columnAuthor = document.querySelectorAll('.author');

  author.value     = '';

  columnAuthor[0].classList.add('hidden-object');
  columnAuthor[1].classList.add('hidden-object');

  identifier.selectedIndex  = '1';

  number_doc.focus();
}

/* Функция
    Показываем колонку "Автор", когда выбран поставщик Белкин
      и выставляем идентификацию по штрихКоду
---------------------------------------------- */
function columnAuthorShow() {
  var columnAuthor = document.querySelectorAll('.author');
  
  columnAuthor[0].classList.remove('hidden-object');
  columnAuthor[1].classList.remove('hidden-object');

  identifier.selectedIndex  = '0';

  number_doc.focus();
}

/* Функция
    Заполняем первую колонку номерами строк
---------------------------------------------- */
function strNumbering() {
  //strNum.value = '1.\n2.\n3.\n4.\n5.\n6.\n7.\n8.\n9.\n10.\n11.\n12.\n13.\n14.\n15.\n16.\n17.\n18.\n19.\n20.\n21.\n22.\n23.\n24.\n25.\n26.\n27.\n28.\n29.\n30.\n31.\n32.\n33.\n34.\n35.\n36.\n37.\n38.\n39.\n40.\n41.\n42.\n43.\n44.\n45.\n46.\n47.\n48.\n49.\n50.\n51.\n52.\n53.\n54.\n55.\n56.\n57.\n58.\n59.\n60.\n61.\n62.\n63.\n64.\n65.\n66.\n67.\n68.\n69.\n70.\n71.\n72.\n73.\n74.\n75.\n76.\n77.\n78.\n79.\n80.\n81.\n82.\n83.\n84.\n85.\n86.\n87.\n88.\n89.\n90.\n91.\n92.\n93.\n94.\n95.\n96.\n97.\n98.\n99.\n100.';
  strNum.value = '1.\n2.\n3.\n4.\n5.\n6.\n7.\n8.\n9.\n10.\n11.\n12.\n13.\n14.\n15.\n16.\n17.\n18.\n19.\n20.';
}

/* Функция
    Если выбран поставщик Самсон, то колонку "Артикул" заполняем
    из колонки "Название".

    Алгоритм работы:
      Очищаем пришедшие данные (то что введено в колонку "Название")
        - Удаляются лишние пробелы и табуляция;
        - Каждая строка становится элементом массива new_Title [];

      - Известно, что первые шесть символов в строке это всегда код Самсона вырезаем его;

      - Проверка №1А Вырезаем "предполагаемый" артикул с конца строки до пробела и если
        это число, то цикл закончен, если нет, дополняем его списком слов которые являются
        частью артикула...

      - Проверка №1Б Вырезаем "предполагаемый" артикул - последние шесть символов строки,
        и если это число - то это артикул;
 
      - Определяем разделяющий символ - запятая или пробел...
      - Вырезаем "предполагаемый" артикул с конца строки до разделяющего символа...
      - Начинаем его очистку:
        -- Удалим слова, которые могут попасть в "предполагаемый" артикул, но 
           артиклом не являются...

      - Проверка №2А Если после очистки "артикул" меньше двух символов, то артикул - код;
      - Проверка №2Б Если после очистки осталось только число, то это и есть артикул;

      - Проверка №3 Проверяем символы наличие которых свидетельствует, 
        что артикула в наименовании нет, поэтому артикул - код Самсона;

      - Проверка №4 А вдруг с конца строки до артикула стоит цвет, тогда удаляем его
        и запускаем цикл заново;

      - Если "артикул" прошёл через все эти проверки, то берём его в первоначальном виде;
---------------------------------------------- */
function samsonRename() {

  if( (provider.selectedIndex == '1') && (identifier.selectedIndex  == '1') && (title.value != '') ) {

   var new_Title = title.value;

 /* Очистка пришедших данных 
---------------------------------------------- */
    var del_Sym = '\n';    
    for( i=0; i<del_Sym.length; i++ ) new_Title = new_Title.replace( RegExp(del_Sym[i], 'g'), ';' );

    //Массивы с символами для удаления
    del_Symbol  = ['\r\n', '"', "'", '\r', '\n', '\t', '&'];
    del_Symbol0 = ['     ', '    ', '   ', '  '];

    //Удаляем лишние пробелы и табуляцию
    for( i=0; i<del_Symbol0.length; i++ ) new_Title = new_Title.replace( RegExp(del_Symbol0[i], 'g'), ' ' );
    for( i=0; i<del_Symbol.length; i++ ) new_Title = new_Title.replace( RegExp(del_Symbol[i], 'g'), '' );


    //Аналог trim() из php. Пробелы с начала и с конца строки
    new_Title = new_Title.trim();

    //Разбиваем строку на массив
    new_Title = new_Title.split(';');
    new_Title.pop();

    //Удаляем лишние пробелы и табуляцию из каждого элемента массива
    for( i=0; i<new_Title.length; i++ ) new_Title[i] = $.trim( new_Title[i] );

 /* Вырезаем код Самсона из строки окончательно и записываем его в переменную
---------------------------------------------- */
    //Код Самсона
    var samsonCode = [];

    //Артикул
    var samsonVCode = [];

    //Артикул не очищенный
    var dirty_samsonVCode = [];

    for( i=0; i<new_Title.length; i++ ) {
      samsonCode[i] = new_Title[i].substring(0, 6);
      new_Title[i]  = new_Title[i].replace(samsonCode[i], '');
      new_Title[i]  = new_Title[i].trim();
    }

    for( i=0; i<new_Title.length; i++ ) {
 
      //Проверка №1А Вырезаем "предполагаемый" артикул - с конца строки до пробела. И если это число - то это артикул
      var from = new_Title[i].lastIndexOf(' '); 
      var to   = new_Title[i].length;

      samsonVCode[i] = new_Title[i].substring(from,to);
      samsonVCode[i] = samsonVCode[i].trim();

      //Если в "артикуле" есть эти слова, то это артикул
      if ( new_Title[i].lastIndexOf('Noris') != '-1' ) {
        new_Title[i] = new_Title[i].replace((', '+samsonVCode[i]), '');
        new_Title[i] = new_Title[i].replace((','+samsonVCode[i]), '');
        new_Title[i] = new_Title[i].replace((' '+samsonVCode[i]), '');
        new_Title[i] = new_Title[i].replace((samsonVCode[i]), '');
        continue;
      }

      if ( $.isNumeric(samsonVCode[i]) ) {
        new_Title[i] = new_Title[i].replace((', '+samsonVCode[i]), '');
        new_Title[i] = new_Title[i].replace((','+samsonVCode[i]), '');
        new_Title[i] = new_Title[i].replace((' '+samsonVCode[i]), '');
        new_Title[i] = new_Title[i].replace((samsonVCode[i]), '');
        continue;
      }

      //Если в строке есть эти слова, то они являются частью артикула
      if ( new_Title[i].lastIndexOf('ЛШ ') != '-1' ) { samsonVCode[i] = 'ЛШ ' + samsonVCode[i]; }

      //Проверка №1Б Вырезаем "предполагаемый" артикул - последние шесть символов строки. И если это число - то это артикул
      var from = new_Title[i].length-6; 
      var to = new_Title[i].length;

      samsonVCode[i] = new_Title[i].substring(from,to);
      samsonVCode[i] = samsonVCode[i].trim();

     if ( $.isNumeric(samsonVCode[i]) ) {
        new_Title[i] = new_Title[i].replace((', '+samsonVCode[i]), '');
        new_Title[i] = new_Title[i].replace((','+samsonVCode[i]), '');
        new_Title[i] = new_Title[i].replace((' '+samsonVCode[i]), '');
        new_Title[i] = new_Title[i].replace((samsonVCode[i]), '');
        continue;
      }

      //Определяем разделяющий символ - запятая или пробел
      var delimiter = ' ';
      if (new_Title[i].lastIndexOf(',') == '-1') { delimiter = new_Title[i].lastIndexOf(' ');}
      else delimiter = new_Title[i].lastIndexOf(',');

      //Вырезаем "предполагаемый" артикул с конца строки до разделяющего символа и очищаем его
      var from = delimiter; 
      var to = new_Title[i].length;

      dirty_samsonVCode[i] = new_Title[i].substring(from,to);
      samsonVCode[i] = dirty_samsonVCode[i];

        //Удаляем слова и символы, которые могут попасть в артикул, но артикулом не являются

        //Не удаляем скобки, если перед ними эти символы
        if (   (samsonVCode[i].lastIndexOf('(B') == '-1') &&
               (samsonVCode[i].lastIndexOf('(V') == '-1') &&
               (samsonVCode[i].lastIndexOf('(T') == '-1') &&
               (samsonVCode[i].lastIndexOf('(Т') == '-1') &&
               (samsonVCode[i].lastIndexOf('(H') == '-1') &&
               (samsonVCode[i].lastIndexOf('(S') == '-1')
            )
        {
          samsonVCode[i] = samsonVCode[i].replace('(', '');
          samsonVCode[i] = samsonVCode[i].replace(')', '');
        }

        //Запятые
        samsonVCode[i] = samsonVCode[i].replace(',', '');

        //Пробелы
        samsonVCode[i] = samsonVCode[i].replace('   ', '');
        samsonVCode[i] = samsonVCode[i].replace('  ', '');
        samsonVCode[i] = samsonVCode[i].replace(' ', '');
        samsonVCode[i] = samsonVCode[i].replace(' ', '');
        samsonVCode[i] = samsonVCode[i].replace(' ', '');
        samsonVCode[i] = samsonVCode[i].replace(' ', '');
        samsonVCode[i] = samsonVCode[i].replace(' ', '');

        samsonVCode[i] = samsonVCode[i].replace('арт.', '');
        samsonVCode[i] = samsonVCode[i].replace('глянцевая', '');
        samsonVCode[i] = samsonVCode[i].replace('круч.', '');
        samsonVCode[i] = samsonVCode[i].replace('шк', '');
        samsonVCode[i] = samsonVCode[i].replace('ш/к', '');

        samsonVCode[i] = samsonVCode[i].replace('HB', '');
        samsonVCode[i] = samsonVCode[i].replace('НВ', '');

        samsonVCode[i] = samsonVCode[i].trim();

      //Проверка №2А Если артикул в результате очистки меньше двух символов, то артикул - это код Самсона
      if ( (samsonVCode[i].length) <= '1') {samsonVCode[i] = samsonCode[i]; continue}

      //Проверка №2Б Если после Очистки осталось только число, то это и есть артикул
      if ( (samsonVCode[i] == samsonCode[i]) || ($.isNumeric(samsonVCode[i])) ) {
        new_Title[i] = new_Title[i].replace((dirty_samsonVCode[i]), '');       
        continue;
      }

      //Проверка №3 Проверяем символы наличие которых свидетельствует, что артикула в наименовании нет

      //Внимание!
        //Здесь уже не может быть пробела в сочетании слов или символов

      //Если на конце что-то типа 177х80мм
      if ( (samsonVCode[i].lastIndexOf('х') != '-1') && (samsonVCode[i].lastIndexOf('мм') != '-1') )
        { samsonVCode[i] = samsonCode[i]; continue; }

      //Символы и Английский
      if ( samsonVCode[i].search('%')   !=-1 ) {samsonVCode[i] = samsonCode[i]; continue;}
      if ( samsonVCode[i].search('CIE') !=-1 ) {samsonVCode[i] = samsonCode[i]; continue;}

      //Бренд
      if ( samsonVCode[i].search('BRAUBERG')!=-1 ) {samsonVCode[i] = samsonCode[i]; continue;}
      if ( samsonVCode[i].search('STAFF')   !=-1 ) {samsonVCode[i] = samsonCode[i]; continue;}

      //Страна производитель
      if ( samsonVCode[i].search('Англия')!=-1 )   {samsonVCode[i] = samsonCode[i]; continue;}
      if ( samsonVCode[i].search('Болгария')!=-1 ) {samsonVCode[i] = samsonCode[i]; continue;}
      if ( samsonVCode[i].search('Италия')!=-1 )   {samsonVCode[i] = samsonCode[i]; continue;}
      if ( samsonVCode[i].search('Россия')!=-1 )   {samsonVCode[i] = samsonCode[i]; continue;}

      //Остальное
      if ( samsonVCode[i].search('9мм')         !=-1 ) {samsonVCode[i] = samsonCode[i]; continue;}
      if ( samsonVCode[i].search('20м')         !=-1 ) {samsonVCode[i] = samsonCode[i]; continue;}
      if ( samsonVCode[i].search('35мм')        !=-1 ) {samsonVCode[i] = samsonCode[i]; continue;}
      if ( samsonVCode[i].search('18л')         !=-1 ) {samsonVCode[i] = samsonCode[i]; continue;}
      if ( samsonVCode[i].search('100шт')       !=-1 ) {samsonVCode[i] = samsonCode[i]; continue;}
      if ( samsonVCode[i].search('500л.')       !=-1 ) {samsonVCode[i] = samsonCode[i]; continue;}
      if ( samsonVCode[i].search('бизнес-класс')!=-1 ) {samsonVCode[i] = samsonCode[i]; continue;}
      if ( samsonVCode[i].search('на200л')      !=-1 ) {samsonVCode[i] = samsonCode[i]; continue;}
      if ( samsonVCode[i].search('оригинальный')!=-1 ) {samsonVCode[i] = samsonCode[i]; continue;}
      if ( samsonVCode[i].search('см')          !=-1 ) {samsonVCode[i] = samsonCode[i]; continue;}
      if ( samsonVCode[i].search('сподсветкой') !=-1 ) {samsonVCode[i] = samsonCode[i]; continue;}

      //Проверка №4 А вдруг с конца строки до артикула стоит цвет, тогда удаляем его и запускаем цикл заново
      if (
          samsonVCode[i].search('красный')   != -1 ||
          samsonVCode[i].search('красная')   != -1 ||

          samsonVCode[i].search('оранжевый') != -1 ||
          samsonVCode[i].search('оранжевая') != -1 ||

          samsonVCode[i].search('желтый')    != -1 ||
          samsonVCode[i].search('жёлтый')    != -1 ||
          samsonVCode[i].search('желтая')    != -1 ||
          samsonVCode[i].search('жёлтая')    != -1 ||

          samsonVCode[i].search('зеленый')   != -1 ||
          samsonVCode[i].search('зелёный')   != -1 ||
          samsonVCode[i].search('зеленая')   != -1 ||
          samsonVCode[i].search('зелёная')   != -1 ||

          samsonVCode[i].search('голубой')   != -1 ||
          samsonVCode[i].search('голубая')   != -1 ||

          samsonVCode[i].search('син')       != -1 ||
          samsonVCode[i].search('синий')     != -1 ||
          samsonVCode[i].search('синяя')     != -1 ||

          samsonVCode[i].search('фиолетовый')!= -1 ||
          samsonVCode[i].search('фиолетовая')!= -1 ||

          samsonVCode[i].search('черн')    != -1 ||
          samsonVCode[i].search('черный')    != -1 ||
          samsonVCode[i].search('чёрный')    != -1 ||
          samsonVCode[i].search('черная')    != -1 ||
          samsonVCode[i].search('чёрная')    != -1 ||

          samsonVCode[i].search('белая')     != -1 ||

          samsonVCode[i].search('белый')     != -1)
      {
        new_Title[i] = new_Title[i].replace((', '+samsonVCode[i]), '');
        new_Title[i] = new_Title[i].replace((','+samsonVCode[i]), '');
        new_Title[i] = new_Title[i].replace((' '+samsonVCode[i]), '');
        new_Title[i] = new_Title[i].replace((samsonVCode[i]), '');

       --i;
        if( i == new_Title.length-1 ) break;
        continue;
      }

        new_Title[i] = new_Title[i].replace((dirty_samsonVCode[i]), '');
        new_Title[i] = new_Title[i].trim();
      }

      title.value   = '';
      barcode.value = '';

      //Выводим результат функции в колонке Название и Идентификатор
      title.value     = new_Title.join('\n') + '\n';
      barcode.value   = samsonVCode.join('\n') + '\n';
    }
}

/* Функция Если выбран поставщик Белкин, то производим очистку наименований
---------------------------------------------- */
function belkinRename() {

  if( (provider.selectedIndex == '0') && (identifier.selectedIndex  == '0') && (title.value != '') ) {

   var new_Title = title.value;

 /* Очистка пришедших данных 
---------------------------------------------- */
    var del_Sym = '\n';    
    for( i=0; i<del_Sym.length; i++ ) new_Title = new_Title.replace( RegExp(del_Sym[i], 'g'), ';' );

    //Массив с символами для удаления
    del_Symbol  = ['\r\n', '"', "'", '\r', '\n', '\t'];
    del_Symbol0 = ['     ', '    ', '   ', '  '];

    //Удаляем лишние пробелы и табуляцию
    for( i=0; i<del_Symbol.length; i++ ) new_Title = new_Title.replace( RegExp(del_Symbol[i], 'g'), '' );
    for( i=0; i<del_Symbol0.length; i++ ) new_Title = new_Title.replace( RegExp(del_Symbol0[i], 'g'), ' ' );

    //Аналог trim() из php. Пробелы с начала и с конца строки
    new_Title = new_Title.trim();

    //Разбиваем строку на массив
    new_Title = new_Title.split(';');
    new_Title.pop();

    //Удаляем лишние пробелы и табуляцию из каждого элемента массива
    for( i=0; i<new_Title.length; i++ ) new_Title[i] = $.trim( new_Title[i] );

/* ----------------------------------------------
    Начинаем переименовывать

      Список замен разбит на следующие разделы:

        - Частные случаи (Строка меняется полностью);
        - Общие случаи:
          -- Века;
          -- Века русские "иксы"; 
          -- Века различные вариации; 
          -- Возраст;
          -- Классы;
          -- Книга;
          -- Листы;
          -- Миллиметры;
          -- Форматы А1 А2...;
          -- Форматы другие;

        - Издательства;
        - Словари и разговорники:
          -- Словари;
          -- Разговорники;
          -- Количество слов;

        - Сокращения:
          -- Альбомы;
          -- Год обучения;
          -- Номера;
          -- Предметы;
          -- Части;
          -- Разные;

        - Знаки и пробелы;
        - Удаляем вообще;
        - Исправляем неудачные замены;

        - Новые - несортированные;
---------------------------------------------- */
    for( i=0; i<new_Title.length; i++ ) {

/*  Частные случаи
---------------------------------------------- */
      new_Title[i] = new_Title[i].replace('Атлас Карт. с к/к 3-5 кл. Природоведение', 'Атлас с контурными картами 3-5кл Природоведение');

      new_Title[i] = new_Title[i].replace('Букварь (Литур) Сред.', 'Букварь Литур (Средний)');
      new_Title[i] = new_Title[i].replace('Биология Общие закон. 9 кл. (Дрофа) Р. т. к учеб. Мамонтова', 'Биология Общие закономерности 9кл Дрофа РТ Мамонтов');
      new_Title[i] = new_Title[i].replace('Баранкин.. (Стрекоза)', 'Баранкин будь человеком Стрекоза');

      new_Title[i] = new_Title[i].replace('Введ. в естественно-науч. предметы 5 кл. (Дрофа) Р. т.', 'Введение в естественно-научные предметы 5кл Дрофа РТ');
      new_Title[i] = new_Title[i].replace('Все правила сов. рус. яз. (Баро-Пресс)', 'Все правила современного русского языка Баро-Пресс');
      new_Title[i] = new_Title[i].replace('Всеобщая ист. к. XIX - н. XXI в. 11 кл. (РС)', 'Всеобщая история 11кл Конец 19-начало 21века Русское слово');

      new_Title[i] = new_Title[i].replace('ГИА Матем. 3000 зад. с отв. Все Зад. ч.1 (Закрытый сегмент) Семёнова, Ященко (Экз)', 'ГИА Математика 3000 задач с ответами. Все задания части 1 Семенова, Ященко Экзамен (Закрытый сегмент)');
      new_Title[i] = new_Title[i].replace('Глобус 210 мм физ. с рельефом', 'Глобус 210мм Физический С рельефом');
      new_Title[i] = new_Title[i].replace('Глобус 250 мм полит. без подсветки', 'Глобус 250мм Политический Без подсветки');

      new_Title[i] = new_Title[i].replace('ЕГЭ АСТ 2014 Обществознание Полный сп-к (Баранов)', 'ЕГЭ 2014 Обществознание Полный справочник Баранов АСТ');

      new_Title[i] = new_Title[i].replace('Компьютер для людей стар. возраста (цвет.) (Питер)', 'Компьютер для людей старшего возраста Питер');
      new_Title[i] = new_Title[i].replace('Кк Дрофа 8 кл. История России 19 в.', 'Контурная карта 8кл История России 19век Дрофа');
      new_Title[i] = new_Title[i].replace('Кк Дрофа 7 кл. История России 17-18 в.', 'Контурная карта 7кл История России 17-18век Дрофа');
      new_Title[i] = new_Title[i].replace('Кк Дрофа 8 кл. История Нового вр. 19 в.', 'Контурная карта 8кл История Нового времени 19век Дрофа');
      new_Title[i] = new_Title[i].replace('Кк Дрофа 7 кл. История Нового вр. ХVI-XVIII в.', 'Контурная карта 7кл История Нового времени 16-18век Дрофа');

      new_Title[i] = new_Title[i].replace('Мир и Россия (расклад.) (Администр.) (Атлас Принт)', 'Мир и Россия Административная Атлас-Принт (Складная)');
      new_Title[i] = new_Title[i].replace('Мир и Россия (расклад.) (Физич.) (Атлас Принт)', 'Мир и Россия Физическая Атлас-Принт (Складная)');
      new_Title[i] = new_Title[i].replace('МУС Занимательные упражнение рус. яз. 5-9 кл. (Вако)', 'МУС Занимательные упражнения Русский язык 5-9кл Вако');

      new_Title[i] = new_Title[i].replace('НХ по лит-ре 3 кл. (Эксмо)', 'Новейшая хрестоматия по литературе 3кл Эксмо');
      new_Title[i] = new_Title[i].replace('Новейший сп-к для поступ. в ВУЗы 2013-2014 г.г. (ДСК)', 'Новейший справочникк для поступающих в ВУЗы 2013-2014г ДСК');

      new_Title[i] = new_Title[i].replace('Портфолио обуч. нач. школы (Планета)', 'Портфолио обучающегося начальной школы Планета');
      new_Title[i] = new_Title[i].replace('Подарок. Рассказы и сказки (Эксмо)', 'Подарок Рассказы и сказки Эксмо');
      new_Title[i] = new_Title[i].replace('Правила для нач. кл. (Стрекоза)', 'Правила для начальных классов Стрекоза');
      new_Title[i] = new_Title[i].replace('ПХ для 1-4 кл. по рус. и зар. лит-ре (Фирма СТД)', 'Полная хрестоматия для 1-4кл по русской и зарубежной литературе Фирма СТД');

      new_Title[i] = new_Title[i].replace('Справочник по рус. яз. и мат. 1-4 кл. (АСТ)', 'Справочник по русскому языку и математике 1-4кл АСТ');
      new_Title[i] = new_Title[i].replace('Сочинения на 5+. Все темы шк. программы 5-11кл (Лада)', 'Сочинения на 5+ Все темы школьной программы 5-11кл Лада');

      new_Title[i] = new_Title[i].replace('Универсальная хрестоматия 3 кл. (Эксмо)', 'Универсальная хрестоматия 3кл Эксмо');
      new_Title[i] = new_Title[i].replace('УМК КР по матем. 5 кл. (Экз.) ко всем учебникам.', 'УМК КР по математике 5кл Экзамен Ко всем учебникам');
      new_Title[i] = new_Title[i].replace('УМК Развивающие зад. 1 кл. Тесты, игры. упр. (Экз.)', 'УМК Развивающие задания 1кл Тесты, игры, упражнения Экзамен');

      new_Title[i] = new_Title[i].replace('Физическая карта мира 1:34 000 000 (Атлас-Принт)', 'Физическая карта мира 1:34 000 000 Атлас-Принт');
      new_Title[i] = new_Title[i].replace('Физическая карта России 1:8 800 000 (Атлас Принт)', 'Физическая карта России 1:8 800 000 Атлас Принт');

      new_Title[i] = new_Title[i].replace('Энц ума (Олма)', 'Энциклопедия ума Олма');

/*  Общие случаи
---------------------------------------------- */
      //Века
      new_Title[i] = new_Title[i].replace(' I ', ' 1 ');
      new_Title[i] = new_Title[i].replace(' II ', ' 2 ');
      new_Title[i] = new_Title[i].replace(' III ', ' 3 ');
      new_Title[i] = new_Title[i].replace(' IV ', ' 4 ');
      new_Title[i] = new_Title[i].replace(' V ', ' 5 ');
      new_Title[i] = new_Title[i].replace(' VI ', ' 6 ');
      new_Title[i] = new_Title[i].replace(' VII ', ' 7 ');
      new_Title[i] = new_Title[i].replace(' VIII ', ' 8 ');
      new_Title[i] = new_Title[i].replace(' IX ', ' 9 ');
      new_Title[i] = new_Title[i].replace(' X ', ' 10 ');
      new_Title[i] = new_Title[i].replace(' XI ', ' 11 ');
      new_Title[i] = new_Title[i].replace(' XII ', ' 12 ');
      new_Title[i] = new_Title[i].replace(' XIII ', ' 13 ');
      new_Title[i] = new_Title[i].replace(' XIV ', ' 14 ');
      new_Title[i] = new_Title[i].replace(' XV ', ' 15 ');
      new_Title[i] = new_Title[i].replace(' XVI ', ' 16 ');
      new_Title[i] = new_Title[i].replace(' XVII ', ' 17 ');
      new_Title[i] = new_Title[i].replace(' XVIII ', ' 18 ');
      new_Title[i] = new_Title[i].replace(' XIX ', ' 19 ');
      new_Title[i] = new_Title[i].replace(' XX ', ' 20 ');
      new_Title[i] = new_Title[i].replace(' XXI ', ' 21 ');

      //Века русские "иксы"
      new_Title[i] = new_Title[i].replace(' IХ ', ' 9 ');
      new_Title[i] = new_Title[i].replace(' Х ', ' 10 ');
      new_Title[i] = new_Title[i].replace(' ХI ', ' 11 ');
      new_Title[i] = new_Title[i].replace(' ХII ', ' 12 ');
      new_Title[i] = new_Title[i].replace(' ХIII ', ' 13 ');
      new_Title[i] = new_Title[i].replace(' ХIV ', ' 14 ');
      new_Title[i] = new_Title[i].replace(' ХV ', ' 15 ');
      new_Title[i] = new_Title[i].replace(' ХVI ', ' 16 ');
      new_Title[i] = new_Title[i].replace(' ХVII ', ' 17 ');
      new_Title[i] = new_Title[i].replace(' ХVIII ', ' 18 ');
      new_Title[i] = new_Title[i].replace(' ХIХ ', ' 19 ');
      new_Title[i] = new_Title[i].replace(' ХХ ', ' 20 ');
      new_Title[i] = new_Title[i].replace(' ХХI ', ' 21 ');

      //Века различные вариации
      new_Title[i] = new_Title[i].replace(' 1 в.', ' 1век');
      new_Title[i] = new_Title[i].replace(' 2 в.', ' 2век');
      new_Title[i] = new_Title[i].replace(' 3 в.', ' 3век');
      new_Title[i] = new_Title[i].replace(' 4 в.', ' 4век');
      new_Title[i] = new_Title[i].replace(' 5 в.', ' 5век');
      new_Title[i] = new_Title[i].replace(' 6 в.', ' 6век');
      new_Title[i] = new_Title[i].replace(' 7 в.', ' 7век');
      new_Title[i] = new_Title[i].replace(' 8 в.', ' 8век');
      new_Title[i] = new_Title[i].replace(' 9 в.', ' 9век');
      new_Title[i] = new_Title[i].replace(' 10 в.', ' 10век');
      new_Title[i] = new_Title[i].replace(' 11 в.', ' 11век');
      new_Title[i] = new_Title[i].replace(' 12 в.', ' 12век');
      new_Title[i] = new_Title[i].replace(' 13 в.', ' 13век');
      new_Title[i] = new_Title[i].replace(' 14 в.', ' 14век');
      new_Title[i] = new_Title[i].replace(' 15 в.', ' 15век');
      new_Title[i] = new_Title[i].replace(' 16 в.', ' 16век');
      new_Title[i] = new_Title[i].replace(' 17 в.', ' 17век');
      new_Title[i] = new_Title[i].replace(' 18 в.', ' 18век');
      new_Title[i] = new_Title[i].replace(' 19 в.', ' 19век');
      new_Title[i] = new_Title[i].replace(' 20 в.', ' 20век');
      new_Title[i] = new_Title[i].replace(' 21 в.', ' 21век');

      new_Title[i] = new_Title[i].replace(' XVI-XVIII ', ' 16-18 ');

      new_Title[i] = new_Title[i].replace('16-18 в.', '16-18век');
      new_Title[i] = new_Title[i].replace('17-18 в.', '17-18век');

      new_Title[i] = new_Title[i].replace('XX-нач. ', '20-начало ');
      new_Title[i] = new_Title[i].replace('к. 19 - н. 21 в.', 'Конец 19-начало 21века');

      new_Title[i] = new_Title[i].replace('начало 21век', 'начало 21века');

      new_Title[i] = new_Title[i].replace('16 - 18век', '16-18век');
      new_Title[i] = new_Title[i].replace('17 - 18век', '17-18век');

      new_Title[i] = new_Title[i].replace('до 10век', 'до 10века');
      new_Title[i] = new_Title[i].replace('до 11век', 'до 11века');
      new_Title[i] = new_Title[i].replace('до 12век', 'до 12века');
      new_Title[i] = new_Title[i].replace('до 13век', 'до 13века');
      new_Title[i] = new_Title[i].replace('до 14век', 'до 14века');
      new_Title[i] = new_Title[i].replace('до 15век', 'до 15века');
      new_Title[i] = new_Title[i].replace('до 16век', 'до 16века');
      new_Title[i] = new_Title[i].replace('до 17век', 'до 17века');
      new_Title[i] = new_Title[i].replace('до 18век', 'до 18века');
      new_Title[i] = new_Title[i].replace('до 19век', 'до 19века');
      new_Title[i] = new_Title[i].replace('до 20век', 'до 20века');
      new_Title[i] = new_Title[i].replace('до 21век', 'до 21века');

      new_Title[i] = new_Title[i].replace('векаа', 'века');

      new_Title[i] = new_Title[i].replace('1500-1800 г.', '1500-1800г');
      new_Title[i] = new_Title[i].replace('1800-1913 г.', '1800-1913г');

      //Возраст
      new_Title[i] = new_Title[i].replace(' 5-7 ', ' 5-7лет ');

      //Классы
      new_Title[i] = new_Title[i].replace('-й кл. ', 'й класс ');
      new_Title[i] = new_Title[i].replace('-й кл ', 'й класс ');

      new_Title[i] = new_Title[i].replace(' кл. ', 'кл ');
      new_Title[i] = new_Title[i].replace(' кл ', 'кл ');
      new_Title[i] = new_Title[i].replace('кл. ', 'кл ');

      new_Title[i] = new_Title[i].replace('1 кл.', '1кл');
      new_Title[i] = new_Title[i].replace('2 кл.', '2кл');
      new_Title[i] = new_Title[i].replace('3 кл.', '3кл');
      new_Title[i] = new_Title[i].replace('4 кл.', '4кл');
      new_Title[i] = new_Title[i].replace('5 кл.', '5кл');
      new_Title[i] = new_Title[i].replace('6 кл.', '6кл');
      new_Title[i] = new_Title[i].replace('7 кл.', '7кл');
      new_Title[i] = new_Title[i].replace('8 кл.', '8кл');
      new_Title[i] = new_Title[i].replace('9 кл.', '9кл');
      new_Title[i] = new_Title[i].replace('10 кл.', '10кл');
      new_Title[i] = new_Title[i].replace('11 кл.', '11кл');

      new_Title[i] = new_Title[i].replace('1кл.', '1кл');
      new_Title[i] = new_Title[i].replace('2кл.', '2кл');
      new_Title[i] = new_Title[i].replace('3кл.', '3кл');
      new_Title[i] = new_Title[i].replace('4кл.', '4кл');
      new_Title[i] = new_Title[i].replace('5кл.', '5кл');
      new_Title[i] = new_Title[i].replace('6кл.', '6кл');
      new_Title[i] = new_Title[i].replace('7кл.', '7кл');
      new_Title[i] = new_Title[i].replace('8кл.', '8кл');
      new_Title[i] = new_Title[i].replace('9кл.', '9кл');
      new_Title[i] = new_Title[i].replace('10кл.', '10кл');
      new_Title[i] = new_Title[i].replace('11кл.', '11кл');

      new_Title[i] = new_Title[i].replace('1-4кл.', '1-4кл');
      new_Title[i] = new_Title[i].replace('1-4 кл.', '1-4кл');
      new_Title[i] = new_Title[i].replace('5-11кл.', '5-11кл');
      new_Title[i] = new_Title[i].replace('5-11 кл.', '5-11кл');

      //Книга
      new_Title[i] = new_Title[i].replace('Книга 1', 'К1');
      new_Title[i] = new_Title[i].replace('Книга 2', 'К2');
      new_Title[i] = new_Title[i].replace('Книга 3', 'К3');
      new_Title[i] = new_Title[i].replace('Книга 4', 'К4');

      new_Title[i] = new_Title[i].replace('в 2-х кн.', '2К');
      new_Title[i] = new_Title[i].replace('в 3-х кн.', '3К');
      new_Title[i] = new_Title[i].replace('в 4-х кн.', '4К');

      //Листы
      new_Title[i] = new_Title[i].replace(' л ', 'л ');
      new_Title[i] = new_Title[i].replace(' л. ', 'л ');
      new_Title[i] = new_Title[i].replace('л. ', 'л ');

      //Миллиметры
      new_Title[i] = new_Title[i].replace(' мм ', 'мм ');

      //Форматы А1 А2...
      new_Title[i] = new_Title[i].replace('(А-1)', 'А1');
      new_Title[i] = new_Title[i].replace('(А-2)', 'А2');
      new_Title[i] = new_Title[i].replace('(А-3)', 'А3');
      new_Title[i] = new_Title[i].replace('(А-4)', 'А4');
      new_Title[i] = new_Title[i].replace('(А-5)', 'А5');
      new_Title[i] = new_Title[i].replace('(А-6)', 'А6');

      new_Title[i] = new_Title[i].replace(' А-1', ' А1');
      new_Title[i] = new_Title[i].replace(' А-2', ' А2');
      new_Title[i] = new_Title[i].replace(' А-3', ' А3');
      new_Title[i] = new_Title[i].replace(' А-4', ' А4');
      new_Title[i] = new_Title[i].replace(' А-5', ' А5');
      new_Title[i] = new_Title[i].replace(' А-6', ' А6');

      new_Title[i] = new_Title[i].replace(' (А1)', ' А1');
      new_Title[i] = new_Title[i].replace(' (А2)', ' А2');
      new_Title[i] = new_Title[i].replace(' (А3)', ' А3');
      new_Title[i] = new_Title[i].replace(' (А4)', ' А4');
      new_Title[i] = new_Title[i].replace(' (А5)', ' А5');
      new_Title[i] = new_Title[i].replace(' (А6)', ' А6');

      //Форматы - разновидности
      new_Title[i] = new_Title[i].replace('70х90', '70*90');

/*  Издательства
---------------------------------------------- */
      new_Title[i] = new_Title[i].replace('(АСТ)', 'АСТ');
      new_Title[i] = new_Title[i].replace('(Ак/кн)', 'Академия');
      new_Title[i] = new_Title[i].replace('(Аделант)', 'Аделант');
      new_Title[i] = new_Title[i].replace('(Азбука-клас.)', 'Азбука');
      new_Title[i] = new_Title[i].replace('(Азбука)', 'Азбука');
      new_Title[i] = new_Title[i].replace('(Айрис)', 'Айрис');
      new_Title[i] = new_Title[i].replace('(Аквилегия)', 'Аквилегия');
      new_Title[i] = new_Title[i].replace('(Антураж)', 'Антураж');
      new_Title[i] = new_Title[i].replace('(Астрель)', 'Астрель');
      new_Title[i] = new_Title[i].replace('(Атберг 98)', 'Атберг98');
      new_Title[i] = new_Title[i].replace('Атберг 98', 'Атберг98');
      new_Title[i] = new_Title[i].replace('(Атлас-Принт)', 'Атлас-Принт');
      new_Title[i] = new_Title[i].replace('(Атлас Принт)', 'Атлас-Принт');

      new_Title[i] = new_Title[i].replace('(Баласс)', 'Баласс');
      new_Title[i] = new_Title[i].replace('(БАО-Пресс)', 'Бао-Пресс');
      new_Title[i] = new_Title[i].replace('(БАО)', 'Бао-Пресс');
      new_Title[i] = new_Title[i].replace('(Бао)', 'Бао-Пресс');
      new_Title[i] = new_Title[i].replace('(Бао-Пресс)', 'Бао-Пресс');
      new_Title[i] = new_Title[i].replace('(Баро-Пресс)', 'Баро-Пресс');
      new_Title[i] = new_Title[i].replace('(БАРО-Пресс)', 'Баро-Пресс');
      new_Title[i] = new_Title[i].replace('(БИНОМ)', 'Бином');

      new_Title[i] = new_Title[i].replace('(В.-Пресс)', 'Вита-Пресс');
      new_Title[i] = new_Title[i].replace('(В-Граф)', 'В-Граф');
      new_Title[i] = new_Title[i].replace('(Вако)', 'Вако');
      new_Title[i] = new_Title[i].replace('(Вернон)', 'Вернон');
      new_Title[i] = new_Title[i].replace('(Вече)', 'Вече');
      new_Title[i] = new_Title[i].replace('(Виктория+)', 'Виктория+');
      new_Title[i] = new_Title[i].replace('(Владис)', 'Владис');

      new_Title[i] = new_Title[i].replace('(Гном)', 'Гном');

      new_Title[i] = new_Title[i].replace('(ДСК)', 'ДСК');
      new_Title[i] = new_Title[i].replace('(Дрофа)', 'Дрофа');

      new_Title[i] = new_Title[i].replace('(Каро)', 'Каро');
      new_Title[i] = new_Title[i].replace('(Кн. Дом)', 'Книжный дом');
      new_Title[i] = new_Title[i].replace('(Кладезь-Букс)', 'Кладезь-букс');

      new_Title[i] = new_Title[i].replace('(Лада)', 'Лада');
      new_Title[i] = new_Title[i].replace('(ЛадКом)', 'ЛадКом');
      new_Title[i] = new_Title[i].replace('(Легион)', 'Легион');
      new_Title[i] = new_Title[i].replace('(Линг)', 'Линг');
      new_Title[i] = new_Title[i].replace('(Литур)', 'Литур');

      new_Title[i] = new_Title[i].replace('(Малыш)', 'Малыш');
      new_Title[i] = new_Title[i].replace('(Махаон)', 'Махаон');
      new_Title[i] = new_Title[i].replace('(Мир Автокниг)', 'Мир автокниг');
      new_Title[i] = new_Title[i].replace('(Мнемозина)', 'Мнемозина');

      new_Title[i] = new_Title[i].replace('(Олма)', 'Олма');
      new_Title[i] = new_Title[i].replace('(Омега)', 'Омега');
      new_Title[i] = new_Title[i].replace('(Омега-Л)', 'Омега');

      new_Title[i] = new_Title[i].replace('(Панорама)', 'Панорама');
      new_Title[i] = new_Title[i].replace('(Перспектива)', 'Перспектива');
      new_Title[i] = new_Title[i].replace('(Питер)', 'Питер');
      new_Title[i] = new_Title[i].replace('(Планета дет.)', 'Планета детства');
      new_Title[i] = new_Title[i].replace('(Планета дет)', 'Планета детства');
      new_Title[i] = new_Title[i].replace('(ПродТерминал)', 'ПродТерминал');
      new_Title[i] = new_Title[i].replace('(Прос.)', 'Просвещение');
      new_Title[i] = new_Title[i].replace('(Проспект)', 'Проспект');
      new_Title[i] = new_Title[i].replace('(Проф-Пресс)', 'Проф-Пресс');
      new_Title[i] = new_Title[i].replace('(Проф-пресс)', 'Проф-Пресс');

      new_Title[i] = new_Title[i].replace('(РС)', 'Русское слово');
      new_Title[i] = new_Title[i].replace('(РУЗ.Ко)', 'РУЗ Ко');
      new_Title[i] = new_Title[i].replace('(Рецепт-Холдинг)', 'Рецепт-Холдинг');
      new_Title[i] = new_Title[i].replace('(Росмэн)', 'Росмэн');
      new_Title[i] = new_Title[i].replace('(Рост-Кн.)', 'РОСТкнига');
      new_Title[i] = new_Title[i].replace('(Русич)', 'Русич');
      new_Title[i] = new_Title[i].replace('(Рыжий Кот)', 'Рыжий кот');

      new_Title[i] = new_Title[i].replace('(Самовар)', 'Самовар');
      new_Title[i] = new_Title[i].replace('(Сов. шк.)', 'Современная школа');
      new_Title[i] = new_Title[i].replace('(Сов.шк.)', 'Современная школа');
      new_Title[i] = new_Title[i].replace('(Стрекоза)', 'Стрекоза');
      new_Title[i] = new_Title[i].replace('(Сфера)', 'Сфера');

      new_Title[i] = new_Title[i].replace('(Тимошка)', 'Тимошка');
      new_Title[i] = new_Title[i].replace('(Титул)', 'Титул');

      new_Title[i] = new_Title[i].replace('(Удача)', 'Удача');
      new_Title[i] = new_Title[i].replace('(Учимся читать)', 'Учимся читать');

      new_Title[i] = new_Title[i].replace('(Фирма СТД)', 'Фирма СТД');
      new_Title[i] = new_Title[i].replace('(Фламинго)', 'Фламинго');

      new_Title[i] = new_Title[i].replace('(Харвест)', 'Харвест');

      new_Title[i] = new_Title[i].replace('(Цитадель-трейд)', 'Цитадель-трейд');

      new_Title[i] = new_Title[i].replace('(Эгмонт)', 'Эгмонт');
      new_Title[i] = new_Title[i].replace('(Эксмо)', 'Эксмо');
      new_Title[i] = new_Title[i].replace('(Экз.)', 'Экзамен');
      new_Title[i] = new_Title[i].replace('(Экз)', 'Экзамен');

      new_Title[i] = new_Title[i].replace('(Ювента)', 'Ювента');
      new_Title[i] = new_Title[i].replace('(Юнвес)', 'Юнвес');

      new_Title[i] = new_Title[i].replace('(Winx)', 'Winx');

/*  Словари и разговорники
---------------------------------------------- */
      //Словари
      new_Title[i] = new_Title[i].replace('Англо-рус. и рус. англ. слов.', 'Англо-Русский«««');
      new_Title[i] = new_Title[i].replace('англо-рус. и рус. англ. слов.', 'Англо-Русский«««');
      new_Title[i] = new_Title[i].replace('англо-рус. и рус.англ.', 'Англо-Русский«««');
      new_Title[i] = new_Title[i].replace('Немецко-рус. и рус. нем.', 'Немецко-Русский«««');
      new_Title[i] = new_Title[i].replace('немецко-рус. и рус. нем.', 'Немецко-Русский«««');

      new_Title[i] = new_Title[i].replace('Новый шк.', 'Новый школьный');

      //Разговорники
      new_Title[i] = new_Title[i].replace('Русско-англ. разговорник', 'Русско-Английский разговорник');
      new_Title[i] = new_Title[i].replace('Русско-нем. разговорник', 'Русско-Немецкий разговорник');

      //Количество слов
      new_Title[i] = new_Title[i].replace(' тыс.', 'т.');

/*  Сокращения
---------------------------------------------- */
      //Альбомы
      new_Title[i] = new_Title[i].replace('1 альбом', '(1й альбом)');
      new_Title[i] = new_Title[i].replace('2 альбом', '(2й альбом)');
      new_Title[i] = new_Title[i].replace('3 альбом', '(3й альбом)');
      new_Title[i] = new_Title[i].replace('4 альбом', '(4й альбом)');
      new_Title[i] = new_Title[i].replace('5 альбом', '(5й альбом)');
      new_Title[i] = new_Title[i].replace('6 альбом', '(6й альбом)');

      //Год обучения
      new_Title[i] = new_Title[i].replace('(1-й год обуч.)', '(1й год обучения)');
      new_Title[i] = new_Title[i].replace('(2-й год обуч.)', '(2й год обучения)');
      new_Title[i] = new_Title[i].replace('(3-й год обуч.)', '(3й год обучения)');
      new_Title[i] = new_Title[i].replace('(4-й год обуч.)', '(4й год обучения)');
      new_Title[i] = new_Title[i].replace('(5-й год обуч.)', '(5й год обучения)');
      new_Title[i] = new_Title[i].replace('(6-й год обуч.)', '(6й год обучения)');

      new_Title[i] = new_Title[i].replace(' 1-й год обуч.', ' (1й год обучения)');
      new_Title[i] = new_Title[i].replace(' 2-й год обуч.', ' (2й год обучения)');
      new_Title[i] = new_Title[i].replace(' 3-й год обуч.', ' (3й год обучения)');
      new_Title[i] = new_Title[i].replace(' 4-й год обуч.', ' (4й год обучения)');
      new_Title[i] = new_Title[i].replace(' 5-й год обуч.', ' (5й год обучения)');
      new_Title[i] = new_Title[i].replace(' 6-й год обуч.', ' (6й год обучения)');

      new_Title[i] = new_Title[i].replace('1-й год об.', '(1й год обучения)');
      new_Title[i] = new_Title[i].replace('2-й год об.', '(2й год обучения)');
      new_Title[i] = new_Title[i].replace('3-й год об.', '(3й год обучения)');
      new_Title[i] = new_Title[i].replace('4-й год об.', '(4й год обучения)');
      new_Title[i] = new_Title[i].replace('5-й год об.', '(5й год обучения)');
      new_Title[i] = new_Title[i].replace('6-й год об.', '(6й год обучения)');

      //Номера
      new_Title[i] = new_Title[i].replace('№ 1', '№1');
      new_Title[i] = new_Title[i].replace('№ 2', '№2');
      new_Title[i] = new_Title[i].replace('№ 3', '№3');
      new_Title[i] = new_Title[i].replace('№ 4', '№4');
      new_Title[i] = new_Title[i].replace('№ 5', '№5');
      new_Title[i] = new_Title[i].replace('№ 6', '№6');

      //Предметы
      new_Title[i] = new_Title[i].replace('по англ. яз.', 'по английскому языку');
      new_Title[i] = new_Title[i].replace('по англ яз.', 'по английскому языку');
      new_Title[i] = new_Title[i].replace('по Матем.', 'по математике');
      new_Title[i] = new_Title[i].replace('по матем.', 'по математике');
      new_Title[i] = new_Title[i].replace('по нем. яз.', 'по немецкому языку');
      new_Title[i] = new_Title[i].replace('по немец. яз.', 'по немецкому языку');
      new_Title[i] = new_Title[i].replace('по рус. яз ', 'по русскому языку ');
      new_Title[i] = new_Title[i].replace('по рус. яз.', 'по русскому языку');

      new_Title[i] = new_Title[i].replace('Алгебра и нач. матем. анализа', 'Алгебра и начало анализа');
      new_Title[i] = new_Title[i].replace('Алгебра и нач. анализа', 'Алгебра и начало анализа');
      new_Title[i] = new_Title[i].replace('алгебра и нач. анализа', 'алгебра и начало анализа');
      new_Title[i] = new_Title[i].replace('Англ. яз. ', 'Английский язык ');
      new_Title[i] = new_Title[i].replace('англ. яз. ', 'английский язык ');

      new_Title[i] = new_Title[i].replace('Введение в общ. биол. и экол.', 'Введение в общую биологию и экологию');
      new_Title[i] = new_Title[i].replace('Внеклассное чт.', 'Внеклассное чтение');    
      new_Title[i] = new_Title[i].replace('Для внекл.чтения', 'Для внеклассного чтения');    
      new_Title[i] = new_Title[i].replace('Всеобщая ист.', 'Всеобщая история');
      new_Title[i] = new_Title[i].replace('всеобщая ист.', 'всеобщая история');

      new_Title[i] = new_Title[i].replace('История России с др. вр.', 'История России с древнейших времен');

      new_Title[i] = new_Title[i].replace('Комплекс. анализ текста', 'Комплексный анализ текста');

      new_Title[i] = new_Title[i].replace('Лит. чтение', 'Литературное чтение');
      new_Title[i] = new_Title[i].replace('лит. чтение', 'литературное чтение');
      new_Title[i] = new_Title[i].replace('Лит-ное чтение', 'Литературное чтение');
      new_Title[i] = new_Title[i].replace('лит-ное чтение', 'литературное чтение');
      new_Title[i] = new_Title[i].replace('Лит-ре', 'Литературе');
      new_Title[i] = new_Title[i].replace('лит-ре', 'литературе');
      new_Title[i] = new_Title[i].replace('Лит-ра', 'Литература');
      new_Title[i] = new_Title[i].replace('лит-ра', 'литература');

      new_Title[i] = new_Title[i].replace('Матем.', 'Математика');

      new_Title[i] = new_Title[i].replace('Немец. яз.', 'Немецкий язык');
      new_Title[i] = new_Title[i].replace('немец. яз.', 'немецкий язык');
      new_Title[i] = new_Title[i].replace('Нов. ист. зар. стран', 'Новейшая история зарубежных стран');

      new_Title[i] = new_Title[i].replace('История Древ. мира', 'История древнего мира');
      new_Title[i] = new_Title[i].replace('История древ. мира', 'История древнего мира');
      new_Title[i] = new_Title[i].replace('История Нового вр.', 'История нового времени');
      new_Title[i] = new_Title[i].replace('История Нов. вр.', 'История нового времени');
      new_Title[i] = new_Title[i].replace('Ист. сред. веков', 'История средних веков');
      new_Title[i] = new_Title[i].replace('ист. сред. веков', 'история средних веков');
      new_Title[i] = new_Title[i].replace('История сред. веков', 'История средних веков');
      new_Title[i] = new_Title[i].replace('История Сред. веков', 'История средних веков');

      new_Title[i] = new_Title[i].replace('Окруж. мир', 'Окружающий мир');

      new_Title[i] = new_Title[i].replace('Рус. азбука', 'Русская азбука');
      new_Title[i] = new_Title[i].replace('Рус. яз.', 'Русский язык');
      new_Title[i] = new_Title[i].replace('Рус. язык', 'Русский язык');

      new_Title[i] = new_Title[i].replace('Франц. яз.', 'Французский язык');

      //Части
      new_Title[i] = new_Title[i].replace('ч. 1', 'Ч1');
      new_Title[i] = new_Title[i].replace('ч. 2', 'Ч2');
      new_Title[i] = new_Title[i].replace('ч. 3', 'Ч3');
      new_Title[i] = new_Title[i].replace('ч. 4', 'Ч4');
      new_Title[i] = new_Title[i].replace('ч. 5', 'Ч5');
      new_Title[i] = new_Title[i].replace('ч. 6', 'Ч6');

      new_Title[i] = new_Title[i].replace('в 2-х. ч.', '2Ч');
      new_Title[i] = new_Title[i].replace('в 3-х. ч.', '3Ч');
      new_Title[i] = new_Title[i].replace('в 4-х. ч.', '4Ч');
      new_Title[i] = new_Title[i].replace('в 5-х. ч.', '5Ч');
      new_Title[i] = new_Title[i].replace('в 5-ти. ч.', '5Ч');
      new_Title[i] = new_Title[i].replace('в 6-x. ч.', '6Ч');
      new_Title[i] = new_Title[i].replace('в 6-ти. ч.', '6Ч');

      new_Title[i] = new_Title[i].replace('в 2-х ч.', '2Ч');
      new_Title[i] = new_Title[i].replace('в 3-х ч.', '3Ч');
      new_Title[i] = new_Title[i].replace('в 4-х ч.', '4Ч');
      new_Title[i] = new_Title[i].replace('в 5-х ч.', '5Ч');
      new_Title[i] = new_Title[i].replace('в 5-ти ч.', '5Ч');
      new_Title[i] = new_Title[i].replace('в 6-х ч.', '6Ч');
      new_Title[i] = new_Title[i].replace('в 6-ти ч.', '6Ч');

      new_Title[i] = new_Title[i].replace(' 2-х ч.', ' 2Ч');
      new_Title[i] = new_Title[i].replace(' 3-х ч.', ' 3Ч');
      new_Title[i] = new_Title[i].replace(' 4-х ч.', ' 4Ч');
      new_Title[i] = new_Title[i].replace(' 5-х ч.', ' 5Ч');
      new_Title[i] = new_Title[i].replace(' 5-ти ч.', ' 5Ч');
      new_Title[i] = new_Title[i].replace(' 6-х ч.', ' 6Ч');
      new_Title[i] = new_Title[i].replace(' 6-ти ч.', ' 6Ч');

      //Сокращения: Разные
      new_Title[i] = new_Title[i].replace(' +CD', ' + CD');
      new_Title[i] = new_Title[i].replace(' + зад.', ' + Задачник');
      new_Title[i] = new_Title[i].replace(' +зад.', ' + Задачник');
      new_Title[i] = new_Title[i].replace(' - в ассорт.', ' Ассорти*');
      new_Title[i] = new_Title[i].replace(' Зелёная обложка', ' (Зеленая)');
      new_Title[i] = new_Title[i].replace(' Зеленая обложка', ' (Зеленая)');
      new_Title[i] = new_Title[i].replace(' зелёная обложка', ' (Зеленая)');
      new_Title[i] = new_Title[i].replace(' зеленая обложка', ' (Зеленая)');
      new_Title[i] = new_Title[i].replace(' (зеленая)', ' (Зеленая)');
      new_Title[i] = new_Title[i].replace(' (синяя)', ' (Синяя)');
      new_Title[i] = new_Title[i].replace(' (Учебник)', ' Учебник');
      new_Title[i] = new_Title[i].replace(' (карточки)', ' (Карточки)');
      new_Title[i] = new_Title[i].replace(' (карм.)', ' (Карманный)');
      new_Title[i] = new_Title[i].replace(' вар.', ' вариантов');
      new_Title[i] = new_Title[i].replace(' для д/с', ' для детского сада');
      new_Title[i] = new_Title[i].replace(' Зеленая', ' (Зеленая)');
      new_Title[i] = new_Title[i].replace(' Зелёная', ' (Зеленая)');
      new_Title[i] = new_Title[i].replace(' лаб. работ ', ' лабораторных работ ');
      new_Title[i] = new_Title[i].replace(' Пров. раб.', ' ПР');
      new_Title[i] = new_Title[i].replace(' пров. раб.', ' ПР');
      new_Title[i] = new_Title[i].replace(' с ком.', ' с комментариями');
      new_Title[i] = new_Title[i].replace(' слов. ', ' словарь ');
      new_Title[i] = new_Title[i].replace(' Синяя', ' (Синяя)');
      new_Title[i] = new_Title[i].replace(' сп-к для поступ. в ВУЗы', ' справочник для поступающих в ВУЗы');
      new_Title[i] = new_Title[i].replace(' соч. медалистов', ' сочинений медалистов');
      new_Title[i] = new_Title[i].replace(' Я.', ' Я');

      new_Title[i] = new_Title[i].replace('+CD', ' + CD');
      new_Title[i] = new_Title[i].replace('+ СД', '+ CD');
      new_Title[i] = new_Title[i].replace('+ зад.', ' + Задачник');
      new_Title[i] = new_Title[i].replace('+зад.', ' + Задачник');

      new_Title[i] = new_Title[i].replace('- в ассорт.', 'Ассорти*');

      new_Title[i] = new_Title[i].replace('(расклад.)', '(Складная)');
      new_Title[i] = new_Title[i].replace('(Раскладная)', '(Складная)');
      new_Title[i] = new_Title[i].replace('(лиса и журавль)', '(Лиса и журавль)');
      new_Title[i] = new_Title[i].replace('(син.)', '(Синяя)');
      new_Title[i] = new_Title[i].replace('(подар)', '(Подарочная)');

      new_Title[i] = new_Title[i].replace('33 накл.', '33н.');

      new_Title[i] = new_Title[i].replace('а/м', 'автомобили');
      new_Title[i] = new_Title[i].replace('Альбом зад.', 'Альбом заданий');
      new_Title[i] = new_Title[i].replace('Аудио СД', 'Аудио CD');

      new_Title[i] = new_Title[i].replace('БК', 'Большая книга');
      new_Title[i] = new_Title[i].replace('БИЭ', 'Большая иллюстрированная энциклопедия');
      new_Title[i] = new_Title[i].replace('Баз. и проф. ур.', 'Базовый и профессиональный уровень');
      new_Title[i] = new_Title[i].replace('Баз. уров.', 'Базовый уровень');
      new_Title[i] = new_Title[i].replace('Баз. ур.', 'Базовый уровень');
      new_Title[i] = new_Title[i].replace('Банк зад.', 'Банк заданий');
      new_Title[i] = new_Title[i].replace('без подсветки', 'Без подсветки');
      new_Title[i] = new_Title[i].replace('Брянская обл.', 'Брянская область');

      new_Title[i] = new_Title[i].replace('Введ. ', 'Введение ');
      new_Title[i] = new_Title[i].replace('в табл. и схемах', 'в таблицах и схемах');

      new_Title[i] = new_Title[i].replace('Гов. бабушкины сказки', 'Говорящие бабушкины сказки');
      new_Title[i] = new_Title[i].replace('Готовлюсь в шк.', 'Готовлюсь в школу');
      new_Title[i] = new_Title[i].replace('газета', '(Газета)');

      new_Title[i] = new_Title[i].replace('для нач. шк.', 'для начальной школы');

      new_Title[i] = new_Title[i].replace('ЕГЭ Экз 20', 'ЕГЭ Экзамен 20');
      new_Title[i] = new_Title[i].replace('ЕГЭ Экз. 20', 'ЕГЭ Экзамен 20');

      new_Title[i] = new_Title[i].replace('Ё', 'Е');
      new_Title[i] = new_Title[i].replace('ё', 'е');

      new_Title[i] = new_Title[i].replace('Зачетная работа', 'ЗР');
      new_Title[i] = new_Title[i].replace('Зачетные работы', 'ЗР');
      new_Title[i] = new_Title[i].replace('Зачет. раб.', 'ЗР');
      new_Title[i] = new_Title[i].replace('Зач. раб.', 'ЗР');
      new_Title[i] = new_Title[i].replace('зелёная', '(Зеленая)');
      new_Title[i] = new_Title[i].replace('зеленая', '(Зеленая)');
      new_Title[i] = new_Title[i].replace('Зад. по физике', 'Задачник по физике');
      new_Title[i] = new_Title[i].replace('За курс нач. шк.', 'За курс начальной школы');
      new_Title[i] = new_Title[i].replace('за курс нач. шк.', 'За курс начальной школы');

      new_Title[i] = new_Title[i].replace('Итог. тест.', 'Итоговое тестирование');
      new_Title[i] = new_Title[i].replace('и др.', 'и другие');

      new_Title[i] = new_Title[i].replace('Кк ', 'Контурная карта ');
      new_Title[i] = new_Title[i].replace('Контрольные работы', 'КР');
      new_Title[i] = new_Title[i].replace('Контр. раб.', 'КР');
      new_Title[i] = new_Title[i].replace('Кн. для чт.', 'Книга для чтения');
      new_Title[i] = new_Title[i].replace('книга + иллюстрированный материал', 'Книга + иллюстрированный материал');

      new_Title[i] = new_Title[i].replace('Логопедические дом. зад.', 'Логопедические домашние задания');

      new_Title[i] = new_Title[i].replace('Многообраз. живых орган.', 'Многообразие живых организмов');
      new_Title[i] = new_Title[i].replace('Муфта, Полботинка и Мох. Борода', 'Муфта, Полботинка и Моховая Борода');

      new_Title[i] = new_Title[i].replace('НХ', 'Новейшая хрестоматия');
      new_Title[i] = new_Title[i].replace('Настольная кн.', 'Настольная книга');
      new_Title[i] = new_Title[i].replace('Нац. образ.', 'Национальное образование');

      new_Title[i] = new_Title[i].replace('ОГЭ Экз. 20', 'ОГЭ Экзамен 20');
      new_Title[i] = new_Title[i].replace('Орфогр. словарь рус. яз.', 'Орфографический словарь русского языка');
      new_Title[i] = new_Title[i].replace('Орфогр. словарь', 'Орфографический словарь');
      new_Title[i] = new_Title[i].replace('Офсет', '(Офсет)');
      new_Title[i] = new_Title[i].replace('офсет.', '(Офсет)');

      new_Title[i] = new_Title[i].replace('ПХ', 'Полная хрестоматия');
      new_Title[i] = new_Title[i].replace('ПДД с коммент.', 'ПДД с комментариями');
      new_Title[i] = new_Title[i].replace('Пров. и контр. раб.', 'Проверочные и КР');
      new_Title[i] = new_Title[i].replace('Проверочные работы', 'ПР');
      new_Title[i] = new_Title[i].replace('Проф. ур.', 'Профессиональный уровень');
      new_Title[i] = new_Title[i].replace('правила сов. рус. яз.', 'правила современного русского языка');
      new_Title[i] = new_Title[i].replace('Полная иллюстр. хрест.', 'Полная иллюстрированная хрестоматия');
      new_Title[i] = new_Title[i].replace('Полный курс рус. яз.', 'Полный курс русского языка');    
      new_Title[i] = new_Title[i].replace('по рус. и зар. литературе', 'по русской и зарубежной литературе');

      new_Title[i] = new_Title[i].replace('Р. т.', 'РТ');
      new_Title[i] = new_Title[i].replace('Р.т.', 'РТ');
      new_Title[i] = new_Title[i].replace('Развивающие зад.', 'Развивающие задания');
      new_Title[i] = new_Title[i].replace('развивающие зад.', 'развивающие задания');

      new_Title[i] = new_Title[i].replace('Сб. упр.', ' Сборник упражнений');
      new_Title[i] = new_Title[i].replace('соч.-шпаргалок', 'сочинений-шпаргалок');
      new_Title[i] = new_Title[i].replace('словарь рус. яз.', 'словарь русского языка');
      new_Title[i] = new_Title[i].replace('с Древ. вр. ', 'с древнейших времен ');
      new_Title[i] = new_Title[i].replace('Самостоятельные. и контрольные работы', 'Самостоятельные и КР');
      new_Title[i] = new_Title[i].replace('Самост. и контр. работы', 'Самостоятельные и КР');
      new_Title[i] = new_Title[i].replace('Сам. и контр. работы', 'Самостоятельные и КР');
      new_Title[i] = new_Title[i].replace('Сам. и контр. раб.', 'Самостоятельные и КР');
      new_Title[i] = new_Title[i].replace('сам. и контр. раб.', 'самостоятельные и КР');
      new_Title[i] = new_Title[i].replace('Самостоятельная работа', 'СР');
      new_Title[i] = new_Title[i].replace('Самостоятельные работы', 'СР');
      new_Title[i] = new_Title[i].replace('Сам. раб.', 'СР');

      new_Title[i] = new_Title[i].replace('Тет. по чистописанию', 'Тетрадь по чистописанию');
      new_Title[i] = new_Title[i].replace('Тестовые зад.', 'Тестовые задания');
      new_Title[i] = new_Title[i].replace('Тренир. задачи', 'Тренировочные задачи');
      new_Title[i] = new_Title[i].replace('Табл. умножение...', 'Таблица умножения*');
      new_Title[i] = new_Title[i].replace('Тренир. примеры', 'Тренировочные примеры');
      new_Title[i] = new_Title[i].replace('Тренир. задания', 'Тренировочные задания');
      new_Title[i] = new_Title[i].replace('Тесты, игры. упр.', 'Тесты, игры, упражнения');

      new_Title[i] = new_Title[i].replace('Учеб.', 'Учебник');

      new_Title[i] = new_Title[i].replace('шк. прог. в крат. из.', 'Школьная программа в кратком изложении');
      new_Title[i] = new_Title[i].replace('Школьный словообр. словарь', 'Школьный словообразовательный словарь');

      new_Title[i] = new_Title[i].replace('Экз билеты', 'Экзаменационные билеты');
      new_Title[i] = new_Title[i].replace('Экз. билеты', 'Экзаменационные билеты');
      new_Title[i] = new_Title[i].replace('Энц. занимательных наук', 'Энциклопедия занимательных наук');

/*  Удаляем вообще
---------------------------------------------- */
      new_Title[i] = new_Title[i].replace(' !!!', '');
      new_Title[i] = new_Title[i].replace(', вырубка...', '');
      new_Title[i] = new_Title[i].replace(' (Под.) 70/90/16 ', '');

      new_Title[i] = new_Title[i].replace(' к уч.', '');
      new_Title[i] = new_Title[i].replace(' к учеб.', '');  
      new_Title[i] = new_Title[i].replace(' газет.', '');
      new_Title[i] = new_Title[i].replace(' мяг.', ''); 
      new_Title[i] = new_Title[i].replace(' покет ст10', ''); 
      new_Title[i] = new_Title[i].replace(' покет ст8', '');
      new_Title[i] = new_Title[i].replace(' с наклейками', '');
      new_Title[i] = new_Title[i].replace(' ст6', '');
      new_Title[i] = new_Title[i].replace(' ст8', '');
      new_Title[i] = new_Title[i].replace(' ст10', '');
      new_Title[i] = new_Title[i].replace(' ст14', '');
      new_Title[i] = new_Title[i].replace(' ст16', '');
      new_Title[i] = new_Title[i].replace(' тверд.', '');

/*  Знаки и пробелы
---------------------------------------------- */
    new_Title[i] = new_Title[i].replace('"', '');
    new_Title[i] = new_Title[i].replace('   ', ' ');
    new_Title[i] = new_Title[i].replace('  ', ' ');

/* Исправляем неудачные замены
---------------------------------------------- */
    new_Title[i] = new_Title[i].replace('(Синяя) птица', 'Синяя птица');

/* Новые - несортированные
---------------------------------------------- */


/* ----------------------------------------------
    Переименование закончено
---------------------------------------------- */

    //Ставим издательства (пока, что только "Дрофа") в самый конец
      if ( new_Title[i].search('Дрофа') != -1) {
        new_Title[i] = new_Title[i].replace(' Дрофа', '');

        if ( (new_Title[i].search('Учебник') == -1) && (new_Title[i].search('РТ') == -1) ) {new_Title[i] = new_Title[i] + ' Дрофа';}

        if ( new_Title[i].search('Учебник') != -1 ) {new_Title[i] = new_Title[i].replace('Учебник', 'Дрофа Учебник');}
        if ( new_Title[i].search('РТ') != -1 ) {new_Title[i] = new_Title[i].replace('РТ', 'Дрофа РТ');}
      }

      title.value   = '';

      //Выводим результат функции в колонке Название
      title.value     = new_Title.join('\n') + '\n';
    }
  }
}

/* Функция Синхронное скролирование в форме
---------------------------------------------- */
function allScroll(id) {

  //Ориентируем смещение по активному в данный момент скроллу
  var a = document.querySelector('#'+id);

  //Смещение скрола в пикселях (если в самом верху то смещение - 0)
  var scrolled = a.scrollTop;

  if (strNum.value   !='') strNum.scrollTop = scrolled;
  if (author.value   !='') author.scrollTop = scrolled;
  if (title.value    !='') title.scrollTop = scrolled;
  if (barcode.value  !='') barcode.scrollTop = scrolled;
  if (quantity.value !='') quantity.scrollTop = scrolled;
  if (price.value    !='') price.scrollTop = scrolled;
}

/* Функция Выбор функции, которая будет переименовывать
---------------------------------------------- */
function selectFun() {
  if ( provider.selectedIndex == '0' ) {belkinRename(); return;}
  if ( provider.selectedIndex == '1' ) {samsonRename(); return;}
  if ( provider.selectedIndex == '4' ) {microsRename(); return;}
}

/* Функция Если выбран поставщик Микрос, то производим очистку наименований от их Артикулов
---------------------------------------------- */
function microsRename() {

  if( (provider.selectedIndex == '4') && (identifier.selectedIndex  == '1') && (title.value != '') ) {
   var new_Title = title.value;

 /* Очистка пришедших данных 
---------------------------------------------- */
    var del_Sym = '\n';
    for( i=0; i<del_Sym.length; i++ ) new_Title = new_Title.replace( RegExp(del_Sym[i], 'g'), ';' );

    //Массив с символами для удаления
    del_Symbol  = ['\r\n', '"', "'", '\r', '\n', '\t', '&'];
    del_Symbol0 = ['     ', '    ', '   ', '  '];

    //Удаляем лишние пробелы и табуляцию
    for( i=0; i<del_Symbol.length; i++ ) new_Title = new_Title.replace( RegExp(del_Symbol[i], 'g'), '' );
    for( i=0; i<del_Symbol0.length; i++ ) new_Title = new_Title.replace( RegExp(del_Symbol0[i], 'g'), ' ' );

    //Аналог trim() из php. Пробелы с начала и с конца строки
    new_Title = new_Title.trim();

    //Разбиваем строку на массив
    new_Title = new_Title.split(';');
    new_Title.pop();

    //Удаляем лишние пробелы и табуляцию из каждого элемента массива
    for( i=0; i<new_Title.length; i++ ) new_Title[i] = $.trim( new_Title[i] );

 /* Вырезаем код Микроса из наименования
---------------------------------------------- */
    //Код Микроса
    var microsCode = [];

    for( i=0; i<new_Title.length; i++ ) {
 
      //Вырезаем "предполагаемый" артикул с начала строки до пробела
      var from = new_Title[i].indexOf(' ');

      microsCode[i] = new_Title[i].substring(0,from);
      microsCode[i] = microsCode[i].trim();

      //Удаляем вырезанный артикул окончательно
      new_Title[i] = new_Title[i].replace((microsCode[i] + ' '), '');
    }
 
    title.value   = '';

    //Выводим результат функции в колонке Название
    title.value     = new_Title.join('\n') + '\n';

  }
}