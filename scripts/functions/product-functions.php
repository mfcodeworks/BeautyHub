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
function loadProduct($id,$page=NULL,$shade=NULL) {
    if(session_status() == PHP_SESSION_NONE) session_start();
    
    if($shade == "NULL") unset($shade);

    //Get product info
    $product = new product($id);

    //Set info
    $img = $product->getImg();
    $name = $product->getName();
    $brand = $product->getBrand();

    //Set size
    switch($page) {
        case "wishlist":
            echo "<div class='col-md-3 col-sm-4 display-product'>";
            break;
        case "search":
            echo "<div class='col-md-4 col-sm-6 display-product'>";
            break;
        default:
            echo "<div class='col-lg-12 col-xs-12'>";
            break;
    }
    
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
            if($page == 'wishlist') {
                if(!isset($shade)) $shade="NULL";
                echo "<p class='buttons'>
                          <a href='javascript:void(0)' onclick='removeWishlist(\"$id,$shade\")' class='btn btn-danger'>Remove from wishlist</a>
                      </p>";
                unset($shade);
            }

            echo"
            </div>
            <!-- /.text -->
        </div>
        <!-- /.product -->
    </div>";
};

//Load wishlist product row
function loadWishlistProducts()
{
    echo "<div class='row products' id='wishlist-product-row'>";

    //Get user
    $user = $_SESSION['user'];
    //else $user = new user($user);
    if(isset($user)) $userID = $user->getID();

    //Get wishlist
    $conn = sqlConnect();
    $sql = "SELECT wishlist
            FROM users
            WHERE ID = $userID;";
    $result = mysqli_query($conn,$sql);
    if($result) {
        while($row = mysqli_fetch_assoc($result)) {
            $wishlist = $row['wishlist'];
        }
    }
    else {
        return;
    }

    if(isset($wishlist)) {
        $wishlist = json_decode($wishlist,true);

        //Print wishlist products
        foreach($wishlist as $p) {
            loadProduct($p['id'],'wishlist',$p['shade'],$user);
        }

        echo "</div>
            <!-- /.products -->";
    }
};

//Load favourites for user
function loadFavourites() {
    //Get favourites
    $conn = sqlConnect();
    $sql = "SELECT favourites
            FROM users
            WHERE ID = ".$_SESSION['user']->getID().";";
    $result = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($result)) {
        $favourites = json_decode($row['favourites'],true);
    }

    //Print favourites
    foreach($favourites as $f) {
        $product = new product($f['id']);
        $content = "<p>" . $product;
        if($f['shade'] != "NULL") $content .= " - " . $f['shade'];
        $content .= "<a href='javascript:void(0)' onclick='removeFromFav(\"" . $f['id'] . "," . $f['shade'] . "\")'>
                        <i class='fa fa-times' style='color:black;opacity:.5;'></i>
                    </a>
                </p>";
        echo $content;
    }
    mysqli_close($conn);
}
?>
