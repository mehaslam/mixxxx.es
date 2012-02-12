<?php
error_reporting(E_ERROR | E_PARSE);
session_start();

	require_once('classes/db.php');
	require('classes/video.php');
	require('classes/boards.php');


//PROCESS LOGINS
if (isset($_POST['user']) && isset($_POST['pass'])) {
	if ($_POST['user'] != null && $_POST['pass'] != null) {

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
			$video = new Video(null, $video_params['v'], null, $boardid);
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
<!DOCTYPE html> 
<!-- twitter.com/samuelgbrown yo. -->
<html>
<head>
	<title>mixxxx.es</title>
	<meta charset="utf-8">
  	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
  	<meta name="description" content="">
  	<meta name="author" content="">
  	<meta name="viewport" content="width=device-width,initial-scale=1">
  	
  	<link rel="stylesheet" href="static/css/stylesheets/bootstrap.css" />
	
	<script src="static/js/libs/modernizr-2.0.6.min.js"></script>
	<link href='http://fonts.googleapis.com/css?family=Contrail+One' rel='stylesheet' type='text/css'>
	<script type="text/javascript" charset="utf-8" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.js"></script>
	
</head>
<body>

<section class="menu">

	<a class="logo medium">mixxxx.es</a>
	
	<ul class="feature-list">
	
		<li><a href="">r/futuregarage</a></li>
		<li><a href="">picks</a></li>
		<li><a href="">classx</a></li>
		<li><a href="">dubstep</a></li>
		<li><a href="">boom</a></li>
		<li><a href="">garage</a></li>
		<li><a href="">dnb</a></li>
		
	</ul>

</section>

<h2>pagetitle</h1>



<script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.0/jquery.min.js"></script>
<script>window.jQuery || document.write('<script src="static/js/libs/jquery-1.6.2.min.js"><\/script>')</script>

<!-- scripts concatenated and minified via ant build script-->
<script src="static/js/plugins.js"></script>
<script src="static/js/script.js"></script>
<!-- end scripts-->

<script type="text/javascript">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-17754914-4']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>

</body>
</html>