<?php
error_reporting(E_ERROR | E_PARSE);
session_start();

	require_once('classes/db.php');
	require('classes/video.php');
	require('classes/boards.php');


//PROCESS LOGINS
if (isset($_POST['user']) && isset($_POST['pass'])) {
	if ($_POST['user'] != null && $_POST['pass'] != null) {
		//	echo 'lol';
		function checkUserPass($user, $pass) {
       
   			$result = mysql_query("SELECT * FROM `users` WHERE user = '".$user."'") or die("Query failed with error: ".mysql_error());
			while ($row = mysql_fetch_array($result)) {
				$the_user = array("user" => $row['user'],"pass" => $row['pass']);
			}
			
			if ($pass == $the_user["pass"]) {
			 	return true;
			} else {
				return false;
			}
		}
		
		$success = checkUserPass($_POST['user'], md5($_POST['pass']));
		
		if ($success == true) {
			$_SESSION['bro'] = 'truetrue';
			header("location: ../");
		} else {
			echo 'Nawwwww. Login wrong breh.';
		}
	}
}

//PROCESS LOGOUT
if (isset($_GET['q'])) {
	session_destroy();
	header("location: ../");
}

//PROCESS CONTENT ADD/DELETES
if (isset($_POST['vid']) && $_POST['vid'] != null && $_SESSION['bro'] == 'truetrue' && isset($_POST['board_id']) && $_POST['board_id'] != null) {
	$boardid = $_POST['board_id'];
	$url = $_POST['vid'];
	parse_str(parse_url( $url, PHP_URL_QUERY ), $video_params);
	
	if ($video_params['v'] != null) {
		$video_exists = video::getVideoByUrl($video_params['v'], $boardid);
		
		//if the POST[vid] video exists, assume we want to delete it, otherwise add it.
		if ($video_exists == null) {
			$video = new Video(null, $video_params['v'], $boardid); 
			$video->save();
		} else {
			$video_exists->delete();	
		}
		
	}
	
	header("location: ../");
}

if ($_SESSION['bro'] == 'truetrue' && $_POST['board_name']) {
	$new_board = new Board(null, $_POST['board_name']);
	$new_board->save();
	header("location: ../");
} ?>
<!-- twitter.com/samuelgbrown yo. -->
<html>
<head>
<title>mixxxx.es</title>
<link href='http://fonts.googleapis.com/css?family=Contrail+One' rel='stylesheet' type='text/css'>
<script type="text/javascript" charset="utf-8" src="http://ajax.googleapis.com/ajax/libs/jquery/1.5.1/jquery.js"></script>
</head>
<body>

<div class="left">
			<h1 id="logo"><a href="">mixxxx.es</a></h1>

			<div class="admins">
					<?php if (!isset($_SESSION['bro']) || $_SESSION['bro'] != 'truetrue') { ?>

					<form id="login_form" method="POST" action="">
						<label>user</label>
						<input name="user" value="" type="text"/>
						<label>pass</label>
						<input name="pass" value="" type="password"/>
						<input type="submit" value="log in"/>
					</form>

					<?php } ?>

					<?php if (isset($_SESSION['bro']) && $_SESSION['bro'] == 'truetrue') { ?>
						
						<form id="submit_board" action="" method="POST">
							<label>add new board</label>
							<input type="text" name="board_name" value=""/>
							<input type="submit" value="add"/>
						<form>
						<?php
						
					} ?>

					<footer>
						<span id="login_link">submit</span>
						
						<?php if (isset($_SESSION['bro']) && $_SESSION['bro'] == 'truetrue') { ?>
						<a id="logout_link" href="?q">logout</a>	
						<?php } ?>
						
					</footer>
			</div>
			
			
			<div class="links">
				<h1><a href="#" id="fg_link">r/futuregarage</a></h1>
				<?php
				
				$boards = board::getAllBoards();
				foreach ($boards as $board) {
					echo '<h1><a href="#" class="board_link" data-rel="'.$board->getID().'">'.$board->getName().'</a></h1>';
				}
				
				?>
			</div>
</div>

<div class="right">

<div id="videos"></div>

</div>

<style>

/* dark theme 
html {background: #272727}
.delete_button {background: #F6F6F6;border: 1px solid #CCC;}
h1 {color:white}
span, p, label {color: white}
a {color: white}*/

/* light theme */
html {background: white}
.delete_button {background: #F6F6F6;border: 1px solid #CCC;}
h1 a {color:black}
#logo {background:black}
#logo a {color:white;}
span, p, label {color: black}
a {color: black}

.left {width:250px;float:left;overflow: hidden;position:fixed;}
.right {width:900px;float:left;overflow: hidden;margin-left:250px;}

.left .links {float: left; width:100%}
.left .links a {display: block}
.admins {float: left;}

h1 {font-family: 'Contrail One', cursive;}
#logo {width: 100px;padding: 50px 14px;border-radius: 100px;margin: 100px 0;}
span, p, label {font-size:11px;}
a {text-decoration: none;}
body {width: 1150px; margin: 0 auto; font-family: Arial; font-size: 12px}
#video_area {width: 100%; overflow: hidden}
.vid_container, iframe {width: 400px; float: left; height: 190px; padding: 5px 12px}
#login_form {display: none;}
#login_link, #logout_link {cursor:pointer; padding-right: 10px; font-size: 11px;}
.delete_link {width: 150px; margin: 0 auto; text-align:center}
.delete_button {padding: 5px 10px;border-radius: 3px;cursor: pointer;}
footer a {font-size:11px;}


<?php if (isset($_SESSION['bro']) && $_SESSION['bro'] == 'truetrue') { ?>
	#login_link {display: none}
<?php } else { ?>
	#logout_link {display: none}
<?php } ?>


</style>

<script>
$(document).ready(function() {

	$('#fg_link').click(function(e) {
		e.preventDefault();
		$('#videos').fadeOut();
		$.ajax({
			  url: 'reddit.php',
			  type: "GET",
			  success: function(res){
				    $('.right').html(res);
				   	$('.right').fadeIn();
			  }
		});
	});
	
	$('.board_link').click(function(e) {
		e.preventDefault();
		var boardid = $(this).attr("data-rel");
		$('#videos').fadeOut();
		$.ajax({
			  url: 'picks.php?id='+boardid,
			  type: "GET",
			  success: function(res){
				    $('.right').html(res);
				   	$('.right').fadeIn();
			  }
		});
	});
	
	
	$('#login_link').click(function() {
		$('#login_form').show();
		$('#login_link').hide();
	})
	
	$('.right').load('reddit.php');

})

</script>

</body>
</html>