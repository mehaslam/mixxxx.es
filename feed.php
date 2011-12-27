<?php
header('Content-type: application/json');

session_start();
require('classes/video.php');
require('classes/boards.php');

$board_name = $_GET['id'];
$board = board::getBoardByName($board_name);

if ($board) {

	$board_id = $board->getID();
	
	$videos = video::getBoardVideos($board_id);
	
	foreach ($videos as $video) {
		$objects[] = array('video' => array('thumbnail' => 'http://i.ytimg.com/vi/'.$video->getUrl().'/default.jpg', 'videoid' => $video->getUrl()));
	}
	
	$json = array('videos' => $objects);
	
	
	echo json_encode($json);

}
?>