<?php
    session_start();
    require_once 'functions.php';
    extract($_POST);

    //Set password
    if($_SESSION['user']->changePassword($oldPassword,$newPassword)) echo "true";
    else echo "false";
?>
