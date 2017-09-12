<?php

function YesNoMap( $value )
{
	if( $value == "0" ) {	
		return "No";
	}
	return "Yes";
}

function PublishUnpublishMap( $value )
{
	if( $value == "0" ) {	
		return "Unpublished";
	}
	return "Published";
}


function chopByDelimeter( $value )
{
	$valArr = explode (":", $value );	
	return implode( "<br>", $valArr );	
}

function ifZeroSetAsBlank( $value )
{
    if( $value == '0' ) return "&nbsp;";
    return $value;
}

?>
