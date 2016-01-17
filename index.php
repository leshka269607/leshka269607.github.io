<!DOCTYPE html>
<html lang="ru">

  <head>
    <meta charset="utf-8">
    <title>Генератор накладных</title>

    <!-- Библиотеки -->
    <link rel="stylesheet" href="css/normalize.min.css">

    <!-- Аддоны -->
      <!-- <link rel="stylesheet" href="addons/downloader/assets/css/style.css"> -->

    <!-- Минифицированные -->
      <link rel="stylesheet" href="addons/downloader/assets/css/style.min.css">
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
      <nav class="main-navigation">

        <ul class="horizon-list">
          <li class="btn button button--saqui button--round-l button--text-thick" data-text="Поехали!">

            <div class="method-import">

              <img src="img/excel.png" width="50" height="50" alt="Excel">
              <img src="img/convert.png" width="30" height="30" alt="Конвертер">
              <img src="img/1C.png" width="50" height="50" alt="1С">

              <a class="block-link" href="copy-paste.php"></a>
            </div>

            <p class="description">Воспользуйтесь универсальным конвертером накладных из Excel</p>
          </li>

          <li>
            <div class="method-import">
              <form id="upload" action="upload.php" method="post" enctype="multipart/form-data">

                <img src="img/xml.png" width="50" height="50" alt="xml">
                <img src="img/convert.png" width="30" height="30" alt="Конвертер">
                <img src="img/1C.png" width="50" height="50" alt="1С">

                <p class="description">Загрузить накладные xml<!-- <br />(только от Белкина)</br>--></p>

                <div id="drop">
                  <p class="ae">Просто перетащите их сюда</p>

                  <a>Обзор</a>
                  
                  <input type="file" name="upl" multiple />
                </div>

                <ul>
                  <!-- Загруженные файлы отображаются здесь -->
                </ul>
              </form>
            </div>
          </li>
         </ul>
      </nav>

      <form action="import_xml.php" method="post">
          <input class="continue btn hidden-object" type="submit" value="Продолжить">
      </form>

      <div class="instruments">
   <!-- <div class="version-history">
          <div class="box bg-1">
            <button class="button--tamaya button--border-thick" data-text="История версий"><span>Вспомнить всё!</span></button>
          </div>
          <p><a href="#" title="Вспомнить всё!">История версий</a></p>
        </div> -->

<!--        <div class="view-xml">
          <div class="box bg-1">
            <button class="button--tamaya button--border-thick" data-text="Инструменты"><span>Воспользоваться!</span></button>
          </div>
        </div>

   <!-- <div class="version-test">
          <div class="box bg-1">
            <button class="button--tamaya button--border-thick" data-text="Тестовая версия"><span>Версия не проверялась!</span></button>
          </div>
          <p><a href="#" title="Версия не проверялась!">Тестовая версия</a></p>
        </div> -->
      </div>

    </main>

    <?php require 'INC/footer.php'; ?>

    <!-- Библиотеки -->
      <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>

    <!-- Аддоны -->

      <!-- Загрузчик xml документов -->
        <!-- JavaScript Includes
          <script src="addons/downloader/assets/js/jquery.knob.js"></script>
        <!-- jQuery File Upload Dependencies
          <script src="addons/downloader/assets/js/jquery.ui.widget.js"></script>
          <script src="addons/downloader/assets/js/jquery.iframe-transport.js"></script>
          <script src="addons/downloader/assets/js/jquery.fileupload.js"></script>
        <!-- Our main JS file
          <script src="addons/downloader/assets/js/script.js"></script>  -->

    <!-- Минифицированные
      <!-- Загрузчик xml документов -->
          <script src="addons/downloader/assets/js/min/jquery.knob.js"></script>
          <script src="addons/downloader/assets/js/min/jquery.ui.widget.js"></script>
          <script src="addons/downloader/assets/js/min/jquery.iframe-transport.js"></script>
          <script src="addons/downloader/assets/js/min/jquery.fileupload.js"></script>
          <script src="addons/downloader/assets/js/min/script.js"></script>

    <!-- Мои скрипты -->
  </body>

</html>