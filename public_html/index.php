<?php

/* index.php
 *
 * This is the main controller for findyourjunk.com
 * All requests are funnelled here.
 */

session_start();
error_reporting(E_ERROR | E_WARNING | E_PARSE);
include( "../include/master_include.php");

$default_classname = "home";
$default_classpath = "../classes/$default_classname.php";

// Process login credentials.
authorize_user();
write_log("IS AUTHORIZED: ".$_SESSION['is_authorized']);
// Do they want to logout?
$_REQUEST['logout'] && do_logout();

// If someone has just attempted log in then
// we want to send them to their user home page
// as the default

if (is_extant($_POST['login']) && is_extant($_POST['pass']) && $_SESSION['is_authorized'])
{
    header("Location: ?c=user_home");
}
else
{
    // Get classname from URL.
    // e.g. www.example.com?c=contact
    $classname = $_REQUEST['c'];
    $classpath = "../classes/$classname.php";
}

is_extant($classname) && is_readable($classpath) && @include($classpath);

if (class_exists($classname))
	$instance = new $classname();
else
{
	@include( $default_classpath );
	$instance = new $default_classname;
}

?>
