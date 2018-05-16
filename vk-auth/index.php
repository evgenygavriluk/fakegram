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
echo "Start";
// Модуль регистрации через VK
include 'dbsettings.inc.php';
    $client_id = '6221068'; // ID 
    $client_secret = 'iejfiCmF5BZlQwpU6IyM'; 
    $redirect_uri = 'http://fakegram.org/vk-auth';
    $display = 'page';	
    $url = 'http://oauth.vk.com/authorize';
    $params = array(
        'client_id'     => $client_id,
		'display' 	=> $display,
		'scope'         => 'wall',
        'redirect_uri'  => $redirect_uri,
        'response_type' => 'code'
    );
	var_dump($params);
	print_r($_GET['code']);
	
if (isset($_GET['code'])) {
    $result = false;
    $params = array(
        'client_id' => $client_id,
        'client_secret' => $client_secret,
        'code' => $_GET['code'],
        'redirect_uri' => $redirect_uri
);

$token = json_decode(@file_get_contents('https://oauth.vk.com/access_token'.'?'.urldecode(http_build_query($params))), true);
if (isset($token['access_token'])) {
   $params = array(
            'uids'         => $token['user_id'],
            'fields'       => 'uid,first_name,last_name,screen_name,sex,bdate,photo_200',
            'access_token' => $token['access_token']
);
$userInfo = json_decode(file_get_contents('https://api.vk.com/method/users.get'.'?'.urldecode(http_build_query($params)).'&v=3.0'), true);
var_dump($userInfo);
    if (isset($userInfo['response'][0]['uid'])) {
         $userInfo = $userInfo['response'][0];
         $result = true;
   }
}
echo $result;
if ($result) {

	echo 'Социальный ID пользователя: ' . $userInfo['uid'] . '<br />';
    echo 'Имя пользователя: ' . $userInfo['first_name'] . '<br />';
	echo 'Имя пользователя: ' . $userInfo['last_name'] . '<br />';
    echo 'Ссылка на профиль пользователя: ' . $userInfo['screen_name'] . '<br />';
    echo 'Пол пользователя: ' . $userInfo['sex'] . '<br />';
    echo 'День Рождения: ' . $userInfo['bdate'] . '<br />';
    echo '<img src="' . $userInfo['photo_200'] . '" />'; 
	echo "<br />";

	// проверяем, есть ли уже в базе такой юзер и если нет, то записываем его в базу
	$date = @date("Y-m-d");
	$bdate = strtotime($userInfo['bdate']);
	$bdate = @date("Y-m-d", $bdate);
	$auth_uid = $userInfo['uid'];
	$firstname = $userInfo['first_name'];
	$secondname = $userInfo['last_name'];
	$birthday = $userInfo['bdate'];
	$photo = $userInfo['photo_200'];
	$link = mysqli_connect(DB_HOST,DB_LOGIN,DB_PASSWORD,DB_NAME);
	$sql = "SELECT id FROM users WHERE auth_provider = 2 AND auth_uid LIKE '$auth_uid'";
	$res = mysqli_query($link, $sql) or die(mysqli_error($link));
	$is_user = mysqli_fetch_assoc($res);
	session_start();
	session_unset();
	session_destroy();
	session_start();
	$_SESSION['id'] = $is_user['id'];
	$id = $is_user['id'];
	$_SESSION['vk_image'] = $userInfo['photo_200']; // Фотография из профиля VK
	$_SESSION['auth_provider'] = 2;
	$_SESSION['firstname'] = $userInfo['first_name'];
	$_SESSION['secondname'] = $userInfo['last_name'];
	// записываем в базу нового пользователя если его нет
	if(!isset($is_user))
	{
		mysqli_set_charset($link, "utf8");
		$sql = "INSERT INTO users(auth_provider, auth_uid, email, pass, date, firstname, secondname, country, lang, birthday, parentid) VALUES('2', '$auth_uid', '', '', '$date', '$firstname', '$secondname', '0', '1', '$bdate','')";
		echo $sql;
		mysqli_query($link, $sql) or die(mysqli_error($link));
		$_SESSION['id'] = mysqli_insert_id($link);
		echo $photo;
		img_resize($photo,'../users-photoes/'.$_SESSION['id'].'.jpg', 200, 75);
	}	
	// Если пользователь есть, обновляем в базе его аватарку
	else 
	{
		img_resize($photo, '../users-photoes/'.$_SESSION['id'].'.jpg', 200, 75);
	}
}
echo "finish";
mysqli_close($link);
Header("Location: http://fakegram.org/index.php");
}
// Конец модуля регистрации через VK
?>