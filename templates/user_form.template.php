<div class="public_form">
<div class="big_page_hdr">Create a User Account</div>

<form action="?c=user_form" method="post">

<div class="form_item"><span class="public_form_label"><?php  echo create_label_mark_if_invalid( 'login', "Screen Name" ) ?></span><br>
<input type="text" class="public_form_text_input" name="login" id="login" value="<?php  echo $login ?>"></div>

<div class="form_item"><span class="public_form_label"><?php  echo create_label_mark_if_invalid( 'firstname', "First Name" ) ?></span><br>
<input type="text" class="public_form_text_input" name="firstname" id="firstname" value="<?php  echo $firstname ?>"></div>

<div class="form_item"><span class="public_form_label"><?php  echo create_label_mark_if_invalid( 'lastname', "Last Name" ) ?></span><br>
<input type="text" class="public_form_text_input" name="lastname" id="lastname" value="<?php  echo $lastname ?>"></div>

<div class="form_item"><span class="public_form_label"><?php  echo create_label_mark_if_invalid( 'email', "Email" ) ?></span><br>
<input type="text" class="public_form_text_input" name="email" id="email" value="<?php  echo $email ?>"></div>

<div class="form_item"><span class="public_form_label"><?php  echo create_label_mark_if_invalid( 'phone', "Phone Number" ) ?></span><br>
<input type="text" class="public_form_text_input" name="phone" id="phone" value="<?php  echo $phone ?>"></div>

<div class="form_item"><span class="public_form_label"><?php  echo create_label_mark_if_invalid( 'phone2', "Alternate Phone Number" ) ?></span><br>
<input type="text" class="public_form_text_input" name="phone2" id="phone2" value="<?php  echo $phone2 ?>"></div>

<div class="form_item"><span class="public_form_label"><?php  echo create_label_mark_if_invalid( 'password', "Password" ) ?></span><br>
<input type="password" class="public_form_password" name="password" id="password" value=""></div>

<div class="form_item"><span class="public_form_label"><?php  echo create_label_mark_if_invalid( 'login', "Confirm Password" )  ?></span><br>
<input type="password" class="public_form_password" name="confirm_password" id="confirm_password" value=""></div><br>

<input type="submit" value="Submit" id="user_submit" />
</form>
</div>

<script>$('login').focus();</script>
