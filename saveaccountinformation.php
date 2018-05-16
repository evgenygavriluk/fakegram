<?php
session_start();
if (isset($_SESSION['id'])) 
{ 
	include 'dbsettings.inc.php';
	$id = $_SESSION['id'];
	$firstname  = $_POST['firstname'];
	$secondname = $_POST['secondname'];
	$country    = $_POST['country'];
	$lang  		= $_POST['lang'];
	$birthday   = $_POST['birthday'];
	$link = mysqli_connect(DB_HOST,DB_LOGIN,DB_PASSWORD,DB_NAME);
	mysqli_set_charset($link, "utf8");
	$sql = "UPDATE users SET firstname = '$firstname', secondname = '$secondname', country = $country , lang = $lang, birthday = '$birthday' WHERE id = '$id'";
	$res = mysqli_query($link, $sql) or die(mysqli_error($link));
	mysqli_close($link);
	$_SESSION['firstname'] = $firstname;
	$_SESSION['secondname'] = $secondname;
	echo $id;
	//echo $sql;
} else Header("Location: index.php");