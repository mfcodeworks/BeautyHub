<?php
    require_once 'functions.php';
    session_start();
    extract($_POST);

    //Try to upload file
    if($_FILES['profilePicUpload']['name'] != '') {
        //echo "File found\n\n";

        //Set var for file
        $f = $_FILES['profilePicUpload'];

        //If image is uploadable
        if(checkUploadImage($f['name'],$f['tmp_name'])) {
            //echo "Image checked\n\n";

            //Set source and target for file
            $source = $f['tmp_name'];
            $target = getFileTarget($f['name'], $_SESSION['user']->getUsername() . "/");
            if(uploadFile($source,$target)) $profilePicLocation = RELATIVE_IMAGE_DIR . $_SESSION['user']->getUsername() . "/" . basename($target);
            else die("Could not upload image");
        }
    }
    else die("Couldn't upload profile pic");

    //Update profile with image location
    //echo "Profile pic at $profilePicLocation\n\n";
    $user = $_SESSION['user']->getUsername();

    if($profilePicLocation != "" && $profilePicLocation != " " && $profilePicLocation != NULL) {
        //Connect to DB
        $conn = sqlConnect();
        //Build SQL 
        $sql = "UPDATE users 
                SET profile_img = \"$profilePicLocation\" 
                WHERE username = \"$user\";";
        //Run SQL
        if(mysqli_query($conn,$sql)) {
            mysqli_close($conn);
            echo "true,$profilePicLocation";
        }
        else {
            mysqli_close($conn);
            die("Couldn't update profile");
        }
    }
    else die("Couldn't upload image");

?>
