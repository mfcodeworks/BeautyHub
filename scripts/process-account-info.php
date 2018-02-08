<?php
    require_once 'functions.php';
    session_start();
    extract($_POST);

    //Get user ID
    $userID = $_SESSION['user']->getID();

    //Get current info
    $conn = sqlConnect();
    $sql = "SELECT social_links,bio,foundation
            FROM users
            WHERE ID = $userID;";
    $result = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($result)) {
        $links = json_decode($row['social_links'],true);
        $db_bio = $row['bio'];
        $db_foundation = json_decode($row['foundation'],true);
    }

    //Update profile info
    if(isset($facebook) && $facebook != "") {
        $links['facebook'] = $facebook;
    }
    if(isset($twitter) && $twitter != "") {
        $links['twitter'] = $twitter;
    }
    if(isset($instagram) && $instagram != "") {
        $links['instagram'] = $instagram;
    }
    if(isset($youtube) && $youtube != "") {
        $links['youtube'] = $youtube;
    }
    if(isset($pinterest) && $pinterest != "") {
        $links['pinterest'] = $pinterest;
    }
    if(isset($foundation) && $foundation != "") {

        //Set product var
        $product = explode(" - ",$foundation);
        foreach($product as $p) {
            $productInfo[] = trim($p);
        }
        $product = $productInfo;
        unset($productInfo);
        
        //Get product info
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
            echo("Product not found");
        }

        //Save new foundation
        $db_foundation['id'] = $id;
        $db_foundation['shade'] = $shade;
    }
    if(isset($bio) && $bio != "") {
        $db_bio = $bio;
    }

    //Put info back
    $links = json_encode($links);
    $db_foundation = json_encode($db_foundation);

    echo $links . "\n";
    echo $db_foundation . "\n";
    echo $db_bio . "\n";

    $sql = "UPDATE users
            SET foundation = '$db_foundation',
                bio = '$db_bio',
                social_links = '$links'
            WHERE ID = $userID;";
    if(mysqli_query($conn,$sql)) {
        mysqli_close($conn);
        die("true");
    }
    else {
        mysqli_close($conn);
        die("Error saving info");
    }
?>
