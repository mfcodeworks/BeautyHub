<?php
    require_once 'functions.php';
    session_start();
    extract($_POST);

    //Set user
    $user = $_SESSION['user'];

    //Set product var
    $product = explode(" - ",$product);
    foreach($product as $p) {
        $productInfo[] = trim($p);
    }
    $product = $productInfo;

    //Get product info
    $conn = sqlConnect();
    $sql = "SELECT ID
            FROM products
            WHERE brand = \"$product[0]\"
            AND name = \"$product[1]\";
            ";
    $result = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($result)) {
        $id = $row['ID'];
    }
    if(isset($id)) {
        if(isset($product[2])) {
            $shade = $product[2];
        }
        else {
            $shade = "NULL";
        }
    }
    else {
        mysqli_close($conn);
        die("Product not found");
    }

    //Save favourite
    $sql = "SELECT favourites
            FROM users
            WHERE ID = ".$user->getID().";
            ";
    $result = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($result)) {
        $favourites = json_decode($row['favourites'],true);
    }

    //Check if in favourites
    $found = false;
    foreach($favourites as $product) {
        if($found == false) {
            if($product['id'] == $id && $product['shade'] == $shade) {
                $found = true;
            }
        }
    }
    if($found) {
        mysqli_close($conn);
        die("Product already in favourites");
    }
    else {
        $favourites[] = [
            'id' => $id,
            'shade' => $shade
        ];
        $favourites = json_encode($favourites);
        $sql = "UPDATE users
                SET favourites = '$favourites'
                WHERE ID=" . $user->getID() . ";
                ";
        echo $sql;
        if(mysqli_query($conn,$sql)) {
            mysqli_close($conn);
            die("true");
        }
        else {
            mysqli_close($conn);
            die("Couldn't save favourites");
        }
    }
?>