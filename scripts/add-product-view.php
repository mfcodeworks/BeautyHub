<?php
    require_once 'functions.php';
    extract($_POST);

    //Add product view
    $conn = sqlConnect();
        $sql = "UPDATE products SET view_count = view_count + 1 WHERE ID = $id;";

    //Return result
    if(mysqli_query($conn,$sql)) echo "true";
    else echo "Error. View count not updated.";
?>
