<?php
    require_once 'functions.php';
    session_start();
    extract($_POST);

    //Set current user
    $user = $_SESSION['user'];

    //Get following
    $conn = sqlConnect();
    $sql = "SELECT following
            FROM users
            WHERE ID = " . $user->getID() . ";";
    $result = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($result)) {
        $following = json_decode($row['following'],true);
    }

    //Check if following
    if(!in_array($id,$following)) {
        //Add to following
        array_push($following,$id);

        //Save following
        $following = json_encode($following);
        $sql = "UPDATE users
                SET following = '$following'
                WHERE ID = " . $user->getID() . ";";
        //Return result
        if(mysqli_query($conn,$sql)) die("true");
        else die("Error followig user.");
    }
    else {
        die("Already following user.");
    }
?>
