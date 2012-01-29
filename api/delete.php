<?php
if (!isset($_SESSION)) {
session_start();
}

//requires user to be logged in to delete a video.
if (isset($_SESSION['bro']) && $_SESSION['bro'] === "truetrue") {

	header('Content-type: application/json');
	require('../classes/functions.php');
	
	//comes in the youtube id format (e.g. PvwCtMFcHqI)
	if (isset($_GET['url'])) {
		$videourl = $_GET['url'];
		$video = getVideoByUrl($videourl);
	}
	
	if (isset($video)) {
	
		$video->delete();
	
	}

}

?>