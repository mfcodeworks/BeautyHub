<?php
    require_once 'functions.php';
    session_start();
    extract($_POST);

    //Set password
    if($_SESSION['user']->changePassword($oldPassword,$newPassword)) echo "true";
    else echo "false";
?>
