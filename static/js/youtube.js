/* Author: Samuel Brown (@samuelgbrown, samuelgbrown.com) */



//globals used for storing current playlist and also checking api/player status.
var apistatus = 0;
var ytplayerstatus = 0;

var playlist = [];
var currentIndex = 0;
var started = 0;
var playerstatus;
var autoPlay;

//required global vars for youtube.
var youtube_player;
if (typeof(window.location.hash) === "undefined" || window.location.hash === "") {
	window.location.hash = "clssx";
	var board = "clssx"; //gets overwritten upon changing board using nav.
} else {
	var board = window.location.hash;
}

//YouTube callbacks, player/queue functions.

function onYouTubePlayerAPIReady() {
	apistatus = 1;
}

function onYouTubePlayerPlayerReady() {
	console.log("player ready");
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
	
	playerstatus = input.data;
	
	if (input.data === 0) { //user finished watching a video
		started = 1;
		playNextVideo();
	}
}

function initiatePlaylist(firstVideo) {
	
	if (apistatus === 0) {
		return;
	}

	//slide up the player bar.
	$('.playlist').animate({bottom: "0px"}, 240, function() {
	
		//clear any existing player
		youtube_player = null;
		$('#playlist_player').empty();
		
		//create player
		youtube_player = new YT.Player('playlist_player', {
			height: '95', width: '200',
			videoId: firstVideo,
			playerVars: {
				'enablejsapi': 1,
				'autoplay': 1,
				'html5': 1,
				'hd': 1,
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
		
		//clear loading animation
		setTimeout(function() {
			$('.player_loading').fadeOut();
			autoPlay = 1;
			$('.right').css("margin-bottom","140px");
			ytplayerstatus = 1;
			if (typeof(playerstatus) === "undefined") {
				playerstatus = 5;
			}
		}, 500);
		
	});
	
}

function onReady() {

	if (apistatus === 0) {
		return;
	}

	//since onYouTubePlayerReady isn't firing, using the onReady event seems to work instead.
	if (typeof(autoPlay) !== "undefined" && autoPlay === 1) {
		youtube_player.playVideo();
	}
}

function goBackOne() {
	currentIndex = currentIndex-1;
	playTune(playlist[currentIndex].url);
	refreshThumbnailQueue();
}

function skipToVideo(i) {

	if (apistatus === 0 || ytplayerstatus === 0) {
		return;
	}

	//skip to song, but preserve all other songs.
	
	if (i > 0) {
	
		//perform skip *needs rewriting into playlist[]+currentIndex.
		
		if (playlist[i]) {

		currentIndex = i;
		playTune(playlist[i].url);
		refreshThumbnailQueue();

		}
		
	} else {
	
		//user only skipped to the next song
		playNextVideo();
	}
}

function playNextVideo() {

	if (apistatus === 0 || ytplayerstatus === 0) {
		return;
	}
	
	if (playlist.length > 0) {
		
		currentIndex = currentIndex + 1;
		playTune(playlist[currentIndex-1].url);
		refreshThumbnailQueue();
		
	}
}

function addThumbnailToQueue(target) {
	//Takes a video from the playlist array (target), appends it to the queue using handlebars.
	
	//target element to render video in
	var strip = $('.playlist .thumbnails .strip');
	
	//pick up values to insert into template
	var title = target.title;
	var url = target.url;
	var thumbnail = target.smallthumb;

	//pick up template, compile
	var source   = $("#thumbnail-template").html();
	var template = Handlebars.compile(source);
	
	//insert values into template, create DOM element.
	var content = {title: title, thumbnail: thumbnail, url: url};
	var html = template(content);
	
	//Append filled-out template into strip, handlebars done.
	strip.append(html);
	
	
	//extend strip length
	strip.width(140*playlist.length);
	
	//pick up last video in strip
	var childCount = $('.playlist .thumbnails .strip .smallthumb').length - 1;
	var lastChild = $('.playlist .thumbnails .strip .smallthumb:eq('+childCount+')')
	
	//bind click event to last video in strip
	lastChild.click(function(e) {
		var index = $(this).index() + 1;
		skipToVideo(index);
	});
}

function refreshThumbnailQueue() {
	//Scrolls the queue area to the current video (& sets width of it).
	//Also binds click events to newly-rendered queue thumbnails.
	
	var strip = $('.playlist .thumbnails .strip');
	
	$('.thumbnails').animate({
		scrollLeft: 140*currentIndex
	}, "medium");
		
}

function playTune(id) {
	//asks the YT player object to cue&play a tune by the youtube video id.
	//also sets the appropriate tune in the queue to 'now playing'.
	youtube_player.loadVideoById(id);
	
	var strip = $('.strip');
	var lastVideo = strip.children('.nowplaying');
	var nextVideo = strip.children('.smallthumb:eq('+(currentIndex-1)+')');
	
	//remove the nowplaying class from the last video.
	if (lastVideo) {
		lastVideo.removeClass('nowplaying');
	}
	
	//add the nowplaing class to the new video.
	if (nextVideo) {
		nextVideo.addClass('nowplaying');
	}
}

//Load youtube player api asynchronously.
var tag = document.createElement('script');
tag.src = "http://www.youtube.com/player_api";
var firstScriptTag = document.getElementsByTagName('script')[0];
firstScriptTag.parentNode.insertBefore(tag,firstScriptTag);