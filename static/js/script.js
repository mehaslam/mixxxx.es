/* Author: Samuel Brown (@samuelgbrown, samuelgbrown.com) */

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

	function onYoutubePlayerReady(id) {
		console.log("wat");
		console.log(id);
	}