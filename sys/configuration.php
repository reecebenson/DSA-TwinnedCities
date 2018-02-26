<?php
	/**
	 * Configuration
	 *
	 * PHP version 5.6.30
	 *
	 * @author   Reece Benson, Lewis Cummins, Devon Davies
	 * @license  MIT License
	 * @link     http://github.com/reecebenson/dsa-twinnedcities/
	 */

	/**
	 * Database Data
	 * 
	 * @param string user Holds the username of the database account
	 * @param string pass Holds the password of the database account
	 * @param string host Holds the host of the database
	 * @param string name Holds the table of what MySQLi should connect to
	 */
	$db_details = array(
		'user' => 'dsa_twincities',
		'pass' => 'hRfex3p6brDKjaZT',
		'host' => 'localhost',
		//'host' => '107.170.12.80',
		'name' => 'dsa_twincities'
	);
	
	/**
	 * Twinned Cities
	 */
	$cities = array(
		'city_one' => array(
			'name' => "Manchester",
			'woeid' => "28218",
			'lat' => 53.4808,
			'long' => -2.2426,
			'timezone' => "Europe/London",
			'population' => 541300,
			'square_ft' => 44.7
		),

		'city_two' => array(
			'name' => "Los Angeles",
			'woeid' => "2442047",
			'lat' => 34.0522,
			'long' => -118.2437,
			'timezone' => "America/Los_Angeles",
			'population' => 3792621,
			'square_ft' => 502.76
		)
	);

	/**
	 * Twitter Data
	 * 
	 * @param string oauth_access_token 		Twitter Access Token
	 * @param string oauth_access_token_secret	Twitter Access Token (Secret)
	 * @param string consumer_key 				Twitter Consumer Key
	 * @param string consumer_secret			Twitter Consumer Secret
	 */
	$tw_details = array(
		'oauth_access_token' => "609854104-dFJgX88k9uxv5aGRae0jSnIGo4BkivsnUHr3Jl29",
		'oauth_access_token_secret' => "3Ing2CoImomjbnMov4MHeVCHODlWvKsxfSTAldn5CXw5m",
		'consumer_key' => "KeCqqgOkwsY8hqJqxypkhUtdj",
		'consumer_secret' => "6z7X0KITM54lnnmHyYvoqLUY5k9TddOxrJrwATedowN2JR47Qr"
	);

	/**
	 * Flickr Data
	 */
	$fl_details = array(
		'key' => 'a1f7b43bfd28a320ceb0f9e62b93dfb6',
		'id' => 'f79a498ed2f7d39f'
	);
?>