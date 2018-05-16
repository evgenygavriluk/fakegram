<?php
session_start();
if (!isset($_SESSION['id']))
{
Header("Location: index.php");
} else $id = $_SESSION['id'];
// Параметры базы данных
include 'dbsettings.inc.php';
?>

<!DOCTYPE HTML>
<html>
<head>
<?php include 'header.inc.php'; ?>
</head>

<body>
<?php include 'mainmenu.tpl'; ?>
<div id="main">
<h1 class="mainaccount_h1">My fakes</h1>
			<?php
			$sql = "SELECT id, firstname, secondname FROM users WHERE parentid =\"$id\"";
			$res = mysqli_query($link, $sql) or die(mysqli_error($link));
			$all_avatars = 0;
			// Выводим список аватаров
			while($av = mysqli_fetch_assoc($res))
			{
				$av_id = $av['id'];
				$av_firstname = $av['firstname'];
				$av_secondname = $av['secondname'];
				echo <<<ITEM
				<div class="public_item">
					<div class="public_item_img"><img src="users-photoes/$av_id.jpg" class="img-circle"></div>
					<div ckass="public_item_text"><h3>$av_firstname $av_secondname</h3></div>
				</div>
ITEM;
				$all_avatars++;
			};
			// Если аватаров нет, пишем, что их нет
			if($all_avatars == 0) echo '<div class="center votstup">Sorry, but you dont have any avatars</div>';
			?>
</div>
</body>
</html>