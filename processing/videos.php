<?php
if(!isset($_SESSION)) { 
	session_start(); 
}

if( !defined( __DIR__ ) )define( __DIR__, dirname(__FILE__) );
require_once(__DIR__.'../../classes/functions.php');


//PROCESS CONTENT ADD/DELETES
if (isset($_POST['vid']) && $_POST['vid'] != null && isset($_SESSION['bro']) && $_SESSION['bro'] == 'truetrue' && isset($_POST['board_id']) && $_POST['board_id'] != null) {

	$boardid = $_POST['board_id'];
	$url = $_POST['vid'];

	parse_str(parse_url( $url, PHP_URL_QUERY ), $video_params);
	
	if (isset($video_params['v']) && $video_params['v'] != null) {

		$video_exists = getVideoByUrl($video_params['v']);

		//if the POST[vid] video exists, assume we want to delete it, otherwise add it.
		if ($video_exists == null) {

			//get info from youtube api.
			$video_data = getVideoData($video_params['v']);

			//stop adding the video if we couldn't fetch data from youtube (usually happens when you add a deleted video).
			if (!isset($video_data) || !$video_data || $video_data === null || $video_data === "404") {
				return;
			}

			//declare and save the video first
			$video = new Video(null, $video_data->entry->title->{'$t'}, $video_params['v'], $video_data->entry->content->{'$t'});
			$video->save();

			//store the id for quick use
			$videoid = $video->getID();

			//put the video into the board it was submitted on
			$boardvideo = new BoardVideo(null, $videoid, $boardid, $_SESSION['userid']);
			$boardvideo->save();

			//save all the thumbnails retrieved from youtube api
			foreach ($video_data->entry->{'media$group'}->{'media$thumbnail'} as $thumb) {
				$thumbnail = new VideoThumb(null, $videoid, $thumb->url);
				$thumbnail->save();
			}

		}
		
	} else {
		header("location: ../?badurl");
	}
	
	header("location: ../");
}

function getVideoData($url) {
  
 	//$json = file_get_contents("http://gdata.youtube.com/feeds/api/videos/".$url."?alt=json");
  	$url = "http://gdata.youtube.com/feeds/api/videos/".$url."?alt=json";
		
	// cURL $url
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

	$json = curl_exec($ch);
	curl_close($ch);

  if ($json) {
  	return json_decode($json);
  } else {
  	return "404";
  }

}

?>