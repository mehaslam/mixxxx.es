/* Author: Samuel Brown (@samuelgbrown, samuelgbrown.com) */

//Load youtube player api asynchronously.
var tag = document.createElement('script');
tag.src = "http://www.youtube.com/player_api";
var firstScriptTag = document.getElementsByTagName('script')[0];
firstScriptTag.parentNode.insertBefore(tag,firstScriptTag);

//required global vars for youtube.
var youtube_player;
var videos = [];
var currentIndex = 0;
var started = 0;
if (typeof(window.location.hash) === "undefined" || window.location.hash === "") {
	window.location.hash = "clssx";
	var board = "clssx"; //gets overwritten upon changing board using nav.
} else {
	var board = window.location.hash;
}

//YouTube callbacks.

function onYouTubePlayerAPIReady() {
	console.log("youtube player api ready homeslice.");
	//getPlaylist(board);
}

function handleError(error) {
	console.log("player error:");
	if (error.data === "150" || error.data === "101") { console.log("video had embedding disabled."); }
	if (error.data === "100") { console.log("video not found (removed?)"); }
	if (error.data === "2") { console.log("video request contained invalid parameter (missing/invalid video id?)"); }
	skipToVideo(currentIndex+1); //skip song
}

function onPlayerStateChange(input) {
	//Possible values are unstarted (-1), ended (0), playing (1), paused (2), buffering (3), video cued (5).
	//console.log("state:" +input.data);
	
	if (input.data === 0) { //user finished watching a video
		started = 1;
		currentIndex++;
		queueVideo();
	}
}

function getPlaylist(board) {
  
  $.ajax({
    dataType: 'json',
    url: 'feed.php?id='+board,
    success: function(response) { //videos[] should be title, thumbnail, videoid.
      if (response.videos && response.videos.length > 0) {
      
        $.each(response.videos, function(i) {
          videos.push({
            'thumbnail': response.videos[i]['video']['thumbnail'],
            'videoid': response.videos[i]['video']['videoid']
          });
        });
		
		//Put playlist into the youtube player.
        initiatePlaylist(videos[0]['videoid']);
        
      } else {
		console.log("problem getting video thumbs/ids");
      }
      
    },
    error: function(err) {
		console.log("feed.php fetch failed:");
		console.log(err.responseText);
    }
  });
}

function queueVideo() {
	
	//Update thumbnails.
	showThumbnails();
	
	if (youtube_player.cueVideoById) {
		youtube_player.cueVideoById(videos[currentIndex]['videoid']);
		if (started == 1) { //they already watched 1 video, so started the playlist
			youtube_player.playVideo();
		}
	} else {
		console.log("cueVideoById wasn't ready!");
	}
	
}

function initiatePlaylist(firstVideo) {

	//clear any existing player
	youtube_player = null;
	$('#playlist_player').empty();
	
	//create player
	youtube_player = new YT.Player('playlist_player', {
		height: '90', width: '200',
		videoId: firstVideo,
		playerVars: {
			'enablejsapi': 1,
			'autoplay': 0,
			'html5': 1,
			'origin': window.location.host
		},
		events: {
			'onStateChange':
					onPlayerStateChange,
			'onError':
					handleError,
			'onReady':
					onReady
		}
	});
	
}

function onReady() {
	//since onYouTubePlayerReady isn't firing, using the onReady event seems to work instead.
	queueVideo();
}

function showThumbnails() {
	//display all of videos above currentIndex.

	$('.inside .thumbnails .strip').empty();
	
	$.each(videos, function(i) {
		if (i > currentIndex) {
			$('.inside .thumbnails .strip').append('<a href="javascript:skipToVideo('+i+');"><img src="'+videos[i]['thumbnail']+'" alt="Upcoming video."/></a>');
		}
	});
}

function skipToVideo(i) {

	if (videos[i]) {
		currentIndex = i;
		youtube_player.cueVideoById(videos[i]['videoid']);
		youtube_player.playVideo();
		showThumbnails();
	} else {
		console.log("video not found!");
	}

}