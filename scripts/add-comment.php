<?php
    require_once 'functions.php';
    session_start();
    extract($_POST);

    //Get user/product info
    $comment = new comment();

    //For each file try to upload
    if($_FILES['authorImg']['name'][0]!='') {
        for($i=0;$i<count($_FILES['authorImg']['name']);$i++) {
            $f = $_FILES['authorImg'];
            if(checkUploadImage($f['name'][$i],$f['tmp_name'][$i])) {
                $source = $f['tmp_name'][$i];
                $target = getFileTarget($f['name'][$i]);
                if(uploadFile($source,$target))
                    $comment->addMedia("img/".basename($target));
                else 
                    die("Could not upload image");
            }
        }
    }

    //Set comment product
    $comment->forProduct($_SESSION['product-view']->getID());
    $comment->saveContent($authorReview);
    //Check fields exist
    if(isset($authorRating)) {
        $comment->setRating($authorRating);
    }

    try {
        //Save comment
        $comment->save();

        /**
         * Save product rating
         */
        //Get all ratings
        $conn = sqlConnect();
        $sql = "SELECT rating FROM comments WHERE product_id = ".$_SESSION['product-view']->getID().";";
        $result = mysqli_query($conn,$sql);

        //Get amount of ratings
        if(mysqli_num_rows($result) > 0 ) {
            $totalRatings = mysqli_num_rows($result);
            $newRating = 0;
            while($row = mysqli_fetch_assoc($result)) {
                $userRatings[] = $row['rating'];
            }
            foreach($userRatings as $r) {
                $newRating += $r;
            }
            $finalRating = $newRating/$totalRatings;

            //Update ratings
            $sql = "UPDATE products 
                    SET rating = $finalRating 
                    WHERE ID=".$_SESSION['product-view']->getID().";";
            if(mysqli_query($conn,$sql)) {
                echo loadComments();
            }
            else {
                echo "false";
            }
            mysqli_close($conn);
            unset($_FILES['authorImg']);
        }
    }
    catch(Exception $exc) {
        echo $exc;
    }
?>
