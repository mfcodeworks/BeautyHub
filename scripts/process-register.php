<?php
    require_once 'functions.php';
    session_start();
    extract($_POST);

    // If make user successful, set logged in and return true
    $user = new user($username, $password, $email);
    if($user != null)
        echo "true";

    // If failed return false
    else echo "false";
?>
