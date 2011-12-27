<?php
session_start();
error_reporting(E_ERROR | E_PARSE);
require('classes/video.php');
require('classes/boards.php');
?>

<div id="video_area">
<?php

$board_name = $_GET['board_name'];
$pageno = $_GET['page'];
$board = board::getBoardByName($board_name);

if (!isset($pageno) || $pageno == "") {
	$pageno = 1;
}

if (isset($board)) {

$videos = video::getBoardVideosAt($board->getID(), $pageno);
$videocount = video::countBoardVideos($board->getID());
$pagecount = ceil($videocount/8);

} else {
	echo 'board not found:';
	print_r($_GET);
	die();
}

if (isset($_SESSION['bro']) && $_SESSION['bro'] == 'truetrue') { ?>

	<form id="submit_form" action="" method="POST">
		<label>youtube url</label>
		<input type="text" name="vid" value=""/>
		<input type="hidden" name="board_id" value="<?php echo $board->getID(); ?>"/>
		<input type="submit" value="add"/>
	</form>

<?php } ?>
	
	<div id="all_videos">	
						
		<?php foreach ($videos as $video) {
			?>
		
		
			<div class="vid_container">
			
				<iframe	width="400" height="150" src="http://www.youtube.com/embed/<?php echo $video->getUrl(); ?>?html5=1&enablejsapi=1" frameborder="0" allowfullscreen></iframe>
				
				<script>
					
				</script>
				
				<?php if (isset($_SESSION['bro']) && $_SESSION['bro'] == 'truetrue') { ?>
				
				<form action="" method="POST" class="delete_link">
					<input type="hidden" name="board_id" value="<?php echo $board->getID(); ?>"/>
					<input type="hidden" name="vid" value="http://www.youtube.com/watch?v=<?php echo $video->getUrl(); ?>"/>
					<input class="delete_button" type="submit" value="delete"/>
				</form>
			
			<?php } ?>
			
			</div>
		
		<?php } ?>

	</div>	
	

	<div id="pagination">
		<?php
			$i = 1;
			while ($i <= $pagecount) {
			
				if ($i == $pageno) {
					echo '<span class="current">'.$i.'</span>';
				} else {
					echo '<a href="#" data-rel="'.$i.'">'.$i.'</a>';
				}
				
				$i++;
				
			}
		?>
	</div>

</div>

