<?php
if(isset($_POST['id']))
{
	include 'dbsettings.inc.php';
	$id = $_POST['id'];
	$sql = "SELECT firstname, secondname, country, langfirst, birthday FROM users WHERE id LIKE '$id'";
	$res = mysqli_query($link, $sql) or die(mysqli_error($link));
	$is_user = mysqli_fetch_assoc($res);
	$otvet = '';
	if(isset($is_user)){
		//$otvet = "$is_user['firstname']:$is_user['secondname']:$is_user['country']:$is_user['langfirst']:$is_user['birthday']:";
		//$otvet = $is_user['firstname'].':'.$is_user['secondname'].':'.$is_user['country'].':'.$is_user['langfirst'].':'.$is_user['birthday'];
		$arr = array('firstname' => $is_user['firstname'], 
					 'secondname' => $is_user['secondname'],
					 'country' => $is_user['country'], 
					 'langfirst' => $is_user['langfirst'],
					 'birthday' => $is_user['birthday']);
	}
	mysqli_close($link);
	//echo $otvet;
	echo json_encode($arr);
}
else Header("Location: index.php");