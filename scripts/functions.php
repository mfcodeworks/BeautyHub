<?php
    /* Created by Musa Fletcher         *
     * Licensed under Apache 2 license  *
     *                                  */

    if(!defined("ABSPATH")) define( 'ABSPATH', dirname(dirname(__FILE__)) . '/' );
    
    // Require config files
    require_once(ABSPATH . "config.php");
    require_once(ABSPATH . "vendor/autoload.php");

    // Require classes
    require_once(ABSPATH . "class/product.php");
    require_once(ABSPATH . "class/user.php");
    require_once(ABSPATH . "class/profile.php");
    require_once(ABSPATH . "class/comment.php");
    require_once(ABSPATH . "class/post.php");

    // Require script functions
    require_once(ABSPATH . "scripts/functions/Encoding.php");
    require_once(ABSPATH . "scripts/functions/generic-functions.php");
    require_once(ABSPATH . "scripts/functions/page-load-functions.php");
    require_once(ABSPATH . "scripts/functions/product-functions.php");
    require_once(ABSPATH . "scripts/functions/sql-functions.php");
    require_once(ABSPATH . "scripts/functions/upload-file-functions.php");
?>
