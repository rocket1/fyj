<div class="public_form">
<div class="big_page_hdr">Report Fradulent Activity for "<?php  echo $desc ?>"</div>
  <?php  dump_errors() ?>
  <form action="?c=scam_form" method="post">

      <span class="public_form_label">
        <?php  echo create_label_mark_if_invalid( 'scam_comment', "Describe in detail why this item may be linked to fraudulent activity:" ) ?>
      </span>
      <br>
        <textarea class="public_form_text_area2" name="scam_comment" id="scam_comment"><?php  echo $scam_comment ?></textarea>


    <input type="submit" value="Submit" id="user_submit" />
    <input type="hidden" value="<?php  echo $_REQUEST['item_id'] ?>" name="item_id" id="item_id" />
  </form>

</div>
