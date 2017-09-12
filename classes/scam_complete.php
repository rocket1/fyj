<?php

class scam_complete
{
	function scam_complete()
	{
		global $site_globals;
	 	$content = $site_globals['TEMPLATE_PATH']."/scam_complete.template.php";
		include $site_globals['TEMPLATE_PATH']."/master.template.php";
    }
}




?>
