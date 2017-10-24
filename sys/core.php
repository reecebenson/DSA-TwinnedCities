<?php
	/* Reece Benson */
	/* BSc Comp Sci */
	require_once('configuration.php');
	require_once('classes/site.php');
	require_once('classes/weather.php');

	// > Initialise our database
	$db = new MySQLi($db_details['host'], $db_details['user'], $db_details['pass'], $db_details['name']);
	if(!$db)
	{
		// > Error connecting
		die('Error connecting to database: ' . mysql_error());
	}

	//die(phpinfo());

	// > Initialise our class
	$site = new Site();
	$www  = "http://uwe.reecebenson.me/dsa-twincities";
?>