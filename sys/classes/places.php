<?php
	/**
	 * Places
	 *
	 * PHP version 5.6.30
	 *
	 * @author   Reece Benson, Lewis Cummins, Devon Davies
	 * @license  MIT License
	 * @link     http://github.com/reecebenson/dsa-twinnedcities/
	 */

    class Places {
		/**
		 * Yahoo API Base URL
		 * @var string
		 */
        private static $yahooBase = "http://query.yahooapis.com/v1/public/yql";

		/**
		 * Open Weather Map Base URL
		 * @var string
		 */
        private static $openWeatherMap = "http://api.openweathermap.org/data/2.5";
        
		/**
		 * Open Weather Map API Key
		 * @var string
		 */
        private static $openWeatherMapKey = "cf7dbec75e0be30c47d8eba673d6b068";
        
		/**
		 * Query the Yahoo YQL database
		 * 
		 * @param string $qry The query to submit to Yahoo YQL Database
		 *
		 * @return array Returns an array from the JSON format
		 */
        public static function queryYahoo($qry) {
            // > Build URL Request
            $args = array(
                'q' => $qry,
                'format' => "json"
            );
            $qryUrl = self::$yahooBase . "?" . http_build_query($args);

            // > Request from URL
            $data = json_decode(file_get_contents($qryUrl), true);
            return $data;
        }
        
		/**
		 * Send a query that retrieves the places matching $place
		 * 
		 * @param string $place The name of the place to retrieve data about
		 *
		 * @return array Returns an array from the JSON format
		 */
        public static function queryPlaces($place) {
            return self::queryYahoo("SELECT * FROM geo.places WHERE text = \"" . $place . "\"");
        }
        
		/**
		 * Send a query that retrieves the places matching the WOEID $id
		 * 
		 * @param int $id The "Where on Earth" identifier (id)
		 *
		 * @return array Returns an array from the JSON format
		 */   
        public static function queryPlaceByWOEID($id) {
            return self::queryYahoo("SELECT * FROM geo.places WHERE woeid = \"" . $id . "\"");
        }

		/**
		 * Query the Open Weather Map API
		 * 
		 * @param string $apiType The type of API request we want to make
         * @param array  $args    The arguments we want to pass to the API
		 *
		 * @return array Returns an array from the JSON format
		 */
        public static function queryOpenWeatherMap($apiType, $args)
        {
            $qryUrl = self::$openWeatherMap . "/" . $apiType . "?" . $args;
            $data = json_decode(file_get_contents($qryUrl), true);
            return $data;
        }

		/**
		 * Send a API request that retrieves the weather data from OpenWeatherMap
         * from the Latitude and Longitude of a location
		 * 
		 * @param float $lat  Latitude
         * @param float $long Longitude
		 *
		 * @return array Returns an array from the JSON format
		 */   
        public static function queryPlaceWeather($lat, $long) {
            $args = array(
                "APPID" => self::$openWeatherMapKey,
                "lat" => $lat,
                "lon" => $long
            );
            return self::queryOpenWeatherMap("weather", http_build_query($args));
        }

		/**
		 * Convert degrees into the compass format
		 * 
		 * @param int $d The degree of that to convert
		 *
		 * @return string Returns the formatted degree
		 */   
        public static function getCompassFromDegree($d) {
			$dirs = array('N', 'NE', 'E', 'SE', 'S', 'SW', 'W', 'NW', 'N'); 
			return $dirs[round($d/45)];
        }

		/**
		 * Takes an array of the place's data retrieved from OpenWeatherMap
         * and converts the data into a easier format for printing out
         * via the fetchPlace(s).php file
		 * 
		 * @param array $data The data about a place in the original format from OpenWeatherMap
		 *
		 * @return array Returns a clearly formatted array with details about the place specified
		 */   
        public static function formatPlace($data) {
            // > Setup our Place
            $place = array(
                "name" => $data['name'],
                "woe_id" => $data['woeid'],
                "type" => $data['placeTypeName']['content'],
                "country" => array(
                    "name" => $data['country']['content'],
                    "woe_id" => $data['country']['woeid'],
                    "classifier" => $data['country']['code']
                )
            );

            // > Check for extra data (region of country/state, county, city, town, postcode, central lat/long, timezone)
            // -> Region/State
            if(!is_null($data['admin1'])) {
                // > Add to our Place
                $place['state'] = array(
                    "name" => $data['admin1']['content'],
                    "woe_id" => $data['admin1']['woeid']
                );
            } else $place['state'] = array("name" => "<em>undefined state</em>", "woe_id" => 0);

            // -> County/Area/LGA
            if(!is_null($data['admin2'])) {
                $place['county'] = array(
                    "name" => $data['admin2']['content'],
                    "woe_id" => $data['admin2']['woeid']
                );
            } else $place['county'] = array("name" => "<em>undefined county</em>", "woe_id" => 0);

            // -> Postcode / ZIP
            if(!is_null($data['postal'])) {
                $place['postcode'] = array(
                    "name" => $data['postal']['content'],
                    "woe_id" => $data['postal']['woeid'],
                    "classifier" => $data['postal']['type']
                );
            } else $place['postcode'] = array("name" => "<em>undefined postcode</em>", "woe_id" => 0, "classifier" => null);

            // -> Central Lat/Long
            if(!is_null($data['centroid'])) {
                $place['location'] = array(
                    "latitude" => $data['centroid']['latitude'],
                    "longitude" => $data['centroid']['longitude']
                );
            } else $place['location'] = array("latitude" => null, "longitude" => null);

            // -> Time Zone
            if(!is_null($data['timezone'])) {
                $place['timezone'] = array(
                    "name" => $data['timezone']['content'],
                    "woe_id" => $data['timezone']['woeid']
                );
            } else $place['timezone'] = array("name" => "<em>undefined timezone</em>", "woe_id" => 0);

            // > Return our place
            return $place;
        }
    }

?>