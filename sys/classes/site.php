<?php
	/* Reece Benson */
	/* BSc Comp Sci */
	class Site
	{
		// > Variables
		private $sys_info;

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

		public function getSystemInfo($name)
		{
			return $this->sys_info[$name];
		}

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