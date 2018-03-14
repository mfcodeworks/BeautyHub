<?php
    require_once "functions.php";
    session_start();
    extract($_POST);

    //Set product ID
    $thisProduct = $_SESSION['product-view']->getID();

    //Set dupe shade
    if($thisShade != null) {
        if( !in_array($thisShade,$_SESSION['product-view']->getShades()) ) {
            $dupe['thisShade'] = "NULL";
        }
        else {
            $dupe['thisShade'] = $thisShade;
        }
    }
    else {
        $dupe['thisShade'] = "NULL";
    }

    //Get Dupe ID
    $dupeInfo = explode(" - ",$dupeName);
    $dupeBrand = $dupeInfo[0];
    $dupeName = $dupeInfo[1];
    $conn = sqlConnect();
    $sql = "SELECT ID
            FROM products
            WHERE brand = \"$dupeBrand\"
            AND name = \"$dupeName\";
            ";
    $result = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($result)) {
        $dupe['dupeID'] = $row['ID'];
    }
    mysqli_close($conn);

    //Set dupe shade
    if($dupeShade == null
    || $dupeShade == ""
    || $dupeShade == " ") {
        $dupe['dupeShade'] = "NULL";
    }
    else {
        $dupe['dupeShade'] = $dupeShade;
    }

    //Push dupe to array 
    $found = false;
    //Check if dupe exists
    foreach($_SESSION['product-view']->getDupes() as $existingDupe) {
        if($existingDupe['dupeID'] == $dupe['dupeID']
        && $existingDupe['dupeShade'] == $dupe['dupeShade']
        && $existingDupe['thisShade'] == $dupe['thisShade']) {
            $found = true;
        }
    }
    if($found) {
        die("Dupe exists");
    }
    else {
        $dupes = $_SESSION['product-view']->getDupes();
        array_push($dupes,$dupe);
        $conn = sqlConnect();
        $dupes = json_encode($dupes);
        $sql = "UPDATE products
                SET dupes = '$dupes'
                WHERE ID = ".$_SESSION['product-view']->getID().";
                ";
        if(mysqli_query($conn,$sql)) {
            mysqli_close($conn);
            die("Dupe added successfully");
        }
        else {
            mysqli_close($conn);
            die("Failed to add new dupe");
        }
    }
?>