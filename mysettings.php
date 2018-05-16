<?php
include 'dbsettings.inc.php';
session_start();
// Определяем язык
if(!isset($_GET['lang']))
{
	$lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2); // вырезаем первые две буквы
	//$link = mysqli_connect(DB_HOST,DB_LOGIN,DB_PASSWORD,DB_NAME);
	$sql = "SELECT * FROM languages WHERE lang_prefix LIKE '$lang'";
	$res = mysqli_query($link, $sql) or die(mysqli_error($link));
	$_SESSION['lang'] = mysqli_fetch_assoc($res);
} else {
	$lang = $_GET['lang'];
	//$link = mysqli_connect(DB_HOST,DB_LOGIN,DB_PASSWORD,DB_NAME);
	$sql = "SELECT * FROM languages WHERE lang_prefix LIKE '$lang'";
	$res = mysqli_query($link, $sql) or die(mysqli_error($link));
	$_SESSION['lang'] = mysqli_fetch_assoc($res);
}

// Проверка работы сессионных переменных 
//echo "email: ".$_SESSION['email']." id: ".$_SESSION['id']." auth_provider: ".$_SESSION['auth_provider']." first name: ".$_SESSION['firstname']." second name: ".$_SESSION['secondname'];
if (!isset($_SESSION['id'])) 
{ 
	$user_menu = '';
}


$id = $_SESSION['id'];
$sql = "SELECT firstname, secondname, country, lang, birthday FROM users WHERE id LIKE '$id'";
$res = mysqli_query($link, $sql) or die(mysqli_error($link));
$is_user = mysqli_fetch_assoc($res);

if(isset($is_user)){
		

}
mysqli_close($link);
 
?>

<!DOCTYPE HTML>
<html>
<head>
<?php include 'header.inc.php'; ?>
	<style>
		label{
			margin-top: 10px;
			margin-bottom: 0px;
		}
	</style>
</head>

<!-- <body onload="ReadAccountInformation(<?php echo $_SESSION['id']; ?>)"> -->
<body>
<?php include 'mainmenu.tpl'; ?>
<div id="main">
<h1 class="mainaccount_h1">My settings</h1>

<form class="center navbar-form" method="post">
	<label><?=$_SESSION['lang']['first_name']?></label>
	<input class="width20" type="text" placeholder="name" name="fname" id="fname" value="<?php echo $is_user['firstname']; ?>" required>			
	<label><?=$_SESSION['lang']['second_name']?></label>
	<input class="width20" type="text" placeholder="name" name="sname" id="sname" value="<?php echo $is_user['secondname']; ?>" required>
	<label><?=$_SESSION['lang']['country']?></label>
	<select name="country" size="1" class="width20" id="country">
	<?php
		$link = mysqli_connect(DB_HOST,DB_LOGIN,DB_PASSWORD,DB_NAME);
		mysqli_set_charset($link, "utf8");
		$sql = "SELECT * FROM countries";
		$res = mysqli_query($link, $sql) or die(mysqli_error($link));
		$selected = "";
		while($countr = mysqli_fetch_assoc($res))
		{
			if ($countr['id'] == $is_user['country']) $selected = "selected";
			echo '<option value='.$countr['id'].' '.$selected.'>'.$countr['country'].'</option>';
			$selected = "";
		}
		mysqli_close($link);
		?>
		</select>
	<label><?php echo $_SESSION['lang']['lng']?></label>
	<select name="language" size="1" class="width20" id="language">
	<?php
		$link = mysqli_connect(DB_HOST,DB_LOGIN,DB_PASSWORD,DB_NAME);
		mysqli_set_charset($link, "utf8");
		$sql = "SELECT * FROM languages";
		$res = mysqli_query($link, $sql) or die(mysqli_error($link));
		$selected = "";
		while($lang = mysqli_fetch_assoc($res))
		{
			if ($lang['id'] == $is_user['lang']) $selected = "selected";
			echo '<option value='.$lang['id'].' '.$selected.'>'.$lang['language'].'</option>';
			$selected = "";
		}
		mysqli_close($link);
		?>
		</select>
		<label><?=$_SESSION['lang']['birthday']?></label>
		<input name="birthday" id="birthday" type="date" value="<?php echo $is_user['birthday'] ?>">       
		<br><br>
		<button class="up btn btn-primary btn-info" type="submit" name="signup_submit" id="AccountSettingsSave" onclick="console.log(<?php echo "'".$_SESSION['id']."'"; ?>,fname.value, sname.value, country.value, language.value, birthday.value);SaveAccountInformation(<?php echo "'".$_SESSION['id']."'"; ?>, fname.value, sname.value, country.value, language.value, birthday.value);"><?php echo $_SESSION['lang']['save']?></button>
</form>		 
</div>
</body>
</html>