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
	<meta charset="utf-8">
  	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  	<meta name="description" content="">
  	<meta name="author" content="">
  	<meta name="viewport" content="width=device-width,initial-scale=1">
  	
  	<link rel="stylesheet" href="assets/css/style.css" />
	
	<script src="assets/js/libs/modernizr-2.0.6.min.js"></script>
	<link href='http://fonts.googleapis.com/css?family=Contrail+One' rel='stylesheet' type='text/css'>
	<script type="text/javascript" charset="utf-8" src="http://ajax.googleapis.com/ajax/libs/jquery/1.5.1/jquery.js"></script>
	
</head>
<body>

<div class="left">
			<h1 id="logo"><a href="">mixxxx.es</a></h1>
	
			<div class="admins" <?php if (isset($_SESSION['bro']) && $_SESSION['bro'] == 'truetrue') { echo "id='authed'";} ?>>
			
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
					echo '<h1><a href="#'.$board->getName().'" class="board_link" data-rel="'.$board->getName().'">'.$board->getName().'</a></h1>';
				}
				
				?>
			</div>
</div>

<div class="right">

<div id="videos"></div>

</div>

<script>
$(document).ready(function() {

	bindBoardEvents();

	$('#fg_link').click(function(e) {
		e.preventDefault();
		window.location.hash = "futureg";
		fetchFutureGarage();
	});
	
	
	$('.board_link').click(function(e) {
		e.preventDefault();
		var boardname = $(this).attr("data-rel");
		window.location.hash = boardname;
		fetchBoard(boardname);
	});
	
	
	$('#login_link').click(function() {
		$('#login_form').show();
		$('#login_link').hide();
	})

	if (window.location.hash != "" && window.location.hash != "#futureg") {
		var board = window.location.hash.replace('#', '');
		fetchBoard(board);
	} else {
		window.location.hash = "futureg";
		fetchFutureGarage();
	}
	
	function fetchBoard(board) {
		$.ajax({
			url: "picks.php?board_name="+board,
			type: "GET",
			success: function(res){
				$('.right').html(res);
				window.board = board;
				bindBoardEvents();
			},
			error: function(err) {
				console.log(err);
			}
		});
	}
	
	function fetchBoardAt(board, page) {
		
		console.log("fetching "+board+", page "+page);
	
		$.ajax({
			url: "picks.php",
			data: {board_name: board, page: page},
			type: "GET",
			success: function(res){
				$('.right').html(res);
				bindBoardEvents();
			},
			error: function(err) {
				console.log(err);
			}
		});
	}
	
	function fetchFutureGarage() {
		$('#videos').fadeOut();
		$.ajax({
			  url: 'reddit.php',
			  type: "GET",
			  success: function(res){
				    $('.right').html(res);
				   	$('.right').fadeIn();
				   	bindBoardEvents();
			  }
		});
	}
	
	
	//Bind any events to elements that may have been created by an ajax call. Call this function after ajax calls.
	function bindBoardEvents() {
		$('#pagination a').click(function(e) {
			e.preventDefault();
			var pageno = $(this).attr("data-rel");
			fetchBoardAt(window.board, pageno);
		});	
	}
})

</script>

</body>
</html>