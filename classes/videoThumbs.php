<?php 
	
       class VideoThumb {

       private $id;
       private $videoid;
       private $url;

       function __construct($id = null, $videoid = null, $url = null)
       {
	       $this->id = $id;
	       $this->videoid = $videoid;
	       $this->url = $url;
       }
       
       
       function getID()
       {
       	return $this->id;
       }

       function getUrl()
       {
              return $this->url;
       }
       
       function getVideoID()
       {
              return $this->videoid;
       }
       

       //Does UPDATE if ID exists, otherwise INSERT.
       function save() {
       
	        if ($this->id == NULL) {
	        	
		        mysql_query("INSERT INTO `videothumbs` ( `videoid`, `url`) VALUES ( '".$this->videoid."', '".$this->url."');") or die("Query failed with error: ".mysql_error());
		        $this->id = mysql_insert_id();
	         
	        } else {
				
		        mysql_query("UPDATE `videothumbs` SET `videoid` = '".$this->videoid."', `url` = '".$this->url."' WHERE `id` = ".$this->id.";") or die("Query failed with error: ".mysql_error());
	        }
	        
       }

       function delete(){
	        mysql_query("DELETE FROM `videothumbs` WHERE `id` = '".$this->id."';") or die("Query failed with error: ".mysql_error());
       }
       
}


?>