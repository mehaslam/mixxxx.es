<?php
if(!isset($_SESSION)) 
{ 
session_start(); 
}

if (isset($_SESSION['bro']) && $_SESSION['bro'] == 'truetrue' && isset($_POST['board_name']) && $_POST['board_name']) {
	$new_board = new Board(null, $_POST['board_name']);
	$new_board->save();
	//header("location: ../");
}

?>