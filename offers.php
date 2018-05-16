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
	<script>
	<?php
		$community_id = $_GET['commname'];
		
		// читаем данные о сообществе 	
		$sql_comminfo = "SELECT id, comm_name, comm_description, comm_admin, comm_active, all_users FROM communities WHERE id=\"$community_id\"";
		$res_comminfo = mysqli_query($link, $sql_comminfo) or die(mysqli_error($link));
		$comminfo = mysqli_fetch_assoc($res_comminfo);

		$comm_id = $comminfo['id'];
		$comm_name = $comminfo['comm_name'];
		$comm_description = $comminfo['comm_description'];
		$all_users = $comminfo['all_users'];
		$comm_admin = $comminfo['comm_admin'];
		$comm_active = $comminfo['comm_active'];
		
		if ($comm_admin == $id){
			echo 'var publicAdmin=1;';
		  }
		  else {
			echo 'var publicAdmin=0;';
		};
	?>

	
	var public_id = <?php echo $_GET['commname']; ?>;
</script>

<script>ReadOfferedTopics(public_id);</script>	


</head>

<body onscroll="OfferTopicScroll();">



<?php include 'mainmenu.tpl'; ?>
<div id="main_publics">


<!-- Разметка для десктопа -->
<div id="desktop_public_list">
<h3><?php echo $comm_name; ?></h3>
<div class="center"><?php echo '<img src="publics-logo/'.$community_id.'.jpg" class="img-circle"></div>'; ?>

</div>


<!-- Разметка для мобильных устройств -->

<div id="public_list">

<div id="public"></div> <!-- Сюда будут выводиться все посты -->

</div>

</div>
<!-- mainblock finish -->


</body>
</html>