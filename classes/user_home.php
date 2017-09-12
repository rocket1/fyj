<?php

/*
 *  FILE: user_form.php
 *
 * This is the controller class for the public user registration form.
 * It handles both Creation and Update of the user record.
 *
 */

class user_home extends secure_page {

	function user_home()
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

        $this->secure_page();

        // Very important.
		global $site_globals;

		// To eliminate typing...
		$link = $site_globals['link'];

		$_REQUEST['item_type'] == 'found' ? $lost_flag = '1' : $lost_flag = '0';

		$sql = "SELECT item_id,description,item_comment,reward,address,city,state,zip,lost_found_flag,DATE_FORMAT(lost_found_date,'%M %e, %Y') as lfd, DATE_FORMAT(date_posted,'%M %e, %Y') as dp, user_id, photo_path  FROM {$site_globals['ITEMS_TABLE']} WHERE user_id='{$_SESSION['user_id']}' AND lost_found_flag='$lost_flag'";

        $qryObj = new QueryObject($sql, $link);

		// The $content template that will be inserted into and displayed with
		// the master template...
		$content = $site_globals['TEMPLATE_PATH']."/user_home.template.php";
		include $site_globals['TEMPLATE_PATH']."/master.template.php";
	}
}
?>
