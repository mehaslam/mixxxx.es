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
		
		//youtube playlist actions
		videos.length = 0;
		board = boardname;
		getPlaylist(board);
	});
	
	
	$('#login_link').click(function() {
		$('#login_form').show();
		$('#login_link').hide();
	});
	
	function fetchBoard(board) {
		$.ajax({
			url: "api/videos.php",
			type: "GET",
			data: "board_name="+board,
			success: function(res){
				$('.right_container .board_name').text(board);
				console.log(res);
				
				for (var video in res) {
					var url = res[video].url;
					// latest
					//pick up all bits we need and populate html.
				}
				
				window.board = board;
				bindBoardEvents();
			},
			error: function(err) {
				console.log("ERROR:");
				console.log(err.responseText);
			}
		});
	}
	
	function fetchBoardAt(board, page) {
	
		$.ajax({
			url: "picks.php",
			data: {board_name: board, page: page},
			type: "GET",
			success: function(res){
				//$('.right').html(res);
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
				//$('.right').html(res);
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
});