<?php
session_start();
//error_reporting(0);
header('Content-type: application/json');
require('../classes/functions.php');

if (isset($_GET['board_name'])) {
	$board_name = $_GET['board_name'];
	$board = getBoardByName($board_name);
}

if (isset($board)) {

	$videos = getBoardVideos($board->getID());

} else {
	echo 'board not found:';
	print_r($_GET);
	die();
}

foreach ($videos as $video) {
	$obj_to_arr[] = array($video->getID(),$video->getBoardID(),$video->getVideoID(),$video->getUploaderID());
}
echo json_encode($obj_to_arr);

?>