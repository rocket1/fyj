<br>
<div style="text-align:left;margin-bottom:15px">
  <table cellspacing="0" cellpadding="0" border="0">
    <tr>
      <td>
        <span class="medium_hdr">Step 1 - Zip Code</span>
      </td>
      <td>
        <input type="text" class="public_form_zip_code" size="5" id="zip_code" name="zip_code" value="<?php  echo $_REQUEST['zip_code'] ?>">
        </td>
      <td>
        <input type="button" value="Go!" onclick="write_zip_code()">
        </td>
    </tr>
  </table>
</div>
<?php

if (is_extant($_REQUEST['zip_code']))
{
	$zip = make_safe($_REQUEST['zip_code']);
	chk_regx_add_err($zip, "zip_code", $site_globals['REGX_ZIP_CODE']);

	if (!in_array("zip_code",$site_globals['BAD_FORM_FIELDS']))//$!errors_exist())
	{
     	echo '<div class="medium_hdr">Step 2 - Select an Item</div>';
    	echo '<div id="tag_cloud_side_column">';
    	echo '<div class="tag_cloud"><div class="hdr1" style="display:block;color:#FD8F19;margin-bottom:6px;font-weight:bold">You\'ve found this...</div>';
    	new tag_cloud("found_tag_cloud_class",0);
    	echo "</div>";
    	echo '<br><div class="tag_cloud"><div class="hdr1" style="display:block;color:#86A1BC;margin-bottom:6px;font-weight:bold">You\'ve lost this...</div>';
    	new tag_cloud("lost_tag_cloud_class",1);
    	echo "</div></div>";
    }
    else
    	echo '<span style="color:red;margin-left:50px;text-align:center">Please enter valid zip code.</span>';

}


?>
