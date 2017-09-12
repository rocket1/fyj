<?php

/*
 *  FILE: user_form.php
 *
 * This is the controller class for the public user registration form.
 * It handles both Creation and Update of the user record.
 *
 */

class user_form {

	function user_form()
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

		// This page is "Create" user by default.
		// If a user_id is present, though this page
		// becomes an "Update" page.
		$user_id = $_REQUEST['user_id'];
		$is_update_form = is_extant($user_id);

		// Are we submitting a form?
		$do_create = array_key_exists('firstname', $_POST );

		// Do an UPDATE instead of an INSERT if this
		// page is both a form submission and an update form.
		$do_update = $do_create && $is_update_form;

		if ($do_create)
		{
			// Retrieve and sanitize the form data.
			$firstname = make_safe($_POST['firstname']);
			$lastname  = make_safe($_POST['lastname']);
			$email = make_safe($_POST['email']);
			$phone = make_safe($_POST['phone']);
			$phone2 = make_safe($_POST['phone2']);
			$login = make_safe($_POST['login']);

			// Check to make sure the screen name (login) doesnt
			// already exist.
			if ( is_extant($login) )
			{
				$sql = "SELECT COUNT(*) FROM {$site_globals['USER_TABLE']} WHERE login='$login'";
				$qry_obj = new QueryObject($sql, $link);
				!query_has_results($qry_obj) && add_err("That Screen Name is already taken.");
			}

			// We may have errors but lets go ahead a begin the db field-value map now.
			// This associative array is passed to the function create_insert_stmt or
			// create_update_stmt to auto-generate the right SQL statement.
			$field_map = array( 'firstname' => $firstname,
			                    'lastname' => $lastname,
			                    'email' => $email,
			                    'login' => $login );

			if ( is_extant($phone) )
			{
				if ($phone = verify_phone($phone))
					$field_map['phone'] = $phone;
				else
					add_err("phone");
			}

			if ( is_extant($phone2) )
			{
				if ($phone2 = verify_phone($phone2))
					$field_map['phone2'] = $phone2;
				else
					add_err("phone2");
			}

			// if do_update, then set $c_pwd to the checkbox value.
			$do_update && ($c_pwd = $_POST['c_pwd'] == "on");

			// Validate the form data.
			chk_regx_add_err($firstname, "firstname", $site_globals['REGX_ONLY_ALPHA']);
			chk_regx_add_err($lastname, "lastname", $site_globals['REGX_ONLY_ALPHA']);
			chk_regx_add_err($login, "login", $site_globals['REGX_LOGIN']);
			chk_regx_add_err($email, "email", $site_globals['REGX_EMAIL']);

			if ( $do_create || ($do_update && $c_pwd) )
			{
				$password = make_safe($_POST['password']);
				$confirm_password = make_safe($_POST['confirm_password']);

				chk_regx_add_err($password, "password", $site_globals['REGX_PASSWORD']);
				chk_regx_add_err($confirm_password, "confirm_password", $site_globals['REGX_PASSWORD']);

				$password != $confirm_password ? add_err("Password and Confirm Password did not match.")
				                               : $field_map['password'] = md5($password);
			}

			if (!errors_exist())
			{
			    if ($do_create)
			    {
					$sql = get_insert_stmt($site_globals['USER_TABLE'], $field_map);
			    	$qry_obj = new QueryObject($sql, $link);
					query_was_good($qry_obj) && header("location: ?c=register_complete");
			    }
			    else if ($do_update)
			    {
			  		$sql = get_update_stmt($site_globals['USER_TABLE'], $field_map);
			  		$sql = " WHERE user_id='$user_id'"; // VERY important!
					$qry_obj = new QueryObject($sql, $link);
					query_was_good($qry_obj) && header("location: ?c=user_form");
			    }
			}
		}

		// The $content template that will be inserted into and displayed with
		// the master template...
		$content = $site_globals['TEMPLATE_PATH']."/user_form.template.php";
		include $site_globals['TEMPLATE_PATH']."/master.template.php";
	}
}
?>
