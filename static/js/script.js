/* Author: Samuel Brown (@samuelgbrown, samuelgbrown.com) */

//(a) load YouTube api (b) prepare YouTube callbacks (c) rest of scripts/doc.ready

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
if (typeof(window.location.hash) === "undefined" || window.location.hash == "") {
	window.location.hash = "clssx";
	var board = "clssx"; //gets overwritten upon changing board using nav.
} else {
	var board = window.location.hash;
}

//YouTube callbacks.

function onYouTubePlayerAPIReady() {
	getPlaylist(board);	
}

function handleError(error) {
	console.log("player error:");
	if (error.data == "150" || error.data == "101") { console.log("video had embedding disabled.") }
	if (error.data == "100") { console.log("video not found (removed?)") }
	if (error.data == "2") { console.log("video request contained invalid parameter (missing/invalid video id?)") }
	skipToVideo(currentIndex+1); //skip song
}

function onPlayerStateChange(input) {
	//Possible values are unstarted (-1), ended (0), playing (1), paused (2), buffering (3), video cued (5).
	//console.log("state:" +input.data);
	
	if (input.data == 0) { //user finished watching a video
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
        	//console.log(response.videos[i]);
          videos.push({
            'thumbnail': response.videos[i]['video']['thumbnail'],
            'videoid': response.videos[i]['video']['videoid'],
          });
        });
		
		//Put playlist into the youtube player.
        initiatePlaylist(videos[0]['videoid']);
        
      } else {
       	console.log("problem getting video thumbs/ids");
      }
      
    },
    error: function(err) {
    	console.log(err);
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
	})
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

//Rest of scripts

$(document).ready(function() {
	
	bindBoardEvents();

	$('#fg_link').click(function(e) {
		e.preventDefault();
		window.location.hash = "futureg";
		fetchFutureGarage();
	});
	
	
	$('.board_link').click(function(e) {
		e.preventDefault();
		
		//board ajax actions
		var boardname = $(this).attr("data-rel");
		window.location.hash = boardname;
		fetchBoard(boardname);
		
		//youtube playlist actions
		videos.length = 0;
		board = boardname;
		getPlaylist(board);
	});
	
	
	$('#login_link').click(function() {
		$('#login_form').show();
		$('#login_link').hide();
	})

	if (window.location.hash != "" && window.location.hash != "#futureg") {
		var board = window.location.hash.replace('#', '');
		fetchBoard(board);
	} else if (window.location.hash == "#futureg") {
		window.location.hash = "futureg";
		fetchFutureGarage();
	} else {
		window.location.hash = "clssx";
		var board = window.location.hash.replace('#', '');
		fetchBoard(board);
	}
	
	function fetchBoard(board) {
		$.ajax({
			url: "picks.php?board_name="+board,
			type: "GET",
			success: function(res){
				$('.right').html(res);
				$('.right_container .board_name').text(board);
				window.board = board;
				bindBoardEvents();
			},
			error: function(err) {
				console.log("ERROR: "+err);
				fetchBoard("clssx");
			}
		});
	}
	
	function fetchBoardAt(board, page) {
	
		$.ajax({
			url: "picks.php",
			data: {board_name: board, page: page},
			type: "GET",
			success: function(res){
				$('.right').html(res);
				$('.right_container .board_name').text(board);
				bindBoardEvents();
			},
			error: function(err) {
				console.log("ERROR: "+err);
				fetchBoard("clssx");
			}
		});
	}
	
	function fetchFutureGarage() {
	
		$('.right_container .board_name').text("futureg");
		
		$.ajax({
			  url: 'reddit.php',
			  type: "GET",
			  success: function(res){
				    $('.right').html(res);
				   	bindBoardEvents();
			  }
		});
	}
	
	
	//Bind any events to elements that may have been created by an ajax call. Call this function after ajax calls.
	function bindBoardEvents() {
	
		$('#pagination a').click(function(e) {
			e.preventDefault();
			var pageno = $(this).attr("data-rel");
			fetchBoardAt(window.board, pageno);
		});	
	}

})



