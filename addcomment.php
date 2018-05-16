<?php
session_start();
require_once('imageresize.php');

if (!isset($_SESSION['id'])) {
    Header("Location: index.php");
} 
// Параметры базы данных
include 'dbsettings.inc.php';

$topic_id = $_POST['TOP_id'];// какому топику принадлежит комментарий	
$text = htmlspecialchars($_POST['comment']);
$id = $_POST['avid'];
$touser = $_POST['reply'];
$msg_type = $_POST['MSG_type'];
	
//$fp = fopen("log_sendmessage.log", 'a+');
//fwrite($fp, 'working topic_id=' . $topic_id . ' text=' . $text . ' id=' . $id . ' touser=' . $touser . ' msg_type=' . $msg_type . ' file=' . $_FILES['userfile']['tmp_name']);

if ($text == "" and $msg_type == 0) {
    return false;
} else {
    $sql = "INSERT INTO comments(user, touser, topic_id, text, msg_type, likes) VALUES('$id', '$touser', '$topic_id', '$text', '$msg_type','0')";
    fwrite($fp, $sql);

    $res = mysqli_query($link, $sql) or die(mysqli_error($link));

    if ($msg_type == 1) {
        $uploaddir = 'uploads' . DIRECTORY_SEPARATOR;
        $uploadfile = $uploaddir . basename(mysqli_insert_id($link) . '.jpg');

        if (move_uploaded_file($_FILES['userfile']['tmp_name'], $uploadfile)) {
            $out = "Файл корректен и был успешно загружен.\n";
            img_resize($uploadfile, $uploadfile, 610, 75);
        } else {
            $out = "Возможная атака с помощью файловой загрузки!\n";
        }
    }
}	


//fclose($fp);

mysqli_close($link);
exit;