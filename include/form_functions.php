<?php
//print_r($_REQUEST);
$form_action = $_REQUEST['form_action'];
//echo "FORM ACTION: $form_action";

if( !empty($form_action) )
{
    $missingFields = array();
    $otherErrors = array();
    $otherMsgs = array();

//////////////////////////////////////////////////////////////////////
//                   ADD / EDIT USER
//////////////////////////////////////////////////////////////////////

    if ( $form_action == "ADD_USER" || $form_action == "EDIT_USER" )
    {
        // Checkboxes.
        $changePass = ($_POST['changepassword'] == "on");

        // Username is commented out because we now use email address.
        $login          = makeSafe($_POST['login']);
        $lastName       = makeSafe($_POST['lastname']);
        $firstName      = makeSafe($_POST['firstname']);
        $email          = makeSafe($_POST['email']);
        $pass           = makeSafe($_POST['password']);
        $confirm_pass   = makeSafe($_POST['confirm_pass']);
        $id             = makeSafe($_POST['id']);
        $role           = makeSafe($_POST['user_type']);
        $able_to        = makeSafe($_POST['able_to']);
        
        $fieldMap = array(  "login"    => $login,
                            "lastname"    => $lastName,
                            "firstname"   => $firstName,
                            "email"       => $email,
                            "role" => $role,
                            "pass" => $pass,
                            "able_to" => $able_to
                            );

       // chkValAddErrMsg( $missingFields, $email, $kEMAIL_ADDRESS );

       // if ( !verifyEmail($email) )
       // {
       //     array_push( $otherErrors, $kEMAIL_ADDRESS_NOT_VALID);
       // }

        if ($changePass)
        {
            chkValAddErrMsg( $missingFields, $uPass, $kPASSWORD );
            chkValAddErrMsg( $missingFields, $uConfirmPass, $kCONFIRM_PASSWORD );

            if ( !empty($uPass) && !empty($uConfirmPass) && ($uPass != $uConfirmPass) )
            {
                addErrMsg($otherErrors, $kPASS_CONF_PASS_NOT_MATCH);
            }

            $fieldMap["userpass"] = doEncrypt($userpass);
        }

        if ( empty($missingFields) && empty($otherErrors) )
        {
            if ( $form_action == "ADD_USER" )
            {
                $sql = getInsertStmt( $kUSER_TABLE, $fieldMap );
                $qryObj = new QueryObject( $sql, $link );

                if ( $qryObj && $qryObj->errno == 0 )
                {
                   // AddSuccessMsg( $kADD_USER_SUCCESS );
                   
                   if ( $role == "FELLOW" )
                   {
                       $user_id = mysql_insert_id($link);
                       $sql = "INSERT INTO $kFELLOWS_STATIC (user_id) VALUES('$user_id')";
                       $qryObj = new QueryObject($sql, $link);
                   }
                   
                   header( "location: ?page=VIEW_USERS" );
                }
                else if ($qryObj)
                {
                    switch ($qryObj->errno)
                    {
                        case 1062:
                            AddErrMsg( $otherErrors, $kUSER_ALREADY_EXISTS );
                            break;

                        default:
                            AddErrMsg( $otherErrors, $kUNKNOWN_ERROR );
                            break;
                    }
                }
                else
                {
                    log_error();
                }
            }
            else if ( $form_action == "EDIT_USER" )
            {
                $sql = getUpdateStmt( $db_usertable, $fieldMap );
                $sql .= " WHERE userID='$id'";
                $qryObj = new QueryObject( $sql, $link );

                if ( $qryObj && $qryObj->errno == 0 )
                {
                    AddSuccessMsg($kUSER_UPDATE_SUCCESS);
                }
                else if ($qryObj)
                {
                    switch ($qryObj->errno)
                    {
                        default:
                            AddErrMsg( $otherErrors, $kUNKNOWN_ERROR );
                            break;
                    }
                }
                else
                {
                    log_error();
                }
            }
        }
    }
    /////////////////////////////////////////////////////////////////
    // Update Static Page
    /////////////////////////////////////////////////////////////////
    if ( $form_action == "EDIT_STATIC" )
    {
        $admin_text_area   = $_REQUEST['admin_text_area'];
        $page_to_overwrite = $static_pages[$_REQUEST['target']][1];

        if ( file_exists($page_to_overwrite) )
        {
           $fd = fopen($page_to_overwrite, "w");
           fwrite( $fd, $page_to_overwrite );
        }
    }
    /////////////////////////////////////////////////////////////////
    // DELETE
    /////////////////////////////////////////////////////////////////
    else if ( $form_action == "DELETE_ARTICLE"
                || $form_action == "DELETE_EVENT"
                || $form_action == "DELETE_PRESS"
                || $form_action == "DELETE_IMAGE"
                || $form_action == "DELETE_LINK" 
                || $form_action == "DELETE_USER"
                || $form_action == "DELETE_VIDEO"
                || $form_action == "DELETE_AUDIO"
                || $form_action == "DELETE_TODO" )
    {
        require_once( "db_connect.php" );

        if ( $form_action == "DELETE_ARTICLE" )
        {
			$dataTable = $kNEWS_TABLE;
            $return_page = "VIEW_ARTICLES";
        }
        else if ($form_action == "DELETE_EVENT")
        {
            $dataTable = $kEVENTS_TABLE;
            $return_page = "VIEW_EVENTS";
        }
        else if ($form_action == "DELETE_PRESS")
        {
			$dataTable = $kNEWS_TABLE;
            $return_page = "VIEW_ARTICLES&article_type=PRESS";
        }
        else if ($form_action == "DELETE_IMAGE")
        {
			$dataTable = $kFELLOWS_PHOTOS;
            $return_page = "VIEW_PHOTOS";
        }
        else if ($form_action == "DELETE_LINK")
        {
			$dataTable = $kFELLOWS_LINKS;
            $return_page = "VIEW_LINKS";
        }
        else if ($form_action == "DELETE_USER")
        {
			$dataTable = $kUSER_TABLE;
            $return_page = "VIEW_USERS";
        }
        else if ($form_action == "DELETE_VIDEO")
        {
			$dataTable = $kFELLOWS_VIDEO;
            $return_page = "VIEW_VIDEOS";
        }
        else if ($form_action == "DELETE_TODO")
        {
			$dataTable = $kFELLOWS_TODO;
            $return_page = "VIEW_TODO";
        }
        else if ($form_action == "DELETE_AUDIO")
        {
			$dataTable = $kFELLOWS_AUDIO;
            $return_page = "VIEW_AUDIO";
            
            $sql = "SELECT path FROM $kFELLOWS_AUDIO WHERE id IN(";
            $ids = split( ":", $_REQUEST['id'] );
            $sql .= implode( ",", $ids );
            $sql .= ")";
            
            $qryObj = new QueryObject( $sql, $link );
            
            while ($row = mysql_fetch_object($qryObj->result))
            {
                $path = "fellows/mp3/{$row->path}";
                file_exists($path) && unlink($path);
            }
        }

        mysql_select_db($kDB);
		$sql = "DELETE FROM $dataTable WHERE id IN (";
		$ids = split( ":", $_REQUEST['id'] );
        $sql .= implode( ",", $ids );
        $sql .= ")";

        //echo $sql;

        $qryObj = new QueryObject( $sql, $link );

        if ( $qryObj && $qryObj->errno == 0 )
        {
            header( "location: ?page=$return_page" );
        }
    }
    /////////////////////////////////////////////////////////////////
    // PUBLISH
    /////////////////////////////////////////////////////////////////
    else if ( $form_action == "PUBLISH_ARTICLE" || $form_action == "UNPUBLISH_ARTICLE"
                || $form_action == "PUBLISH_EVENT" || $form_action == "UNPUBLISH_EVENT"
                || $form_action == "PUBLISH_PRESS" || $form_action == "UNPUBLISH_PRESS"
                )
    {
        require_once( "db_connect.php" );

        if ( $form_action == "PUBLISH_ARTICLE" || $form_action == "UNPUBLISH_ARTICLE" )
        {
			$dataTable = $kNEWS_TABLE;
            $form_action == "PUBLISH_ARTICLE" ? $set_val = '1' : $set_val = '0';
            $return_page = "VIEW_ARTICLES";
		}

        if ( $form_action == "PUBLISH_PRESS" || $form_action == "UNPUBLISH_PRESS" )
        {
			$dataTable = $kNEWS_TABLE;
            $form_action == "PUBLISH_PRESS" ? $set_val = '1' : $set_val = '0';
            $return_page = "VIEW_ARTICLES&article_type=PRESS";
		}

        if ( $form_action == "PUBLISH_EVENT" || $form_action == "UNPUBLISH_EVENT" )
        {
			$dataTable = $kEVENTS_TABLE;
            $form_action == "PUBLISH_EVENT" ? $set_val = '1' : $set_val = '0';
            $return_page = "VIEW_EVENTS";
		}

        mysql_select_db($kDB);
		$sql = "UPDATE $dataTable SET PUBLISH='$set_val' WHERE id IN (";
		$ids = split( ":", $_REQUEST['id'] );
        $sql .= implode( ",", $ids );
        $sql .= ")";

        //echo $sql;

        $qryObj = new QueryObject( $sql, $link );

        if ( $qryObj && $qryObj->errno == 0 )
        {
            header( "location: ?page=$return_page" );
        }
    }
    ///////////////////////////////////////////////////////////////////////////////////////////////////////
//                                        UPLOAD PHOTO
///////////////////////////////////////////////////////////////////////////////////////////////////////

	else if( $form_action == "UPLOAD_PHOTO" )
    {
		$img_tmp_name = $HTTP_POST_FILES['upload_img']['tmp_name'];
		$img_name     = $HTTP_POST_FILES['upload_img']['name'];
		$img_size     = $HTTP_POST_FILES['upload_img']['size'];
		$img_type     = $HTTP_POST_FILES['upload_img']['type'];
		$img_error    = $HTTP_POST_FILES['upload_img']['error'];

		if( trim($img_name) == "" )
            header( "location: admin_index.php?page=IMAGE_UPLOAD&msg=uploadfailure" );

		if( $img_type != "image/jpeg" && $img_type != "image/gif" )
            header( "location: admin_index.php?page=IMAGE_UPLOAD&msg=uploadfailure_imgtype" );


		$img_path = "uploaded_images/$img_name";

		if( is_uploaded_file($img_tmp_name)) {
			if(!move_uploaded_file($img_tmp_name, $img_path)) {
                header( "location: admin_index.php?page=IMAGE_UPLOAD&msg=uploadfailure" );
			}
		}

		//$sql = "INSERT INTO $photo_table (photo_name, caption) VALUES ('{$userfile_big_name}','{$caption}')";
		//mysql_query($sql) or die( mysql_error($link) );

	//	echo createRedirect("../index.php?d=forms/upload_photo.php&message=uploadSuccessful");

        header( "location: admin_index.php?page=IMAGE_UPLOAD&msg=uploadsuccess" );

	}

///////////////////////////////////////////////////////////////////////////////////////////////////////
//                                        DELETE PHOTO
///////////////////////////////////////////////////////////////////////////////////////////////////////

	else if( $form_action == "DELETE_PHOTO" ) {

		if( unlink( "../../uploads/photos/thumbnails/{$imgThumb}")
			&& unlink( "../../uploads/photos/bigimages/{$imgBig}" ) )
		{
			echo createRedirect("../index.php?d=forms/edit_photos.php&message=goodFileDelete");
		} else {
			echo createRedirect("../index.php?d=forms/edit_photos.php&message=failedFileDelete");
		}

		$sql = "DELETE FROM photo_thepeels WHERE photo_name='{$imgBig}'";
		mysql_query($sql) or die( mysql_error($link) );
	}

    else if($form_action == "FELLOW_UPLOAD_PHOTO")
    {

        if ($_FILES['upload_img']['size'] > 0)
        {
            $fileName = $_FILES['upload_img']['name'];
            $tmpName  = $_FILES['upload_img']['tmp_name'];
            $fileSize = $_FILES['upload_img']['size'];
            $fileType = $_FILES['upload_img']['type'];

            $newName = "fellows/fellow_photos/$fileName";

            if ($fileType == 'image/jpeg' && rename("$tmpName", $newName) )
            {
            	$fileName = addslashes($fileName);
            	$sql = "INSERT INTO $kFELLOWS_PHOTOS (user_id, file_name, file_size, file_type) VALUES ('$user_id','$fileName', '$fileSize', '$fileType')";
            	$qryObj = new QueryObject($sql, $link);
                  
	            if ( $qryObj->errno != 0 )
                    AddErrMsg( "SIX POINTS: Error uploading photo." );

                $img_id = mysql_insert_id($link);

            	// creating thumbnail
            	$g_is=getimagesize($newName);
            	$img_src=imagecreatefromjpeg($newName);
            	$img_dst=imagecreatetruecolor(128,133);
            	imagecopyresampled($img_dst, $img_src , 0, 0, 0, 0, 128, 133, $g_is[0], $g_is[1]);
            	ob_start(); // start a new output buffer
            	imagejpeg( $img_dst, NULL, 100 );
            	$img_data = ob_get_contents();
            	ob_end_clean; // stop this output buffer

            	$thumb_name = "fellows/thumbs/{$user_id}.{$img_id}.jpg";
            	$fd = fopen($thumb_name, "w");
                fwrite( $fd, $img_data );
                fclose($fd);
                header( "Location: ?page=VIEW_PHOTOS" );
            }
        }
    }
    
    else if($form_action == "UPLOAD_AUDIO")
    {

        
        if ($_FILES['upload_file']['size'] > 0)
        {
            
            $fileName = $_FILES['upload_file']['name'];
            $tmpName  = $_FILES['upload_file']['tmp_name'];
            $fileSize = $_FILES['upload_file']['size'];
            $fileType = $_FILES['upload_file']['type'];
            $title = $_REQUEST['title'];
            $fp      = fopen($tmpName, 'r');
            $content = fread($fp, filesize($tmpName));
            fclose($fp);
            $fileName = addslashes($fileName);
            $sql = "INSERT INTO $kFELLOWS_AUDIO (user_id, title, path) VALUES ('$user_id', '$title', '$fileName')";
            $qryObj = new QueryObject($sql, $link);
            $fname = "fellows/mp3/$fileName";
            $fd = fopen($fname, "w");
            fwrite( $fd, $content );
            
            if ( $qryObj->errno != 0 )
            {
                AddErrMsg( $otherErrors, "Error uploading audio." );
            }
            
            header( "Location: ?page=VIEW_AUDIO" );
        }
    }

    else if($form_action == "EDIT_IMAGE")
    {
        $id = $_REQUEST['id'];
        $image_title = trim(addslashes($_REQUEST['image_title']));
        $caption = trim(addslashes($_REQUEST['caption']));
        $comment = trim(addslashes($_REQUEST['comment']));

        $fieldMap = array( "image_title" => $image_title,
                           "caption" => $caption,
                           "comment" => $comment );

        $sql = getUpdateStmt( $kFELLOWS_PHOTOS, $fieldMap );
        $sql .= " WHERE id='$id'";
        $qryObj = new QueryObject( $sql, $link );

        if ( $qryObj && $qryObj->errno == 0 )
        {
            header( "Location: ?page=VIEW_PHOTOS" );
        }
        else if ($qryObj)
        {
            switch ($qryObj->errno)
            {
                case 1062:
                    AddErrMsg( $otherErrors, $kUSER_ALREADY_EXISTS );
                    break;

                default:
                    AddErrMsg( $otherErrors, $kUNKNOWN_ERROR );
                    break;
            }
        }
        else
        {
            log_error();
        }
    }
    else if ($form_action == "SAVE_RESUME")
    {
        $edit_textarea = addslashes($_REQUEST['edit_textarea']);
        $linebreaks = $_REQUEST['linebreaks'] == "on";

        if (is_extant($edit_textarea))
        {
            $sql = "UPDATE $kFELLOWS_STATIC SET resume='$edit_textarea'";

            if ($linebreaks)
            {
                $sql .= ",resume_linebreaks='1'";
            }

            $sql .= " WHERE user_id='$user_id'";
            $qryObj = new QueryObject($sql, $link);
        }

    }

    else if ($form_action == "SAVE_STATEMENT")
    {
        $edit_textarea = addslashes($_REQUEST['edit_textarea']);
        $linebreaks = $_REQUEST['linebreaks'] == "on";

        if (is_extant($edit_textarea))
        {
            $sql = "UPDATE $kFELLOWS_STATIC SET artist_statement='$edit_textarea'";

            if ($linebreaks)
            {
                $sql .= ",artist_statement_linebreaks='1'";
            }

            $sql .= " WHERE user_id='$user_id'";
            $qryObj = new QueryObject($sql, $link);
        }

    }
    else if ($form_action == "ADD_FELLOWS_LINK")
    {
        $link_url = addslashes($_REQUEST['link_url']);
        $link_desc = addslashes($_REQUEST['link_desc']);
        
        $sql = "INSERT INTO $kFELLOWS_LINKS (user_id, url, link_text) VALUES ('$user_id', '$link_url','$link_desc')";
        $qryObj = new QueryObject($sql, $link);
        header( "Location: ?page=VIEW_LINKS" );
    }
    else if( $form_action == "ADD_VIDEO" )
    {
         $html = addslashes($_REQUEST['html']);
         $title = addslashes($_REQUEST['title']);
         $comment = addslashes($_REQUEST['comment']);
         
         $formFields = array( "html" => $html,
                      "title" => $title,
                      "comment" => $comment,
                      "user_id" => $user_id );
                      
         $sql = getInsertStmt($kFELLOWS_VIDEO,  $formFields);
         $qryObj = new QueryObject($sql, $link);
         header( "location: ?page=VIEW_VIDEOS" );
    }
    else if( $form_action == "SAVE_CONTACT" )
    {
         $contact = addslashes($_REQUEST['edit_textarea']);
         
         $formFields = array( "contact" => $contact );
                      
         $sql = getUpdateStmt($kFELLOWS_USER,  $formFields);
         $sql .= "UPDATE $kUSER_TABLE SET contact='$contact' WHERE id='$user_id'";
        // echo $sql;
         $qryObj = new QueryObject($sql, $link);
         header( "location: ?page=CONTACT" );
    }
}

?>
