<?php
    require_once 'functions.php';
    session_start();
    extract($_POST);

    //Get user info
    $user = $_SESSION['user'];
    $userID = $user->getID();

    //Get current wishlist
    $conn = sqlConnect();
    $sql = "SELECT wishlist FROM users WHERE ID = $userID;";
    $result = mysqli_query($conn,$sql);
    if($result) {
        while($row = mysqli_fetch_assoc($result)) {
            $wishlist = $row['wishlist'];
        }
    }
    else echo false;

    //Remove product/shade from wishlist
    $remove = "$id,$shade,";
    if(strpos($wishlist,$remove) == 0) $wishlist = str_replace($remove, "", $wishlist);
    else if(strpos($wishlist,$remove) > 0) {
        $remove = ",$id,$shade,";
        $wishlist = str_replace($remove, ",", $wishlist);
    }
    else echo false;


    $wishlist = str_replace(",$id,$shade,", ",", $wishlist);
    $wishlist = ltrim($wishlist,",");

    //Put wishlist back
    $sql = "UPDATE users SET wishlist = '$wishlist' WHERE ID = $userID;";
    if(mysqli_query($conn,$sql)) {
        mysqli_close($conn);
        echo true;
    }
    else {
        mysqli_close($conn);
        echo false;
    }
?>
