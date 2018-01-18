<?php
    require_once 'functions.php';
    session_start();
    extract($_POST);

    //Get user info
    $user = $_SESSION['username'];
    $userID = getUserID($user);
    $userID = $userID[0];
    $wishlist = "$productID,$productShade,";
    $result = expandColumn($userID,$wishlist,'wishlist','users');
    if($result == "1") echo "true";
?>
