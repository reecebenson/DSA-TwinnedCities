<?php
	/* Reece Benson */
	/* BSc Comp Sci */
	require_once('configuration.php');
	require_once('classes/site.php');
	require_once('classes/places.php');

	// > Set our timezone (dependant on platform, so we should always set it)
	date_default_timezone_set("Europe/London");

	// > Initialise our database
	$db = new MySQLi($db_details['host'], $db_details['user'], $db_details['pass'], $db_details['name']);
	if(!$db)
	{
		// > Error connecting
		die('Error connecting to database: ' . mysql_error());
	}

	// > Initialise our class
	$site = new Site();
	$www  = "http://uwe.reecebenson.me/dsa-twincities";

	// > Twitter Authentication
	require_once('classes/twitter.php');
?>