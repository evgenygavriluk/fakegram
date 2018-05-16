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
 
// Записываем аватар в базу 
if(isset($_POST['NewAvatar'])){
	$avatarFirstName =  $_POST['fname'];
	$avatarSurname = $_POST['sname'];
	$parantId = $_SESSION['id'];
	$date = @date("Y-m-d");
	$image = $_SESSION['avatarImage']; // Изображение аватарки

	$link = mysqli_connect(DB_HOST,DB_LOGIN,DB_PASSWORD,DB_NAME);
	mysqli_set_charset($link, "utf8");
	$sql = "INSERT INTO users(auth_provider, auth_uid, email, pass, date, firstname, secondname, country, lang, birthday, parentid) VALUES('4','', '', '', '$date', '$avatarFirstName', '$avatarSurname', '0', '1', '$date', '$parantId')";
	mysqli_query($link, $sql) or die(mysqli_error($link));

	$avatar_id = mysqli_insert_id($link); // Получаем id созданного аватара

	list($type, $image) = explode(';', $image);
	list(, $image)      = explode(',', $image);
	if(isset($image)) 
	{
		$data = base64_decode($image);
		$imageName = $avatar_id.'.jpg';
		file_put_contents('users-photoes/'.$imageName, $data);
	}
	else {
		copy('users-photoes/avatar.jpg', 'users-photoes/'.$avatar_id.'.jpg');
	}
		
	mysqli_close($link);	
	Header("Location: index.php");
}	
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
<script>
function show_modal(id)
{
	$(id).modal('show');
};
</script>

<?php include 'mainmenu.tpl'; ?>
<div id="main">
<h1 class="mainaccount_h1">Create avatar</h1>

<div id="avatar_photo" class="center"><div id="avatar_photo_img"><img src="users-photoes/avatar.jpg" width="150px" class="img-circle"></div><div class="center"></div>
<div class="center"><a href="#" onclick="show_modal('#ImageUpload')"><small><?php echo $_SESSION['lang']['upload_image']; ?></small></a></div>
<form class="center navbar-form" action="" method="post">
	<label><?php echo $_SESSION['lang']['first_name']?></label>
	<input class="width20" type="text" placeholder="name" name="fname" id="fname" value="Anonimous" required>			
	<label><?php echo $_SESSION['lang']['second_name']?></label>
	<input class="width20" type="text" placeholder="name" name="sname" id="sname" value="Noname" required>
	<label><?php echo $_SESSION['lang']['country']?></label>
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
		<label><?php echo $_SESSION['lang']['birthday']?></label>
		<input name="birthday" id="birthday" type="date">       
		<br><br>
		<button class="up btn btn-primary btn-info" type="submit" name="NewAvatar" id="NewAvatar"><?php echo $_SESSION['lang']['save']?></button>
</form>	
</div>	 
<?php include 'imageupload.tpl'; ?>

<script>
// кроппинг	  
$uploadCrop = $('#upload-demo').croppie({
    enableExif: true,
    viewport: {
        width: 200,
        height: 200,
        type: 'circle'
    },
    boundary: {
        width: 200,
        height: 200
    }
});

$('#upload').on('change', function () { 
  var reader = new FileReader();
    reader.onload = function (e) {
      $uploadCrop.croppie('bind', {
        url: e.target.result
      }).then(function(){
        console.log('jQuery bind complete');
      });
      
    }
    reader.readAsDataURL(this.files[0]);
});

$('.upload-result').on('click', function (ev) {
  $uploadCrop.croppie('result', {
    type: 'canvas',
    size: 'viewport'
  }).then(function (resp) {
	  $.ajax({
			url: "ajaxpro-avatars.php",
			type: "POST",
			data: {"image":resp},
			success: function (data) {
					$('#ImageUpload').modal('hide')	
					html = '<img src="' + resp + '" width="150px" class="img-circle"><div class="center"><p><a href="#" onclick="show_modal(\'#ImageUpload\')"><small><?php echo $_SESSION['lang']['upload_image']; ?></small></a></div>';
					$("#ImageAvatar").html(html);
					html = '<img src="' + resp + '" width="150px" class="img-circle">';
					$("#avatar_photo_img").html(html);
					}
		});
  });
});
</script>

</body>
</html>