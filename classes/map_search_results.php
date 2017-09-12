<?php

/*
 *  FILE: map_search_results.php
 *
 */

class map_search_results {

	function map_search_results()
	{
	    /*
	     *  The following functions are defined in /include/util_functions.php
	     *
	     * 	is_extant( $some_value )
	     * 	make_safe( $some_value )
	     *  verify_phone( $phone_number )
	     * 	add_err( $some_error_message )
	     * 	chk_regx_add_err( $value_to_check, $error_message_on_failure, $regular_expression )
	     * 	get_insert_stmt( $table_name, $sql_statement )
	     * 	get_update_stmt( $table_name, $sql_statement )
	     * 	errors_exist()
	     *
	     * The following are defined in /include/QueryObject_PHP4.php
	     *
	     * 	new QueryObject( $sql_statement, $database_link );
	     * 	query_was_good( $QueryObject_instance )    <- wont blowup on null ref
	     *  query_has_results( $QueryObject_instance ) <- wont blowup on null ref
	     *
	     */

		// Very important.
		global $site_globals;

		// To eliminate typing...
		$link = $site_globals['link'];
		
		$zip = $_REQUEST['zip_code'];
		
		$lost_found_flag = $_REQUEST['lost_found_flag'] == '1' ? '1' : '0';
		
		$sql = "SELECT item_id,description,item_comment,reward,address,city,state,zip,lost_found_flag,email,phone,phone2, DATE_FORMAT(lost_found_date,'%M %e, %Y') as lfd, DATE_FORMAT(date_posted,'%M %e, %Y') as dp, photo_path, {$site_globals['ITEMS_TABLE']}.user_id FROM {$site_globals['ITEMS_TABLE']},{$site_globals['USER_TABLE']} WHERE zip='$zip' AND lost_found_flag='$lost_found_flag' AND item_id IN(SELECT item_id FROM {$site_globals['KEYWORDS_TABLE']} WHERE keyword='{$_REQUEST['item']}') AND {$site_globals['ITEMS_TABLE']}.user_id = {$site_globals['USER_TABLE']}.user_id";
		$qry_obj = new QueryObject($sql, $link);
		$lf = $_REQUEST['item_type'] == 'found' ? "Found" : "Lost";
		
		while ($row = mysql_fetch_object($qry_obj->result))
		{
			$divs_html .= gen_item_card( $row, true );
			//$links_html .= "{$row->date_posted}<br /><a style=\"text-decoration:none;font-size:14pt\" href=\"javascript:show_div({$row->item_id})\">{$row->description}</a><br><br>";
		
			$photo_path = is_extant($row->photo_path) ? $row->photo_path : $site_globals['NO_IMAGE_PATH'];
			$d = addslashes($row->description);
			$js_cmds .= "showAddress('{$row->address} {$row->city} {$row->state} {$row->zip}', '<table cellspacing=\"0\" border=\"0\" cellpadding=\"5\"><tr><td style=\"font-size:11px\">{$d}<br><br>{$row->address}<br>{$row->city}, {$row->state} {$row->zip}<br><br><a href=\"javascript: show_div(\'{$row->item_id}\')\">details</a></td><td align=\"right\"><img width=\"100\" height=\"125\" src=\"item_photos/$photo_path\" border=\"0\"></td></tr></table>');";
		}

		// The $content template that will be inserted into and displayed with
		// the master template...
		$content = $site_globals['TEMPLATE_PATH']."/map_search_results.template.php";
		include $site_globals['TEMPLATE_PATH']."/master.template.php";
	}
}
?>
