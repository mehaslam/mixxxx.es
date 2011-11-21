<?php
	
	require('db.php');
	
    class Board {

       private $id;
       private $name;

       function __construct($id = null, $name = null)
       {
	       $this->id = $id;
	       $this->name = $name;
       }
       
       
       function getID()
       {
       	return $this->id;
       }

       function getName()
       {
        return $this->name;
       }
       
       function setName($name)
       {
        $this->name = $name;
       }
       

       //Does UPDATE if ID exists, otherwise INSERT.
       function save() {
       
	        if ($this->id == NULL) {
	        	
		        mysql_query("INSERT INTO `boards` ( `name` ) VALUES ( '".$this->name."' );") or die("Query failed with error: ".mysql_error());
		        $this->id = mysql_insert_id();
	         
	        } else {

		        mysql_query("UPDATE `boards` SET `name` = '".$this->name."' WHERE `id` = ".$this->id.";") or die("Query failed with error: ".mysql_error());
	         
	        }
	        
       }

       function delete(){
	        mysql_query("DELETE FROM `boards` WHERE `id` = '".$this->id."';") or die("Query failed with error: ".mysql_error());
       }
       
       
       function getAllBoards() {
       
   			$result = mysql_query("SELECT * FROM `boards`") or die("Query failed with error: ".mysql_error());
			while ($row = mysql_fetch_array($result)) {
				$board = new Board($row['id'],$row['name']);
				$boards[] = $board;
			}
			
			return $boards;

       }
       
       function getBoardByName($name) {
       	
   			$result = mysql_query("SELECT * FROM `boards` WHERE `name` = '".$name."'") or die("Query failed with error: ".mysql_error());
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

}


?>