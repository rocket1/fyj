<?php

/*
 *  FILE: scam_form.php
 *
 * This is the controller class for the public scam reporting form.
 * It handles Creation of scam report.
 *
 */

class scam_form {

	function scam_form()
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
	     * 	query_was_good( $QueryObject_instance )    <- will not blowup on null ref
	     *  query_has_results( $QueryObject_instance ) <- will not blowup on null ref
	     *
	     */

		// Very important.
		global $site_globals;

   		// To eliminate typing...
		$link = $site_globals['link'];

		// Are we submitting a form?
		$do_create = array_key_exists('scam_comment', $_POST );

		if ($do_create)
		{
		
			//echo "dsfgdsfg";die;
			// Retrieve and sanitize the form data.
			$scam_comment = make_safe($_POST['scam_comment']);

			// We may have errors but lets go ahead a begin the db field-value map now.
			// This associative array is passed to the function create_insert_stmt or

			if (!is_extant($scam_comment))
				add_err('<span style="color:red">Please enter something in the comment field.</span>');
			
			if (!errors_exist())
			{
				// create_update_stmt to auto-generate the right SQL statement.
				$field_map = array( 'scam_comment' => $scam_comment, 'item_id'=>$_REQUEST['item_id'] );
				$sql = get_insert_stmt($site_globals['SCAMS_TABLE'], $field_map);
				$qry_obj = new QueryObject($sql, $link);
				
				header( "location: ?c=scam_complete" );
			}
		}

        // Get the item description for the page title...
        $sql = "SELECT description FROM {$site_globals['ITEMS_TABLE']} WHERE item_id='{$_REQUEST['item_id']}'";
        $qry_obj = new QueryObject($sql, $link);
        $row = mysql_fetch_row($qry_obj->result);
        $desc = $row[0];
        
        // The $content template that will be inserted into and displayed with
		// the master template...
		$content = $site_globals['TEMPLATE_PATH']."/scam_form.template.php";
		include $site_globals['TEMPLATE_PATH']."/master.template.php";
	}
}
?>
