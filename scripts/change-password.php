<?php
    require_once 'functions.php';
    session_start();
    extract($_POST);

    //Set password
    try {
        $_SESSION['user']->changePassword($oldPassword,$newPassword);
        echo "true";
    }
    catch(Exception $exc) {
        echo $exc;
    }
?>
