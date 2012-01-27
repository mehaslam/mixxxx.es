<?php
		session_start();
		error_reporting(0);
		header('Content-type: application/json');
		
		require_once('../classes/db.php');
		require_once('../classes/boards.php');
		
	    $boards = board::getAllBoardsInJson();


		if (isset($board)) {
	   		echo $boards;
	    } else {
			echo 'board not found:';
			print_r($_GET);
			die();
		}
?>