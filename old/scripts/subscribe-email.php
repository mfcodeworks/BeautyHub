<?php
    require_once "functions.php";
    extract($_POST);

    //Check if email exists
    if(sqlExists($email,"email","email_list")) {
        echo "true";
        return;
    }

    //Connect to DB
    $conn = sqlConnect();
    //Get ID
    $id = getMaxId("email_list");
    //Buld SQL
    $sql = "INSERT INTO email_list(ID,email)
            VALUES($id,'$email');";
    if(mysqli_query($conn,$sql)) {
        echo "true";
        return;
    }
    else {
        mysqli_close($conn);
        echo "false";
        return;
    }
?>