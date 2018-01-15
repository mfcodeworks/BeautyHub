<?php
    session_start();
    //Logout
    unset($_SESSION['user']);
    echo "true";
?>
