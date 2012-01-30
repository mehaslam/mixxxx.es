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
		
		if (typeof(board) === "undefined" || board === null || board === "") {
			return;
		}
		
		if (typeof(pageno) !== "number") {
			pageno = 0;	//see picks.php
		}
		
		//request videos JSON from api.
		
		$.ajax({
			url: "api/videos.php",
			type: "GET",
			data: {board_name: board, page: pageno},
			success: function(res){
				
				$('.right_container .board_name').text(board);
				$('.videos_area').empty();
				var videocount;
				
				if (res && res.videos && res.videos.length > 0) {
		
					$('#current_board_id').val(res.videos[0].boardid);
					videocount = res.videocount;
				
					//Videos were returned - now process them.
					for (var video in res.videos) {

						var url = res.videos[video].video.url;
						var title = res.videos[video].video.title;
						var description = res.videos[video].video.description;
						var uploader = res.videos[video].uploader;
						var thumbnails = [];
						
						//check thumbnails - if they are null, the video has likely been removed from youtube.
						if (res.videos[video].thumbnails !== null) {
						
							thumbnails = res.videos[video].thumbnails;
							
							//Pick up handlebars.js template for a video
							var source   = $("#video-template").html();
							var template = Handlebars.compile(source);
							
							//Push content into template, generate html
							var content = {url: url, title: title, thumbnail: thumbnails[0].url, smallthumb: thumbnails[1].url};
							var html = template(content);
							
							//Push html into DOM
							$('.videos_area').append(html);
							
						} else {
							//Video likely removed from youtube - provide notice (with dismiss/delete from mixxxxes shortcuts).
							//Other scenario is that this tune was added before mixxxxes backend structure was updated (no thumbs grabbed)
							$('.videos_area').prepend('<h3 class="notice" data-url="'+url+'">Notice: Thumbnails missing for '+title+' ('+url+'). May have been deleted from YouTube.<span class="shortcuts"><a class="notice_delete">Delete video from mixxxx.es</a><a class="dismiss">Dismiss</a></span></h3>');
						}
						
					}
					
					//Finished processing videos, now generate pagination html based on videocount
					if (typeof(videocount) === "number" && videocount > 9) {
						
						//calculate page count, generate html and insert into DOM.
						var pagecount = videocount/9; //9 videos per page
						
						var links = "";
						
						for (var i = 0; i<pagecount+1; i++) {
							links += '<a class="pagination_link" href="#">'+(i+1)+'</a>';
						}
						
						var paginationhtml = '<div class="pagination">'+links+'</div>';
						if ($('.right .pagination')) {
							$('.right .pagination').remove();
						}
						$('.right').append(paginationhtml);
						
						if ($('.right .pagination')) {
							$('.right .pagination a:eq('+(pageno)+')').addClass("active");
						}
						
						$('.right .pagination .pagination_link').click(function(e) {
							e.preventDefault();
							var page = $(this).index();
							if (typeof(board) === "string") {
								fetchBoard(board, page);
							}
						});
					}
					
				} else {
					$('#current_board_id').val(res.boardid);
					$('.videos_area').empty().append("No videos found.");
					console.log("Empty board ("+res.boardid+")");
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

				bindBoardEvents();
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
			console.log(this_video);
			
			if (typeof(playerstatus) === "undefined") {
				initiatePlaylist(this_video.url);
			} else {
				queuedVideos.push(this_video);
				refreshThumbnailQueue();
			}
		});
		
		$('.videos_area .notice .dismiss').click(function() {
			var parent = $(this).parent().parent();
			parent.fadeOut("medium",function() {
				parent.remove();
			});
		});
		
		//actions binded if there is a notice (e.g. videos taken down from youtube).
		if ($('.videos_area .notice').length) {
		
			$('.videos_area .notice .notice_delete').click(function() {
				
				var parent = $(this).parent().parent();
				var urlToDelete = parent.attr("data-url");
				
				if (typeof(urlToDelete) !== "undefined" && urlToDelete !== null && urlToDelete !== "") {
					$.ajax({
						url: "api/delete.php",
						method: "GET",
						data: {"url":urlToDelete},
						success: function(res) {
							parent.addClass("success");
							parent.removeClass("notice");
							parent.text("Successfully removed.");
							parent.fadeOut(1300,function() {
								parent.remove();
							});
						},
						error: function(err) {
							parent.text("Error deleting video "+urlToDelete+" :(");
						}
					});
				} else {
					parent.text("Error deleting video URL '"+urlToDelete+"'.");
				}
			});
		
		}
	}
	
});