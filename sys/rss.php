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
     * Set Headers
     */
    header("Content-Type: application/xml");
    
    /**
     * Requirements
     */
    require_once('./core.php');

    /**
     * Build RSS feed
     */

    // Channel
    $channel = "<channel>";
    $channel .= "<title>Twinned Cities - " . $cities['city_one']['name'] . " and " . $cities['city_two']['name'] . "</title>";
    $channel .= "<link>" . $www . "</link>";
    $channel .= "<description></description>";
    $channel .= "<language>en-gb</language>";
    $channel .= "<lastBuildDate>" . date("d-M-Y H:i:sa e", time()) . "</lastBuildDate>";

    // Items
    $items = "";
    foreach($cities as $tag => $city) {
        $woeId = $city['woeid'];

        // Create City Item
        $items .= "<item>";
        $items .= "<title>Weather for " . $city['name'] . "</title>";
        $items .= "<link>" . $www . "</link>";
        $items .= "<description>";


        // Get Weather Data
        $weather = $site->getStoredWeatherData($woeId);
        
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

        // Fix temperatures
        $tempCurrent = floor($weather['main']['temp'] - 273.15);
        $tempMin = floor($weather['main']['temp_min'] - 273.15);
        $tempMax = floor($weather['main']['temp_max'] - 273.15);

        $tempString = "Currently " . $tempCurrent . "℃, from " . $tempMin . "℃ to " . $tempMax . "℃";
        $windString = "Wind: " . $weather['wind']['speed'] . "m/s, " . $weather['wind']['deg'] . "°";
        $sunRiseSet = "Sunrise: " . $weather['sunrise'] . ", Sunset: " . $weather['sunset'];

        // Populate Item
        $items .= "The current weather is classed as " . $weather['weather'][0]['description'] . ". " . $tempString . ". " . $windString . ". " . $sunRiseSet;
        
        $items .= "</description>";
        $items .= "</item>";
    }

    $channel .= $items . "</channel>";

    /*
    // Cities
    $citiesStr = "<cities>";
    foreach($cities as $tag => $city) {
        $citiesStr .= "<city>";
        foreach($city as $key => $val) {
            if(!is_array($val))
                $citiesStr .= "<" . $key . ">" . $val . "</" . $key . ">";
        }
        $citiesStr .= "</city>";
    }
    $citiesStr .= "</cities>";

    // Points of Interest
    $poiStr = "<poi>";
    foreach($cities as $tag => $city) {
        $poiStr .= "<point woeid=\"" . $city['woeid'] . "\">";
        foreach($city['poi'] as $name => $data) {
            $poiStr .= "<place>";
            foreach($data as $key => $val) {
                $poiStr .= "<" . $key . ">" . htmlspecialchars($val) . "</" . $key . ">";
            }
            $poiStr .= "</place>";
        }
        $poiStr .= "</point>";
    }
    $poiStr .= "</poi>";

    // Tweets
    $tweetsStr = "\n";

    // Flickr
    $flickrStr = "\n";

    // Weather
    $weatherStr = "\n";*/
?>
<?xml version="1.0"?>
<rss version="2.0">
    <?=$channel;?>
</rss>