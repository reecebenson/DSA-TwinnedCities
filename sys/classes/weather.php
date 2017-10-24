<?php
	// > Definements
	define('WINDSPEED',		1); 
	define('WINDDIRECTION',	3); 
	define('TEMPERATURE',	4); 
	define('BAROMETRIC',	6);
	define('TIMEHH',		29); 
	define('TIMEMM',		30); 
	define('STATION',		32); 
	define('SUMMARY',		49); 

	class Weather
	{
		public static function getSite($url, $extra = false)
		{
			return $url . "/clientraw" . ($extra ? 'extra' : '') . ".txt";
		}

		public static function getSites()
		{
			$sites = array(
				"http://www.alvestonweather.co.uk",
				"http://www.uptonmanor.eclipse.co.uk/weatherdisp",
				"http://rosepark.net/WeatherDisplay",
				"http://www.newquayweather.com",
				"http://www.greatbosullow.net",
				"http://www.clevedonweather.co.uk",
				"http://www.paulwilman.com",
				"http://www.martynhicks.uk/weather",
				"http://www.woottonbassettweather.co.uk",
				"http://nw3weather.co.uk"

			);
			return $sites;
		}

		public static function cleanUnderscores($str)
		{
			return str_replace("_", " ", $str);
		}

		public static function degreesToCompass($d)
		{
			$dirs = array('N', 'NE', 'E', 'SE', 'S', 'SW', 'W', 'NW', 'N'); 
			return $dirs[round($d/45)];
		}

		public static function handleWeatherData($site, $data, $type)
		{
			if($type == "html")
			{
				return Weather::buildWeatherHTML($site, $data);
			}
			else if($type == "rss")
			{
				return '
				<channel> 
					<title>' . Weather::cleanUnderscores($data[STATION]) .'</title> 
					<link>' . $site . '</link> 
					<item> 
						<title>Weather at ' . $data[TIMEHH] . ':' . $data[TIMEMM] .'</title> 
						<description>Summary: ' . Weather::cleanUnderscores($data[SUMMARY]) .'. Wind: ' . $data[WINDSPEED] .' knots from ' . Weather::degreesToCompass($data[WINDDIRECTION]) . ' (' . $data[WINDDIRECTION] .' degrees). Temperature: ' . $data[TEMPERATURE] . ' &#0176;C. Barometric: ' . $data[BAROMETRIC] . ' hPa.</description> 
					</item> 
				</channel>';
			}
		}

		public static function buildWeatherHTML($site, $data)
		{
			global $www;

			// > Get our weather template page
			$page = file_get_contents($www . "/sys/templates/weather_html.html");

			// > Get our content from our data
			$cont = 'Summary: ' . Weather::cleanUnderscores($data[SUMMARY]) .'.<br/>Wind: ' . $data[WINDSPEED] .' knots from ' . Weather::degreesToCompass($data[WINDDIRECTION]) . ' (' . $data[WINDDIRECTION] .' degrees).<br/>Temperature: ' . $data[TEMPERATURE] . ' &#0176;C.<br/>Barometric: ' . $data[BAROMETRIC] . ' hPa.';

			// > Fix our page variables
			$page = str_replace("{www}", $www, $page);
			$page = str_replace("{title}", Weather::cleanUnderscores($data[STATION]), $page);
			$page = str_replace("{link}", $site, $page);
			$page = str_replace("{developer}", "Reece Benson", $page);
			$page = str_replace("{content}", $cont, $page);
			$page = str_replace("{graph}", Weather::buildGraphHTML($site), $page);

			// > Return our built webpage
			return $page;
		}

		public static function buildGraphHTML($site)
		{
			global $www;

			// > Get our site's data from extra
			$newSite = Weather::getSite($site, true);
			$fgc = file_get_contents($newSite);
			$data = explode(' ', $fgc);

			// > Get our weather template page
			$page = file_get_contents($www . "/sys/templates/weather_chart.html");

			// > Get our wind data
			$windConstruct = "";
			for($i = 1; $i <= 20; $i++)
				$windConstruct .= "[" . $i . "," . $data[$i] . "],";
			$windData = "[" . rtrim($windConstruct, ",") . "]";

			// > Fix our page variables
			$page = str_replace("{WIND_DATA}", $windData, $page);
			$page = str_replace("{TITLE}", '"' . Weather::cleanUnderscores($data[STATION]) . '"', $page);

			// > Return our built webpage
			return $page;
		}
	}
?>