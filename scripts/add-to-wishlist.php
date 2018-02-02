<?php
    require_once 'functions.php';
    session_start();
    extract($_POST);

    //Get user info
    $user = $_SESSION['user'];
    $userID = $user->getID();
    $wishlist = "$productID,$productShade,";
    $result = expandColumn($userID,$wishlist,'wishlist','users');
    if($result == "1") echo "true";
?>
