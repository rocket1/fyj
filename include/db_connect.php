<?php

$site_globals['link'] = mysql_connect( $site_globals['DB_HOST'], $site_globals['DB_USER'], $site_globals['DB_PASS'] );

if (!$site_globals['link'])
{
    echo "<span style=\"color:red\">There was an internal error.  Please, do something else now.</span>";
}

mysql_select_db($site_globals['DB']);

?>
