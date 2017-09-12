<?php

class home {
	
	function home()
	{
		global $site_globals;
		$content = $site_globals['TEMPLATE_PATH']."/home.template.php";
		include $site_globals['TEMPLATE_PATH']."/master.template.php";
	}
}

?>
