<?php
    require_once 'functions.php';
    session_start();
    extract($_POST);

    //If logged in, return true
    if(isset($_SESSION['user'])) echo "true";

    //If login successful, return true
    $user = new user($username, $password);
    if($user->getID() != null)
        echo "true";

    //If login failed return false
    else echo "false";
?>