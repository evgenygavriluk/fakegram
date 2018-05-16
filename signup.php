<?php
function img_resize($filename, $newfilename, $new_width=180, $quolity=100)
{
		list($width, $height, $type) = getimagesize($filename);
		$new_height = $height/($width/$new_width);
		$image_p = imagecreatetruecolor($new_width, $new_height);
		switch($type){
			case 1: $image = imagecreatefromgif($filename); break;
			case 2: $image = imagecreatefromjpeg($filename); break;
			case 3: $image = imagecreatefrompng($filename); break;
			default: echo "unsupperted file format";
		}
		imagecopyresampled($image_p, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
		imagejpeg($image_p, $newfilename, $quolity);
		imagedestroy($image_p);
}
?>
<?php
include 'dbsettings.inc.php';
$email = $_POST['login'];
$pass = md5(trim($_POST['passwd']));
$date = @date("Y-m-d");
$link = mysqli_connect(DB_HOST,DB_LOGIN,DB_PASSWORD,DB_NAME);
$sql = "SELECT id FROM users WHERE email LIKE '$email'";
$res = mysqli_query($link, $sql) or die(mysqli_error($link));
$is_user = mysqli_fetch_assoc($res);
if(!isset($is_user))
{
	$sql = "INSERT INTO users(auth_provider, auth_uid, email, pass, date, firstname, secondname, country, lang, birthday) VALUES('0','', '$email', '$pass', '$date', 'Noname', 'Anonimous', '0', '1', '$date')";
	echo $sql;
	mysqli_query($link, $sql) or die(mysqli_error($link));
	session_start();
	$_SESSION['id'] = mysqli_insert_id($link);
	img_resize('users-photoes/avatar.jpg','users-photoes/'.$_SESSION['id'].'.jpg', 200, 75);
	mysqli_close($link);
	$_SESSION['auth_provider'] = 0;
	$_SESSION['firstname'] = 'Anonymous';
	$_SESSION['secondname'] = 'Noname';
	Header("Location: index.php");
} 
else 
	echo 'USER_EXISTS';
	mysqli_close($link);
?>