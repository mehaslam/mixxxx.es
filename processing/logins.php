<?php
if(!isset($_SESSION)) 
{ 
session_start(); 
}

if ($_SERVER['HTTP_HOST'] === "mixxxx.es") {
	require(getcwd().'/classes/db.php');
} else {
	require(__DIR__.'../../classes/db.php');
}

//PROCESS LOGINS
if (isset($_POST['user']) && isset($_POST['pass'])) {
	if ($_POST['user'] != null && $_POST['pass'] != null) {
		//	echo 'lol';
		function checkUserPass($user, $pass) {
       
   			$result = mysql_query("SELECT * FROM `users` WHERE user = '".$user."'") or die("Query failed with error: ".mysql_error());
			while ($row = mysql_fetch_array($result)) {
				$the_user = array("user" => $row['user'],"pass" => $row['pass']);
			}
			
			if ($pass == $the_user["pass"]) {
			 	return true;
			} else {
				return false;
			}
		}

		function getUserId($user) {
			$result = mysql_query("SELECT * FROM `users` WHERE user = '".$user."'") or die("Query failed with error: ".mysql_error());
			while ($row = mysql_fetch_array($result)) {
				$id = $row['ID'];
			}

			return $id;
		}
		
		$success = checkUserPass($_POST['user'], md5($_POST['pass']));
		
		if ($success == true) {
			$_SESSION['bro'] = 'truetrue';
			$_SESSION['userid'] = getUserId($_POST['user']);
			header("location: ../");
		} else {
			echo 'Nawwwww. Login wrong breh.';
		}
	}
}

//PROCESS LOGOUT
if (isset($_GET['q'])) {
	session_destroy();
	header("location: ../");
}

?>