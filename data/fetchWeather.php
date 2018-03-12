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
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];
    $city = $site->getCityData($woeid);

    /**
     * Check for errors
     */
    if(!isset($_POST)) {
        $response['status'] = 500;
        $response['error'] = "No data was sent in the request parameters.";
    }

    /**
     * Find our previous Weather Log - finished
     */
    $weather = null;
    $response['last_pull'] = $site->getSystemInfo("last_weather_pull_".$woeid);
    if((time() - (int)$response['last_pull']) > 3600) {
        // Pull new weather data
        $weather = Places::queryPlaceWeather($latitude, $longitude);

        // Reset timer
        $site->setSystemInfo("last_weather_pull_".$woeid, time());

        // Update stored weather data
        $weather['woeid'] = $woeid;
        $site->storeWeatherData($weather);
    } else {
        // Pull old data
        $weather = $site->getStoredWeatherData($woeid);
        $weather['pull_time'] = (time() - (int)$response['last_pull']);
    }
    
    /**
     * Set our timeago string
     */
    $response['timeago'] = $site->timeago((int)$site->getSystemInfo("last_weather_pull_".$woeid));

    /**
     * Fix for 'degree' in weather response
     */
    if(!isset($weather['wind']['deg']))
        $weather['wind']['deg'] = "<em>unknown</em>";

    /**
     * Set our dates for sunrise/sunset
     */
    // Set Timezone to requested city
    $defaultTimezone = date_default_timezone_get();
    date_default_timezone_set($city['timezone']);

    // Update Variables
    $weather['sunrise'] =  date("H:i:sa", $weather['sys']['sunrise']);
    $weather['sunset'] =  date("H:i:sa", $weather['sys']['sunset']);

    // Reset Timezone back to default
    date_default_timezone_set($defaultTimezone);

    /**
     * Data to send back
     */
    $response['weather'] = $weather;
    
    /**
     * Output
     */
    die(json_encode($response, JSON_PRETTY_PRINT));
?>