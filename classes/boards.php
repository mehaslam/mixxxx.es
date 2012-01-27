<?php
	
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
}


?>