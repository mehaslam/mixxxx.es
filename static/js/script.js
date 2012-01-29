/* Author: Samuel Brown (@samuelgbrown, samuelgbrown.com) */

$(document).ready(function() {
	bindBoardEvents();

	var board;
	var videosArr = [];

	if (window.location.hash !== "" && window.location.hash != "#futureg") {
		board = window.location.hash.replace('#', '');
		fetchBoard(board);
	} else if (window.location.hash == "#futureg") {
		window.location.hash = "futureg";
		fetchFutureGarage();
	} else {
		window.location.hash = "clssx";
		board = window.location.hash.replace('#', '');
		fetchBoard(board);
	}

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
		board = boardname;
	});
	
	
	$('#login_link').click(function() {
		$('#login_form').show();
		$('#login_link').hide();
	});
	
	function fetchBoard(board, pageno) {
	
		if (typeof(pageno) !== "number") {
			pageno = 0;	 //see picks.php
		}
		
		$.ajax({
			url: "api/videos.php",
			type: "GET",
			data: {board_name: board, page: pageno},
			success: function(res){
				
				$('.right_container .board_name').text(board);
				$('.videos_area').empty();
				
				if (res) {
				
					for (var video in res) {
					
						var url = res[video].video.url;
						var title = res[video].video.title;
						var description = res[video].video.description;
						var uploader = res[video].uploader;
						var thumbnails = [];
						
						if (res[video].thumbnails !== null) {
						
							thumbnails = res[video].thumbnails;
							
							var source   = $("#video-template").html();
							var template = Handlebars.compile(source);
							
							var content = {url: url, title: title, thumbnail: thumbnails[0].url, smallthumb: thumbnails[1].url};
							var html = template(content);
							$('.videos_area').append(html);
							
						} else {
							$('.videos_area').prepend('<h3 class="notice">Notice: Thumbnails missing for '+title+'</h3>');
						}
						
					}
					
				} else {
					$('.videos_area').empty().append("No videos found.");
				}
				
				window.board = board;
				bindBoardEvents();
			},
			error: function(err) {
				$('.right_container .board_name').text(board);
				if (err.responseText !== null && err.responseText !== "") {
					$('.videos_area').empty().append(err.responseText);
				} else {
					$('.videos_area').empty().append("No videos found.");
				}
			}
		});
	}
	
	function fetchFutureGarage() {
	
		$('.right_container .board_name').text("futureg");
		
		$.ajax({
			url: 'reddit.php',
			type: "GET",
			success: function(res){
				//$('.right').html(res);
				bindBoardEvents();
			}
		});
	}
	
	
	//Bind any events to elements that may have been created by an ajax call. Call this function after ajax calls.
	function bindBoardEvents() {
	
		$('.videos_area .video').click(function(e) {
			var url = $(this).attr("data-url");
			var title = $(this).attr("data-title");
			var smallthumb = $(this).attr("data-smallthumb");
			
			var this_video = {"title": title, "url": url, "smallthumb": smallthumb};
			
			if (typeof(playerstatus) === "undefined") {
				initiatePlaylist(this_video.url);
			} else {
				queuedVideos.push(this_video);
				refreshThumbnailQueue();
			}
		});
	
		$('#pagination a').click(function(e) {
			e.preventDefault();
			var pageno = $(this).attr("data-rel");
			fetchBoardAt(window.board, pageno);
		});
	}
	
});