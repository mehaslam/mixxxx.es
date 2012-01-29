<?php
session_start();
header('Content-type: application/json');
require('../classes/functions.php');

if (isset($_GET['board_name'])) {
	$board_name = $_GET['board_name'];
	$board = getBoardByName($board_name);
}

if (isset($_GET['page'])) {
	$pageno = $_GET['page'];
} else {
	$pageno = 0;
}

if (isset($board) && isset($pageno)) {

	$boardvideos = getBoardVideosAt($board->getID(), $pageno);

} else {
	echo 'board not found:';
	print_r($_GET);
	die();
}

if (isset($boardvideos)) {

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
			
			
			$thumbnails = getVideoThumbnails($videoid);
			
			
			$frontend_content[] = array(
					"id" => $boardvideo->getID(),
					"boardid" => $boardvideo->getBoardID(),
					"uploaderid" => $boardvideo->getUploaderID(),
					"video" => $video,
					"thumbnails" => $thumbnails
			);
		
		}
	}
	
	if (isset($frontend_content)) {
	
		echo json_encode(array(
			"videocount" => countBoardVideos($boardvideo->getBoardID()),
			"videos" => $frontend_content
			));
		
	} else {
		echo json_encode(array("boardid"=>$board->getID()));
	}
	
} else {
	echo json_encode(array("boardid"=>$board->getID()));
}

?>