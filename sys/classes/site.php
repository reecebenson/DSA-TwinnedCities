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

			// > Grab all data
			$statement = $db->prepare("SELECT `name`, `value` FROM `system_information`");
			$statement->execute();
			$statement->bind_result($name, $val);

			// > Store data
			while($statement->fetch())
			{
				$this->sys_info[$name] = $val;
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
		 * Convert a unix timestamp into a readable "time ago" format:
		 * 'just now', 'x seconds ago', 'x minutes ago', etc...
		 *
		 * @param int $ptime A unix timestamp
		 *
		 * @return string The "time ago" string
		 */
		public function timeago($ptime) {
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