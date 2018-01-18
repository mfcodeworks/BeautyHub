<?php
    require_once 'functions.php';
    session_start();
    extract($_POST);

    //Try to upload file
    if($_FILES['profilePicUpload']['name']!='') {
        echo "File found\n\n";
        $f = $_FILES['profilePicUpload'];
        if(checkUploadImage($f['name'],$f['tmp_name'])) {
            echo "Image checked\n\n";
            $source = $f['tmp_name'];
            $target = getFileTarget($f['name']);
            if(uploadFile($source,$target)) $profilePicLocation = "img/".basename($target);
            else die("Could not upload image");
        }
    }
    else die("Couldn't upload profile pic");

    //Update profile with image location
    echo "Profile pic at $profilePicLocation\n\n";
    $user = $_SESSION['username'];
    $conn = sqlConnect();
    $sql = "UPDATE users SET profile_img = \"$profilePicLocation\" WHERE username = \"$user\";";
    if(mysqli_query($conn,$sql)) echo "true";
    else die("Couldn't update profile");
?>
