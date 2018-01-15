<?php
    session_start();
    require_once 'functions.php';
    extract($_POST);

    //Get product ID
    $id = $_SESSION['product-view'];

    //Create info string
    $priceSite = "$price,$site,$siteName";

    //Enter into DB
    $result = expandColumn($id,$priceSite,'price_site','products');

    //Return result
    echo "$result";
?>
