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
$client_id = '558163431858-4ur2c9b7m0otu6e8g7841gvpkqsa9s3p.apps.googleusercontent.com'; // Client ID
$client_secret = 'n8eEyFCoK8K855CUtfANqG0F'; // Client secret
$redirect_uri = 'http://fakegram.org/gp-auth'; // Redirect URIs

$url = 'https://accounts.google.com/o/oauth2/auth';

$params = array(
    'redirect_uri'  => $redirect_uri,
    'response_type' => 'code',
    'client_id'     => $client_id,
    'scope'         => 'https://www.googleapis.com/auth/userinfo.profile'
);

if (isset($_GET['code'])) {
    $result = false;

    $params = array(
        'client_id'     => $client_id,
        'client_secret' => $client_secret,
        'redirect_uri'  => $redirect_uri,
        'grant_type'    => 'authorization_code',
        'code'          => $_GET['code']
    );

    $url = 'https://accounts.google.com/o/oauth2/token';

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, urldecode(http_build_query($params)));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    $result = curl_exec($curl);
    curl_close($curl);
    $tokenInfo = json_decode($result, true);

    if (isset($tokenInfo['access_token'])) {
        $params['access_token'] = $tokenInfo['access_token'];

        $userInfo = json_decode(file_get_contents('https://www.googleapis.com/oauth2/v1/userinfo'.'?'.urldecode(http_build_query($params))), true);
		echo $userInfo;
        if (isset($userInfo['id'])) {
            $userInfo = $userInfo;
            $result = true;
        }
    }
}

if ($result) {
	var_dump($userInfo);
	echo "Социальный ID пользователя: " . $userInfo['id'] . '<br />';
	echo "Имя пользователя: " . $userInfo['name'] . '<br />';
	echo "Email: " . $userInfo['email'] . '<br />';
	echo "Ссылка на профиль пользователя: " . $userInfo['link'] . '<br />';
	echo "Пол пользователя: " . $userInfo['gender'] . '<br />';
	echo '<img src="' . $userInfo['picture'] . '" />'; echo "<br />";
		
	// проверяем, есть ли уже в базе такой юзер и если нет, то записываем его в базу
	$date = @date("Y-m-d");
	//$bdate = strtotime($userInfo['birthday']);
	//$bdate = @date("Y-m-d", $bdate);
	$auth_uid = $userInfo['id'];

	$names = explode(" ", $userInfo['name']);
	$firstname = $names[0];
	$secondname = $names[1];
		
	//$birthday = $userInfo['bdate'];
	$link = mysqli_connect(DB_HOST,DB_LOGIN,DB_PASSWORD,DB_NAME);
	$sql = "SELECT id FROM users WHERE auth_provider = 3 AND auth_uid LIKE '$auth_uid'";
	$res = mysqli_query($link, $sql) or die(mysqli_error($link));
	$is_user = mysqli_fetch_assoc($res);
	session_start();
	session_unset();
	session_destroy();
	session_start();
	$_SESSION['id'] = $is_user['id'];
	$_SESSION['auth_provider'] = 3;
	$_SESSION['firstname'] = $firstname;
	$_SESSION['secondname'] = $secondname;
	// записывае в базу нового пользователя если его нет
	if(!isset($is_user))
	{
		$sql = "INSERT INTO users(auth_provider, auth_uid, email, pass, date, firstname, secondname, country, langfirst, langsecond, birthday) VALUES('3', '$auth_uid', '', '', '$date', '$firstname', '$secondname', '0', '1', '1', '$date')";
		echo $sql;
		mysqli_query($link, $sql) or die(mysqli_error($link));
		$_SESSION['id'] = mysqli_insert_id($link);
		// Сохраняем изображение к себе на сервер
		echo $userInfo['picture'];
		img_resize($userInfo['picture'], '../users-photoes/'.$_SESSION['id'].'.jpg', 200, 75);
	}
		else // Пересохраняем изображение т.к. оно могло измениться
	{
		img_resize($userInfo['picture'], '../users-photoes/'.$_SESSION['id'].'.jpg', 200, 75);
	}
}
mysqli_close($link);
Header("Location: http://fakegram.org/index.php");
?>