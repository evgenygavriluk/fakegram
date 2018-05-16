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
	<style>
		#main_publics{
			margin-top: 50px;
		}
		
		.top_header{
			margin-bottom: 10px;
		}
		
		.top_header span{
			margin-left: 10px;
			font-weight: bold;
		}
		
		.black{
			color: black;
		}
		
	#public_list .accordion, #public_list h3, #public_list .center{
		display: block;
	}
	
	
		.question {
	display: block;	
	min-height: 25px;
	max-width: 280px;
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
	display: block;
	min-height: 25px;
	max-width: 280px;
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

.wall_post{
	background-color: white;
	color: black;
}
	</style>

<script>ReadNewsWall(<?php echo $id;?>);</script>	
</head>

<body onscroll="WallScroll(<?php echo $id;?>);">

<?php include 'mainmenu.tpl'; ?>
<div id="main_publics">

<div id="public_list">
<div class="center">

<?php 
$sql_topicslist = "SELECT id, text, community, photo, likes, repost_id, date, del FROM topics WHERE (community IN (SELECT public_id FROM followers WHERE user_id = $id) OR community IN (SELECT id FROM communities WHERE comm_admin = $id))  AND del is null AND repost_id is null ORDER BY id DESC LIMIT 3";
$res_topicslist = mysqli_query($link, $sql_topicslist) or die(mysqli_error($link));
$topic = mysqli_fetch_assoc($res_topicslist);
if(!isset($topic)){
	echo '<span class="answer right">Look for interesting publics or create them</span>';
	echo '<span class="answer right">For example the last created topic is';
	
	$sql_lasttopic = "SELECT id, text, community, photo, likes, repost_id, date, del FROM topics ORDER BY id DESC LIMIT 1";
	$res_lasttopic = mysqli_query($link, $sql_lasttopic) or die(mysqli_error($link));
	$topic = mysqli_fetch_assoc($res_lasttopic);
?>	
	<div id="public_post92" class="wall_post">
		<div class="public_time">
		<p>Владимир Николаевич!</p>
		<img src="topic_uploads/84.jpg" class="topicimage">
			<div id="topic_time92" class="topic_time">
				<span>February 15th 2018, 11:24 pm</span><span id="repost"></span><i class="icon-retweet"></i><span id="topiclike92" class="tl_font"></span><i class="icon-heart"></i>
			</div>
		</div>
	</div></span>
<?php
}
?>
<div id="public"></div> <!-- Сюда будут выводиться все посты -->

</div>
<!-- mainblock finish -->

</body>
</html>