<?php
	
    class BoardVideo {

		private $id;
		private $videoid;
		private $boardid;
		private $uploaderid;

       function __construct($id = null, $videoid = null, $boardid = null, $uploaderid = null)
       {
	       $this->id = $id;
	       $this->videoid = $videoid;
	       $this->boardid = $boardid;
	       $this->uploaderid = $uploaderid;
       }
       
       
       function getID()
       {
       	return $this->id;
       }

       function videoID()
       {
        return $this->videoid;
       }
       
       function boardID()
       {
        return $this->boardid;
       }

       function getUploaderID() {
       	return $this->uploaderid;
       }

		//Does UPDATE if ID exists, otherwise INSERT.
    	function save() {
       
	        if ($this->id == NULL) {
	        	
		        mysql_query("INSERT INTO `boardvideos` ( `videoid`, `boardid`, `uploaderid` ) VALUES ( ".$this->videoid.", ".$this->boardid.", ".$this->uploaderid." );") or die("Query failed with error: ".mysql_error());
		        $this->id = mysql_insert_id();
	         
	        } else {
				
		        mysql_query("UPDATE `boardvideos` SET `videoid` = ".$this->videoid.", `boardid` = ".$this->boardid.", `uploaderid` = ".$this->uploaderid." WHERE `id` = ".$this->id.";") or die("Query failed with error: ".mysql_error());
	        }
	        
       }

       function delete() {
	        mysql_query("DELETE FROM `boardvideos` WHERE `id` = '".$this->id."';") or die("Query failed with error: ".mysql_error());
       }
   }

?>