<?php
    require_once 'functions.php';
    session_start();
    extract($_POST);

    //Check for photos
    if($_FILES['postPicUpload']['name'][0] != '') {
        //For every photo attached
        for($i = 0; $i < count($_FILES['postPicUpload']['name']); $i++) {
            $f = $_FILES['postPicUpload'];
            //If image is uploadable
            if(checkUploadImage($f['name'][$i],$f['tmp_name'][$i])) {
                //Set source and target for file
                $source = $f['tmp_name'][$i];
                //Custom directory
                $customDir = $_SESSION['user']->getUsername() . "/";
                $target = getFileTarget($f['name'][$i], $customDir);
                //Try uploading image
                if(uploadFile($source,$target)) $picLocation[] = RELATIVE_IMAGE_DIR . $customDir . basename($target);
                else die("Could not upload image");
            }
            else {
                die("Couldn't upload image");
            }

        }
    }

    //Handle post data
    $post = new post();
    //echo "New post $newPost";
    $post->content($newPost);
    $post->media($picLocation);
    //$post->album($newPostAlbum);
    try {
        $post->save();
    }
    catch(Exception $exc) {
        echo $exc;
    }
?>