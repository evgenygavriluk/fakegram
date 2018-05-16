<?php
include 'dbsettings.inc.php';
$email = trim($_POST['email']);
$pass = md5(trim($_POST['passwd']));
//$link = mysqli_connect(DB_HOST,DB_LOGIN,DB_PASSWORD,DB_NAME);
$sql = "SELECT id, pass, auth_provider, firstname, secondname FROM users WHERE email LIKE '$email'";
$res = mysqli_query($link, $sql) or die(mysqli_error($link));
$is_user = mysqli_fetch_assoc($res);
mysqli_close($link);
$otvet = '';
if(!isset($is_user)) $otvet = 'NO_USER_OR_PASS';

if(isset($is_user)){
	if($is_user['pass'] != $pass) $otvet = 'NO_USER_OR_PASS';
	else
	{
		session_start();
		session_destroy();
		session_start();
		$_SESSION['email'] = $email;
		$_SESSION['id'] = $is_user['id'];
		$_SESSION['auth_provider'] = $is_user['auth_provider'];
		$_SESSION['firstname'] = $is_user['firstname'];
		$_SESSION['secondname'] = $is_user['secondname'];
	}
}
echo $otvet;
?>