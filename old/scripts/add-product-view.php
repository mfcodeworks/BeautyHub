<?php
    require_once 'functions.php';
    extract($_POST);

    //Add product view
    $conn = sqlConnect();
        $sql = "UPDATE products SET view_count = view_count + 1 WHERE ID = $id;";

    //Return result
    if(mysqli_query($conn,$sql)) die("true");
    else die("Error. View count not updated.");
?>
