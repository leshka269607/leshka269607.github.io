<?php

//Список разрешенных расширений файлов
$allowed = array('xml');

  if(isset($_FILES['upl']) && $_FILES['upl']['error'] == 0){

    $extension = pathinfo($_FILES['upl']['name'], PATHINFO_EXTENSION);

    if(!in_array(strtolower($extension), $allowed)){
      echo '{"status":"error"}';
      exit;
    }

// Формируем имя и создаём директорию для пришедших файликов
// ==========================================================================
    $id = file_get_contents('id.txt');
    $date = date('Y-m-d');

    $dir_name = "uploads/$date" . '_' . $id;
    mkdir($dir_name, 0700);

// Переписываем пришедшие файлики на сервер
// ==========================================================================
    if(move_uploaded_file($_FILES['upl']['tmp_name'], $dir_name . '/' . $_FILES['upl']['name'])){
      echo '{"status":"success"}';
      exit;
    }
  }

echo '{"status":"error"}';
exit;