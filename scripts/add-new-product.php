<?php
    require_once 'functions.php';
    extract($_POST);

    //Get new product info
    $id = getMaxId('products');

    //Enter info into DB
    $conn = sqlConnect();
    $sql = "INSERT INTO products(id,name,brand,shade,product_type,rating,price_site) VALUES($id,\"$productName\",\"$productBrand\"";

    if($productShades != "") {
        $sql .= ",\"$productShades\"";
    }
    else $sql .= ",NULL";


    if($productType != "") {
        $sql .= ",\"$productType\"";
    }
    else $sql .= ",NULL";

    if($productRating != "") {
        $productRating = round($productRating,2);
        $sql .= ",$productRating";
    }
    else $sql .= ",NULL";


    if($productSite != "" && $productPrice != "") {
        $productSitePrice = $productPrice . "," . $productSite;
        $sql .= ",\"$productSitePrice\"";
    }
    else $sql .= ",NULL";

    $sql.=");";

    if(!mysqli_query($conn,$sql)) echo("Error. Could not save product.");
    else echo "$id";
?>
