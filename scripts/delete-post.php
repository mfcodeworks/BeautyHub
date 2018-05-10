<?php
    require_once("functions.php");
    extract($_POST);

    //Connect to DB
    $conn = sqlConnect();
    $sql = "DELETE FROM posts
            WHERE ID = $id;";
    if(mysqli_query($conn,$sql)) {
        die("true");
    }
    else {
        die("Error occured deleting post");
    }
?>