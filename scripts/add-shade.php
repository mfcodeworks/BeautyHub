<?php
    require_once "functions.php";
    extract($_POST);

    // Connect to DB
    $conn = sqlConnect();

    // Check product exists
    try {
        $product = new product($id);
    }
    catch(Exception $exc) {
        die($exc."\n");
    }

    // Get shades for product
    $sql = "SELECT shade_img
            FROM products 
            WHERE ID = $id;";

    $result = mysqli_query($conn,$sql);

    // If result exists, save result
    while($row = mysqli_fetch_assoc($result)) {
        $shadeList = json_decode($row['shade_img'],true);
    }

    //Add new shade
    $shadeList[] = array(
        "shade" => $shade,
        "img" => NULL
    );

    //Save shades
    $shadeList = json_encode($shadeList);
    $sql = "UPDATE products 
            SET shade_img = '$shadeList'
            WHERE ID = $id;
            ";
    echo "SQL: $sql\n\n";

    // If query successful return true
    if(mysqli_query($conn,$sql)) {
        mysqli_close($conn);
        $message = "
        <html>
            <body>
                <div style='text-align: center'>
                    <h2>New product shade added</h2>
                    <p>A new shade was added for product $id, ".$product->getName().".<br>
                    An image is needed for this shade.</p>
                </div>
            </body>
        </html>";
        mailMessage($message,"<BeautyHub> New Shade Added, Product $id");
    }
    // If unsuccessful return error
    else {
        mysqli_close($conn);
        die("Error saving shades to product.\n");
    }
?>
