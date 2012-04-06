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
  	
  	<link rel="stylesheet" href="css/screen.css" />
	
	<script src="js/libs/modernizr-2.5.2.min.js"></script>
	<link href='http://fonts.googleapis.com/css?family=Contrail+One' rel='stylesheet' type='text/css'>
	
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
 	<script>window.jQuery || document.write('<script src="js/libs/jquery-1.7.1.min.js"><\/script>')</script>
	
</head>
<body>

<div class="left">
			<h1 id="logo"><a href="">mixxxx.es</a></h1>
			
			<ul class="links">
			
				<!--<h1><a href="#" id="fg_link">r/futuregarage</a></h1>-->
				
				<?php
				
				$boards = getAllBoards();

				foreach ($boards as $board) { ?>

					<li>
						<a href="#<?php echo $board->getName(); ?>" class="board_link" data-rel="<?php echo $board->getName() ?>"><?php echo $board->getName() ?></a>

						<span class="actions">
							<span class="edit" data-board="<?php echo $board->getName(); ?>">
								<img src="images/edit.png" alt="edit board"/>
							</span>

							<span class="remove" data-board="<?php echo $board->getName(); ?>">
								<img src="images/bin.png" alt="remove board"/>
							</span>

							<form class="save" data-board="<?php echo $board->getName(); ?>">
								<input type="text" class="new_name" value=""/>
								<span class="cancel">CANCEL</span>
								<input type="submit" value="SAVE"/>
							</form>
						</span>
					</li>
				
				<?php }
				
				?>
			</ul>
			
			<div class="admins">

				<script id="login-form-template" type="text/x-handlebars-template">
					<form id="login_form" method="POST" action="">
						<label>user</label>
						<input name="user" value="" type="text"/>
						<label>pass</label>
						<input name="pass" value="" type="password"/>
						<input type="submit" value="log in"/>
					</form>
				</script>

				<script id="authed-template" type="text/x-handlebars-template">
					<form id="submit_board" action="" method="POST">
						<label>add new board</label>
						<input type="text" name="board_name" class="board_name" value=""/>
						<input type="submit" value="add"/>
					</form>
				</script>

				<script id="login-state-template" type="text/x-handlebars-template">
					<a class="{{state}}_link" href="#">{{message}}</a>
				</script>

			
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
						<input type="text" name="board_name" class="board_name" value=""/>
						<input type="submit" value="add"/>
					</form>
					<?php
					
				} ?>

				<footer class="login-state-links">
					
					<?php if (isset($_SESSION['bro']) && $_SESSION['bro'] == 'truetrue') { ?>
						<a class="logout_link" href="#">logout</a>
					<?php } else { ?>
						<a class="login_link" href="#">submit</a>
					<?php } ?>
					
				</footer>
			</div>
</div>

<div class="right_container">

	<h1 class="board_name"></h1>
	
	<div class="right_mask"></div>
	
	<div class="right">

			<script id="add-video-template" type="text/x-handlebars-template">
				<form id="submit_form" action="#" method="POST" style="display:none">
					<label>youtube url</label>
					<input type="text" name="vid" value="" id="vid_field"/>
					<input type="hidden" name="board_id" value="" id="current_board_id"/>
					<input type="submit" value="add"/>
				</form>
			</script>

			<?php if (isset($_SESSION['bro']) && $_SESSION['bro'] == 'truetrue') { ?>

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
		
		<div class="player_loading"><img src="images/loading.gif" alt="loading"/></div>
		<div id="playlist_player"></div>
		<div class="thumbnails">
			<script id="thumbnail-template" type="text/x-handlebars-template">
				<div class="smallthumb" data-url="{{url}}">
						<div class="now_playing_overlay"><span>NOW PLAYING</span></div>
						<h4>{{title}}</h4>
						<div class="playbtn"></div>
						<img src="{{thumbnail}}" alt="{{title}}"/>
				</div>
			</script>
			<div class="strip"></div>
		</div>
	</div>
</div>

<script id="board-template" type="text/x-handlebars-template">
	<li>
		<a href="#{{name}}" class="board_link" data-rel="{{name}}">{{name}}</a>

		<span class="actions">
			<span class="edit" data-board="{{name}}">
				<img src="images/edit.png" alt="edit board"/>
			</span>

			<span class="remove" data-board="{{name}}">
				<img src="images/bin.png" alt="remove board"/>
			</span>

			<form class="save" data-board="{{name}}">
				<input type="text" class="new_name" value=""/>
				<span class="cancel">CANCEL</span>
				<input type="submit" value="SAVE"/>
			</form>
			
		</span>
	</li>
</script>

<script src="js/youtube.js"></script>
<script src="js/plugins.js"></script>
<script src="js/libs/handlebars.js"></script>
<script src="js/script.js"></script>

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