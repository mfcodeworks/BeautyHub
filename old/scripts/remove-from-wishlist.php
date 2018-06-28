<?php
    require_once 'functions.php';
    session_start();
    extract($_POST);

    /**
     * POST:
     * 	   Product ID: $id
     *     Product Shade: $shade
     */

    //Get user info
    $userID = $_SESSION['user']->getID();

    //Get current wishlist
    $conn = sqlConnect();
    $sql = "SELECT wishlist 
            FROM users 
            WHERE ID = $userID;";
    $result = mysqli_query($conn,$sql);
    if($result) {
        while($row = mysqli_fetch_assoc($result)) {
            $wishlist = json_decode($row['wishlist'],true);
        }
    }
    else {
        echo false;
        return;
    }

	//Remove product/shade from wishlist
	$i = 0;
	$found = false;
    foreach($wishlist as $product) {
        if($found == false) {
            if($product['id'] == $id && $product['shade'] == $shade) {
				$found = true;
				unset($wishlist[$i]);
            }
		}
		$i++;
	}
	$wishlist = array_values($wishlist);

    //Put wishlist back
    $wishlist = json_encode($wishlist);
    $sql = "UPDATE users 
            SET wishlist = '$wishlist' 
            WHERE ID = $userID;";
    if(mysqli_query($conn,$sql)) {
        mysqli_close($conn);
        echo "true";
        return;
    }
    else {
        mysqli_close($conn);
        echo "false";
        return;
    }
?>
