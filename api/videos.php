<?php
session_start();
header('Content-type: application/json');
require('../classes/functions.php');

if (isset($_GET['board_name'])) {
	$board_name = $_GET['board_name'];
	$board = getBoardByName($board_name);
}

if (isset($board)) {

	$boardvideos = getBoardVideos($board->getID());

} else {
	echo 'board not found:';
	print_r($_GET);
	die();
}

foreach ($boardvideos as $boardvideo) {
	
	$videoid = $boardvideo->getVideoID();
	
	$videoobj = getVideoById($videoid);
	if (isset($videoobj)) {
		$video = array(
			"id" => $videoobj->getID(),
			"title" => $videoobj->getTitle(),
			"url" => $videoobj->getUrl(),
			"description" => $videoobj->getDescription()
		);
	}
	
	$thumbnails = getVideoThumbnails($videoid);
	
	
	$frontend_content[] = array(
		"id" => $boardvideo->getID(),
		"boardid" => $boardvideo->getBoardID(),
		"uploaderid" => $boardvideo->getUploaderID(),
		"video" => $video,
		"thumbnails" => $thumbnails
	);
}

//print_r($frontend_content);
echo json_encode($frontend_content);

?>