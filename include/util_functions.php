<?php

/*
 * FILE: util_functions.php
 *
 * Repository for global utility funcitons.
 *
 */

function write_log( $msg )
{
    global $site_globals;

    $seperator = "----------------------------------------------------------------";

    $fd = fopen( $site_globals['LOG_FILE'], 'a' );
    fwrite($fd, convert_timestamp(time())."\n\n");
    fwrite($fd, $_SERVER['REQUEST_URI']."\n\n");
    fwrite($fd , $msg."\n\n".$seperator."\n\n");
    fclose($fd);
}

function chk_regx_add_err( $val, $description, $regx  )
{
	global $site_globals;

	if (!is_extant($val) || ( is_extant($regx) && !preg_match($regx, $val) ) )
	//if (( is_extant($regx) && !preg_match($regx, $val) ) )
	{
	   	array_push( $site_globals['BAD_FORM_FIELDS'], $description );
		//echo $description;
		//echo $val;
		return false;
	}

	return true;
}

function add_err($err)
{
	global $site_globals;
	is_extant($err) && array_push($site_globals['BAD_FORM_FIELDS'], $err);
}

function errors_exist()
{
   	global $site_globals;
   	return !empty($site_globals['BAD_FORM_FIELDS']);
}

function dump_errors()
{
	global $site_globals;
	if (is_extant($site_globals['BAD_FORM_FIELDS'])) echo implode("<br>",$site_globals['BAD_FORM_FIELDS']);
}

function send_email( $to, $from, $subject, $body )
{
    $swift = new Swift(new Swift_Connection_SMTP('localhost'));

    if ( !$swift->send($to, $from, $subject, $body) )
        add_err("There was an error emailing $to");
    else
        add_err("There was an error emailing $to");

    $swift->close();
}

function is_extant($var)
{
    if (isset($var))
    {
        if ( empty($var) && ($var !== 0) && ($var !== '0') )
			return false;
		else
			return true;
	}
	else
		return false;
}

function get_insert_stmt( $tableName, $fieldMap )
{
    if( !empty($tableName) && !empty($fieldMap) )
    {
        $sql = "INSERT INTO $tableName (".(implode( ",", array_keys($fieldMap) )).") VALUES (";
        $vals = array();

        foreach ( $fieldMap as $key => $value )
            array_push( $vals, "'$value'" );

        $sql .= (implode( ",", $vals )).")";
        return $sql;
    }
    return "";
}

function get_update_stmt( $tableName, $fieldMap )
{
    if( !empty($tableName) && !empty($fieldMap) )
    {
        $sql = "UPDATE $tableName SET ";
        $vals = array();

        foreach ( $fieldMap as $key => $value )
            array_push( $vals, "$key='$value'" );

        $sql .= (implode( ",", $vals ));
        return $sql;
    }
    return "";
}

function create_label_mark_if_invalid( $field_name, $label_text )
{
    global $site_globals;

    if ( in_array( $field_name, $site_globals['BAD_FORM_FIELDS']) )
    {
    	return '<span class="public_form_marked_label"> *Please correct* &nbsp </span><span class="public_form_marked_label"><i>'.$label_text.'</i></span>';
	}

	return $label_text;
}

/////////////////////////////////////////////////////////////////////////////////////////////////
// FUNCTION: getFilename
// PURPOSE: Extracts the filename from a full path for security when including a file.
// This custom function was wriiten as to return '' from a path like: foo/bar/foobar/ with
// the trailing slash.  Normal php basename() function would return 'foobar' which is bad.
/////////////////////////////////////////////////////////////////////////////////////////////////
function get_just_filename( $fullpath )
{
	if ( !isset($fullpath) || empty($fullpath) )
		return "";

	return substr($fullpath, -1) == '/' ? '' : basename($fullpath);
}

/////////////////////////////////////////////////////////////////////////////////////////////////
// FUNCTION: convertTimestamp
// PURPOSE: Converts a UNIX timestamp to a human readable format like: mm-dd-yy hh:mm:ss am/pm
/////////////////////////////////////////////////////////////////////////////////////////////////
function convert_timestamp( $tS, $showTime = 1 )
{
	if( empty($tS) )
		return "";

	$ampm = "am";

	$dt = getdate($tS);

	$year = $dt['year'];
	$mth  = $dt['mon'];
	$day  = $dt['mday'];
	$hour = $dt['hours'];

	$min  = add_leading_zero($dt['minutes']);
	$sec  = add_leading_zero($dt['seconds']);

	if ($hour > 12)
    {
		$ampm = "pm";
		$hour = $hour - 12;
	}

	$resultDate = "{$mth}-{$day}-{$year}";

	if ($showTime)
		$resultDate .= " {$hour}:{$min}:{$sec}{$ampm}";

	return $resultDate;
}

/////////////////////////////////////////////////////////////////////////////////////////////////
// FUNCTION: addLeadingZero
// PURPOSE: Adds a leading zero for numbers less than 10. (e.g. 9 becomes 09)
/////////////////////////////////////////////////////////////////////////////////////////////////
function add_leading_zero( $num )
{
	if ( !is_extant($num) || (int)$num > 9  )
		return $num;

	return "0{$num}";
}

function autop($pee, $br = 1)
{
    $pee = $pee . "\n"; // just to make things a little easier, pad the end
    $pee = preg_replace('|<br />\s*<br />|', "\n\n", $pee);
    $pee = preg_replace('!(<(?:table|ul|ol|li|pre|form|blockquote|h[1-6])[^>]*>)!', "\n$1", $pee); // Space things out a little
    $pee = preg_replace('!(</(?:table|ul|ol|li|pre|form|blockquote|h[1-6])>)!', "$1\n", $pee); // Space things out a little
    $pee = preg_replace("/(\r\n|\r)/", "\n", $pee); // cross-platform newlines
    $pee = preg_replace("/\n\n+/", "\n\n", $pee); // take care of duplicates
    $pee = preg_replace('/\n?(.+?)(?:\n\s*\n|\z)/s', "\t<p>$1</p>\n", $pee); // make paragraphs, including one at the end
    $pee = preg_replace('|<p>\s*?</p>|', '', $pee); // under certain strange conditions it could create a P of entirely whitespace
    $pee = preg_replace("|<p>(<li.+?)</p>|", "$1", $pee); // problem with nested lists
    $pee = preg_replace('|<p><blockquote([^>]*)>|i', "<blockquote$1><p>", $pee);
    $pee = str_replace('</blockquote></p>', '</p></blockquote>', $pee);
    $pee = preg_replace('!<p>\s*(</?(?:table|tr|td|th|div|ul|ol|li|pre|select|form|blockquote|p|h[1-6])[^>]*>)!', "$1", $pee);
    $pee = preg_replace('!(</?(?:table|tr|td|th|div|ul|ol|li|pre|select|form|blockquote|p|h[1-6])[^>]*>)\s*</p>!', "$1", $pee);
    if ($br) $pee = preg_replace('|(?<!<br />)\s*\n|', "<br />\n", $pee); // optionally make line breaks
    $pee = preg_replace('!(</?(?:table|tr|td|th|div|dl|dd|dt|ul|ol|li|pre|select|form|blockquote|p|h[1-6])[^>]*>)\s*<br />!', "$1", $pee);
    $pee = preg_replace('!<br />(\s*</?(?:p|li|div|th|pre|td|ul|ol)>)!', '$1', $pee);
    $pee = preg_replace('/&([^#])(?![a-z]{1,8};)/', '&#038;$1', $pee);
    return $pee;
}

function dump_session()
{
    echo "<pre>";
    echo "SESSION ARRAY:\n\n";
    print_r($_SESSION);
    echo "</pre>";
}

function destroy_session()
{
	$_SESSION = NULL;
	session_destroy();
	session_id(NULL);
	session_name(NULL);
	return;
}

////////////////////////////////////////////////////////////
// FUNCTION: better_date_widget()
////////////////////////////////////////////////////////////
function better_date_widget( $params )
{
    global $months;

    $today_date = getDate();
    $dflt_year  = $today_date['year'];
    $dflt_month = $today_date['mon'];
    $dflt_day   = $today_date['mday'];
    $dflt_start_year = 2000;
    $dflt_end_year = 2020;
    $dflt_class_name = "date_widget_dflt_class";

    is_extant($params["year"]) ? $year  = $params["year"] : $year = $dflt_year;
    is_extant($params["month"]) ? $month = $params["month"] : $month = $dflt_month;
    is_extant($params["day"]) ? $day = $params["day"] : $day = $dflt_day;
    is_extant($params["start_year"]) ? $start_year = $params["start_year"] : $start_year = $dflt_start_year;
    is_extant($params["end_year"]) ? $end_year = $params["end_year"] : $end_year = $dflt_end_year;
    is_extant($params["class_name"]) ? $class_name = $params["class_name"] : $class_name = $dflt_class_name;

    $widget_name = $params["widget_name"];

    if ( is_extant($params["db_date"]) && $params["db_date"] != "0000-00-00" ) // e.g. 2006-09-31
    {
        $db_date_tokens = explode( "-", $params["db_date"] );
        $year  = $db_date_tokens[0];
        $month = $db_date_tokens[1];
        $day   = $db_date_tokens[2];
    };


    $HTML .= "<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\"><tr><td><select class=\"$class_name\" id=\"${widget_name}_year\" name=\"${widget_name}_year\">";

    for ($y = $start_year; $y <= $end_year; $y++)
    {
        $HTML .= "<option value=\"$y\"";

        if ($y == $year)
        {
            $HTML .= " SELECTED";
        }

        $HTML .= ">$y";
    }

    $HTML .= "</select></td><td style=\"width:3px\"></td><td><select class=\"$class_name\" id=\"${widget_name}_month\" name=\"${widget_name}_month\">";

    foreach ($months as $key => $value)
    {
        $HTML .= "<option value=\"$key\"";

        if ($key == $month)
        {
            $HTML .= " SELECTED";
        }

        $HTML .= ">$value";
    }

    $HTML .= "</select></td><td style=\"width:3px\"></td><td><select class=\"$class_name\" id=\"${widget_name}_day\" name=\"${widget_name}_day\">";

    for ($d = 1; $d <= 31; $d++)
    {
        $HTML .= "<option value=\"$d\"";

        if ($day == $d)
        {
            $HTML .= " SELECTED";
        }

        $HTML .= ">$d";
    }

    $HTML .= "</select></td></tr></table>";

    return $HTML;
}

////////////////////////////////////////////////////////////
// FUNCTION: better_date_widget()
////////////////////////////////////////////////////////////
function better_time_widget( $params )
{
    $today_date = getDate();
    $dflt_hours  = $today_date['year'];
    $dflt_min   = $today_date['mon'];

    $dflt_start_hours   = 0;
    $dflt_end_hours     = 23;
    $dflt_start_minutes = 0;
    $dflt_end_minutes   = 45;
    $dflt_minute_interval = 15;
    $dflt_class_name = "time_widget_dflt_class";

    is_extant($params["hours"]) ? $hours  = $params["hours"] : $hours = $dflt_hours;
    is_extant($params["minutes"]) ? $minutes = $params["minutes"] : $month = $dflt_minutes;
    is_extant($params["start_hours"]) ? $start_hours = $params["start_hours"] : $start_hours = $dflt_start_hours;
    is_extant($params["end_hours"]) ? $end_hours = $params["end_hours"] : $end_hours = $dflt_end_hours;
    is_extant($params["start_minutes"]) ? $start_minutes = $params["start_minutes"] : $start_minutes = $dflt_start_minutes;
    is_extant($params["end_minutes"]) ? $end_minutes = $params["end_minutes"] : $end_minutes = $dflt_end_minutes;
    is_extant($params["minute_interval"]) ? $minute_interval = $params["minute_interval"] : $minute_interval = $dflt_minute_interval;
    is_extant($params["class_name"]) ? $class_name = $params["class_name"] : $class_name = $dflt_class_name;

    $widget_name = $params["widget_name"];

    $HTML .= '<table cellspacing="0" cellpadding="0" border="0"><tr><td><select class="'.$class_name.'" id="'.$widget_name.'_hours" name="'.$widget_name.'_hours">';

    for ($h = $start_hours; $h <= $end_hours; $h++ )
    {
        $HTML .= '<option value="'.add_leading_zero($h).'"';

        if ($h == $hours)
        {
            $HTML .= " SELECTED";
        }

        $HTML .= '>'.add_leading_zero($h);
    }

    $HTML .= '</select></td><td style="text-align: center; width:15px">:</td><td><select class="$class_name" id="'.$widget_name.'_minutes" name="'.$widget_name.'_minutes">';

    for ($m = $start_minutes; $m <= $end_minutes; $m += $minute_interval  )
    {
        $HTML .= '<option value="'.add_leading_zero($m).'"';

        if ($m == $minutes)
        {
            $HTML .= " SELECTED";
        }

        $HTML .= '>'.add_leading_zero($m);
    }

    $HTML .= "</select></td></tr></table>";

    return $HTML;
}

////////////////////////////////////////////////////////////
// FUNCTION: phone_widget()
////////////////////////////////////////////////////////////
function phone_widget( $params )
{
    $area_code = $params['area_code'];
    $prefix = $params['prefix'];
    $suffix = $params['suffix'];
    $widget_name = $params["widget_name"];
    $html  = '<table cellspacing="0" cellpadding="0" border="0"><tr>'
           . '<td>(<input type="text" id="'.$widget_name.'_area_code" name="'.$widget_name.'_area_code" size="3" value="'.$area_code.'" />)</td>'
           . '<td style="width:3px"></td><td><input type="text" id="'.$widget_name.'_prefix" name="'.$widget_name.'_prefix" size="3" value="'.$prefix.'" /></td>'
           . '<td style="text-align: center; width:15px">-</td></td><td><input type="text" id="'.$widget_name.'_suffix" name="'.$widget_name.'_suffix" size="4" value="'.$suffix.'" /></td>'
           . '</tr></table>';
    return $html;
}

function create_captcha( &$capSessCode )
{
    if ( empty($capSessCode) )
        $capSessCode = md5(round(rand(0,40000)));

    $my_captcha = new captcha( $capSessCode, '__TEMP__/' );
    return $my_captcha->get_pic( 4 );
}

function verify_email( $email )
{
   // $filtered = filter_data($email, FILTER_VALIDATE_EMAIL);
    return !empty($filtered );
}

function verify_phone( $phone )
{
   	if( $phone ) {

		$phone = ereg_replace( '[^[:digit:]]', '', $phone );

		if( strlen($phone) == 7 ) {
			$phone = (substr($phone,0,3))."-".(substr($phone,3,4));
			return $phone;
		}

		else if( strlen($phone) == 10 ) {
			$phone = "".(substr($phone,0,3))."-".(substr($phone,3,3))."-".(substr($phone,6,4));
			return $phone;
		}
		else
			return false;
   	}
}

function make_safe( $txt )
{
    //$txt = filter_data($txt, FILTER_SANITIZE_STRING);
    //$txt = filter_data($txt, FILTER_SANITIZE_MAGIC_QUOTES);
    $txt = trim($txt);
    
    if (!get_magic_quotes_gpc())
    {
		$txt = mysql_real_escape_string($txt);
	}
	
    return $txt;
}


function get_keywords( $param_array )
{
	global $site_globals;
	$stop_list_file = $site_globals['STOP_LIST'];
	$parse_text = $param_array['parse_text'];

	if (is_extant($parse_text) && is_readable($stop_list_file))
	{
		$fd = fopen( $stop_list_file, 'r' );
		$stop_list = array();

		while (!feof($fd))
		{
       	    $buffer = fgets($fd, 4096);
			array_push($stop_list, trim($buffer));
		}

		// remove all punctuation from input.
		$parse_text = preg_replace( "/\\\\'*s/", "", $parse_text );
		$parse_text = preg_replace( "/\\\\'*/", "", $parse_text );
		$parse_text = preg_replace( "/'*/", "", $parse_text );
		$parse_text = preg_replace( "/\"*/", "", $parse_text );
        $parse_tokens = explode( " ", strtolower($parse_text) );

		

		if (!empty($stop_list))
            return array_diff( $parse_tokens, $stop_list );
	}

	return array();
}

function gen_item_card( $row, $show_contact )
{
	$none_specified = "<i>none specified</i>";
	$no_image_path = "no_image.gif";

	$dp = $row->dp;
	$lfd = $row->lfd;
	$desc = is_extant($row->description) ? $row->description : $none_specified;
	$item_comment = is_extant($row->item_comment) ? $row->item_comment : $none_specified;
	$reward = is_extant($row->reward) ? $row->reward : $none_specified;
	$addr = is_extant($row->address) ? $row->address : $none_specified;
	$city = is_extant($row->city) ? $row->city : $none_specified;
	$state = is_extant($row->state) ? $row->state : $none_specified;
	$zip = is_extant($row->zip) ? $row->zip : $none_specified;
	$img_tag = is_extant($row->photo_path) ? "<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\"><tr><td><a href=\"item_photos/{$row->photo_path}\" rel=\"lightbox\"><img border=\"0\" width=\"100\" height=\"125\" src=\"item_photos/{$row->photo_path}\"></a></td></tr><tr><td align=\"right\"><a href=\"item_photos/{$row->photo_path}\" rel=\"lightbox\" style=\"font-size:9px\">click to zoom</a></td></tr></table>" : "<img width=\"100\" height=\"125\" src=\"item_photos/$no_image_path\">";
	$lf = $_REQUEST['item_type'] == 'found' ? "Found" : "Lost";
	$contact_info = "";
	
	if ($show_contact)
	{
		$contact_info .= "<br>";
		is_extant($row->email) && $contact_info .= "<b>Email:</b> <a href=\"mailto:{$row->email}\">{$row->email}</a><br />";
		is_extant($row->phone) && $contact_info .= "<b>Phone:</b> {$row->phone}<br />";
		is_extant($row->phone2) && $contact_info .= "<b>Phone 2:</b> {$row->phone2}<br />";
		$fraud = '<tr><td colspan="2" valign="bottom" align="right"><img src="images/exclamation.gif"> <a style="font-size:9px" href="?c=scam_form&item_id='.($row->item_id).'">report fraudulent activity</a></td></tr>';
	}

	$divs_html = <<<EOT
		<div class="item_card" id="item_{$row->item_id}">
		<table cellspacing="0" cellpadding="3" border="0" width="100%" height="100%">
		<tr><td>
		<span style="text-decoration:none;font-size:14pt; margin-bottom:5px">$lf: <b>$desc</b></span><br><br>
		<span style="font-size: 12px">
		<b>$lf on:</b> $lfd<br>
		<b>Posted on:</b> $dp<br>
		<b>Reward:</b> \$$reward<br /><br />
		<b>Comment:</b> $item_comment<br /><br />
		<b>Address:</b> $addr<br />
		<b>City:</b> $city<br />
		<b>State:</b> $state<br />
		<b>Zip:</b> $zip<br />
		$contact_info
		</span>
		</td>
		<td align="right">
		$img_tag
		</td>
		</tr>
		$fraud
		</table>
		</div>
EOT;
	return $divs_html;
}

function do_encrypt( $valToEncrypt )
{
  //  $newVal = md5($valToEncrypt);
  //  return $newVal;

    if( !is_extant($valToEncrypt) )
        return "";

    global $encrypt_code_key,$encrypt_algo,$encrypt_mode;

    $td = mcrypt_module_open($encrypt_algo, '', $encrypt_mode, '');

    // Create the IV and determine the keysize length, used MCRYPT_RAND
    // on Windows instead
    $iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_DEV_RANDOM);
    $ks = mcrypt_enc_get_key_size($td);

    // Create key
    $key = substr($encrypt_code_key, 0, $ks);

    // Intialize encryption
    mcrypt_generic_init($td, $key, $iv);

    // Encrypt data
    $encrypted = mcrypt_generic($td, $valToEncrypt);

    // Terminate encryption handler
    mcrypt_generic_deinit($td);

    return base64_encode($encrypted);
}

function do_decrypt( $valToDecrypt )
{
    if( !is_extant($valToDecrypt) )
        return "";

    global $encrypt_code_key,$encrypt_algo,$encrypt_mode;

    // Open the cipher
    $td = mcrypt_module_open($encrypt_algo, '', $encrypt_mode, '');

    // Create the IV and determine the keysize length, used MCRYPT_RAND
    // on Windows instead
    $iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_DEV_RANDOM);
    $ks = mcrypt_enc_get_key_size($td);

    // Create key
    $key = substr($encrypt_code_key, 0, $ks);

    // Initialize encryption module for decryption
    mcrypt_generic_init($td, $key, $iv);

    // Decrypt encrypted string
    $decrypted = mdecrypt_generic($td, base64_decode($valToDecrypt));

    // Terminate decryption handle and close module
    mcrypt_generic_deinit($td);
    mcrypt_module_close($td);

    return trim($decrypted);
}

$months = array( '1'=>'January', '2'=>'February', '3'=>'March', '4'=>'April', '5'=>'May', '6'=>'June', '7'=>'July', '8'=>'August', '9'=>'September', '10'=>'October', '11'=>'November', '12'=>'December' );

$dayConversion = array( '0'=>'Sunday','1'=>'Monday','2'=>'Tuesday','3'=>'Wednesday','4'=>'Thursday','5'=>'Friday','6'=>'Saturday');

$site_globals['countries'] = array(
"ar"=>"Argentina",
"au"=>"Australia",
"at"=>"Austria",
"be"=>"Belgium",
"bo"=>"Bolivia",
"br"=>"Brazil",
"bg"=>"Bulgaria",
"ca"=>"Canada",
"cl"=>"Chile",
"cn"=>"China",
"cy"=>"Cyprus",
"cz"=>"Czech Republic",
"cs"=>"Czechoslovakia",
"dk"=>"Denmark",
"ec"=>"Ecuador",
"ee"=>"Estonia",
"fi"=>"Finland",
"fr"=>"France",
"de"=>"Germany",
"gr"=>"Greece",
"hk"=>"Hong Kong",
"hu"=>"Hungary",
"is"=>"Iceland",
"in"=>"India",
"ie"=>"Ireland",
"il"=>"Israel",
"it"=>"Italy",
"jp"=>"Japan",
"jo"=>"Jordan",
"kw"=>"Kuwait",
"lv"=>"Latvia",
"lt"=>"Lithuania",
"lu"=>"Luxembourg",
"my"=>"Malaysia",
"mt"=>"Malta",
"mx"=>"Mexico",
"mc"=>"Monaco",
"ma"=>"Morocco",
"nl"=>"Netherlands",
"nz"=>"New Zealand",
"ni"=>"Nicaragua",
"no"=>"Norway",
"pk"=>"Pakistan",
"pa"=>"Panama",
"py"=>"Paraguay",
"pe"=>"Peru",
"ph"=>"Philippines",
"pl"=>"Poland",
"pt"=>"Portugal",
"qa"=>"Qatar",
"ro"=>"Romania",
"ru"=>"Russian Federation",
"sa"=>"Saudi Arabia",
"sg"=>"Singapore",
"za"=>"South Africa",
"kr"=>"South Korea",
"es"=>"Spain",
"lk"=>"Sri Lanka",
"se"=>"Sweden",
"ch"=>"Switzerland",
"tw"=>"Taiwan",
"tz"=>"Tanzania",
"th"=>"Thailand",
"tr"=>"Turkey",
"ae"=>"United Arab Emirates",
"uk"=>"United Kingdom",
"us"=>"United States",
"uy"=>"Uruguay",
"ve"=>"Venezuela",
"ye"=>"Yemen",
"yu"=>"Yugoslavia" );

$site_globals['states'] = array("-1" => "--Select State--", "AL"=>"Alabama",
"AK"=>"Alaska",
"AZ"=>"Arizona",
"AR"=>"Arkansas",
"CA"=>"California",
"CO"=>"Colorado",
"CT"=>"Connecticut",
"DE"=>"Delaware",
"FL"=>"Florida",
"GA"=>"Georgia",
"HI"=>"Hawaii",
"ID"=>"Idaho",
"IL"=>"Illinois",
"IN"=>"Indiana",
"IA"=>"Iowa",
"KS"=>"Kansas",
"KY"=>"Kentucky",
"LA"=>"Louisiana",
"ME"=>"Maine",
"MD"=>"Maryland",
"MA"=>"Massachusetts",
"MI"=>"Michigan",
"MN"=>"Minnesota",
"MS"=>"Mississippi",
"MO"=>"Missouri",
"MT"=>"Montana",
"NE"=>"Nebraska",
"NV"=>"Nevada",
"NH"=>"New Hampshire",
"NJ"=>"New Jersey",
"NM"=>"New Mexico",
"NY"=>"New York",
"NC"=>"North Carolina",
"ND"=>"North Dakota",
"OH"=>"Ohio",
"OK"=>"Oklahoma",
"OR"=>"Oregon",
"PA"=>"Pennsylvania",
"RI"=>"Rhode Island",
"SC"=>"South Carolina",
"SD"=>"South Dakota",
"TN"=>"Tennessee",
"TX"=>"Texas",
"UT"=>"Utah",
"VT"=>"Vermont",
"VA"=>"Virginia",
"WA"=>"Washington",
"DC"=>"Washington D.C.",
"WV"=>"West Virginia",
"WI"=>"Wisconsin",
"WY"=>"Wyoming" );

function do_logout()
{
    destroy_session();
    header("location: ?c=home");
}

function authorize_user()
{
	global $site_globals, $_POST;
	$link = $site_globals['link'];

	// Did we pass in a username and password with the session?
	if (!$_SESSION['login'] || !$_SESSION['pass'])
	{
		// is_extant($_SESSION) && destroy_session();

		// Start by checking the username
		if ($_POST['login'])
		{
			// If we have a username stuff it into the session for validation.
			$_SESSION['login'] = make_safe($_POST['login']);

		}
		else
		{
			// If no login provided, force a login.
			write_log("USERNAME WAS NULL");
			return false;
		}

		// On to the password
		if ($_POST['pass'])
		{
			// If we have a password, stuff it in the session for validation.
			$_SESSION['pass'] = md5($_POST['pass']);//doEncrypt($_POST['pass']);

		}
		else
		{
			// If no password, then set it to NULL and for a login.
			write_log("PASSWORD WAS NULL");

			return false;
		}
    }

	$sql = "SELECT * FROM {$site_globals['USER_TABLE']} WHERE password='{$_SESSION['pass']}' AND login='{$_SESSION['login']}'";
	$qryObj = new QueryObject($sql, $link);

	if (!query_has_results($qryObj) || $qryObj->numRows > 1)
	{
        write_log("FINDING VALID RECORD FOR USERNAME AND PASSWORD FAILED(1)");
		echo "<span class=\"login_error\">*Unrecognized your username and/or password. </span>";
		session_destroy();//destroySession();

		return false;
	}

	$row = mysql_fetch_object($qryObj->result);
	mysql_free_result($qryObj->result);

	$_SESSION['type'] = $row->type;
	$_SESSION['user_id']  = $row->user_id;
	$_SESSION['firstname'] = $row->firstname;
	$_SESSION['is_authorized'] = 1;

	return true;
}

?>
