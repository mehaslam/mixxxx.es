<?php

if(!isset($_SESSION)) { 
	session_start();
}

if( !defined( __DIR__ ) )define( __DIR__, dirname(__FILE__) );
require_once(__DIR__.'../../classes/boards.php');
require_once(__DIR__.'../../classes/functions.php');

if (isset($_SESSION['bro']) && $_SESSION['bro'] == 'truetrue') {

	if (isset($_POST['board_name'])) {

		if (isset($_POST['add_board'])) {
			$new_board = new Board(null, $_POST['board_name']);
			$new_board->save();
			header('HTTP/1.0 200 OK');
			exit();
		} else if (isset($_POST['edit_board']) && isset($_POST['new_name'])) {
			$board = getBoardByName($_POST['board_name']);
			$board->setName($_POST['new_name']);
			$board->save();
			header('HTTP/1.0 200 OK');
			exit();
		} else if (isset($_POST['delete_board'])) {
			$board = getBoardByName($_POST['board_name']);
			$board->delete();
			header('HTTP/1.0 200 OK');
			exit();
		}

	} else {
		header('HTTP/1.1 400 Bad Request');
	}

} else {
	header('HTTP/1.0 401 Unauthorized');
}

?>