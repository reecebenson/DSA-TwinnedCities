<?php
	/**
	 * Homepage
	 *
	 * PHP version 5.6.30
	 *
	 * @author   Reece Benson, Lewis Cummins, Devon Davies, Daisy
	 * @license  MIT License
	 * @link     http://github.com/reecebenson/dsa-twinnedcities/
	 */

	/**
	 * Requirements
	 */
    require_once('../sys/core.php');
    
    /**
     * Variables
     */
    $response = array('status' => 200, 'error' => null);
    $woeid = $_POST['woeid'];

    /**
     * Check for errors
     */
    if(!isset($_POST)) {
        $response['status'] = 500;
        $response['error'] = "No data was sent in the request parameters.";
    }

    /**
     * Get city data
     */
    $response['city'] = $site->getCityData($woeid);

    /**
     * Output
     */
    die(json_encode($response, JSON_PRETTY_PRINT));
?>