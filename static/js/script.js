/* Author: Samuel Brown (@samuelgbrown, samuelgbrown.com) */

var Mixxxxes = (function() {

	var board;
	var videosArr = [];

	return {

		board: this.board,
		videosArr: this.videosArr,
		templates: this.templates,

		init: function() {

			var self = this;

			self.bindInitialEvents();
			self.gatherTemplates();
			self.bindAdminEvents();
		},

		gatherTemplates: function() {
			//pick up all handlebars templates, store them & remove from DOM.
			var self = this;

			var elements = {
				"video_element" : $("#video-template"),
				"authed_element": $("#authed-template"),
				"login_form_element": $('#login-form-template'),
				"login_state_element": $('#login-state-template')
			}

			self.templates = {
				"video_template" : elements.video_element.html(),
				"authed_template": elements.authed_element.html(),
				"login_state_template": elements.login_state_element.html(),
				"login_form_template": elements.login_form_element.html()
			}

			for (var key in elements) {
			   var obj = elements[key];
			   obj.remove();
			}

		},

		bindInitialEvents: function() {

			var self = this;
			
			/*$('#fg_link').click(function(e) {
				e.preventDefault();
				window.location.hash = "futureg";
				self.fetchFutureGarage();
			});*/
			
			
			$('.board_link').click(function(e) {
				e.preventDefault();
				
				//board ajax actions
				var boardname = $(this).attr("data-rel");
				window.location.hash = boardname;
				self.fetchBoard(boardname);
				self.board = boardname;
			});

		},

		bindBoardEvents: function() {
			$('.videos_area .video').click(function(e) {

				//Prevent play from occuring when user clicked 'del' button.
				if (!$(e.target).hasClass("closebtn")) {

					var url = $(this).attr("data-url");
					var title = $(this).attr("data-title");
					var smallthumb = $(this).attr("data-smallthumb");
					
					var this_video = {"title": title, "url": url, "smallthumb": smallthumb};
					
					playlist.push(this_video);
					
					if (typeof(playerstatus) === "undefined") {
						initiatePlaylist(this_video.url);
					} else {
						addThumbnailToQueue(playlist[playlist.length-1]);
					}

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

			$('.videos_area .video .closebtn').click(function(e) {

				var urlToDelete = $(this).parent().attr("data-url");
				var entry = $(this).parent();
				$('.right').prepend('<h4 class="success">Successfully deleted.</h4>');
				var notice = $('.right .success');

				if (typeof(urlToDelete) !== "undefined" && urlToDelete !== null && urlToDelete !== "") {
					$.ajax({
						url: "api/delete.php",
						method: "GET",
						data: {"url":urlToDelete},
						success: function(res) {

							entry.fadeOut(700,function() {
									entry.remove();
							});

							notice.fadeOut(700,function() {
								notice.remove();
							});
						},
						error: function(err) {
							notice.text("Error deleting video "+urlToDelete+" :(");
						}
					});
				} else {
					notice.text("Error deleting video URL '"+urlToDelete+"'.");
				}
			});
		},

		bindAdminEvents: function() {
			
			var self = this;
			var admin_area = $('.admins');

			$('.login_link').click(function() {
				$('.login_link').hide();
				$('#login_form').fadeIn();
			});

			$('#login_form').submit(function(e) {
				e.preventDefault();

				var user = $(this.user).val();
				var pass = $(this.pass).val();
				
				$.ajax({
					url: 'processing/logins.php',
					type: "POST",
					dataType: "json",
					data: {"user": user, "pass": pass},
					success: function(res) {
						admin_area.fadeOut(function(){
							admin_area.empty();

							var authed_html = self.handlebarsRender(self.templates.authed_template, {});
							admin_area.append(authed_html);

							var logout_html = self.handlebarsRender(self.templates.login_state_template, {"state":"logout","message":"logout"});
							admin_area.append(logout_html);

							self.bindAdminEvents();
						}).fadeIn();
					},
					error: function(res) {
						admin_area.parepend('<h3 class="error">Login failed.</h3>');
					}	
				});
			});

			$('#submit_form').submit(function(e) {

				e.preventDefault();

				$.ajax({
					url: "processing/videos.php",
					type: "POST",
					data: {"board_id": $(this).children('#current_board_id').val(), "vid": $(this).children('#vid_field').val()},
					success: function(res) {
						var success_notice = '<h4 class="success added">Tune successfully added!</h4>';
						$('.right').prepend(success_notice);

						self.fetchBoard(self.board, 0);
						$('.right .added').fadeOut(850,function() {
							$('.right .added').remove();
						});
					
					},
					error: function(err) {
						$('.right').prepend(err.responseText);
						$('.right').prepend('<h4 class="notice">Oops! There was a problem adding that tune!</h4>');
					}
				});
			});

			$('.logout_link').click(function(e) {
				e.preventDefault();
				$.ajax({
					url: 'processing/logins.php',
					type: 'GET',
					data: {"q": true},
					success: function(res) {
						admin_area.fadeOut(function(){
							
							admin_area.empty();

							var login_form_html = self.handlebarsRender(self.templates.login_form_template, {});
							admin_area.append(login_form_html);

							var login_html = self.handlebarsRender(self.templates.login_state_template, {"state":"login","message":"submit"});
							admin_area.append(login_html);

							self.bindAdminEvents();

						}).fadeIn();
					},
					error: function(res) {
						admin_area.prepend('<h3>Error logging out.</h3>');
					}
				});
			});
		},

		fetchBoard: function(board, pageno) {

			var self = this;

			$('.right .pagination').remove();

			if (typeof(board) === "undefined" || board === null || board === "") {
				return;
			}
			
			if (typeof(pageno) === "undefined" || typeof(pageno) !== "number") {
				pageno = 0;
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
								
								var video_html = self.handlebarsRender(self.templates.video_template, {
									url: url,
									title: title,
									thumbnail: thumbnails[0].url,
									smallthumb: thumbnails[1].url
								});

								$('.videos_area').append(video_html);
								
							} else {
								//Video likely removed from youtube - provide notice (with dismiss/delete from mixxxxes shortcuts).
								//Other scenario is that this tune was added before mixxxxes backend structure was updated (no thumbs grabbed)
								$('.videos_area').prepend('<h3 class="notice" data-url="'+url+'">Notice: Thumbnails missing for '+title+' ('+url+'). May have been deleted from YouTube.<span class="shortcuts"><a class="notice_delete">Delete video from mixxxx.es</a><a class="dismiss">Dismiss</a></span></h3>');
							}
							
						}
						
						//Finished processing videos, now generate pagination html based on videocount
						if (typeof(videocount) === "number" && videocount > 9) {

							
							//calculate page count, generate html and insert into DOM.
							var pagecount = Math.ceil(videocount/9); //9 videos per page
							
							var links = "";
							
							for (var i = 1; i<pagecount+1; i++) {
								links += '<a class="pagination_link" href="#" data-pageno="'+i+'">'+i+'</a>';
							}
							
							var paginationhtml = '<div class="pagination">'+links+'</div>';
							if ($('.right .pagination')) {
								$('.right .pagination').remove();
							}
							$('.right').append(paginationhtml);
							
							if ($('.right .pagination')) {
								if (pageno) {
									$('.right .pagination a:eq('+(pageno-1)+')').addClass("active");
								} else {
									$('.right .pagination a:eq(0)').addClass("active");
								}
							}
							
							$('.right .pagination .pagination_link').click(function(e) {
								e.preventDefault();
								var pagenum = parseInt($(this).attr("data-pageno"));
								if (typeof(self.board) === "string") {
									self.fetchBoard(self.board, pagenum);
								}
							});
						}

						
					} else {
						$('#current_board_id').val(res.boardid);
						$('.videos_area').empty().append("No videos found.");
					}

					//videos and pagination loaded & inserted to dom, reset scroll position to top.
					$(window).scrollTop(0);
					
					self.board = board;
					self.bindBoardEvents();

				},
				error: function(err) {
					$('.right_container .board_name').text(board);
					if (err.responseText !== null && err.responseText !== "") {
						$('.videos_area').empty().append(err.responseText);
					} else {
						$('.videos_area').empty().append("No videos found.");
					}

					self.bindBoardEvents();
				}
			});
		},

		handlebarsRender: function(template, content) {

			var self = this;

			//Pick up handlebars.js template
			var source  = template;

			//Compile
			var template = Handlebars.compile(source);
			
			//Push content into template
			var values = content;

			//Return html
			return template(values);
		}

	}
})();

$(document).ready(function() {

	Mixxxxes.init();

	if (window.location.hash !== "" && window.location.hash != "#futureg") {
		Mixxxxes.board = window.location.hash.replace('#', '');
		Mixxxxes.fetchBoard(Mixxxxes.board);
	} /*else if (window.location.hash == "#futureg") {
		window.location.hash = "futureg";
		fetchFutureGarage();
	}*/ else {
		window.location.hash = "clssx";
		Mixxxxes.board = window.location.hash.replace('#', '');
		Mixxxxes.fetchBoard(Mixxxxes.board);
	}
	
	/*function fetchFutureGarage() {
	
		$('.right_container .board_name').text("futureg");
		
		$.ajax({
			url: 'reddit.php',
			type: "GET",
			success: function(res){
				//$('.right').html(res);
				bindBoardEvents();
			}
		});
	}*/
	
});