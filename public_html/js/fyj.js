function add_onload_func(myfunc)
{
	if(window.addEventListener)
		window.addEventListener('load', myfunc, false);
	else if(window.attachEvent)
		window.attachEvent('onload', myfunc);
}

var map;

function createMarker(point, text)
{
    var marker = new GMarker(point);
    GEvent.addListener(marker, "click", function() {
        marker.openInfoWindowHtml(text);
    });
    return marker;
}

function showAddress(address, html)
{
  var geocoder = new GClientGeocoder();
  geocoder.getLatLng(
    address,
    function(point) {
      if (point)
      {
         map.setCenter(point, 13);
         map.addOverlay(createMarker(point, html));
      }
    }
  );
}

function AjaxOpt() {
    
    // Handle 404
    this.on404 = function(t) {
        alert('Error 404: location "' + t.statusText + '" was not found.');
    }
    
    // Handle other errors
    this.onFailure = function(t) {
        alert('Error ' + t.status + ' -- ' + t.statusText);
    }   
}

function expand_tag_cloud()
{
    var opt = new AjaxOpt();
    opt.method ='post';
    opt.postBody = 'zip='  + $('zip_code').value;
   // alert(opt.postBody);
    
    opt.onSuccess = function(t)
    {
        alert(t.responseText);
        $('tag_cloud_side_column').innerHTML = t.responseText;
    }
    
    new Ajax.Request('tag_cloud_ajax.php', opt);
}

function createCookie(name,value,days)
{
	if (days)
	{
		var date = new Date();
		date.setTime(date.getTime()+(days*24*60*60*1000));
		var expires = "; expires="+date.toGMTString();
	}
	else var expires = "";
	document.cookie = name+"="+value+expires+"; path=/";
}

function readCookie(name)
{
	var nameEQ = name + "=";
	var ca = document.cookie.split(';');
	for(var i=0;i < ca.length;i++)
	{
		var c = ca[i];
		while (c.charAt(0)==' ') c = c.substring(1,c.length);
		if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
	}
	return null;
}

function eraseCookie(name)
{
	createCookie(name,"",-1);
}

function write_zip_code()
{
    createCookie('zip_code', $('zip_code').value, 5*365 );
    document.location = document.location.href;
}

function update_map(item)
{
	location.href = "?c=map_search_results&item=" + item;
}

function show_div(item_id)
{
    item_card_array = document.getElementsByClassName('item_card');

    for (i = 0; i < item_card_array.length; i++)
    {
       item_card_array[i].style.display = "none";
    }

    $('item_' + item_id).style.display = "block";
}
  
function confirm_address()
{
    return true;
    // We are going to check to see if the address provided
    // if a valid geocoded address.

    addr = $('address').value;
    state = $('state').options[$('state').selectedIndex].value;
    city = $('city').value;
    zip = $('zip').value;
    
    //alert( addr + " " + state + " " + city + " " + zip );
    
    // Although one of the fields may be empty, and therefore invalid,
    // submit the form anyway and let the server detect the error
    // and print the appropriate error message.    
      if (!addr || !state || !city || !zip )
        return true;
        
    address = addr + " " + state + " " + city + " " + zip;
    alert(address);
    var geocoder = new GClientGeocoder();
    geocoder.getLatLng(
    address,
    function(point) {
      if (!point)
      {
      //  if (!confirm("The address that you entered cannot be translated into a map coordinate.  Proceed anyway?"))
      //  {
            return false;
     //   }
     //   else
     //   {
//return true;
      //  }
      }
    }
  );
}
