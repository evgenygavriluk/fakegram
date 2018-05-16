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
} else 
{
		if($_SESSION['auth_provider'] == 1){ // Facebook
		$UN = $_SESSION['firstname'].' '.$_SESSION['secondname'];
		$user_menu = 
		'<h1 class="mainaccount_h1">'.$UN.'</h1>
		<ul class="mainaccount">
			<li id="ImageAvatar" class="center"><img class="object-fit_cover img-circle" src="users-photoes/'.$_SESSION['id'].'.jpg"></li>
			<li><a href="mypublics.php">'.$_SESSION['lang']['my_publics'].'</a></li>
			<li><a href="newpublic.php">'.$_SESSION['lang']['new_public'].'</a></li>			
			<li><hr></li>
			<li><a href="myavatars.php">'.$_SESSION['lang']['my_avatars'].'</a></li>
			<li><a href="newavatar.php">'.$_SESSION['lang']['new_avatar'].'</a></li>			
			<li><hr></li>
			<li><a href="mysettings.php">'.$_SESSION['lang']['account_settings'].'</a></li>
			<li><a href="logout.php">'.$_SESSION['lang']['log_out'].'</a></li>
		</ul>';
		}
	if($_SESSION['auth_provider'] == 2){ // VK
		$UN = $_SESSION['firstname'].' '.$_SESSION['secondname'];
		$user_menu = 
		'<h1 class="mainaccount_h1">'.$UN.'</h1>
		<ul class="mainaccount">
			<li id="ImageAvatar" class="center"><img src="users-photoes/'.$_SESSION['id'].'.jpg" width="150px" class="img-circle"></li>
			<li><a href="mypublics.php">'.$_SESSION['lang']['my_publics'].'</a></li>
			<li><a href="newpublic.php">'.$_SESSION['lang']['new_public'].'</a></li>			
			<li><hr></li>
			<li><a href="myavatars.php">'.$_SESSION['lang']['my_avatars'].'</a></li>
			<li><a href="newavatar.php">'.$_SESSION['lang']['new_avatar'].'</a></li>			
			<li><hr></li>
			<li><a href="mysettings.php">'.$_SESSION['lang']['account_settings'].'</a></li>
			<li><a href="logout.php">'.$_SESSION['lang']['log_out'].'</a></li>
		</ul>';
		}
	if($_SESSION['auth_provider'] == 3){ // G+
		$UN = $_SESSION['firstname'].' '.$_SESSION['secondname'];
		$user_menu = 
		'<h1 class="mainaccount_h1">'.$UN.'</h1>
		<ul class="mainaccount">
			<li id="ImageAvatar" class="center"><img class="object-fit_cover img-circle" src="users-photoes/'.$_SESSION['id'].'.jpg"></li>
			<li><a href="mypublics.php">'.$_SESSION['lang']['my_publics'].'</a></li>
			<li><a href="newpublic.php">'.$_SESSION['lang']['new_public'].'</a></li>			
			<li><hr></li>
			<li><a href="myavatars.php">'.$_SESSION['lang']['my_avatars'].'</a></li>
			<li><a href="newavatar.php">'.$_SESSION['lang']['new_avatar'].'</a></li>			
			<li><hr></li>			
			<li><a href="mysettings.php">'.$_SESSION['lang']['account_settings'].'</a></li>
			<li><a href="logout.php">'.$_SESSION['lang']['log_out'].'</a></li>
		</ul>';

		}	
	if(	$_SESSION['auth_provider'] == 0){ // InnFriends
		if($_SESSION['firstname'] == '') {
					$UN = $_SESSION['email'];
					}
					else if($_SESSION['secondname']=='') {
						$UN = $_SESSION['firstname'];
						} else {
							$UN = $_SESSION['firstname'].' '.$_SESSION['secondname'];
							}	
		$user_menu = 
		'<h1 class="mainaccount_h1">'.$UN.'</h1>
		<ul class="mainaccount">
			<li id="ImageAvatar" class="center"><img src="users-photoes/'.$_SESSION['id'].'.jpg" width="150px" class="img-circle"><div class="center"><a href="#" onclick="show_modal(\'#ImageUpload\')"><small>'.$_SESSION['lang']['upload_image'].'</small></a></div></li>
			<li><a href="mypublics.php">'.$_SESSION['lang']['my_publics'].'</a></li>
			<li><a href="newpublic.php">'.$_SESSION['lang']['new_public'].'</a></li>			
			<li><hr></li>
			<li><a href="myavatars.php">'.$_SESSION['lang']['my_avatars'].'</a></li>
			<li><a href="newavatar.php">'.$_SESSION['lang']['new_avatar'].'</a></li>			
			<li><hr></li>
			<li><a href="mysettings.php">'.$_SESSION['lang']['account_settings'].'</a></li>
			<li><a href="logout.php">'.$_SESSION['lang']['log_out'].'</a></li>
		</ul>';
	}
}

?>

<!DOCTYPE HTML>
<html>
<head>
<?php include 'header.inc.php'; ?>
</head>

<body>

<script>
function show_modal(id)
{
	$(id).modal('show');
};
</script>

<?php include 'mainmenu.tpl'; ?>
<div id="main">
<?php echo $user_menu; ?>
<?php include 'imageupload.tpl'; ?>
</div>
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
			url: "ajaxpro.php",
			type: "POST",
			data: {"image":resp},
			success: function (data) {
					$('#ImageUpload').modal('hide')	
					html = '<img src="' + resp + '" width="150px" class="img-circle"><div class="center"><p><a href="#" onclick="show_modal(\'#ImageUpload\')"><small><?php echo $_SESSION['lang']['upload_image']; ?></small></a></div>';
					$("#ImageAvatar").html(html);
					}
		});
  });
});
</script>
</body>
</html>