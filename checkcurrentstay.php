<?php
session_start();
if(isset($_SESSION['id']))
{
	include 'dbsettings.inc.php';
	$sessionid = $_SESSION['id'];
	//$link = mysqli_connect(DB_HOST,DB_LOGIN,DB_PASSWORD,DB_NAME);
	$sql = "SELECT * FROM stay WHERE user_id LIKE $sessionid AND DATE(NOW())<= DATE(till_date) AND DATE(NOW())>=DATE(from_date) AND finish=0";
		$res = mysqli_query($link, $sql) or die(mysqli_error($link));
		$stay = mysqli_fetch_assoc($res);
		if(isset($stay))
		{
			echo "current_yes";				
		}
		else echo "current_no";
	mysqli_close($link);
}
else Header("Location: index.php");
?>