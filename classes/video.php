<?php 
	
    class Video {

       private $id;
       private $title;
       private $url;
	private $description;

       function __construct($id = null, $title = null, $url = null, $description = null)
       {
	       $this->id = $id;
	       $this->title = $title;
	       $this->url = $url;
	       $this->description = $description;
       }
       
       
       function getID()
       {
       	return $this->id;
       }

       function getUrl()
       {
        return $this->url;
       }
       
       function getTitle()
       {
        return $this->title;
       }

       function getDescription()
       {
        return $this->description;
       }
       

       //Does UPDATE if ID exists, otherwise INSERT.
       function save() {
       
	        if ($this->id == NULL) {
	        	
		        mysql_query("INSERT INTO `videos` ( `title`, `url`, `description` ) VALUES ( '".$this->title."', '".$this->url."', '".$this->description."' );") or die("Query failed with error: ".mysql_error());
		        $this->id = mysql_insert_id();
	         
	        } else {
				
		        mysql_query("UPDATE `videos` SET `title` = '".$this->title."', `url` = '".$this->url."', `description` = '".$this->description."' WHERE `id` = ".$this->id.";") or die("Query failed with error: ".mysql_error());
	        }
	        
       }

       function delete(){
	        mysql_query("DELETE FROM `videos` WHERE `id` = '".$this->id."';") or die("Query failed with error: ".mysql_error());
       }
       
}


?>