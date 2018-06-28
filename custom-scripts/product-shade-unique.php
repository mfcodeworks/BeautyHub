<?php
    function sqlConnect() {
        return(mysqli_connect("localhost","root","","project1"));
    }

    $conn = sqlConnect();
    $sql = "SELECT id,shade_img 
            FROM products
            WHERE shade_img IS NOT NULL;";
    $result = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($result)) {
        $products[] = [
            "id" => $row["id"],
            "shades" => json_decode($row["shade_img"],true)
        ];
    }
    mysqli_close($conn);
    
    for($i = 0; $i < count($products); $i++) {
        $currentProduct = $products[$i];
        $shadeArray = $currentProduct["shades"];
        $newShadeArray = [];
        $existingShades = [];

        for($j = 0; $j < count($shadeArray); $j++) {
            if( in_array($shadeArray[$j]["shade"], $existingShades) == false ) {
                $existingShades[] = $shadeArray[$j]["shade"];
                $newShadeArray[] = [
                    "shade" => $shadeArray[$j]["shade"],
                    "img" => $shadeArray[$j]["img"]
                ];
            }
        }

        $newShadeArray = json_encode($newShadeArray);
        $conn = sqlConnect();
        $sql = "UPDATE products
                SET shade_img = '$newShadeArray'
                WHERE id = " . $currentProduct["id"] . ";";
        if(mysqli_query($conn,$sql)) {
            echo "Product updated " . $i . "/" . count($products) . "\n";
        }
        else {
            echo "Product update failed\n$sql\n";
        }
        mysqli_close($conn);

        unset($currentProduct);
        unset($shadeArray);
        unset($newShadeArray);
        unset($existingShades);
    }
    
?>