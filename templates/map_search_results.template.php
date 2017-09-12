<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=ABQIAAAA7J33RBFGZDvIZTqXiXGcQxQqa9Iv8uNDN7K5GHNo_Bdg7O9UBhT-5a7wJ_OZRvKf1W1Oc-xa20H5Mw"></script>
<script>

  add_onload_func( function() {
  if (GBrowserIsCompatible()) {
  map = new GMap2(document.getElementById("map"));
  map.setCenter(new GLatLng(37.4419, -122.1419), 13);
  map.addControl(new GSmallMapControl());
  map.addControl(new GMapTypeControl());
  <?php  echo $js_cmds; ?>
    }
  });

</script>
<span class="medium_hdr">Items that match "<?php  echo $_REQUEST['item'] ?>" <?php  echo $lost_found_flag == 0 ? "lost" : "found"; ?> in <?php  echo $_REQUEST['zip_code']; ?>.<br><br>
</span>

<?php  echo $divs_html ?>
<span class="medium_hdr">Step 3 - Click on a map location.</span>
<div id="map"></div>

