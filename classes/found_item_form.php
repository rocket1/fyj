<?php

/*
 *  FILE: found_item_form.php
 *
 * This is the controller class for the public user registration form.
 * It handles both Creation and Update of the user record.
 *
 */

class found_item_form extends secure_page {

	function found_item_form()
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

		$this->secure_page();

		// Very important.
		global $site_globals;

   		// To eliminate typing...
		$link = $site_globals['link'];

		// This page is "Create" found item by default.
		// If a item_id is present, though this page
		// becomes an "Update" page.
		$item_id = $_REQUEST['item_id'];
		$is_update_form = is_extant($item_id);

		if ($is_update_form)
			$form_title = "Update found item";
		else
		 	$form_title = "Post Found Item";

		// Are we submitting a form?
		$do_create = array_key_exists('address', $_POST );

		// Do an UPDATE instead of an INSERT if this
		// page is both a form submission and an update form.
		$do_update = $do_create && $is_update_form;

		if ($do_create)
		{
			// Retrieve and sanitize the form data.

			$description = make_safe($_POST['description']);
			$item_comment= make_safe($_POST['item_comment']);
			$address = make_safe($_POST['address']);
			$city = make_safe($_POST['city']);
			$state = make_safe($_POST['state']);
			$zip = make_safe($_POST['zip']);
			$user_id = $_SESSION['user_id'];
			$month = make_safe($_POST['month']);
			$day = make_safe($_POST['day']);
			$year = make_safe($_POST['year']);
			$found_day = make_safe($_POST['found_day']);
			$found_month = make_safe($_POST['found_month']);
			$found_year = make_safe($_POST['found_year']);
			$lost_found_date = "{$found_year}-{$found_month}-{$found_day}";
			$now_toks = getdate(mktime());
			$now = "{$now_toks['year']}-{$now_toks['mon']}-{$now_toks['mday']}";

			// We may have errors but lets go ahead a begin the db field-value map now.
			// This associative array is passed to the function create_insert_stmt or
			// create_update_stmt to auto-generate the right SQL statement.
			$field_map = array( 'description' => $description,
								'address' => $address, //**removed 'tags' addition to map
			                    'city' => $city,
			                    'state' => $state,
			                    'zip' => $zip,
			                    'user_id' => $user_id,
			                    'item_comment' => $item_comment,
			                    'lost_found_flag' => '1',
			                    'lost_found_date' => $lost_found_date,
			                    'date_posted' => $now );

			// Validate the form data.
			chk_regx_add_err($description, "description", '');
			chk_regx_add_err($address, "address", $site_globals['REGX_ONLY_ALPHA_NUMERIC']);
			chk_regx_add_err($city, "city", $site_globals['REGX_ONLY_ALPHA']);
			chk_regx_add_err($state, "state", $site_globals['REGX_ONLY_ALPHA']);
			chk_regx_add_err($zip, "zip", $site_globals['REGX_ZIP_CODE']);

			$img_tmp_name = $_FILES['upload_img']['tmp_name'];
			$img_name     = $_FILES['upload_img']['name'];
			$img_size     = $_FILES['upload_img']['size'];
			$img_type     = $_FILES['upload_img']['type'];
			$img_error    = $_FILES['upload_img']['error'];

			if ( is_extant(trim($img_tmp_name)) )
			{
				if(  $img_type != "image/jpeg" && $img_type != "image/gif" && $img_type != "image/pjpeg")
				{
					add_err("Image must be a .jpg or .gif file.");
				}
				else
				{
					$img_path = "item_photos/$img_name";

					if (is_uploaded_file($img_tmp_name))
					{
						if (!move_uploaded_file($img_tmp_name, $img_path))
						{
							add_err("Saving file failed.  Contact webmaster.");
						}
						else
						{
							$field_map['photo_path'] = $img_name;
						}
					}
				}
			}

			if (!errors_exist())
			{
			    if ($do_create)
			    {
					$sql = get_insert_stmt($site_globals['ITEMS_TABLE'], $field_map);
			    	$qry_obj = new QueryObject($sql, $link);

					$item_id = mysql_insert_id($link);

					// Add to the keywords table.

					$desc_toks = get_keywords(array('parse_text'=>$description));

					foreach ($desc_toks as $dt)
					{
						$sql = "INSERT INTO {$site_globals['KEYWORDS_TABLE']} (keyword,item_id) VALUES ('$dt', '$item_id')";
						new QueryObject($sql, $link);
					}

					query_was_good($qry_obj) && header("location: ?c=user_home");
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
		$content = $site_globals['TEMPLATE_PATH']."/found_item_form.template.php";
		include $site_globals['TEMPLATE_PATH']."/master.template.php";
	}
}
?>
