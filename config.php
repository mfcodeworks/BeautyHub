<?php
    /**
     * Set absolute config variables
     */
    define('DB_SERVER','localhost');
    define('DB_USER','root');
    define('DB_PASSWORD','');
    define('DB','project1');
    define('ADMIN_EMAIL','admin@email.com');
    define('WEBMASTER_EMAIL','webmaster@website.com');
    define('IMAGE_DIR', dirname(dirname(__FILE__)) . '/img/');
    define('RELATIVE_IMAGE_DIR', "/img/");
    
    /**
     * SESSION settings for user data
     */

    // Server should keep session data for AT LEAST 1 week
    ini_set('session.gc_maxlifetime', 604800);
    ini_set('session.cookie_lifetime',604800);
    
    // Each client should remember their session id for EXACTLY 1 week
    session_set_cookie_params(604800);
?>
