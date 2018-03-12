<?php
	/**
	 * Core
	 * - Included into every public file
	 *
	 * PHP version 5.6.30
	 *
	 * @author   Reece Benson, Lewis Cummins, Devon Davies
	 * @license  MIT License
	 * @link     http://github.com/reecebenson/dsa-twinnedcities/
	 */

	/**
	 * Configuration File
	 */
	require_once('configuration.php');

	/**
	 * Class Requirements
	 */
	require_once('classes/site.php');
	require_once('classes/places.php');
	require_once('classes/twitter.php');

	/**
	 * Set our timezone (dependant on platform, so we should always set it)
	 */
	date_default_timezone_set("Europe/London");

	/**
	 * Initialise our database
	 * 
	 * @throws If Error - kills the web page and displays the MySQL Error
	 */
	$db = null;
	try {
		$db = new PDO("mysql:dbname=" . $db_details['name'] . ";host=" . $db_details['host'], $db_details['user'], $db_details['pass']);
	} catch(PDOException $e) {
		die('Error connecting to database: ' . $e->getMessage());
	}

	/**
	 * Initialise our Site class
	 */
	$site = new Site();
	
	/**
	 * Define our website URL variable (which is accessible to every file)
	 */
	$www  = "http://uwe.reecebenson.me/dsa-twincities";
?>