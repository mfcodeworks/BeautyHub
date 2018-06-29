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

    for($i = 0; $i < count($products); $i++) {
        $currentProduct = $products[$i];
        for($j = 0; $j < count($currentProduct["shades"]); $j++) {
            $product_id = $currentProduct["id"];
            $product_shade = $currentProduct["shades"][$j]["shade"];
            $product_shade_image = $currentProduct["shades"][$j]["img"];
            $sql = "INSERT INTO product_shades(product_id,shade,image)
                    VALUES($product_id,\"$product_shade\",\"$product_shade_image\");";
            if(mysqli_query($conn,$sql)) {
                echo "Created shade $j/" . count($currentProduct["shades"]) . " for product $i/" . count($products) . "\n";
            }
            else {
                echo "Failed saving shade $j/" . count($currentProduct["shades"]) . " for product $i/" . count($products) . "\n";
            }
            unset($product_id);
            unset($product_shade);
            unset($product_shade_image);
        }
        unset($currentProduct);
    }
    mysqli_close($conn);
    
?>