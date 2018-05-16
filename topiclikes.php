<?php

session_start();
if (!isset($_SESSION['id'])){
	Header("Location: index.php");
}else
{
	include 'dbsettings.inc.php';
	$user_id = $_POST['user_id'];
	$topic_id  = $_POST['topic_id'];

	// Проверяем, уникальный пост или был репостнут?
	$sql = "SELECT repost_id FROM topics WHERE id=\"$topic_id\"";
	$res = mysqli_query($link, $sql) or die(mysqli_error($link));
	$answer = mysqli_fetch_assoc($res);
	$repost_id = $answer['repost_id'];
	
	//$fp = fopen("log_topiclikes.log", 'w+');
	//$str_post = "answer: $answer[repost_id]";
	//fwrite($fp, $str_post);
	
	// Если пост уникальный, добавляем лайк этому посту
	if(!isset($repost_id)){
	
		// Проверяем, лайкал юзер уже этот пост или нет
		$sql = 'SELECT user_id FROM topiclikes WHERE topic_id LIKE "'.$topic_id.'" and user_id LIKE "'.$user_id.'"';
		$res = mysqli_query($link, $sql) or die(mysqli_error($link));
		$answer = mysqli_fetch_assoc($res);
		$uid = $answer['user_id'];
		
		//$str_post = "answer: $uid";
		//fwrite($fp, $str_post);
		//fclose($fp);
	
		if(!isset($uid)){	
			
			$sql = "INSERT INTO topiclikes (topic_id, user_id) VALUES('$topic_id', '$user_id')";
			$res = mysqli_query($link, $sql) or die(mysqli_error($link));
			
			$sql = "UPDATE topics SET likes = likes+1 WHERE id = '$topic_id'";
			$res = mysqli_query($link, $sql) or die(mysqli_error($link));
			echo 1;
		}
		else echo 0;
	} else{
	// Если пост не уникальный, то добавляем лайк уникальному посту
		// Добавляем лайк в таблицу
		$sql = 'SELECT user_id FROM topiclikes WHERE topic_id LIKE "'.$repost_id.'" and user_id LIKE "'.$user_id.'"';
		$res = mysqli_query($link, $sql) or die(mysqli_error($link));
		$answer = mysqli_fetch_assoc($res);
		$uid = $answer['user_id'];
		$str_post = "answer: $uid";
		//fwrite($fp, $str_post);
		
	
		if(!isset($uid)){	
			
			$sql = "INSERT INTO topiclikes (topic_id, user_id) VALUES('$repost_id', '$user_id')";
			$res = mysqli_query($link, $sql) or die(mysqli_error($link));
			
			$sql = "UPDATE topics SET likes = likes+1 WHERE id = '$repost_id'";
			$res = mysqli_query($link, $sql) or die(mysqli_error($link));
			echo 1;
		}
		else echo 0;
	
	}
	//fclose($fp);
	mysqli_close($link);
}