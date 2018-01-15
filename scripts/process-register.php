<?php
    session_start();
    require_once 'functions.php';
    extract($_POST);

    // If make user successful, set logged in and return true
    $user = new user($username, $password, $email);
    if($user != null)
        echo "true";

    // If failed return false
    else echo "false";
?>
