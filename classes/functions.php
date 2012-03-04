<?php
		
		require('db.php');
		require('video.php');
		require('boardVideo.php');
		require('boards.php');
		require('videoThumbs.php');

 		function getVideoByUrl($url) {
       	
   			$result = mysql_query("SELECT * FROM `videos` WHERE `url` = '".$url."'") or die("Query failed with error: ".mysql_error());
			while ($row = mysql_fetch_array($result)) {
				$video = new Video ($row['id'],$row['title'],$row['url'],$row['description']);
			}
			
			if (isset($video)) {
				return $video;
			} else {
				return null;
			}
       }

       function getVideoById($id) {
       	
   			$result = mysql_query("SELECT * FROM `videos` WHERE `id` = ".$id) or die("Query failed with error: ".mysql_error());
			while ($row = mysql_fetch_array($result)) {
				$video = new Video ($row['id'],$row['title'],$row['url'],$row['description']);
			}
			
			if (isset($video)) {
				return $video;
			} else {
				return null;
			}
       }
       
       function getVideoThumbnails($videoid) {
       
       		$result = mysql_query("SELECT * FROM `videothumbs` WHERE `videoid` =".$videoid) or die("Query failed with error: ".mysql_error());
			
			while ($row = mysql_fetch_array($result)) {
				$thumbnail = array("id"=>$row['id'],"videoid"=>$row['videoid'],"url"=>$row['url']);
				$thumbnails[] = $thumbnail;
			}
			
			if (isset($thumbnails)) {
				return $thumbnails;
			} else {
				return null;
			}
       }


       function getBoardVideos($boardid) {

			$result = mysql_query("SELECT * FROM `boardvideos` WHERE `boardid` =".$boardid) or die("Query failed with error: ".mysql_error());
			
			while ($row = mysql_fetch_array($result)) {
				$video = array($row['id'],$row['videoid'],$row['boardid'],$row['uploaderid']);
				$vids[] = $video;
			}
			
			if (isset($vids)) {
				$videos = array_reverse($vids);
				
				foreach ($videos as $video) {
					$r_videos[] = new BoardVideo($video[0],$video[1],$video[2],$video[3]);
				}
				
				return $r_videos;
			
			} else {
				return null;
			}

		}
		
		function getBoardVideosAt($boardid, $pageno) {

			if ($pageno == 0) {
				$pageno = 1;
			}

			$result = mysql_query("SELECT * FROM `boardvideos` WHERE `boardid` =".$boardid." ORDER BY `Date_Added` DESC") or die("Query failed with error: ".mysql_error());
			
			while ($row = mysql_fetch_array($result)) {
				$video = array($row['id'],$row['videoid'],$row['boardid'],$row['uploaderid']);
				$videos[] = $video;
			}
			
			if (isset($videos)) {
				
				foreach ($videos as $video) {
					$r_videos[] = new BoardVideo($video[0],$video[1],$video[2],$video[3]);
				}
				
				$perpage = 16;
				$startpos = (($pageno-1)*$perpage);

				$videocount = count($r_videos);

				if ($pageno * $perpage > $videocount) {
					$lastpage = $videocount - ($perpage*($pageno-1));
				}
				
				if (isset($lastpage)) {
					$this_page = array_slice($r_videos, $startpos, $lastpage);
				} else {
					$this_page = array_slice($r_videos, $startpos, $perpage);
				}
				
				return $this_page;
			
			} else {
				return null;
			}

		}


		function countBoardVideos($boardid) {

			$result = mysql_query("SELECT * FROM `boardvideos` WHERE `boardid` =".$boardid) or die("Query failed with error: ".mysql_error());
			
			while ($row = mysql_fetch_array($result)) {
				$vids[] = $row['id'];
			}
			
			return count($vids);

		}


		function getAllBoards() {
       
   			$result = mysql_query("SELECT * FROM `boards`") or die("Query failed with error: ".mysql_error());
			while ($row = mysql_fetch_array($result)) {
				$board = new Board($row['id'],$row['name']);
				$boards[] = $board;
			}
			
			return $boards;

       }
       
       function getAllBoardsInJson() {
       
       	   	$result = mysql_query("SELECT * FROM `boards`") or die("Query failed with error: ".mysql_error());
       	   	
			while ($row = mysql_fetch_array($result)) {
				$board = array("id" => $row['id'],"name" =>$row['name']);
				$boards[] = $board;
			}
			
			return json_encode($boards);
       
       }
       
       function getBoardByName($name) {
       	
   			$result = mysql_query("SELECT * FROM `boards` WHERE `name` = '".$name."'") or die("getBoardByName failed with error: ".mysql_error());
			while ($row = mysql_fetch_array($result)) {
				$board = new Board ($row['id'],$row['name']);
			}
			
			if (isset($board)) {
				return $board;
			} else {
				return null;
			}
       }
	   
	    function getBoardByID($id) {
       	
   			$result = mysql_query("SELECT * FROM `boards` WHERE `id` = '".$id."'") or die("Query failed with error: ".mysql_error());
			while ($row = mysql_fetch_array($result)) {
				$board = new Board ($row['id'],$row['name']);
			}
			
			if (isset($board)) {
				return $board;
			} else {
				return null;
			}
       }


?>