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
	// Сортировка пабликов по введенным буквам
	var count;
	var znachenie;
    var enter;
    var i;
    bspace = 0;
	function topicSarch()
	{
		var elems = document.getElementsByClassName('all_text');
		console.log(elems);
		console.log(elems.length);
		
		var input = document.getElementById('MsgResultSearch');
		if(bspace!=0) enter = enter;
		else enter = input.value+getChar(event);
		
		console.log('Вы ввели = '+enter);
		console.log(elems[0].innerText);
		
		for(i=0; i<elems.length; i++)
		{	
			znachenie = elems[i].innerText;
			console.log(elems[i].parentNode.parentNode);
			//console.log('innerText = '+znachenie.slice(0,enter.length));
			
			// ищет по вхождению букв
			if (znachenie.toLowerCase().indexOf(enter.toLowerCase()) == -1 ) elems[i].parentNode.parentNode.parentNode.style.display ='none'; 
			else elems[i].parentNode.parentNode.parentNode.style.display = 'inline-block';
			
			/* ищет по первым буквам
			if (znachenie.slice(0,count=enter.length).toLowerCase() != enter.toLowerCase() ) elems[i].parentNode.parentNode.style.display ='none';
			else elems[i].parentNode.parentNode.style.display = 'inline-block';
			*/
		}
		bspace = 0;
	}
	
	document.onkeydown = function(e) {
		if (e.keyCode == 8) {
			count--; 
			//console.log(count); 
			enter = enter.slice(0,-1); 
			//console.log('backspace enter = '+enter); 
			//console.log('znachenie = '+znachenie.slice(0,count=enter.length));
			//if(znachenie.slice(0,count=enter.length) == enter) {
				bspace = 1;
				topicSarch();
				//}
		}	
	}


	// event.type должен быть keypress
	function getChar(event) {
	  if (event.which == null) { // IE
		//if (event.keyCode < 32) return null; // спец. символ
		return String.fromCharCode(event.keyCode)
	  }

	  if (event.which != 0 && event.charCode != 0) { // все кроме IE
		//if (event.which < 32) return null; // спец. символ
		return String.fromCharCode(event.which); // остальные
	  }

	  return ''; // спец. символ
	}
	</script>
</head>

<body>
<?php include 'mainmenu.tpl'; ?>
<div id="main">
<h1 class="mainaccount_h1">My publics</h1>
<script>
var groups = [
			<?php
			$sql = "SELECT communities.id, communities.comm_name, communities.comm_description, communities.comm_admin, communities.all_users FROM communities INNER JOIN followers ON communities.id = followers.public_id WHERE followers.user_id =\"$id\" UNION SELECT id, comm_name, comm_description, communities.comm_admin, all_users FROM communities WHERE comm_admin =\"$id\"";
			$res = mysqli_query($link, $sql) or die(mysqli_error($link));
			// Выводим список всех сообществ
			while($comm = mysqli_fetch_assoc($res))
			{
				$comm_id = $comm['id'];
				$comm_name = $comm['comm_name'];
				$comm_description = $comm['comm_description'];
				$status_admin = 0; 
				if($comm['comm_admin'] == $id) $status_admin = 1;
				$all_users = $comm['all_users'];
				echo "[$comm_id, '$comm_name', '$comm_description', $status_admin, $all_users],";
			};
			?>
];
</script>


    <div class="tabbable"> <!-- Only required for left/right tabs -->
       <ul class="nav nav-tabs">
	
          <li class="active"><a  href="#tab1" id="a-black" data-toggle="tab"><strong>All publics</strong></a></li>
          <li><a  href="#tab2" id="a-black" data-toggle="tab"><strong>Manage</strong></a></li>
       </ul>
    <div class="tab-content">
        <div class="tab-pane active" id="tab1"> 
		<input id="MsgResultSearch" name="MsgResultSearch" type="text" placeholder="Search by publics" style="float: right; margin-right: 10px; display: block;" onkeypress="topicSarch();">

		<script>
			var groupsCnt = 0;
			while(groupsCnt < groups.length)
			{
				document.getElementById('tab1').innerHTML+='<a href="/'+groups[groupsCnt][0]+'">\
				<div class="public_item all">\
					<div class="public_item_img"><img src="publics-logo/'+groups[groupsCnt][0]+'.jpg" class="img-circle"></div>\
					<div class="public_item_text"><h3 class="all_text">'+groups[groupsCnt][1]+'</h3>\
					<p>'+groups[groupsCnt][2]+'</p>\
					<small>'+groups[groupsCnt][4]+' members</small>\
					</div>\
				</div>\
				</a>';
				groupsCnt++;
			}
		</script>
			<?php
			/*
			$sql = "SELECT communities.id, communities.comm_name, communities.comm_description, communities.comm_admin, communities.all_users FROM communities INNER JOIN followers ON communities.id = followers.public_id WHERE followers.user_id =\"$id\" UNION SELECT id, comm_name, comm_description, communities.comm_admin, all_users FROM communities WHERE comm_admin =\"$id\"";
			$res = mysqli_query($link, $sql) or die(mysqli_error($link));
			$all_comm = 0;
			// Выводим список всех сообществ
			while($comm = mysqli_fetch_assoc($res))
			{
				$comm_id = $comm['id'];
				$comm_name = $comm['comm_name'];
				$comm_description = $comm['comm_description'];
				$all_users = $comm['all_users'];
				echo <<<ITEM
				<a href="/$comm_id">
				<div class="public_item">
					<div class="public_item_img"><img src="publics-logo/$comm_id.jpg" class="img-circle"></div>
					<div class="public_item_text"><h3>$comm_name</h3>
					<p>$comm_description</p>
					<small>$all_users members</small></div>
				</div>
				</a>
				<hr>
ITEM;
				$all_comm++;
				print_r($comm);
			};
			
			// Если сообществ нет, пишем, что их нет
			if($all_comm == 0) echo '<div class="center votstup">Sorry, but you dont have any avatars</div>';
			*/
			?>
 
       </div>
       <div class="tab-pane" id="tab2">
	   		<script>
			var groupsCnt = 0;
			while(groupsCnt < groups.length)
			{
				if(groups[groupsCnt][3] ==1)
				{
				document.getElementById('tab2').innerHTML+='<a href="/'+groups[groupsCnt][0]+'">\
				<div class="public_item manage">\
					<div class="public_item_img"><img src="publics-logo/'+groups[groupsCnt][0]+'.jpg" class="img-circle"></div>\
					<div class="public_item_text"><h3>'+groups[groupsCnt][1]+'</h3>\
					<p>'+groups[groupsCnt][2]+'</p>\
					<small>'+groups[groupsCnt][4]+' members</small></div>\
				</div>\
				</a>';
				groupsCnt++;
				}
				else {
					groupsCnt++;
					continue;
					};
			}
		</script>
			<?php
			/*
			// Выводим только те, где администратор
			$sql = "SELECT id, comm_name, comm_description, all_users FROM communities WHERE comm_admin =\"$id\"";
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
				<a href="/$comm_id">
				<div class="public_item">
					<div class="public_item_img"><img src="publics-logo/$comm_id.jpg" class="img-circle"></div>
					<div class="public_item_text"><h3>$comm_name</h3>
					<p>$comm_description</p>
					<small>$all_users members</small></div>
				</div>
				</a>
				<hr>
ITEM;
				$all_comm++;
			};
			// Если сообществ нет, пишем, что их нет
			if($all_comm == 0) echo '<div class="center votstup">Sorry, but you dont have any avatars</div>';
			*/
			?>
		</div>
    </div>
</div>

</div>
</body>
</html>