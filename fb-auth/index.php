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
$client_id = '892263737615828'; // Client ID
$client_secret = 'a15fa011e82be5d6a19ef06d129a8ebb'; // Client secret
$redirect_uri = 'http://fakegram.org/fb-auth'; // Redirect URIs
$url = 'https://www.facebook.com/dialog/oauth';
$params = array(
    'client_id'     => $client_id,
    'redirect_uri'  => $redirect_uri,
    'response_type' => 'code',
    'fields'         => 'email,user_birthday' // 'scope'         => 'email,user_birthday'
);
echo $link = '<p><a href="' . $url . '?' . urldecode(http_build_query($params)) . '">Аутентификация через Facebook</a></p>';
if (isset($_GET['code'])) {
    $result = false;
    $params = array(
        'client_id'     => $client_id,
        'redirect_uri'  => $redirect_uri,
        'client_secret' => $client_secret,
        'code'          => $_GET['code']
    );
    $url = 'https://graph.facebook.com/oauth/access_token';
	//parse_str(file_get_contents($url . '?' . http_build_query($params)));
	$resurl = file_get_contents($url . '?' . http_build_query($params));
	$tokenInfo = json_decode($resurl, true);
	
	$fp = fopen("log_fb.log", 'a+');
	fwrite($fp, 'TOKEN_INFO='.$tokenInfo['access_token'].';');
	//fwrite($fp, 'TOKEN='.$tokenInfo[0].';');
	fwrite($fp, 'COUNT='.count($tokenInfo).';');
	fclose($fp);
	
	
    if (count($tokenInfo) > 0 && isset($tokenInfo['access_token'])) {
        $params = array('access_token' => $tokenInfo['access_token']);

		$fp = fopen("log_fb.log", 'a+');
		fwrite($fp, 'PARAMS='.serialize($params).';');
		fclose($fp);
		
        $userInfo = json_decode(file_get_contents('https://graph.facebook.com/me' . '?' . urldecode(http_build_query($params))), true);

		$fp = fopen("log_fb.log", 'a+');
		fwrite($fp, 'USER_INFO='.serialize($userInfo).';');
		fclose($fp);
		
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
        echo "ДР: " . $userInfo['birthday'] . '<br />';
        echo '<img src="http://graph.facebook.com/' . $userInfo['id'] . '/picture?type=large" />'; echo "<br />";
		
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
		$sql = "SELECT id FROM users WHERE auth_provider = 1 AND auth_uid LIKE '$auth_uid'";
		$res = mysqli_query($link, $sql) or die(mysqli_error($link));
		$is_user = mysqli_fetch_assoc($res);
		session_start();
		session_unset();
		session_destroy();
		session_start();
		$_SESSION['id'] = $is_user['id'];
		$_SESSION['fb_image'] = $userInfo['id']; // Фотография из профиля FB
		$_SESSION['auth_provider'] = 1;
		$_SESSION['firstname'] = $firstname;
		$_SESSION['secondname'] = $secondname;
		// записываем в базу нового пользователя если его нет
		if(!isset($is_user))
		{
			$sql = "INSERT INTO users(auth_provider, auth_uid, email, pass, date, firstname, secondname, country, lang, birthday) VALUES('1', '$auth_uid', '', '', '$date', '$firstname', '$secondname', '0', '1', '$date')";
			echo $sql;
			mysqli_query($link, $sql) or die(mysqli_error($link));
			$_SESSION['id'] = mysqli_insert_id($link);
			echo $userInfo['picture'];
			img_resize('http://graph.facebook.com/'.$userInfo['id'].'/picture?type=large','../users-photoes/'.$_SESSION['id'].'.jpg', 200, 75);
		}
		else // Пересохраняем изображение т.к. оно могло измениться
		{
			img_resize($userInfo['picture'], '../users-photoes/'.$_SESSION['id'].'.jpg', 200, 75);
		}		
}
mysqli_close($link);
Header("Location: http://fakegram.org/index.php");
?>