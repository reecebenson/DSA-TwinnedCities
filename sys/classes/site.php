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
	}
?>