<?php
session_start();

require_once('imageresize.php');


if (!isset($_SESSION['id'])) 
{ 
Header("Location: index.php");
} else $id = $_SESSION['id'];
// Параметры базы данных
include 'dbsettings.inc.php';

$creator = $_SESSION['id'];					





if (isset($_POST['offer_new_topic'])){
	$text = htmlspecialchars($_POST['topic_description']);
	$comm_id = $_POST['comm_id'];
	$topic_type = $_POST['OFFER_type'];

$fp = fopen("log_offertopic.log", 'a+');
fwrite($fp, 'creator='.$creator.' text='.$text.' id='.$comm_id.' topic_type='.$topic_type.' file='.$_FILES['offerfile']['tmp_name']);

	$sql = "INSERT INTO offertopics(text, creator, community, photo, likes) VALUES('$text', $creator,'$comm_id','$topic_type',0)";
	$res = mysqli_query($link, $sql) or die(mysqli_error($link));
	
	if($topic_type == 1){
		$uploaddir = 'topic_offers'.DIRECTORY_SEPARATOR;
		$uploadfile = $uploaddir . basename(mysqli_insert_id($link).'.jpg');
			
		if (move_uploaded_file($_FILES['offerfile']['tmp_name'], $uploadfile)) {
			$out = "Файл корректен и был успешно загружен.\n";
			img_resize($uploadfile, $uploadfile, 610, 75);
		} else {
		$out = "Возможная атака с помощью файловой загрузки!\n";
		}
	}
	
	if ($_SERVER['HTTP_REFERER'] != null) header("Location: ".$_SERVER['HTTP_REFERER']);
	exit;
}