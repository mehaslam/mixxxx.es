<?php 

	require('db.php');
	
    class Video {

       private $id;
       private $url;
	   private $board_id;

       function __construct($id = null, $url = null, $board_id = null)
       {
	       $this->id = $id;
	       $this->url = $url;
		   $this->board_id = $board_id;
       }
       
       
       function getID()
       {
       	return $this->id;
       }

       function getUrl()
       {
        return $this->url;
       }
       
       function setUrl($url)
       {
        $this->url = $url;
       }
	   
	   function getBoardID() {
			return $this->board_id;
	   }
	   
	   function setBoardID($board_id) {
			$this->board_id = $board_id;
	   }
       

       //Does UPDATE if ID exists, otherwise INSERT.
       function save() {
       
	        if ($this->id == NULL) {
	        	
		        mysql_query("INSERT INTO `videos` ( `url`, `board_id` ) VALUES ( '".$this->url."', '".$this->board_id."' );") or die("Query failed with ersror: ".mysql_error());
		        $this->id = mysql_insert_id();
	         
	        } else {

		        mysql_query("UPDATE `videos` SET `url`, `board_id` = '".$this->url."', '".$this->board_id."' WHERE `id` = ".$this->id.";") or die("Qzuery failed with error: ".mysql_error());
	         
	        }
	        
       }

       function delete(){
	        mysql_query("DELETE FROM `videos` WHERE `id` = '".$this->id."';") or die("Query failed with error: ".mysql_error());
       }
       
       
       function getBoardVideos($boardid) {
       
   			$result = mysql_query("SELECT * FROM `videos` WHERE `board_id` =".$boardid) or die("Query failed with error: ".mysql_error());
			while ($row = mysql_fetch_array($result)) {
				$video = array($row['id'],$row['url']);
				$vids[] = $video;
			}
			
			$videos = array_reverse($vids);
			
			foreach ($videos as $video) {
				$r_videos[] = new Video($video[0],$video[1]);
			}
			
			return $r_videos;

       }
       
       function getVideoByUrl($url, $boardid) {
       	
   			$result = mysql_query("SELECT * FROM `videos` WHERE `url` = '".$url."' AND `board_id` =".$boardid) or die("query failed with error: ".mysql_error());
			while ($row = mysql_fetch_array($result)) {
				$video = new Video ($row['id'],$row['url'],$row['board_id']);
			}
			
			if (isset($video)) {
				return $video;
			} else {
				return null;
			}
       }

}


?>