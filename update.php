<?php
session_start();
require('classes/video.php');
require('classes/boards.php');

$board_name = $_GET['id'];
$board = board::getBoardByName($board_name);
$board_id = $board->getID();

$videos = video::getBoardVideos($board_id);

foreach ($videos as $video) {
	$video_json = json_decode(file_get_contents('https://gdata.youtube.com/feeds/api/videos/'.$video->getUrl().'?v=2&alt=jsonc'));
	$thumbnail_url =  $video_json->data->thumbnail->sqDefault;
	$video->setThumbnail($thumbnail_url);
	$video->save();
}

?>