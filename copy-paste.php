<!DOCTYPE html>
<html lang="ru">

  <head>
    <meta charset="utf-8">
    <title>Генератор накладных</title>

    <!-- Библиотеки -->
    <link rel="stylesheet" href="css/normalize.min.css">

    <!-- Аддоны -->
      <!-- <link rel="stylesheet" href="addons/calendar/style.css" media="screen"> -->

    <!-- Минифицированные -->
      <link rel="stylesheet" href="addons/calendar/style.min.css" media="screen" >
      <!-- <link rel="stylesheet" href="css/style.min.css"> -->

    <!-- Мои стилевые файлы -->
      <link rel="stylesheet" href="css/style.css">

    <!-- favicon -->
      <link rel="icon" href="favicon.ico" type="image/x-icon" />
      <link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
  </head>

  <body onLoad="winLoad();" onInput="btnShow();">

    <header class="main-header">
      <?php require 'INC/version_info.php'; ?>
    </header>

    <main class="layout-center">
      <div>
        <p class="error">*Внимание! Все поля формы обязательны для заполнения.</p>
      </div>

      <form action="import_copy-paste.php" method="post">

        <div class="second_date layout-center">
          <ul class="horizon-list">

            <li><h1>Поступление ТМЦ (купля-продажа) №</h1></li>
            <li class="number"><input type="text" size="8" id="number_doc" class="number_doc" onKeydown="if(event.keyCode==13){return false;}" name="number_doc" maxlength="7" autofocus required></li>
            <li>от</li>
            <li><input type="text" size="10" id="date_doc" class="date_doc" name="date_doc" required></li>

          </ul>
          
          <ul class="horizon-list provider">
            <li>Поставщик:</li>

            <li>
              <select size="1" name="provider" id="provider">
                <option onClick="columnAuthorShow(); selectFun();" value="C5228354-E5AF-4946-B7B6-032B0B8DDEBD" selected>ИП Белкин Н.В.</option>
                <option onClick="columnAuthorHidden(); selectFun();" value="8AC8DD0F-422F-4D87-8220-E2DD6B4A8FA1">ООО "Самсон-Опт"</option>
                <option onClick="columnAuthorHidden();" value="D2862286-2BCA-4A30-A155-2AD6D860C9F7">ООО "Рельеф-Центр"</option>
                <option onClick="columnAuthorHidden();" value="9D7AC28D-B420-47E3-ACD8-9A79DC6997B3">ЗАО "Фарм"</option>
                <option onClick="columnAuthorHidden(); selectFun();" value="B29CB545-C8B7-4C3A-9A9E-78F8682546C5">ООО "Микрос"</option>
              </select>
            </li>
          </ul>

          <ul class="horizon-list identifier">
            <li>Идентификация по:</li>

            <li>
              <select size="1" name="identifier" id="identifier">
                <option value="2014" selected>ШтрихКоду</option>
                <option value="63E072CC-B4E5-492F-A84E-3687EDE026A6">Артикулу</option>
              </select>
            </li>
          </ul>
        </div>

        <table class="main_date">

          <tr>
            <th>№</th>
            <th class="author">Автор</th>
            <th>Название</th>
            <th>Идентификатор</th>
            <th>Кол-во</th>
            <th>Цена</th>
          </tr>

          <tr class="property">
            <td><textarea onScroll="allScroll('strNum');" id="strNum" name="strNum" rows="20" cols="2" wrap="off" readonly></textarea></td>
            <td class="author"><textarea onScroll="allScroll('author');" id="author" name="author" rows="20" cols="12" wrap="off"></textarea></td>
            <td><textarea onScroll="allScroll('title');" onInput="belkinRename(); samsonRename(); microsRename();" id="title" name="title" rows="20" cols="65" wrap="off" required></textarea></td>
            <td><textarea onScroll="allScroll('barcode');" id="barcode" name="barcode" rows="20" cols="13" wrap="off" required></textarea></td>
            <td><textarea onScroll="allScroll('quantity');" id="quantity" name="quantity" rows="20" cols="8" wrap="off" required></textarea></td>
            <td><textarea onScroll="allScroll('price');" onInput="colomnLineReal();" id="price" name="price" rows="20" cols="8" wrap="off" required></textarea></td>
          </tr>
        </table>

        <input class="btn res" type="button" onClick="res()" value="Очистить">
        <input class="btn btn-continue" type="submit" value="Продолжить">
      </form>

      <p class="verification" id="verification"></p>
    </main>

  <?php require 'INC/footer.php'; ?>

    <!-- Библиотеки -->
      <script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script> 

    <!-- Аддоны -->
      <!-- Календарь -->
        <!-- <script src="addons/calendar/will_pickdate.js"></script> -->

    <!-- Минифицированные -->
      <script src="addons/calendar/will_pickdate.min.js"></script>
      <script src="js/script.min.js"></script>

    <!-- Мои скрипты <script src="js/jquery-1.7.2.min.js"></script>-->
       <!-- <script src="js/script.js"></script> -->

  </body>

</html>