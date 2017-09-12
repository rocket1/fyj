<html>
<head>
<title>FindYourJunk.com</title>
<link rel="stylesheet" href="css/findyourjunk.css"></link>
<link rel="stylesheet" href="css/lightbox.css" type="text/css" media="screen" />
<script src="js/prototype.js"></script>
  <!--
  <script src="js/rico.js"></script> -->
<script src="js/fyj.js"></script>
<script type="text/javascript" src="js/scriptaculous.js?load=effects"></script>
<script type="text/javascript" src="js/lightbox.js"></script>
<!--<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=ABQIAAAA7J33RBFGZDvIZTqXiXGcQxQqa9Iv8uNDN7K5GHNo_Bdg7O9UBhT-5a7wJ_OZRvKf1W1Oc-xa20H5Mw"></script>-->

<!--<script type="text/javascript" src="http://us.js2.yimg.com/us.js.yimg.com/lib/common/utils/2/yahoo_2.0.0-b2.js"></script>
<script type="text/javascript" src="http://us.js2.yimg.com/us.js.yimg.com/lib/common/utils/2/event_2.0.0-b2.js" ></script>
<script type="text/javascript" src="http://us.js2.yimg.com/us.js.yimg.com/lib/common/utils/2/dom_2.0.2-b3.js"></script>
<script type="text/javascript" src="http://us.js2.yimg.com/us.js.yimg.com/lib/common/utils/2/animation_2.0.0-b3.js"></script>-->


</head>
  <body vlink="#663333">

    <div id="left_column">
      <div id="logo_main">
        <a href="?c=home">
          <img src="images/fyj_logo_main_large.jpg" border="0">
          </a>
      </div>

    </div>

    <div id="middle_column">
      <?php  file_exists($content) && @include($site_globals['TEMPLATE_PATH']."/$content"); ?>

	 <br /><br /><br />
      <div class = "footer" id = "navfooter" >
        <br />
        <br />
        <table border = "0" width = "110%">
          <tr>
            <td>
              <a style = "text-decoration:none" href="?c=home" tabindex = "80"> Home </a> &nbsp;
            </td>
            <td>
              <a style = "text-decoration:none" href="http://www.professorsolomon.com/tips.html" tabindex = "80"> Tips to find your lost item </a> &nbsp;
            </td>
            <td>
              <a style = "text-decoration:none" href="?c=lost_item_form" tabindex = "80"> Post a lost item* </a> &nbsp;
            </td>
            <td>
              <a style = "text-decoration:none" href="?c=found_item_form" tabindex = "80"> Post a found item*</a> &nbsp;
            </td>
          </tr>
        </table>
        <div class = "footer" id = "footercopyright">  &copy; 1999 &ndash; 2007 Find your junk. All rights reserved. &nbsp; * must be logged in.</div>
        <!--footer-->

      </div>
      <!--navfooter-->

    </div>
    <!--middle_column-->

    <div id="right_column">

      <div id="login_box"></div>
      <div id="login_content">

        <?php

if ($_SESSION['is_authorized'] != 1)
{
    echo <<<EOT

<table cellspacing=0 cellpadding=0 border=0>
<form action="{$site_globals['ROOT_PATH']}/public_html/" method="post">
<tr><td style="color:white; font-size:9px">Login Name</td><td style="color:white; font-size:9px">Password</td><td></td</tr>
<tr><td><input name="login" id="login" type="text" style="width:120px; margin-right:3px" ></td><td><input name="pass" id="pass" type="password" style="width:120px;margin-right:3px" value=""></td><td><input style="width:40px" type="submit" value="Go!"></td></tr>
<tr><td align="right" colspan="3"><a style="color:blue;font-size:12px" href="?c=user_form">not yet registered?</a></td><td></td></tr>
<input type="hidden" name="c" id="c" value="{$_REQUEST['c']}">
</form></table>

<script>$('login').focus();</script>

EOT;
}
else
{
    echo <<<EOT
    <table border ="0" width = "90%">
     <tr>
      <td><a class= "nav" href="?logout=1">Signout {$_SESSION['login']}</a></td>
      <td><a class= "nav" href="?c=found_item_form">Post Found Item</a></td>
     </tr>
      <td><a class= "nav" href="?c=user_home">My Home</a></td>
      <td><a class= "nav" href="?c=lost_item_form">Post Lost Item</a></td>
     </tr>
    </table>

EOT;
}

?>

      </div>

      <?php  include('quick_search_template.php'); ?>

    </div>

  </body>
</html>
