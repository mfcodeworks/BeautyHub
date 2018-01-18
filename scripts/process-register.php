<?php
    require_once 'functions.php';
    session_start();
    extract($_POST);

    // If make user successful, set logged in and return true
    try { $user = new user($username, $password, $email); }
    catch(Exception $exc) { echo $exc->getMessage(); }

    if(isset($user) && $user->getID() != null)
        echo "true";

    // If failed return false
    else echo "false";
?>
