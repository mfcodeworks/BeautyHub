<?php
    require_once 'functions.php';
    session_start();
    extract($_POST);

    //Get user/product info
    $userID = $_SESSION['user']->getID();
    $productName = $_SESSION['product-view'];
    $date = getDateNow();
    $commentID = getMaxId('comments');

    //For each file try to upload
    if($_FILES['authorImg']['name'][0]!='') {
        for($i=0;$i<count($_FILES['authorImg']['name']);$i++) {
            $f = $_FILES['authorImg'];
            if(checkUploadImage($f['name'][$i],$f['tmp_name'][$i])) {
                $source = $f['tmp_name'][$i];
                $target = getFileTarget($f['name'][$i]);
                if(uploadFile($source,$target))
                  $sessionFiles[$i] = "img/".basename($target);
                else die("Could not upload image");
            }
        }
        //Create string of uploaded files
        $sessionFilesString = implode(',',$sessionFiles);
    }

    //Save comment to DB
    $conn = sqlConnect();
    //Check fields exist
    if(!isset($authorRating)) $authorRating=null;
    if(!isset($sessionFilesString)) $sessionFilesString = null;
    if(!isset($authorReview)) die("false");

    //Prepare SQL
    $sql = "INSERT INTO comments VALUES($commentID,\"$productName\",$userID,\"$authorReview\",\"$date\",\"$sessionFilesString\",$authorRating);";

    //If SQL successful
    if(mysqli_query($conn,$sql)) {

        //Save product rating
        //Get all ratings
        $sql = "SELECT rating FROM comments WHERE product_name = \"$productName\";";
        $result = mysqli_query($conn,$sql);

        //If no ratings, set total to 1
        if(mysqli_num_rows($result)>0) $totalRatings = mysqli_num_rows($result);
        else $totalRatings = 1;
        $newRating = 0;
        while($row = mysqli_fetch_assoc($result)) $userRatings[] = $row['rating'];
        foreach($userRatings as $r) $newRating += $r;
        $finalRating = $newRating/$totalRatings;

        //Update ratings
        $sql = "UPDATE products SET rating = $finalRating WHERE name=\"$productName\";";
        if(mysqli_query($conn,$sql)) echo loadComments();
        else echo "false";
        unset($_FILES['authorImg']);

    }
    else echo "false";
    unset($_FILES['authorImg']);
?>
