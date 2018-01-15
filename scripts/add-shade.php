<?php
    require_once "functions.php";
    extract($_POST);

    // Connect to DB
    $conn = sqlConnect();
    // Get shades for product
    $sql = "SELECT shade FROM products WHERE ID=$id;";
    $result = mysqli_query($conn,$sql);
    // If result exists, save result
    if($result) {
        while($row = mysqli_fetch_assoc($result))
            $shadeList = $row['shade'];
    }
    // If no result product doesn't exist
    else {
        echo "Product doesn't exist.\n";
        return false;
    }

    // If there's existing shades
    if(isset($shadeList)) {

        // Get shades as array
        if(strpos($shadeList,',') != false) $shadeList = explode(",",$shadeList);
        else $shadeList = array($shadeList);

        // If new shade isn't in array, add it to array
        if(!in_array($shade,$shadeList)) {
            array_push($shadeList,$shade);
            $shadeList = implode(",",$shadeList);
        }
    }

    // If no shades exist, add this alone
    else {
        $shadeList = $shade;
    }

    // Add shades back to DB
    $shadeList = trim($shadeList,",");
    $sql = "UPDATE products SET shade = \"$shadeList\" WHERE ID=$id;";
    echo "SQL: $sql\n\n";
    // If query successful return true
    if(mysqli_query($conn,$sql)) {
        mysqli_close($conn);
        echo "Product saved successfully.\n";
        return true;
    }
    // If unsuccessful return error
    else {
        echo "Error saving shades to product.\n";
        return false;
    }
?>
