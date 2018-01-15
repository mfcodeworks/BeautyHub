<?php
    session_start();
    extract($_POST);
    require_once 'functions.php';

    //Get user info
    $user = $_SESSION['username'];
    $userID = getUserID($user);
    $userID = $userID[0];
    $wishlist = "$productID,$productShade,";
    $result = expandColumn($userID,$wishlist,'wishlist','users');
    if($result == "1") echo "true";
?>
