<?php
    require_once 'functions.php';
    session_start();
    extract($_POST);

    //If user is logged in, return true
    if(isset($_SESSION['user']) && $_SESSION['user']->getID() != null) echo "true";

    //If login successful, print login form
    else {
        $user = new user($username,$password);
        if($user->getID() != null) {
            loadCommentForm();
        }
        //If login failed, return false
        else echo "false";
    }
?>
