<?php 
if ($_SERVER['HTTP_HOST'] == 'labs' || $_SERVER['HTTP_HOST'] == 'localhost' || $_SERVER['HTTP_HOST'] == 'mixxxxes') {
		$host = "localhost";
		$dbuser = "root";
		$pass = "root";
		$database = "mixxxxes";
} else if ($_SERVER['HTTP_HOST'] == 'mixxxx.es' || $_SERVER['HTTP_HOST'] == 'www.mixxxx.es') {
		require('protected.php');
}
	
$db = mysql_connect($host, $dbuser, $pass);
if (!$db) {
	die('SQL connection error. '.mysql_error());
}

$db.mysql_select_db($database) or die(mysql_error());

?>