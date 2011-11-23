<?php
session_start();
error_reporting(E_ERROR | E_PARSE);
require('classes/video.php');
require('classes/boards.php');
?>

<div id="video_area">
<?php

$boardid = $_GET['id'];

$videos = video::getBoardVideos($boardid);
$board = board::getBoardByID($boardid);
?> <h1><?php echo $board->getName(); ?></h1> <?php

if (isset($_SESSION['bro']) && $_SESSION['bro'] == 'truetrue') { ?>

	<form id="submit_form" action="" method="POST">
		<label>youtube url</label>
		<input type="text" name="vid" value=""/>
		<input type="hidden" name="board_id" value="<?php echo $boardid; ?>"/>
		<input type="submit" value="add"/>
	</form>

<?php } 
						
						
foreach ($videos as $video) {
	?>


	<div class="vid_container">
	
		<iframe	width="460" height="250" src="http://www.youtube.com/embed/<?php echo $video->getUrl(); ?>?html5=1" frameborder="0" allowfullscreen></iframe>
		
		<?php if (isset($_SESSION['bro']) && $_SESSION['bro'] == 'truetrue') { ?>
		
		<form action="" method="POST" class="delete_link">
			<input type="hidden" name="vid" value="http://www.youtube.com/watch?v=<?php echo $video->getUrl(); ?>"/>
			<input class="delete_button" type="submit" value="delete"/>
		</form>
	
	<?php } ?>
	
	</div>

<?php } ?>

</div>

