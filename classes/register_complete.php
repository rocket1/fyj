<?php

class register_complete
{
	function register_complete()
	{
		global $site_globals;
	 	$content = $site_globals['TEMPLATE_PATH']."/register_complete.template.php";
		include $site_globals['TEMPLATE_PATH']."/master.template.php";
    }
}




?>
