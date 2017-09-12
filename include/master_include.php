<?php

$site_globals['ROOT_PATH'] = "..";
$site_globals['INCLUDE_PATH']  = "{$site_globals['ROOT_PATH']}/include";
require("{$site_globals['INCLUDE_PATH']}/globals.php");
require("{$site_globals['INCLUDE_PATH']}/browser_detection.php");
require("{$site_globals['INCLUDE_PATH']}/QueryObject_PHP4.php");
require("{$site_globals['INCLUDE_PATH']}/db_connect.php");
require("{$site_globals['INCLUDE_PATH']}/util_functions.php");
//require("$INCLUDE_PATH/form_functions.php");
require("{$site_globals['INCLUDE_PATH']}/htmlTable_PHP4/htmlTable.php");
require("{$site_globals['INCLUDE_PATH']}/../classes/secure_page.php");
require("{$site_globals['INCLUDE_PATH']}/../classes/tag_cloud.php");
//include_once("$INCLUDE_PATH/userauth.php");
//include_once("$INCLUDE_PATH/third_party/Swift/Swift.php" );
//include_once("$INCLUDE_PATH/third_party/Swift/Swift/Connection/Sendmail.php" );
//include_once("$INCLUDE_PATH/third_party/Swift/Swift/Connection/SMTP.php" );

//print_r($_REQUEST);

?>
