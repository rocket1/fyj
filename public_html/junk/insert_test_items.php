<?php

$items = array( "jacket", "wallet", "keys", "purse", "briefcase", "luggage", "glasses", "hearing aid", "toothbrush", "bible", "car", "mind", "tickets", "hat", "umbrella");
$streets = array( "tremont", "clementine", "ditmar", "nevada", "horne", "cleveland", "wisconsin", "pacific" );
$link = mysql_connect( 'localhost', 'jasonfri_cs643', 'password' );

for ($i = 0; $i < 200; $i++ )
{
	$street_num = rand(0, 999);
	$street_name = rand(0, count($streets)-1);
	$lost_or_found = rand(0,1);
	$item = $items[rand(0,count($items)-1)];
	$address = "$street_num $streets[$street_name]";
	$date = date('Y-m-d', mktime());
	$sql = "INSERT INTO jasonfri_findyourjunk.fyj_item (description,address,city,state,zip,lost_found_flag,date_posted,user_id,tags)
	        VALUES ('I am a description', '$address', 'Oceanside', 'CA', '92054', '$lost_or_found', '$date', '5', '$item' )";
	mysql_query($sql) || die(mysql_error());
	echo $sql."<br>";

}

?>
