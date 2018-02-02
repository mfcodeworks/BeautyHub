<?php
    require_once "functions.php";
    extract($_POST);
    try {
        loadWishlistProducts($user);
    }
    catch(Exception $exc) {
        echo $exc;
    }
?>
