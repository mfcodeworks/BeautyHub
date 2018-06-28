<?php
    require_once 'functions.php';
    extract($_POST);

    try {
        echo product::newProduct($productName,$productBrand,$productType,$productImg,$productDescription,$productShades,$productRating,$productPrice,$productSite);
    }
    catch(Exception $exc) {
        echo $exc;
    }
?>
