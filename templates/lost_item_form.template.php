<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=ABQIAAAA7J33RBFGZDvIZTqXiXGcQxQqa9Iv8uNDN7K5GHNo_Bdg7O9UBhT-5a7wJ_OZRvKf1W1Oc-xa20H5Mw"></script>
<div class="public_form">
<div class="big_page_hdr"><?php  echo $form_title ?></div>

<form enctype="multipart/form-data" action="?c=lost_item_form" method="post" id="lost_form"  onsubmit="return confirm_address(this)">

<div class="form_item"><span class="public_form_label"><?php  echo create_label_mark_if_invalid( 'description', "Item Description" ) ?> (e.g. pink leather jacket)</span><br>
<input type="text"  class="public_form_text_input" name="description" id="description" value="<?php  echo stripslashes($description) ?>"></div>

<div class="form_item"><span class="public_form_label">Additional Comments (not required)</span><br>
<textarea class="public_form_text_area" name="item_comment" id="item_comment"><?php  echo stripslashes($item_comment) ?></textarea></div>

  <div class="form_item">
    <span class="public_form_label">Date Item Was Lost:</span>
    <br>
      <?php  echo better_date_widget( array('year'=>$lost_year,'month'=>$lost_month,'day'=>$lost_day, 'widget_name'=>'lost') ) ?>
    </div>
  
<div class="form_item"><span class="public_form_label"><?php  echo create_label_mark_if_invalid( 'reward', "Reward" ) ?> (e.g. 50) (not required)
</span><br>
$<input type="text" class="" name="reward" id="reward" value="<?php  echo $reward ?>"></div>

<div class="form_item"><span class="public_form_label"><?php  echo create_label_mark_if_invalid( 'address', "Address of Lost Item:" ) ?></span><br>
<input type="text" class="public_form_text_input" name="address" id="address" value="<?php  echo stripslashes($address) ?>"></div>

<div class="form_item"><span class="public_form_label"><?php  echo create_label_mark_if_invalid( 'city', "City of Lost Item:" ) ?></span><br>
<input type="text" class="public_form_text_input" name="city" id="city" value="<?php  echo stripslashes($city)?>"></div>

<div class="form_item"><span class="public_form_label"><?php  echo create_label_mark_if_invalid( 'state', "State of Lost Item:" ) ?></span><br>
<select class="public_form_select" name="state" id="state">
<?php

foreach ($site_globals['states'] as $key => $value )
{
	echo "<option value=\"$key\"";

	if ($key == $state)
		echo " SELECTED";

	echo ">$value</option>\n";
}

?>
</select>
</div>

<div class="form_item"><span class="public_form_label"><?php  echo create_label_mark_if_invalid( 'zip', "Zip Code of Lost Item:" ) ?></span><br>
<input type="zip" class="public_form_zip_code2" name="zip" id="zip" value="<?php  echo $zip ?>"></div>

<div class="form_item"><span class="public_form_label">Photo (not required)</span><br>
<input type="file" class="public_form_text_input" name="upload_img" id="upload_img" value=""></div><br>
<input type="submit" value="Post Lost Item!" id="user_submit"/>



</div>
