<?php
    require_once "functions.php";
    extract($_POST);

    //Connect to DB
    $conn = sqlConnect();
    //Get distinct usernames matching at tag
    $sql = "SELECT DISTINCT username
            FROM users 
            WHERE LOWER(username) LIKE LOWER('%$tag%');
            ";
    $result = mysqli_query($conn,$sql);
    //Check for result
    if($result) {
        //Save results to array
        while($row = mysqli_fetch_assoc($result)) {
            $usernames[] = $row['username'];
        }
        mysqli_close($conn);
        //If array exists return JSON encoded array
        if(isset($usernames)) {
            mysqli_close($conn);
            die(json_encode($usernames));
        }
        else {
            die("false");
        }
    }
    else {
        mysqli_close($conn);
        die("false");
    }
    

?>