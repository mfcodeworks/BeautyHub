<?php
    // Get dependencies
    require_once "functions.php";

    $products = productURIScraper::productURIList(70000,34000);

    for($i = 0; $i < count($products); $i++) {
        try {
            $product = new temptaliaURIScraper($products[$i]);
            error_log( "Name ".$product->name());
            error_log( "Type ".$product->type());
            error_log( "Brand ".$product->brand());
            error_log( "Description ".$product->description());
            error_log( "Image URL ".$product->img());
            error_log( "Shade ".$product->shade());
            error_log( "Shade Image ".$product->shadeImg());
            unset($product);
        }
        catch(Excepton $exc) {
        }
    }

?>