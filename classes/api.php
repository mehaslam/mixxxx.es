<?php
		header("Content-type: text/json");
		
		require_once('db.php');
		require_once('boards.php');
		
	    $boards = board::getAllBoardsInJson();
	    
	    echo $boards;
?>