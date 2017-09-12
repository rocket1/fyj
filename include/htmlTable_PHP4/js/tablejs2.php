function doPublish() {
	if( noneChecked() == true ) {
		alert("Please select one or more items to publish.");
		return false;
	}
	if( confirm("Are you sure you want to publish the selected items?") ) {
		publishLocation = "?d=actions/do_publish_article.php&id=" + getIDParam() + "&order=&maxRows=&currRowPtr=";

		location.href = publishLocation;
	}
}

function doUnpublish() {
	if( noneChecked() == true ) {
		alert("Please select one or more items to unpublish.");
		return false;
	}
	if( confirm("Are you sure you want to unpublish the selected items?") ) {	
		unPublishLocation = "?d=actions/do_unpublish_article.php&id=" + getIDParam() + "&order=&maxRows=&currRowPtr=";

		location.href = unPublishLocation;
	}
}

function doArchive() {
	if( noneChecked() == true ) {
		alert("Please select one or more items to Archive.");
		return false;
	}
}

function doUnarchive() {
	if( noneChecked() == true ) {
		alert("Please select one or more items to Unarchive.");
		return false;
	}
}

function doSetFromTour() {
	if( noneChecked() == true ) {
		alert("Please select one or more items to \"set from tour\".");
		return false;
	}
}

function doUnsetFromTour() {
	if( noneChecked() == true ) {
		alert("Please select one or more items to \"set NOT from tour\".");
		return false;
	}
}