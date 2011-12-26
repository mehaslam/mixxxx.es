/* Author: Samuel Brown (@samuelgbrown, samuelgbrown.com) */

//(a) load YouTube api (b) prepare YouTube callbacks (c) rest of scripts/doc.ready

//Load youtube player api asynchronously.
var tag = document.createElement('script');
tag.src = "http://www.youtube.com/player_api";
var firstScriptTag = document.getElementsByTagName('script')[0];
firstScriptTag.parentNode.insertBefore(tag,firstScriptTag);
var player;
var videos = [];
var currentIndex = 0;
var started = 0;

//YouTube callbacks.

function onYouTubePlayerAPIReady() {

	player = new YT.Player('playlist_player', {
		height: '70', width: '200',
		playerVars: {
			'autoplay': 1,
			'html5': 1
		},
		events: {
			'onStateChange': 
					onPlayerStateChange,
			'onError':
					handleError
		}
	});
}

function onYouTubePlayerReady() {
	console.log("YEEAAAHH!!");
	getPlaylist();
}

function handleError(error) {
	console.log("player error:");
	console.log(error);
}

function onPlayerStateChange(input) {
	//Possible values are unstarted (-1), ended (0), playing (1), paused (2), buffering (3), video cued (5).
	console.log("state:" +input.data);
	
	if (input.data == 0) {
		//console.log("video ended! load next url from videos[] into player!");
		getPlaylist();
		started = 1;
	}
}

function getPlaylist() {
  
  $.ajax({
    dataType: 'json',
    url: 'feed.php?id=clssx',
    success: function(response) { //videos[] should be title, thumbnail, videoid.
      if (response.videos && response.videos.length > 0) {
      
        $.each(response.videos, function(i) {
        	//console.log(response.videos[i]);
          videos.push({
            'thumbnail': response.videos[i]['video']['thumbnail'],
            'videoid': response.videos[i]['video']['videoid'],
          });
        });
		        
        queueVideo();
        
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
	//player.cueVideoById(videoId:String, startSeconds:Number, suggestedQuality:String):Void
	//e.g. player.cueVideoById(videos[0]['videoid'],0,large);

	player.cueVideoById(videos[currentIndex]['videoid']);
	if (started == 1) { //they already watched 1 video, so started the playlist
		player.playVideo();
	}
	currentIndex++;
	
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
		var boardname = $(this).attr("data-rel");
		window.location.hash = boardname;
		fetchBoard(boardname);
	});
	
	
	$('#login_link').click(function() {
		$('#login_form').show();
		$('#login_link').hide();
	})

	if (window.location.hash != "" && window.location.hash != "#futureg") {
		var board = window.location.hash.replace('#', '');
		fetchBoard(board);
	} else {
		window.location.hash = "futureg";
		fetchFutureGarage();
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



