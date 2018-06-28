<?php
    require_once("functions.php");
    extract($_POST);

    //Check if product is in wishlist
    $currentUser = new profile($_SESSION['user']->getID());
    if( $currentUser->isInWishlist($id,$shade) ) {
        die("true");
    }
    else {
        die("false");
    }
?>