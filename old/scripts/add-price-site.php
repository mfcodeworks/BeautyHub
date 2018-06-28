<?php
    require_once 'functions.php';
    extract($_POST);

    //Get product ID
    $id = $_SESSION['product-view']->getID();

    //Create array
    $priceSite = [
        "price" => $price,
        "site" => $site,
        "siteName" => $siteName
    ];

    //Get old array from DB
    $conn = sqlConnect();
    $sql = "SELECT price_site 
            FROM products
            WHERE ID = $id;";
    $result = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($result)) {
        $currentPriceSite = json_decode($row["site_price"],true);
    }

    // Update array
    $oldPriceSite[] = $priceSite;

    // Update product
    $priceSite = json_encode($oldPriceSite);
    $sql = "UPDATE products
            SET price_site = '$priceSite'
            WHERE ID = $id;";

    if(mysqli_query($conn,$sql)) {
        mysqli_close($conn);
        echo "true";
    }
    else {
        mysqli_close($conn);
    }
?>
