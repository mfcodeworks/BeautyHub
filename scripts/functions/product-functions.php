<?php
// Format scraped Sephora description
function formatSephoraDescription($text,$heading)
{
    $pos = strpos($text,$heading);
    if($pos !== false) {
        $text = substr_replace($text,"</b><br>",$pos+strlen($heading),0);
        if($pos == 0) $text = substr_replace($text,"<b>",$pos,0);
        else $text = substr_replace($text,"<br><b>",$pos,0);
    }
    return $text;
};

// Check wishlist for duplicate entries
function checkWishlist($dataArray,$colArray)
{
    //Seperate into associative arrays
    $tempArray = $dataArray;
    $dataArray = [];
    for($i=0; $i<count($tempArray); $i+=2) {
        $dataArray += [
            $tempArray[$i] => $tempArray[$i + 1],
        ];
    }
    unset($tempArray);

    $tempArray = $colArray;
    $colArray = array();
    //For each item in existing column
    for($i=0; $i<count($tempArray); $i+=2) {
        if( !isset($colArray[ $tempArray[$i] ]) )
            $colArray[ $tempArray[$i] ] = [$tempArray[$i+1]];
        else
            array_push($colArray[ $tempArray[$i] ], $tempArray[$i+1]);
    }

    //If shade of product exists in data already, return false
    reset($dataArray);
    $key = key($dataArray);

    //Check product exists in array
    if( array_key_exists($key, $colArray) ) {
        echo "Product $key in favourites, checking shade\n\n";
        //Check shade of product already added
        if( in_array( $dataArray[$key], $colArray[$key] ) ) {
            echo "Product $key, shade $dataArray[$key] already in favourites\n";
            return false;
        }
    }
    return true;

};

//Load product for wishlist/category/etc.
function loadProduct($id,$page,$shade=NULL,$user=NULL) {
    if(session_status() == PHP_SESSION_NONE) session_start();

    //Get user ID
    if(!isset($user)) $user = $_SESSION['user']->getUsername();

    //Get product info
    $product = new product($id);

    //Set info
    $img = $product->getImg();
    $name = $product->getName();
    $brand = $product->getBrand();

    //Set size
    if($page == 'wishlist') echo "<div class='col-md-3 col-sm-4 display-product'>";
    else if($page == 'search') echo "<div class='col-md-4 col-sm-6 display-product'>";

    //Print product info
    echo "
        <div class='product' id='product-display-$id'>
            <a href='detail.php?id=$id'>
                <img src='$img' alt='$name' class='img-responsive'>
            </a>
            <div class='small text-center' >
                <h4><a href='detail.php?id=$id' style='color:#333'>$brand<br>$name";
    if(isset($shade)) echo " - Shade $shade";
            echo "</a></h4>
                <p class='buttons'>
                    <a href='detail.php?id=$id' class='btn btn-default'>View detail</a>
                </p>";
            if($page == 'wishlist')
                echo "<p class='buttons'>
                          <a href='javascript:void(0)' onclick='removeWishlist(\"$id,$shade,$user\")' class='btn btn-danger'>Remove from wishlist</a>
                      </p>";

            echo"
            </div>
            <!-- /.text -->
        </div>
        <!-- /.product -->
    </div>";
};

//Load wishlist product row
function loadWishlistProducts($user = NULL)
{
    session_start();
    echo "<div class='row products' id='wishlist-product-row'>";

    //Get user
    if(!isset($user)) $user = $_SESSION['user']->getUsername();

    //Get wishlist
    $wishlist = selectAll('wishlist','users','username',"$user");

    for($i = 0; $i < count($wishlist); $i += 2) {
        $wishlist_array[] = [
            "item" => new product($wishlist[$i]),
            "shade" => $wishlist[$i + 1]
        ];
    }

    //Print wishlist products
    foreach($wishlist_array as $p) {
        loadProduct($p['item']->getID(),'wishlist',$p['shade'],$user);
    }

    echo "</div>
        <!-- /.products -->";
};
?>
