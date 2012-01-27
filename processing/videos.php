<?php
if(!isset($_SESSION)) { 
	session_start(); 
}

print_r($_POST);

require_once(__DIR__.'../../classes/functions.php');

//PROCESS CONTENT ADD/DELETES
if (isset($_POST['vid']) && $_POST['vid'] != null && isset($_SESSION['bro']) && $_SESSION['bro'] == 'truetrue' && isset($_POST['board_id']) && $_POST['board_id'] != null) {

	$boardid = $_POST['board_id'];
	$url = $_POST['vid'];

	parse_str(parse_url( $url, PHP_URL_QUERY ), $video_params);
	
	if ($video_params['v'] != null) {

		$video_exists = getVideoByUrl($video_params['v']);

		//if the POST[vid] video exists, assume we want to delete it, otherwise add it.
		if ($video_exists == null) {

			
			$video_data = getVideoData($video_params['v']);
			//echo '<pre>';
			//print_r($video_data);

			$video = new Video(null, $video_data->entry->title->{'$t'}, $video_params['v'], $video_data->entry->content->{'$t'});
			$video->save();

			$videoid = $video->getID();

			$boardvideo = new BoardVideo(null, $videoid, $boardid, $_SESSION['userid']);
			$boardvideo->save();

			foreach ($video_data->entry->{'media$group'}->{'media$thumbnail'} as $thumb) {
				$thumbnail = new VideoThumb(null, $videoid, $thumb->url);
				$thumbnail->save();
			}

		} else {
			
			//$video_exists->delete();
		}
		
	}
	
	//header("location: ../");
}

function getVideoData($url) {
  
  $json = file_get_contents("http://gdata.youtube.com/feeds/api/videos/".$url."?alt=json");

  return json_decode($json);

}

?>