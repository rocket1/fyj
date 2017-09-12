<?php

/*
 * FILE: globals.php
 *
 * Repository for all site-wide variables.
 * PHP does not import non-SUPERGLOBALS into functions automatically
 * which is why we store our globals in an array that can be easily
 * imported into scope like this: global $site_globals;
 */

$site_globals['TEMPLATE_PATH'] = "{$site_globals['ROOT_PATH']}/templates";
$site_globals['DEFAULT_TEMPLATE'] = "home.template.php";
$site_globals['STOP_LIST'] = "{$site_globals['ROOT_PATH']}/resources/stop_list.txt";
$site_globals['ERR'] = array(); // This holds error msgs.
$site_globals['BAD_FORM_FIELDS'] = array();
$site_globals['LOG_FILE'] = "{$site_globals['ROOT_PATH']}/log/log.txt";
$site_globals['NO_IMAGE_PATH'] = "no_image.gif";

/*
 * Database Vars
*/
////////////////////////////////////////////////////////////////////////////////
$site_globals['DB_HOST']        = "localhost";
$site_globals['DB_USER']        = "jasonfri_cs643";
$site_globals['DB_PASS']        = "password";
$site_globals['DB']             = "jasonfri_findyourjunk";
$site_globals['USER_TABLE']     = "fyj_user";
$site_globals['SCAMS_TABLE']    = "fyj_scams";
$site_globals['ITEMS_TABLE']    = "fyj_item";
$site_globals['MESSAGES_TABLE'] = "fyj_message";
$site_globals['KEYWORDS_TABLE'] = "fyj_keywords";

/*
 * Regular Expressions for Form Validations
*/
$site_globals['REGX_ONLY_ALPHA_NUMERIC'] = "/[0-9a-zA-Z^@*$]/";
$site_gloabls['REGX_ONLY_NUMERIC'] = "/^[0-9]*$/";
$site_globals['REGX_ONLY_ALPHA'] = "/[a-zA-Z]/";
$site_globals['REGX_EMAIL'] = "/^[\w-]+(?:\.[\w-]+)*@(?:[\w-]+\.)+[a-zA-Z]{2,7}$/";
$site_globals['REGX_PASSWORD'] = "";
$site_globals['REGX_LOGIN'] = "";
$site_globals['REGX_ZIP_CODE'] = "/^\d{5}(-\d{4})?$/";
?>
