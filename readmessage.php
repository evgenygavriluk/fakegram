<?php
session_start();
if (isset($_SESSION['id']) and ($_SESSION['id']==$_POST['from_id'])) 
{ 
	include 'dbsettings.inc.php';
	$from_id 	= $_POST['from_id'];
	$to_id  	= $_POST['to_id'];
	$arr = array();
	//$link = mysqli_connect(DB_HOST,DB_LOGIN,DB_PASSWORD,DB_NAME);
	
	//?
	if(!isset($_POST['MSG_scroll'])) $scroll_key = 10; else $scroll_key = $_POST['MSG_scroll'];
	
	$sql = "SELECT msg.id, msg.msg, msg.status_read, msg.msg_type, msg.msg_date, msg.from_id, usr.firstname, usr.secondname, usr.auth_provider, usr.auth_uid FROM users usr INNER JOIN messages msg ON usr.id = msg.from_id WHERE (msg.from_id=$from_id AND msg.to_id=$to_id) OR (msg.from_id=$to_id AND msg.to_id=$from_id) ORDER BY msg.id DESC LIMIT $scroll_key";
	
	//echo $sql;
	$data = array();
	$res = mysqli_query($link, $sql) or die(mysqli_error($link));
	//echo '<div class="row-fluid">';
	while($msg = mysqli_fetch_assoc($res))
	{
		$data[] = $msg; 
	}
	echo json_encode($data, JSON_UNESCAPED_UNICODE);
	$sql = "UPDATE messages SET status_read=1 WHERE from_id ='$to_id' AND status_read=0";
	$res = mysqli_query($link, $sql) or die(mysqli_error($link));
	//echo '</div>';
	mysqli_close($link);
} else 
{
	mysqli_close($link);
	Header("Location: index.php");
}