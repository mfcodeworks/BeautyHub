<?php
    require_once 'functions.php';
    session_start();
    extract($_POST);

    $data = explode(",",$data);
    if(!isset($data[1])) $data[1] = "NULL";
    $data = [
        'id' => $data[0],
        'shade' => $data[1]
    ];

    //Set user ID
    $userID = $_SESSION['user']->getID();

    //Get favourite
    $conn = sqlConnect();
    $sql = "SELECT favourites
            FROM users
            WHERE ID = $userID;
            ";
    $result = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($result)) {
        $favourites = json_decode($row['favourites'],true);
    }

	//Remove product/shade from favourites
	$i = 0;
	$found = false;
    foreach($favourites as $product) {
        if($found == false) {
            if($product['id'] == $data['id'] && $product['shade'] == $data['shade']) {
				$found = true;
				unset($favourites[$i]);
            }
		}
		$i++;
	}
    $favourites = array_values($favourites);
    
    //Put favourites back
    $favourites = json_encode($favourites);
    $sql = "UPDATE users 
            SET favourites = '$favourites' 
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