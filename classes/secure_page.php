<?php


class secure_page {

    function secure_page()
    {
        global $site_globals;
        
        if(!$_SESSION['is_authorized'])
        {
            destroy_session();
            header("location: ?c=home");
        }
    } 
}

?>
