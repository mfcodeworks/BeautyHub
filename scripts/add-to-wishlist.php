<?php
    require_once 'functions.php';
    session_start();
    extract($_POST);

    /**
     * POST:
     *     Product ID: $productID
     *     Product Shade: $productShade
     */

    //Get user info
    $userID = $_SESSION['user']->getID();

    //Connect to DB
    $conn = sqlConnect();

    //Build SQL
    $sql = "SELECT wishlist
            FROM users
            WHERE ID=$userID;";

    //Get wishlist
    $result = mysqli_query($conn,$sql);

    if ( isset($result) ) {
        while($row = mysqli_fetch_assoc($result)) {
            $wishlist = $row['wishlist'];
        }
    }

    //Decode wishlist 
    if(isset($wishlist)) {
        $wishlist = json_decode($wishlist,true);
    }

    //Set found as false
    $found = false;
    foreach($wishlist as $product) {
        if($found == false) {
            if($product['id'] == $productID && $product['shade'] == $productShade) {
                $found = true;
            }
        }
    }

    //If not found add product, otherwise exit
    if($found == false) {
        $wishlist[] = [
            "id" => $productID,
            "shade" => $productShade
        ];
    }
    else {
        mysqli_close($conn);
        echo "true";
        return;
    }

    //Put wishlist back in DB
    $wishlist = json_encode($wishlist);
    $sql = "UPDATE users
            SET wishlist = '$wishlist'
            WHERE ID = $userID;";

    //Check success
    if(mysqli_query($conn,$sql)) {
        echo "true";
        mysqli_close($conn);
        return;
    }
    else {
        echo "Error putting wishlist in database";
        mysqli_close($conn);
        return;
    }
?>
