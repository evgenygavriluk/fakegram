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


// Блок переменных для регистрации через FB
$fb_client_id = '892263737615828'; // Client ID
$fb_client_secret = 'a15fa011e82be5d6a19ef06d129a8ebb'; // Client secret
$fb_redirect_uri = 'http://fakegram.org/fb-auth'; // Redirect URIs
$fb_url = 'https://www.facebook.com/dialog/oauth';
$fb_params = array(
    'client_id'     => $fb_client_id,
    'redirect_uri'  => $fb_redirect_uri,
    'response_type' => 'code',
    'fields'         => 'email,user_birthday'
);
// Конец блока переменных для регистрации через FB 

// Блок переменных для регистрации через VK
$vk_client_id = '6221068'; // ID 
$vk_client_secret = 'iejfiCmF5BZlQwpU6IyM'; 
$vk_redirect_uri = 'http://fakegram.org/vk-auth';
$vk_display = 'page';	
$vk_url = 'http://oauth.vk.com/authorize';
$vk_params = array(
    'client_id'     => $vk_client_id,
	'display' 		=> $vk_display,
    'redirect_uri'  => $vk_redirect_uri,
    'response_type' => 'code'
);

// Блок переменных для регистрации через G+
$client_id = '558163431858-4ur2c9b7m0otu6e8g7841gvpkqsa9s3p.apps.googleusercontent.com'; // Client ID
$client_secret = 'n8eEyFCoK8K855CUtfANqG0F'; // Client secret
$redirect_uri = 'http://fakegram.org/gp-auth'; // Redirect URIs
$url = 'https://accounts.google.com/o/oauth2/auth';
$params = array(
    'redirect_uri'  => $redirect_uri,
    'response_type' => 'code',
    'client_id'     => $client_id,
    'scope'         => 'https://www.googleapis.com/auth/userinfo.email https://www.googleapis.com/auth/userinfo.profile'
);
?>

<!DOCTYPE HTML>
<html>
<head>
<?php include 'header.inc.php'; ?>	
	<style>
	#main-wrap{
		margin: 0 auto 0;
		max-width: 260px
	}
	
	.question {
	display: none;	
	min-height: 25px;
    position: relative;
    padding: 5px;
    margin: 0.5em 0 1em;
    color: #000;
    background: #f3961c;
    background: -webkit-gradient(linear, 0 0, 0 100%, from(#f9d835), to(#f3961c));
    background: -moz-linear-gradient(#f9d835, #f3961c);
    background: -o-linear-gradient(#f9d835, #f3961c);
    background: linear-gradient(#f9d835, #f3961c);
    -webkit-border-radius: 10px;
    -moz-border-radius: 10px;
    border-radius: 10px;
}

.question.left {
    margin-left: 30px;
}

.question.left:before {
    top: 10px;
    bottom: auto;
    left: -30px;
    border-width: 15px 30px 15px 0;
    border-color: transparent #f3961c;
}

.question:after {
    content: "";
    position: absolute;
    bottom: -13px;
    left: 47px;
    border-width: 13px 13px 0;
    border-style: solid;
    border-color: #f3961c transparent;
    display: block;
    width: 0;
}

.answer {
	display: none;
	min-height: 25px;
    position: relative;
    padding: 5px;
    margin: 0.5em 0 1em;
    color: #fff;
    background: #075698;
    background: -webkit-gradient(linear, 0 0, 0 100%, from(#2e88c4), to(#075698));
    background: -moz-linear-gradient(#2e88c4, #075698);
    background: -o-linear-gradient(#2e88c4, #075698);
    background: linear-gradient(#2e88c4, #075698);
   -webkit-border-radius: 10px;
    -moz-border-radius: 10px;
    border-radius: 10px;
}

.answer.right {
    margin-right: 20px;
}


.answer:after {
    content: "";
    position: absolute;
    bottom: -13px;
    right: 47px;
    border-width: 13px 13px 0;
    border-style: solid;
    border-color: #075698 transparent;
    display: block;
    width: 0;
}
	</style>
	<style>
#f1{
	display: inline-block;
	font-size: 18px;
	margin-left: auto;
	margin-right: 20px;
	margin-top: 20px;
	margin-bottom: 20px;
}	

#f9{
	display: none;
}
	</style>
</head>

<body>
<?php if (!isset($_SESSION['id'])){ ?>
<div class="index-container">
	<div class="width350 vertical">   
	<h1 id="title26">fakegram.org</h1>
    <div class="tabbable"> <!-- Only required for left/right tabs -->
       <ul class="nav nav-tabs">
          <li class="active"><a  href="#tab1" id="a-black" data-toggle="tab"><strong><?=$_SESSION['lang']['sign_in']?></strong></a></li>
          <li><a  href="#tab2" id="a-black" data-toggle="tab"><strong><?=$_SESSION['lang']['sign_up']?></strong></a></li>
       </ul>
    <div class="tab-content">
        <div class="tab-pane active" id="tab1">
			<div class="socialButton fontWhite">
				<a href ="<?php echo $fb_link = $fb_url.'?'.urldecode(http_build_query($fb_params));?>"><div class="facebookOuter"><div class="socialButtonText"><?php echo $_SESSION['lang']['continue_with']?> Facebook</div></div></a>  
			</div>		
			<div class="socialButton fontWhite">
				<?php echo $vklink = '<a href="'.$vk_url.'?'. urldecode(http_build_query($vk_params)) . '">'; ?><div class="vkOuter"><div class="socialButtonText"><?php echo $_SESSION['lang']['continue_with']?> VK</div></div></a>			
			</div>
			<div class="socialButton fontWhite">
				<?php echo $gp = '<a href="'.$url.'?'.urldecode(http_build_query($params)).'">'; ?><div class="gpOuter"><div class="socialButtonText"><?php echo $_SESSION['lang']['continue_with']?> Google+</div></div></a>
			</div>
			
			<div class="MakeFake_enter">
			<strong><?=$_SESSION['lang']['or_with_account']?></strong>
			
			<form class="center navbar-form" method="post" return false;>
				<label id="label_email"><?=$_SESSION['lang']['email_login']?></label>
				<input class="width100per" type="email" placeholder="Email" id="login_signin" name="login_signin" value=" " required>
				<label id="label_passwd"><?= $_SESSION['lang']['passwd_login']?></label>
				<input class="width100per" type="password" placeholder="Password" id="passwd_signin" name="passwd_signin" required>
				<br><br>
				<button class="up btn btn-primary btn-info" type="submit" name="signin_submit" id="SignInSubmit" onclick="flogin_signin(login_signin.value, passwd_signin.value)"><?php echo $_SESSION['lang']['sign_in']?></button>
			 </form> 
			</div> 
       </div>
       <div class="tab-pane" id="tab2">
			<div class="socialButton fontWhite">
				<a href ="<?php echo $fb_link = $fb_url.'?'.urldecode(http_build_query($fb_params));?>"><div class="facebookOuter"><div class="socialButtonText"><?php echo $_SESSION['lang']['continue_with']?> Facebook</div></div></a>  
			</div>		
			<div class="socialButton fontWhite">
				<?php echo $vklink = '<a href="' . $url . '?' . urldecode(http_build_query($params)) . '">'; ?><div class="vkOuter"><div class="socialButtonText"><?php echo $_SESSION['lang']['continue_with']?> VK</div></div></a>			
			</div>
			<div class="socialButton fontWhite">
				<?php echo $gp = '<a href="'.$url.'?'.urldecode(http_build_query($params)).'">'; ?><div class="gpOuter"><div class="socialButtonText"><?php echo $_SESSION['lang']['continue_with']?> Google+</div></div></a>
			</div>
			
			<div class="MakeFake_enter">	
				<strong><?=$_SESSION['lang']['create_new_account']?></strong>
				<div id="error_signup"></div>
				<form class="center navbar-form" method="post">
					<label id="label_email"><?=$_SESSION['lang']['email_login']?></label>
					<input class="width100per" type="email" placeholder="Email" name="login_signup" required>
					<label id="label_passwd"><?=$_SESSION['lang']['passwd_login']?></label>
					<input class="width100per" type="password" placeholder="Password" name="passwd_signup" required>
					<br><br>
					<button class="up btn btn-primary btn-info" type="submit" name="signup_submit" id="SignUpSubmit" onclick="flogin_signup(login_signup.value, passwd_signup.value);"><?php echo $_SESSION['lang']['sign_up']?></button>
				 </form>
			 </div>
		</div>
    </div>
</div> 
</div>
</div>
<?php } 
	else{ 	
	$user_id = $_SESSION['id'];
	$sql = "SELECT id FROM users WHERE id='$user_id' AND first_enter IS NOT null";
	$res = mysqli_query($link, $sql) or die(mysqli_error($link));
	$otvet = mysqli_fetch_assoc($res);
	if(!isset($otvet)){
	 include 'mainmenu.tpl';
	 echo '<div id="main">'; 
		echo '<div id="main-wrap">';
			 echo '<span id="f1" class="header">What is fakegram?</span><a href="newswall.php"><button id="f9">Let\'s go</button></a>';
			 echo '<span id="f2" class="question right">Is it a social network?</span>';
			 echo '<span id="f3" class="answer left">Mmm... may be.</span>';
			 echo '<span id="f4" class="question right">Can I create an account, send messages and photoes?</span>';
			 echo '<span id="f5" class="answer left">Yes exactly!</span>';
			 echo '<span id="f6" class="answer left">...but more.</span>';
			 echo '<span id="f7" class="answer left">You can create many fake accounts and send messages as different people.</span>';
			 echo '<span id="f8" class="answer left">Express different opinions, joke on over interloutors, troll them and remain unknown thanks to different fake accounts!</span>';
		echo '</div>';
	 echo '</div>';


	
?>
<script>
	setTimeout("$('#f2').css('display','block')",  500);
	setTimeout("$('#f3').css('display','block')", 1500);
	setTimeout("$('#f4').css('display','block')", 3000);
	setTimeout("$('#f5').css('display','block')", 3500);
	setTimeout("$('#f6').css('display','block')", 4500);
	setTimeout("$('#f9').css('display','inline-block')", 5000);
	setTimeout("$('#f7').css('display','block')", 5500);
	setTimeout("$('#f8').css('display','block')", 6000);

</script>
<?php
	$sql = "UPDATE users SET first_enter = '1' WHERE id = $user_id";
	$res = mysqli_query($link, $sql) or die(mysqli_error($link));
	mysqli_close($link);
	}
	else Header("Location: newswall.php");
}
?>
</body>
</html>