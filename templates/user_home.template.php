<?php  $lf = $_REQUEST['item_type'] == 'found' ? "Found" : "Lost"; ?>

<script>

  function show_div(item_id)
  {
    item_card_array = document.getElementsByClassName('item_card');

    for (i = 0; i < item_card_array.length; i++)
    {
       item_card_array[i].style.display = "none";
    }

    $('item_' + item_id).style.display = "block";
  }

</script>


<div style="margin-bottom:15px; width:400px">
 <table width = "100%" border ="0" ><tr>
  <td><div style="display:inline" class="big_page_hdr">Your <?php  echo $lf ?> Junk.</td></div>
  <td><a class = "user_home_nav" href="?c=user_home&item_type=found">Found&nbsp;Items</a></td>
  <td><a class = "user_home_nav" href="?c=user_home">Lost&nbsp;Items</a></td>
  </tr></table>
</div>


<?php



if(query_has_results($qryObj))
{
    while ($row = mysql_fetch_object($qryObj->result))
    {
        $divs_html .= gen_item_card( $row, false );
        $links_html .= "$lf on: {$row->lfd}<br /><a style=\"text-decoration:none;font-size:14pt\" href=\"javascript:show_div({$row->item_id})\">{$row->description}</a><br><br>";
    }

    echo $divs_html;
    echo $links_html;
}
else
{
    echo"<div class = \"no_results\"> No results found.</div>";
}
?>

