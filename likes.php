<?php
session_start();
if (!isset($_SESSION['id'])){
	Header("Location: index.php");
}else
{
	include 'dbsettings.inc.php';
	$user_id = $_POST['user_id'];
	$comment_id  = $_POST['comment_id'];

	// Добавляем лайк в таблицу фолловеров
	
	$sql = 'SELECT user_id FROM likes WHERE comment_id LIKE "'.$comment_id.'" and user_id LIKE "'.$user_id.'"';
	$res = mysqli_query($link, $sql) or die(mysqli_error($link));
	$answer = mysqli_fetch_assoc($res);
	
	$fp = fopen("log_likes.log", 'w+');
	$str_post = "answer: $answer[user_id]";
	fwrite($fp, $str_post);
	fclose($fp);
	
	if(!isset($answer['user_id'])){	
		
		$sql = "INSERT INTO likes (comment_id, user_id) VALUES('$comment_id', '$user_id')";
		$res = mysqli_query($link, $sql) or die(mysqli_error($link));
		
		$sql = "UPDATE comments SET likes = likes+1 WHERE id = '$comment_id'";
		$res = mysqli_query($link, $sql) or die(mysqli_error($link));
		echo 1;
	}
	else echo 0;
	mysqli_close($link);
}