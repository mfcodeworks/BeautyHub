<?php
    require_once 'functions.php';
    extract($_POST);

    //Add report
    $conn = sqlConnect();
    $sql = "UPDATE users 
            SET reports = reports + 1 
            WHERE ID = $id;";

    //Return result
    if(mysqli_query($conn,$sql)) die("true");
    else die("Error. Report not sent.");
?>
