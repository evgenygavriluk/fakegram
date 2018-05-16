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
// Блок управления аватарами
	//var current_avatar = 0;
	var avatars_id = new Array;
		avatars_id[0] = <?php echo $_SESSION['id']; ?>;
	var avatars_names = new Array('<?php echo $_SESSION['firstname']; ?>');
		
	function init_avatar(){
		$('#avatar_item').html('<img src="users-photoes/'+avatars_id[0]+'.jpg" width="50px" class="img-circle"> '+ avatars_names[0]);
	}
	
	<?php
		$community_id = $_GET['commname'];
		
		$sql = "SELECT id, firstname, secondname FROM users WHERE parentid =\"$id\"";
		$res = mysqli_query($link, $sql) or die(mysqli_error($link));
		// Выводим список сообществ
		$i = 1;
		while($av = mysqli_fetch_assoc($res))
		{
			echo "avatars_id[$i] = ".$av['id'].';';
			echo "avatars_names[$i] = '".$av['firstname'].'\';';
			$av_secondname = $av['secondname'];
			$i++;
		};
	?>
	
	<?php
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
	?>
	<?php if ($comm_admin == $id){
			echo 'var publicAdmin=1;';
		  }
		  else {
			echo 'var publicAdmin=0;';
		  };
	?>
	var all_avatars = avatars_id.length;
	
	function avatars_plus(){
		current_avatar++;
		if(current_avatar >= all_avatars) current_avatar = current_avatar-all_avatars;
		console.log(current_avatar);
		$('#avatar_item').html('<img src="users-photoes/'+avatars_id[current_avatar]+'.jpg" width="50px" class="img-circle"> '+ avatars_names[current_avatar]);
	}
	
	function avatars_minus(){
		current_avatar--;
		if(current_avatar < 0) current_avatar = all_avatars-1;
		console.log(current_avatar);
		$('#avatar_item').html('<img src="users-photoes/'+avatars_id[current_avatar]+'.jpg" width="50px" class="img-circle"> '+ avatars_names[current_avatar])
	}
	
	var public_id = <?php echo $_GET['commname']; ?>;
</script>
<!-- <script>setInterval(ReadTopics(public_id),300000);</script>-->
<script>ReadTopics(public_id);</script>	

<script>
    function showVisible() {
      var ppdiv = document.getElementsByClassName('public_post');
      for (var i = 0; i < ppdiv.length; i++) {
       	var t = parseInt(ppdiv[i].id.replace(/\D/g,''));
	if(show_comments[t]!=1) UpdateComments(t);
      }
    }
	
window.addEventListener('scroll',showVisible);	
</script>

</head>

<body onload="init_avatar();" onscroll="TopicScroll();">

<script>
function show_modal(id)
{
	DeleteFotoFromTopic();
	$(id).modal('show');
}

function repost(repost_id)
{
	RepostId = repost_id;
	$('#myRepost').modal('show');
}

function textarea_onblur(id) {
   var textarea = document.getElementById(id);
	if (textarea.value == "") textarea.rows = 2;
}
</script>


<?php include 'mainmenu.tpl'; ?>
<div id="avatar_list"><img id="leftstr" src="left.png" height="35px" onclick="avatars_minus();"><span id="avatar_item" class="avatar_list_item"></span><img id="rightstr" src="right.png" height="35px" onclick="avatars_plus();"></div>
<div id="main_publics">


<!-- Разметка для десктопа -->
<div id="desktop_public_list">
<h3><?=$comm_name; ?></h3>
<div class="center"><?php echo '<img src="publics-logo/'.$community_id.'.jpg" class="img-circle"></div>'; ?>
<div class="accordion" id="desktop_accordion">
<div class="accordion-group">
	<div class="accordion-heading">
		<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#desktop_collapseOne">
			Aboute this public
		</a>
	</div>
	<div id="desktop_collapseOne" class="accordion-body collapse">
		<div class="accordion-inner">
			<small><?=$comm_description; ?></small>
		</div>
		<div class="accordion-inner">
			<small><?php echo 'Total users '.$all_users; ?></small>
		</div>
	</div>
</div>

<?php if ($comm_admin == $id){ ?> 
	<div class="accordion-group">
		<div class="accordion-heading">
			<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#desktop_collapseTwo">Administrate public settings</a>
		</div>
		<div id="desktop_collapseTwo" class="accordion-body collapse">
			<div class="accordion-inner">
				<a href="editpublic.php?pb=<?php echo $comm_id;?>">Edit public settings</a>
			</div>
			<div id="hide_public" class="accordion-inner">
			<?php
				if($comm_active==0) echo '<a href="#" onclick="HidePublic(public_id, 1)">Show public</a>';
				if($comm_active==1) echo '<a href="#" onclick="HidePublic(public_id, 0)">Hide public</a>';
			?>
			</div>
			<div class="accordion-inner">
				<a href="">Delete public</a>
			</div>
		</div>
	</div>		
<?php }
	else {
		$sql_followers = "SELECT * FROM followers WHERE public_id=\"$comm_id\" and user_id=\"$id\"";
		$res_followers = mysqli_query($link, $sql_followers) or die(mysqli_error($link));
		$followers = mysqli_fetch_assoc($res_followers);

		if(isset($followers['id'])){ ?>
					
			<div class="accordion-group">
				<div id="desktop_follow_public" class="accordion-heading">
					<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#desktop_collapseTwo" onclick="followPublic(public_id, 0);">Exit from followers</a>
				</div>
			</div>	
					
<?php } else { ?>
			<div class="accordion-group">
				<div id="desktop_follow_public" class="accordion-heading">
					<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#desktop_collapseTwo" onclick="followPublic(public_id, 1);">Follow this public</a>
				</div>			
			</div>

<?php }
} ?>

<?php
// *** Есть ли предложенные топики? Если есть, показываем кнопку *************************************************************************************************************************
	$sql_offers = "SELECT * FROM offertopics WHERE community=\"$comm_id\" AND del is null";
	$res_offers = mysqli_query($link, $sql_offers) or die(mysqli_error($link));
	$offers = mysqli_fetch_assoc($res_offers);

		if(isset($offers) and $comm_admin == $id){ 
?>					
			<div class="accordion-group">
				<div class="accordion-heading">
					<a id="offer_topics" class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" onclick="document.location.replace('/offers.php?commname=<?php echo $community_id; ?>')">Offered topics</a>
				</div>
			</div>	
<?php }
// ***************************************************************************************************************************************************************************************			
?>
<?php 
// *** Если админ, то выводим New topic, если нет, то Offer topic ************************************************************************************************************************
if ($comm_admin == $id){?>
<div class="accordion-group">
	<div class="accordion-heading">
		<a href="" id="desktop_new_topic_button" class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#desktop_collapseTwo" onclick="show_modal('#myModal')">New topic</a>
	</div>
</div>
<?php } else { ?>
<div class="accordion-group">
	<div class="accordion-heading">
		<a href="" id="desktop_new_topic_button" class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#desktop_collapseTwo" onclick="show_modal('#offerTopic')">Offer topic</a>
	</div>
</div>
<?php } 
// ***************************************************************************************************************************************************************************************
?>
</div>
</div>


<!-- Разметка для мобильных устройств -->

<div id="public_list">
<h3><?=$comm_name; ?></h3>
<div class="center"><?php echo '<img src="publics-logo/'.$community_id.'.jpg" class="img-circle"></div>'; ?>
<div class="accordion" id="accordion">
<div class="accordion-group">
	<div class="accordion-heading">
		<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapseOne">
			Aboute this public
		</a>
	</div>
	<div id="collapseOne" class="accordion-body collapse">
		<div class="accordion-inner">
			<small><?=$comm_description; ?></small>
		</div>
		<div class="accordion-inner">
			<small><?php echo 'Total users '.$all_users; ?></small>
		</div>
	</div>
</div>

<?php if ($comm_admin == $id){ ?> 
	<div class="accordion-group">
		<div class="accordion-heading"> 
			<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapseTwo">Administrate public settings</a>
		</div>
		<div id="collapseTwo" class="accordion-body collapse">
			<div class="accordion-inner">
				<a href="editpublic.php?pb=<?php echo $comm_id;?>">Edit public settings</a>
			</div>
			<div id="hide_public" class="accordion-inner">
			<?php
				if($comm_active==0) echo '<a href="#" onclick="HidePublic(public_id, 1)">Show public</a>';
				if($comm_active==1) echo '<a href="#" onclick="HidePublic(public_id, 0)">Hide public</a>';
			?>
			</div>
			<div class="accordion-inner">
				<a href="">Delete public</a>
			</div>
		</div>
	</div>		
<?php }
	else {
		if(isset($followers['id'])){?>
					
			<div class="accordion-group">
				<div id="follow_public" class="accordion-heading">
					<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapseTwo" onclick="followPublic(public_id, 0);">Exit from followers</a>
				</div>
			</div>	
					
<?php } else {?>
			<div class="accordion-group">
				<div id="follow_public" class="accordion-heading">
					<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#collapseTwo" onclick="followPublic(public_id, 1);">Follow this public</a>
				</div>			
			</div>

<?php }
} ?>

<?php
// *** Есть ли предложенные топики? Если есть, показываем кнопку *************************************************************************************************************************
		if(isset($offers) and $comm_admin == $id){ 
?>					
			<div class="accordion-group">
				<div id="offer_topics" class="accordion-heading">
					<a id="offer_topics" class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" onclick="document.location.replace('/offers.php?commname=<?php echo $community_id; ?>')">Offered topics</a>
				</div>
			</div>	
<?php }
// ***************************************************************************************************************************************************************************************			
?>

<?php if ($comm_admin == $id){?>
<div class="accordion-group">
	<div class="accordion-heading">
		<a href="" id="desktop_new_topic_button" class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#desktop_collapseTwo" onclick="show_modal('#myModal')">New topic</a>
	</div>
</div>
<?php } else { ?>
<div class="accordion-group">
	<div class="accordion-heading">
		<a href="" id="desktop_new_topic_button" class="accordion-toggle" data-toggle="collapse" data-parent="#accordion2" href="#desktop_collapseTwo" onclick="show_modal('#offerTopic')">Offer topic</a>
	</div>
</div>
<?php } ?>

</div>

<div id="public"></div> <!-- Сюда будут выводиться все посты -->

</div>
<!-- mainblock finish -->

<!-- Модальное окно Ноый топик -->
<div class="modal hide fade" id="myModal" tabindex="-1">
	<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">X</button>
			<img src="publics-logo/<?=$community_id; ?>.jpg" class="img-circle" width="50px">
			<h3>New topic</h3>
	</div>
	<div class="modal-body">
		<form action="addtopic.php" method="post" enctype="multipart/form-data">
			<textarea name="topic_description" rows="5" class="span12"></textarea>
			<input type="hidden" name="comm_id" value="<?=$comm_id; ?>" />
			<span class="edit center" id="TOPICfoto" style="display: none;"></span>
			<input id="TOPIC_type" name="TOPIC_type" type="hidden" value="0">
			<div class="send_form">
				<button id="save_new_topic" name="save_new_topic"><span>Save</span></button>
				<input type="file" name="topicfile" id="topicfile<?=$community_id; ?>" class="file-select" onchange="loadfile_topic(this.value);">
				<label for="topicfile<?=$community_id; ?>"></label>
			</div>
			
		</form>
	</div>
</div>
<!-- Конец модального окна -->

<!-- Модальное окно Предложить топик -->
<div class="modal hide fade" id="offerTopic" tabindex="-1">
	<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">X</button>
			<img src="publics-logo/<?=$community_id; ?>.jpg" class="img-circle" width="50px">
			<h3>Offer topic</h3>
	</div>
	<div class="modal-body">
		<form action="offertopic.php" method="post" enctype="multipart/form-data">
			<textarea name="topic_description" rows="5" class="span12"></textarea>
			<input type="hidden" name="comm_id" value="<?=$comm_id; ?>" />
			<span class="edit center" id="OFFERfoto" style="display: none;"></span>
			<input id="OFFER_type" name="OFFER_type" type="hidden" value="0">
			<div class="send_form">
				<button id="offer_new_topic" name="offer_new_topic"><span>Save</span></button>
				<input type="file" name="offerfile" id="offerfile<?=$community_id; ?>" class="file-select" onchange="loadfile_offer(this.value);">
				<label for="offerfile<?=$community_id; ?>"></label>
			</div>
			
		</form>
	</div>
</div>
<!-- Конец модального окна -->

<!-- Модальное окно Репост -->
<div class="modal hide fade" id="myRepost" tabindex="-1">
	<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">X</button>
			<img src="publics-logo/<?php echo $community_id; ?>.jpg" class="img-circle" width="50px">
			<h3>Repost topic to:</h3>
	</div>
	<div class="modal-body">
		<?php
		
		// Выбираем группы в которых пользователь админ
			$sql = "SELECT id, comm_name, comm_description, all_users FROM communities WHERE comm_admin =\"$id\" and id!=\"$community_id\"";
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
				<div id="repost_item$comm_id" class="repost" onclick="saveRepost($comm_id);">
					<div id="repost_item_img$comm_id"><img src="publics-logo/$comm_id.jpg" class="img-circle" width="50px"><h3>$comm_name</h3></div>
					<small>$all_users users</small>
				</div>
ITEM;
				$all_comm++;
			};
		// Теперь выбираем группы в которых пользователь фолловер
			$sql = "SELECT comm.id, comm.comm_name, comm.comm_description, comm.all_users FROM communities comm INNER JOIN followers rufoll ON rufoll.public_id = comm.id WHERE rufoll.user_id =\"$id\"";
			$res = mysqli_query($link, $sql) or die(mysqli_error($link));
			// Выводим список сообществ
			while($comm = mysqli_fetch_assoc($res))
			{
				$comm_id = $comm['id'];
				$comm_name = $comm['comm_name'];
				$comm_description = $comm['comm_description'];
				$all_users = $comm['all_users'];
				echo <<<ITEM
				<div id="repost_item$comm_id" class="repost" onclick="saveRepost($comm_id);">
					<div id="repost_item_img$comm_id">
						<img src="publics-logo/$comm_id.jpg" class="img-circle" width="50px">
						<h3>$comm_name</h3>
						
					</div>
					<small>$all_users users</small>
				</div>
ITEM;
				$all_comm++;
			};
		
			// Если сообществ нет, пишем, что их нет
			if($all_comm == 0) echo '<div class="center votstup">Sorry, but you dont have any avatars</div>';
		?>
	</div>
</div>
<!-- Конец модального окна -->
</body>
</html>