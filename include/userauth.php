<?
//
// Routines for authenticating the current user.
//

// Does the page require authorized access?
$errorMsg = "";
$DEBUG = 0;
function authorizeUser()
{	echo "HERE";
    global $errorMsg, $DEBUG,  $DB_HOST, $DB_USER, $DB_PASS, $kDB , $kUSER_TABLE;

    $loginFailed = "Login Failed. Check your username and password.";
    $internalError = "There was an internal error.";

    // Did we pass in a username and password with the session?
    if (!$_SESSION['Username'] || !$_SESSION['Password'])
    {
        // Start by checking the username
        if ($_POST['login'])
        {
            // If we have a username stuff it into the session for validation.
            $_SESSION['Username'] = addslashes($_POST['login']);
        }
        else
        {
            // If no username, then set it to NULL and force a login.
            $_SESSION['Username'] = NULL;
            if($DEBUG == 1) echo "USERNAME WAS NULL";
            echo "29";

            return false;
        }
/*
        // On to the password
        if ($_POST['pass'])
        {
            // If we have a password, stuff it in the session for validation.
            $_SESSION['Password'] = $_POST['pass'];//doEncrypt($_POST['pass']);
        }
        else {
            // If no password, then set it to NULL and for a login.
            $_SESSION['Password'] = NULL;
            $errorMsg = "Please enter a valid Password";
            if($DEBUG == 1) echo "PASSWORD WAS NULL";

            return false;
        }*/
    }

    // Connect to the database server or bust!  See globals.inc for login
    $db_connect = mysql_connect($DB_HOST, $DB_USER, $DB_PASS);

    if( !$db_connect )
    {
        $errorMsg = $internalError;
        if($DEBUG == 1) echo "DATABASE CONNECTION ERROR";
        return false;
    }

    // Select the correct database.  See globals.php for the details
    $db_select = mysql_select_db($kDB, $db_connect);
    if( !$db_select )
    {
        $errorMsg = $internalError;
        if($DEBUG == 1) echo "DATABASE SELECTION ERROR";
        return false;
    }
    $sql = "SELECT count(id) FROM $kUSER_TABLE WHERE login='$_SESSION[Username]'";
   // $sql = "SELECT count(id) FROM $kUSER_TABLE WHERE pass='$_SESSION[Password]' AND login='$_SESSION[Username]'";

  //  echo $sql;

    // Make the initial query for valid username and password
   $db_result = mysql_query($sql);

  // echo $sql;


    if( !$db_result )
    {
        $errorMsg = $internalError;
        if($DEBUG == 1) echo "FINDING VALID RECORD FOR USERNAME AND PASSWORD FAILED(1): $db_result";
        echo "invalid Password84";
        return false;
    }

    // How many results did we get back (it should never be more than 1)
    $num = mysql_result($db_result, 0);

    // If no results were returned then the user is not valid
    if ($num == 0)
    {
        $errorMsg = $loginFailed;
        echo "ERROR: 93";
        destroySession();

        if($DEBUG == 1) echo "FINDING VALID RECORD FOR USERNAME AND PASSWORD FAILED(2): $db_result";

        return false;
    }
    else if (!$num == 0)
    {
        // We should have one result back but just in case, handle any #
        // In any event, if we get here, weve got a valid user and password
        // validiate security levels and name and directory paths
        $db_result = mysql_query ("SELECT * FROM $kUSER_TABLE WHERE login='{$_SESSION['Username']}'");
        //$db_result = mysql_query ("SELECT * FROM $kUSER_TABLE WHERE pass='{$_SESSION['Password']}' AND login='{$_SESSION['Username']}'");

        if ( !$db_result )
        {
            $errorMsg = $internalError;
            destroySession();

            if($DEBUG == 1) echo "FINDING VALID RECORD FOR USERNAME AND PASSWORD FAILED(3): $db_result";
            return false;
        }

        // How many records did we get back, better be just one, like before!
        $num = mysql_affected_rows($db_connect);

        if ($num == 0)
        {
            $errorMsg = $loginFailed;
            // We be screwed, we didnt get any data back!
            destroySession();
echo "127";
            if($DEBUG == 1) echo "FINDING VALID RECORD FOR USERNAME AND PASSWORD FAILED(4): $db_result";
            return false;
        }
        else if ($num == 1)
        {
            // We got exactly one result!  Thats a good thing!
            // Stuff the results into the session for use later.
            $db_row = mysql_fetch_object($db_result);

            $_SESSION['userType'] = $db_row->role;
            $_SESSION['user_id'] = $db_row->id;
            $_SESSION['able_to'] = $db_row->able_to;
            $_SESSION['firstname'] = $db_row->firstname;
            mysql_free_result($db_result);
        }
        else
        {
            // Oops.  Too many results came back!
            $errorMsg = $loginFailed;;
            destroySession();
			echo "ERROR Logging in. 148";
            return false;
        }

        $_SESSION['isAuthorized'] = 1;

        mysql_close($db_connect);
        return true;
    }

    // A username and password were in this session, so these
    // person is assumed to be authorized (bad assumption?)
	return true;
}

$_SESSION['isAuthorized'] = authorizeUser();

// This person is authorized!!

// If there is a support record ID set, then they
// are to be redirected to the support record with that ID.

if ($_SESSION['isAuthorized'] != 0 && $_SESSION['userType'] == "ADMIN" )
{
    header("Location: admin_index.php");
}

if ($_SESSION['isAuthorized'] != 0 && $_SESSION['userType'] == "FELLOW" )
{
    header("Location: fellows_admin_index.php");
}

?>


