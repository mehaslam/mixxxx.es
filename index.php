<?php
session_start();

	require_once('classes/functions.php');

	include_once('processing/logins.php');
	include_once('processing/videos.php');
	include_once('processing/boards.php');


?>

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
  	
  	<!--<link rel="stylesheet" href="static/css/stylesheets/bootstrap.css" type="text/css" />
	<link rel="stylesheet" href="http://meyerweb.com/eric/tools/css/reset/reset.css" type="text/css" />-->
  	<link rel="stylesheet" href="static/css/stylesheets/screen.css" />
	
	<script src="static/js/libs/modernizr-2.0.6.min.js"></script>
	<link href='http://fonts.googleapis.com/css?family=Contrail+One' rel='stylesheet' type='text/css'>
	<script type="text/javascript" charset="utf-8" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.js"></script>
	
</head>
<body>

<div class="left">
			<h1 id="logo"><a href="">mixxxx.es</a></h1>
			
			<div class="links">
				<!--<h1><a href="#" id="fg_link">r/futuregarage</a></h1>-->
				<?php
				
				$boards = getAllBoards();
				foreach ($boards as $board) {
					echo '<h1><a href="#'.$board->getName().'" class="board_link" data-rel="'.$board->getName().'">'.$board->getName().'</a><span class="remove"><img src="static/images/bin.png" alt="remove board"/></span><span class="edit"><img src="static/images/edit.png" alt="edit board"/></span></h1>';
				}
				
				?>
			</div>
			
			<div class="admins" <?php if (isset($_SESSION['bro']) && $_SESSION['bro'] == 'truetrue') { echo "id='authed'";} ?>>
			
					<?php if (!isset($_SESSION['bro']) || $_SESSION['bro'] != 'truetrue') { ?>

					<form id="login_form" method="POST" action="processing/logins.php">
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
						</form>
						<?php
						
					} ?>

					<footer>
						<span id="login_link">submit</span>
						
						<?php if (isset($_SESSION['bro']) && $_SESSION['bro'] == 'truetrue') { ?>
						<a id="logout_link" href="?q">logout</a>	
						<?php } ?>
						
					</footer>
			</div>
</div>

<div class="right_container">

	<h1 class="board_name"></h1>
	
	<div class="right_mask"></div>
	
	<div class="right">

			<?php if (isset($_SESSION['bro']) && $_SESSION['bro'] == 'truetrue') { ?>
			
			<?php if (isset($_GET['badurl'])) { echo '<span class="error">Invalid URL.</span>'; } ?>

			<form id="submit_form" action="#" method="POST">
				<label>youtube url</label>
				<input type="text" name="vid" value="" id="vid_field"/>
				<input type="hidden" name="board_id" value="" id="current_board_id"/>
				<input type="submit" value="add"/>
			</form>			

			<?php } ?>
			
			<script id="video-template" type="text/x-handlebars-template">
					<div class="video" data-url="{{url}}" data-title="{{title}}" data-smallthumb="{{smallthumb}}">
						<h3>{{title}}</h3>
						<div class="playbtn"></div>
						<img src="{{thumbnail}}" alt="{{title}}"/>

						<?php if (isset($_SESSION['bro']) && $_SESSION['bro'] == "truetrue") { ?>
							<div class="closebtn">DELETE</div>
						<?php } ?>
					</div>
			</script>
			<div class="videos_area"></div>
	</div>
	
</div>

<div class="playlist">
	<div class="inside">
		
		<div class="player_loading"><img src="static/images/loading.gif" alt="loading"/></div>
		<div id="playlist_player"></div>
		<div class="thumbnails">
			<script id="thumbnail-template" type="text/x-handlebars-template">
					<div class="smallthumb" data-url="{{url}}">
							<h4>{{title}}</h4>
							<div class="playbtn"></div>
							<img src="{{thumbnail}}" alt="{{title}}"/>
					</div>
			</script>
			<div class="strip"></div>
		</div>
	</div>
</div>

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.0/jquery.min.js"></script>
<script>window.jQuery || document.write('<script src="static/js/libs/jquery-1.6.2.min.js"><\/script>')</script>

<!-- scripts concatenated and minified via ant build script-->
<script src="static/js/youtube.js"></script>
<script src="static/js/plugins.js"></script>
<script src="static/js/libs/handlebars.js"></script>

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