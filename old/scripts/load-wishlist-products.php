<?php
    require_once "functions.php";
    session_start();
    extract($_POST);
    try {
        loadWishlistProducts();
    }
    catch(Exception $exc) {
        echo $exc;
    }
?>
