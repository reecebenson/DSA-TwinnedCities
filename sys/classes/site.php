<?php
	/**
	 * Site
	 *
	 * PHP version 5.6.30
	 *
	 * @author   Reece Benson, Lewis Cummins, Devon Davies
	 * @license  MIT License
	 * @link     http://github.com/reecebenson/dsa-twinnedcities/
	 */

	class Site
	{
		/**
		 * Holds data from the `system_information` table as cache
		 * @var string
		 */
		private $sys_info;

		/**
		 * Instantiates the $sys_info variable
		 */
		function __construct()
		{
			global $db;

			// > Store data
			foreach($db->query("SELECT `name`, `value` FROM `system_information`") as $res)
			{
				$this->sys_info[$res['name']] = $res['value'];
			}
		}

		/**
		 * Grab some information out of the $sys_info variable
		 *
		 * @param string $name The name of the setting to retrieve
		 *
		 * @return string The value of the setting specified
		 */
		public function getSystemInfo($name)
		{
			return $this->sys_info[$name];
		}

		/**
		 * Set data within the `system_information` table
		 * 
		 * @param string $name The name of the setting to set
		 * @param string $val  The value of the setting to set
		 */
		public function setSystemInfo($name, $val)
		{
			global $db;

			// Update Database
			$statement = $db->prepare("UPDATE `system_information` SET `value` = ? WHERE `name` = ? LIMIT 1");
			$statement->bindParam(1, $val);
			$statement->bindParam(2, $name);
			$statement->execute();

			// Set the value locally
			$this->sys_info[$name] = $val;
		}
		
		/**
		 * Store Weather Data
		 * 
		 * @param string $weatherData
		 */
		public function storeWeatherData($weatherData) {
			global $db;

			// Update Database
			$statement = $db->prepare("UPDATE `city_weather` SET `temp` = :temp, `temp_min` = :tempMin, `temp_max` = :tempMax, `description` = :weatDesc, `icon` = :weatIcon, `cloud_percent` = :cloudPercent, `wind_speed` = :windSpeed, `wind_dir` = :windDir, `time_sunrise` = :sunRise, `time_sunset` = :sunSet WHERE `woeid` = :woeId LIMIT 1");
			$statement->bindParam(':temp', 			$weatherData['main']['temp']);
			$statement->bindParam(':tempMin',		$weatherData['main']['temp_min']);
			$statement->bindParam(':tempMax', 		$weatherData['main']['temp_max']);
			$statement->bindParam(':weatDesc', 		$weatherData['weather'][0]['description']);
			$statement->bindParam(':weatIcon', 		$weatherData['weather'][0]['icon']);
			$statement->bindParam(':cloudPercent', 	$weatherData['clouds']['all']);
			$statement->bindParam(':windSpeed', 	$weatherData['wind']['speed']);
			$statement->bindParam(':windDir', 		$weatherData['wind']['deg']);
			$statement->bindParam(':sunRise', 		$weatherData['sys']['sunrise']);
			$statement->bindParam(':sunSet', 		$weatherData['sys']['sunset']);
			$statement->bindParam(':woeId',			$weatherData['woeid']);
			$statement->execute();
		}

		/**
		 * Get Stored Weather Data
		 * 
		 * @param string $woeid
		 * @return JSON JSON Element
		 */
		public function getStoredWeatherData($woeid) {
			global $db;

			// Get data from database
			$statement = $db->prepare("SELECT * FROM `city_weather` WHERE `woeid` = ? LIMIT 1");
			$statement->bindParam(1, $woeid);
			$statement->execute();

			// Get row
			$row = $statement->fetch(PDO::FETCH_ASSOC);

			// Convert to Array formatted for "fetchWeather" file
			$resp = array();
			$resp['weather'] = array();
			$resp['weather'][0]['description'] = $row['description'];
			$resp['weather'][0]['icon'] = $row['icon'];
			$resp['main']['temp'] = $row['temp'];
			$resp['main']['temp_min'] = $row['temp_min'];
			$resp['main']['temp_max'] = $row['temp_max'];
			$resp['wind']['speed'] = $row['wind_speed'];
			$resp['wind']['deg'] = $row['wind_dir'];
			$resp['clouds']['all'] = $row['cloud_percent'];
			$resp['sys']['sunrise'] = $row['time_sunrise'];
			$resp['sys']['sunset'] = $row['time_sunset'];
			return $resp;
		}

		/**
		 * Get the city data from the database specified by WOEID
		 * 
		 * @param int $woeid
		 * @return array
		 */
		public function getCityData($woeid) {
			global $db;

			// Get data from database
			$statement = $db->prepare("SELECT * FROM `city` WHERE `woeid` = ? LIMIT 1");
			$statement->bindParam(1, $woeid);
			$statement->execute();

			// Get row
			$row = $statement->fetch(PDO::FETCH_ASSOC);

			// Add POI
			if($row)
				$row['poi'] = $this->getPointsOfInterest($woeid);

			// Fix Floats
			$row['lat'] = (float)$row['lat'];
			$row['long'] = (float)$row['long'];

			// Return
			return ($row ? $row : null);
		}

		public function getPointsOfInterest($woeid) {
			global $db;

			// Setup POI Variable
			$poi = array();

			// Get data from database
			$statement = $db->prepare("SELECT * FROM `places` WHERE `city_id` = ?");
			$statement->bindParam(1, $woeid);
			$statement->execute();

			// Fetch all data
			foreach($statement->fetchAll() as $res)
			{
				// Setup Place
				$place = array(
					"name" => htmlspecialchars($res['name']),
					"desc" => htmlspecialchars($res['description']),
					"www" => $res['www'],
					"phone" => $res['phone'],
					"address" => $res['address'],
					"image" => $res['img_source']
				);

				// Add Place to POI Array
				$poi[$res['name']] = $place;
			}

			// Get Points of Interest from Configuration
			return $poi;
		}

		/**
		 * Convert a unix timestamp into a readable "time ago" format:
		 * 'just now', 'x seconds ago', 'x minutes ago', etc...
		 *
		 * @param int $ptime A unix timestamp
		 *
		 * @return string The "time ago" string
		 */
		public function timeago($ptime)
		{
			$etime = time() - $ptime;

			if ($etime < 1)
				return 'just now';

			$a = array( 12 * 30 * 24 * 60 * 60  =>  'year',
				30 * 24 * 60 * 60       		=>  'month',
				24 * 60 * 60            		=>  'day',
				60 * 60                 		=>  'hour',
				60                      		=>  'minute',
				1                       		=>  'second'
				);

			foreach ($a as $secs => $str)
			{
				$d = $etime / $secs;
				if ($d >= 1)
				{
					$r = round($d);
					$returnstr = $r . ' ' . $str . ($r > 1 ? 's' : '') . ' ago';
					return $returnstr;
				}
			}
		}
	}
?>