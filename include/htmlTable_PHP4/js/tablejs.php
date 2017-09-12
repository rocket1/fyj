function noneChecked() {
	for( i = 0; i < document.tableForm.elements.length; i++ ) {
		tableFormItem = document.tableForm.elements[i];
		if( tableFormItem.type == 'checkbox' ) {
			if( tableFormItem.checked ) {
				return false;
			}
		}
	}
	return true;
}

function doAction( command ) {
	if( command == "DELETE_USER" ) {
		selectMsg = "Please select one or more Users to delete.";
		confirmMsg = "Are you sure you want to delete the selected User(s)?";
	}
	else if( command == "DELETE_GROUP" ) {
		selectMsg = "Please select one or more Groups to delete.";
		confirmMsg = "Are you sure you want to delete the selected Group(s)?";	
	}
	else if( command == "SET_USER_ACTIVE" ) {
		selectMsg = "Please select one or more Users to set as Active.";
		confirmMsg = "Are you sure you want to set the User(s) as Active?";	
	}
	else if( command == "SET_USER_INACTIVE" ) {
		selectMsg = "Please select one or more Users to set as Inactive.";
		confirmMsg = "Are you sure you want to set the User(s) as Inactive?";	
	}
	else if( command == "DELETE_ARTICLE" ) {
		selectMsg = "Please select one or more articles to delete.";
		confirmMsg = "Are you sure you want to delete the selected articles(s)?";
	}
    else if( command == "PUBLISH_ARTICLE" ) {
		selectMsg = "Please select one or more articles that you want to publish.";
		confirmMsg = "Are you sure you want to publish the selected articles?";
	}
    else if( command == "UNPUBLISH_ARTICLE" ) {
		selectMsg = "Please select one or more articles that you want to unpublish.";
		confirmMsg = "Are you sure you want to unpublish the selected articles?";
	}
    else if( command == "DELETE_EVENT" ) {
		selectMsg = "Please select one or more events to delete.";
		confirmMsg = "Are you sure you want to events the selected event(s)?";
	}
    else if( command == "PUBLISH_EVENT" ) {
		selectMsg = "Please select one or more articles that you want to publish.";
		confirmMsg = "Are you sure you want to publish the selected event(s)?";
	}
    else if( command == "UNPUBLISH_EVENT" ) {
		selectMsg = "Please select one or more events that you want to unpublish.";
		confirmMsg = "Are you sure you want to unpublish the selected event(s)?";
	}
    else if( command == "PUBLISH_PRESS" ) {
		selectMsg = "Please select one or more press stories that you want to publish.";
		confirmMsg = "Are you sure you want to publish the selected press stories?";
	}
    else if( command == "UNPUBLISH_PRESS" ) {
		selectMsg = "Please select one or more press stories that you want to unpublish.";
		confirmMsg = "Are you sure you want to unpublish the selected press stories?";
	}
    else if( command == "DELETE_PRESS" ) {
		selectMsg = "Please select one or more press stories to delete.";
		confirmMsg = "Are you sure you want to delete the selected press stories?";
	}
    else if( command == "DELETE_IMAGE" ) {
		selectMsg = "Please select one or more images to delete.";
		confirmMsg = "Are you sure you want to delete the selected images?";
	}
    else if( command == "DELETE_LINK" ) {
		selectMsg = "Please select one or more links to delete.";
		confirmMsg = "Are you sure you want to delete the selected links?";
	}
    else if( command == "DELETE_VIDEO" ) {
		selectMsg = "Please select one or more videos to delete.";
		confirmMsg = "Are you sure you want to delete the selected videos?";
	}
    else if( command == "DELETE_AUDIO" ) {
		selectMsg = "Please select one or more audio files to delete.";
		confirmMsg = "Are you sure you want to delete the selected audio files?";
	}
    else if( command == "DELETE_TODO" ) {
		selectMsg = "Please select one or more todo's to delete.";
		confirmMsg = "Are you sure you want to delete the selected todo's?";
	}
	if( noneChecked() == true ) {
		alert(selectMsg);
		return false;
	}
	if( confirm(confirmMsg) ) {
		loc = "<?=$_SERVER[PHP_SELF]?>?form_action=" + command + "&id=" + getIDParam();
		location.href = loc;
	}
}

function getIDParam() {
	idString = "";
	firstTime = true;

	for( i = 0; i < document.tableForm.elements.length; i++ ) {
		tableFormItem = document.tableForm.elements[i];
		if( tableFormItem.name == 'checkAllBox' )
			continue;
		if( tableFormItem.type == 'checkbox' ) {
			if( !tableFormItem.checked )
				continue;
			if( !firstTime )
				idString += ":";
			idString += tableFormItem.value;
			firstTime = false;
		}
	}
	return idString;
}

function doCBClick( CB ) {
	if( CB.name == 'checkAllBox' ) {
		for( i = 0; i < document.tableForm.elements.length; i++ ) {
			tableFormItem = document.tableForm.elements[i];
			if( tableFormItem.type == 'checkbox' ) {
				tableFormItem.checked = tableForm.checkAllBox.checked;
			}
		}
	}
	else {

		for( i = 0; i < document.tableForm.elements.length; i++ ) {
			tableFormItem = document.tableForm.elements[i];
			if( tableFormItem.type == 'checkbox' ) {
				if( !tableFormItem.checked ) {
					tableForm.checkAllBox.checked = tableFormItem.checked;
					return;
				}
			}
		}
	}
}