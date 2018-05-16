<?php
// Параметры базы данных
define('DB_HOST','localhost');
define('DB_LOGIN','fakegram_root');
define('DB_PASSWORD','Latrom1980');
define('DB_NAME','fakegram');
$link = mysqli_connect(DB_HOST,DB_LOGIN,DB_PASSWORD,DB_NAME) or die(mysqli_connect_error());
mysqli_set_charset($link, "utf8");
mysqli_query("SET NAMES 'utf8'")
?>