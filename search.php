<?php
session_start();
if (!isset($_SESSION['id']))
{
Header("Location: index.php");
} else $id = $_SESSION['id'];
// Параметры базы данных
include 'dbsettings.inc.php';

$search = trim($_POST['public_search_input']);
?>

<!DOCTYPE HTML>
<html>
<head>
<?php include 'header.inc.php'; ?>
</head>

<body>
<?php include 'mainmenu.tpl'; ?>
<div id="main">
<h1 class="mainaccount_h1">You are in interesting</h1>
			<?php
			$sql = "SELECT id, comm_name, comm_description, all_users FROM communities WHERE (comm_name LIKE '%$search%' OR comm_description LIKE '%$search%') AND comm_active LIKE '1'";
			$res = mysqli_query($link, $sql) or die(mysqli_error($link));
			$all_comm = 0;
			// Выводим список сообществ
			while($comm = mysqli_fetch_assoc($res))
			{
				$comm_id = $comm['id'];
				$comm_name = $comm['comm_name'];
				$comm_description = $comm['comm_description'];
				$all_users = $comm['all_users'];
				echo <<<ITEM
				<div class="public_item">
					<div class="public_item_img"><a href=""><img src="publics-logo/$comm_id.jpg" class="img-circle"></a></div>
					<div class="public_item_text"><a href="/$comm_id"><h3>$comm_name</h3></a>
					<p>$comm_description</p>
					<small>$all_users users</small></div>
				</div>
ITEM;
				$all_comm++;
			};
			// Если сообществ нет, пишем, что их нет
			if($all_comm == 0) echo '<div class="center votstup">Sorry, no results</div>';
			?>
</div>
</body>
</html>