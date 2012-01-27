<?php
session_start();
error_reporting(0);
header('Content-type: application/json');
require('../classes/video.php');
require('../classes/boards.php');

$board_name = $_GET['board_name'];
$board = board::getBoardByName($board_name);

if (isset($board)) {

	$videos = video::getBoardVideosJson($board->getID());

} else {
	echo 'board not found:';
	print_r($_GET);
	die();
}

echo json_encode($videos);

?>