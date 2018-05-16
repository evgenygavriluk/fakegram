<?php
session_start();

if (!isset($_SESSION['id'])) 
{ 
	Header("Location: index.php");
} else $id = $_SESSION['id'];
// Параметры базы данных
include 'dbsettings.inc.php';

$public_id = $_POST['public_id'];
$repost_id = $_POST['repost_id'];

// Принадлежит ли паблик пользователю, а значит он имеет право в него постить, или пост должен попадать в таблицу offertopics
$sql = "SELECT * FROM communities WHERE id =\"$public_id\" and comm_admin=\"$id\"";
$res = mysqli_query($link, $sql) or die(mysqli_error($link));
$result = mysqli_fetch_assoc($res);
// Если принадлежит, постим сразу в паблик
if(isset($result)){

	// Узнаем, это уникальный пост или уже репост был по полю repost_id
	$sql_topicslist = "SELECT * FROM topics WHERE id = \"$repost_id\"";
	$res_topicslist = mysqli_query($link, $sql_topicslist) or die(mysqli_error($link));
	$topic = mysqli_fetch_assoc($res_topicslist);

		$topic_id 	  = $topic['id'];
		$topic_text   = $topic['text'];
		$topic_creator= $topic['creator'];
		$topic_type   = $topic['photo'];
		$topic_likes  = $topic['likes'];
		$topic_repost = $topic['repost_id'];
		
	$qw = "topic_id = $topic_id, topic_text = $topic_text, topic_creator = $topic_creator, topic_type = $topic_type, topic_likes = $topic_likes, topic_repost = $topic_repost";
	$fp = fopen("log_addtopic.log", 'a+');	
	fwrite($fp, $qw);
		if(!isset($topic_repost)){
			$sql = "INSERT INTO topics(text, creator, community, photo, likes, repost_id) VALUES('$topic_text', '$topic_creator','$public_id','$topic_type', '$topic_likes', '$repost_id')";
			if(!isset($topic_type)) $sql = "INSERT INTO topics(text, creator, community, likes, repost_id) VALUES('$topic_text', '$topic_creator','$public_id','$topic_likes', '$repost_id')";
			fwrite($fp, 'Srabotal variant 1 topic_likes=$topic_likes');
		} else{
			$likesql = "SELECT likes FROM topics WHERE id = \"$topic_repost\"";
			$reslike = mysqli_query($link, $likesql) or die(mysqli_error($link));
			$topiclike = mysqli_fetch_assoc($reslike);
			$topiclikes = $topiclike['likes'];
			fwrite($fp, "Srabotal variant 2 toiciplikes=$topiclikes");
			$sql = "INSERT INTO topics(text, creator, community, photo, likes, repost_id) VALUES('$topic_text', '$topic_creator','$public_id','$topic_type', '$topiclikes', '$topic_repost')";
			if(!isset($topic_type)) $sql = "INSERT INTO topics(text, creator, community, likes, repost_id) VALUES('$topic_text', '$topic_creator','$public_id','$topic_likes', '$topic_repost')";
		}

	fwrite($fp, $sql);
	$res = mysqli_query($link, $sql) or die(mysqli_error($link));

}
// Если нет, добавляем сначала в offertopics
else{
	// Узнаем, это уникальный пост или уже репост был по полю repost_id
	$sql_topicslist = "SELECT * FROM topics WHERE id = \"$repost_id\"";
	$res_topicslist = mysqli_query($link, $sql_topicslist) or die(mysqli_error($link));
	$topic = mysqli_fetch_assoc($res_topicslist);

		$topic_id 	  = $topic['id'];
		$topic_text   = $topic['text'];
		$topic_creator= $topic['creator'];
		$topic_type   = $topic['photo'];
		$topic_likes  = $topic['likes'];
		$topic_repost = $topic['repost_id'];
		
	$qw = "topic_id = $topic_id, topic_text = $topic_text, topic_creator = $topic_creator, topic_type = $topic_type, topic_likes = $topic_likes, topic_repost = $topic_repost";
	$fp = fopen("log_addtopic.log", 'a+');	
	fwrite($fp, $qw);
		if(!isset($topic_repost)){
			$sql = "INSERT INTO offertopics(text, creator, community, photo, likes, repost_id) VALUES('$topic_text', '$topic_creator','$public_id','$topic_type', '$topic_likes', '$repost_id')";
			if(!isset($topic_type)) $sql = "INSERT INTO offertopics(text, creator, community, likes, repost_id) VALUES('$topic_text', '$topic_creator','$public_id','$topic_likes', '$repost_id')";
			fwrite($fp, 'Srabotal variant 1 topic_likes=$topic_likes');
		} else{
			$likesql = "SELECT likes FROM topics WHERE id = \"$topic_repost\"";
			$reslike = mysqli_query($link, $likesql) or die(mysqli_error($link));
			$topiclike = mysqli_fetch_assoc($reslike);
			$topiclikes = $topiclike['likes'];
			fwrite($fp, "Srabotal variant 2 toiciplikes=$topiclikes");
			$sql = "INSERT INTO offertopics(text, creator, community, photo, likes, repost_id) VALUES('$topic_text', '$topic_creator','$public_id','$topic_type', '$topiclikes', '$topic_repost')";
			if(!isset($topic_type)) $sql = "INSERT INTO offertopics(text, creator, community, likes, repost_id) VALUES('$topic_text', '$topic_creator','$public_id','$topic_likes', '$topic_repost')";
		}

	fwrite($fp, $sql);
	$res = mysqli_query($link, $sql) or die(mysqli_error($link));

}
exit;