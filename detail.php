<?php
require_once 'scripts/functions.php';
extract($_GET);
//Check product exists and chck for dupe brand requirements
if (!isset($id)) {
    headerLocation('index.php');
}
if (!sqlExists($id, 'ID', 'products')) {
    headerLocation('index.php');
}
if (isset($dupeBrands)) {
    $dupeBrands = explode(",", $dupeBrands);
}
//Load page
loadHead();
loadTopBar();
loadNavBar();
beginContent();
if (isset($dupeBrands)) {
    loadProductDetails($id, $dupeBrands);
} else {
    loadProductDetails($id);
}
loadFoot();


function loadProductDetails($id, $dupeBrands = null)
{
    //Load product
    $product = new product($id);

    // Set autocomplete for dupe area
    $conn = sqlConnect();
    $sql = "SELECT name,brand
            FROM products;";
    $result = mysqli_query($conn, $sql);
    while ($row = mysqli_fetch_assoc($result)) {
        $allProducts[] = $row['brand'] . " - " . $row['name'];
    }
    mysqli_close($conn);
    $script = 
    "<script>
        $(document).ready(function() {
            var products = [";
    for ($i = 0; $i < count($allProducts); $i++) {
        $script .= "\"" . $allProducts[$i] . "\",";
    }
    $script = trim($script, ",") .
            "];
            $('#dupeName').autocomplete({
                source: products
            });
            var shades = [";
    if ($product->getShades() != null) {
        foreach ($product->getShades() as $s) {
            $script .= "\"" . $s . "\",";
        }
    }
    $script = trim($script, ",") . 
            "];
            if(shades.length > 0) {
                $('#thisShade').autocomplete({
                    source: shades
                });
            }
            else {
                $('#thisShadeContainer').hide();
            }
        });
    </script>";
    echo $script;

    //Define vars
    $_SESSION['product-view'] = $product;
    $name = $product->getName();
    $brand = $product->getBrand();
    $type = $product->getType();
    $img = $product->getImg();
    $rating = $product->getRating();

    //Print navbar info
    echo        "<div class='col-md-12'>
                    <ul class='breadcrumb'>
                        <li><a href='index.php' class='nav-link'>Home</a>
                        </li>
                        <li><a href='search.php?brand=$brand' class='nav-link'>$brand</a>
                        </li>
                        <li><a href='search.php?type=$type' class='nav-link'>$type</a>
                        </li>
                        <li>$name</li>
                    </ul>
                </div>";

    //Load sidebar categories
    loadSideCategories();

    //Print shade selector
    echo "<div class='panel panel-default sidebar-menu'>
            <div class='panel-heading'>
                <h3 class='panel-title'>Shades</h3>
            </div>
            <div class='panel-body'>";

    if ($product->getShadeImg()[0]['shade']  != null) {

        echo "<select class='form-control' id='shade-selector'>";

        foreach ($product->getShadeImg() as $si) {
            echo "<option value='$si[img],$si[shade]'>$si[shade]</option>";
        }

        echo "</select>
            <div style='margin:15px;'>
                <img src='" . $product->getShadeImg()[0]['img'] . "' alt='' class='img-responsive' id='shade-img'>
            </div>
            <div class='text-center'>
                <a href='javascript:void(0)' class='btn btn-primary' id='add-a-shade'>Add New Shade</a>
            </div>
            </div>
            </div>";

    } else {
        echo "<div class='text-center'>
                    <a href='javascript:void(0)' class='btn btn-primary' id='add-a-shade'>Add New Shade</a>
                </div>
            </div>
        </div>";
    }

    //Load search filters for dupes (Brand checkboxes on side)
    loadSearchFilters($dupeBrands);

    //Print product info
        echo "<div class='col-md-9'>
                    <div class='row' id='productMain'>
                        <div class='col-sm-6'>
                            <div id='mainImage'>
                                <img src='$img' alt='$name' class='img-responsive'>
                            </div>
                        </div>";
                  echo "<div class='col-sm-6'>
                            <div class='box text-center'>
                                <h1 class='text-center' id='product-title' name='$id'>$name</h1>";
                if (isset($rating) && $rating != "" && $rating != " ") {
                    echo "<h4>Rating:<br/>";
                    for ($i=0;$i<round($rating);$i++) echo "<i class='fa fa-star'></i>";
                    echo "</h4>";
                }
                echo "          </h4>
                                <p class='goToDescription'><a href='#details' class='scroll-to'>Scroll to product details, material & care and sizing</a>
                                </p>";

    //Print prices/sites
    if ($product->getPriceSite()[0]['price'] != '') {
        echo "<select class='form-control' id='price-site-select'>";
        foreach ($product->getPriceSite() as $ps) {
            //Set var
            $currentPrice = $ps['price'];
            $currentSite = $ps['site'];
            $currentSiteName = $ps['siteName'];
            //Echo info
            echo "  <option value='$currentPrice,$currentSite,$currentSiteName'>
                        <div style='text-align:center'><a class='price' href='$currentSite'>$currentPrice</a>
                        <p>$currentSiteName</p></div>
                    </option>";
        }
        echo "</select></br>";
        $currentPrice = $product->getPriceSite()[0]['price'];
        $currentSite = $product->getPriceSite()[0]['site'];
        $currentSiteName = $product->getPriceSite()[0]['siteName'];
        echo "
        <div style='text-align:center' id='product-price-current'>
            <a class='price' href='$currentSite'>$currentPrice</a>
            <p>$currentSiteName</p>
        </div>";
    }

    //Print form for users to add site/price
    echo     "<p style='text-align:center;'>Found a better deal? Link it here with the site and price (USD) to share with the beauty community!</p>
                <table id='price-site-table' style='display:none;'>
                <tr>
                      <td style='width:30%;text-align:center;'>
                        Price
                        (With currency e.g. USD)
                      </td>
                      <td style='width:70%;text-align:right;'>
                        <input type='text' class='form-control' id='product-price' placeholder='$34.00 USD/£12.00 GBP/$42.00 SGD'>
                      </td>
                      </br>
                </tr>
                <tr>
                      <td style='width:30%;text-align:center;'>
                        Site
                      </td>
                      <td style='width:70%;text-align:right;'>
                        <input type='text' class='form-control' id='product-site' placeholder='http://sephora.com/product/...'>
                      </td>
               </tr>
               <tr>
                     <td style='width:30%;text-align:center;'>
                       Site Name
                     </td>
                     <td style='width:70%;text-align:right;'>
                       <input type='text' class='form-control' id='product-site-name' placeholder='Sephora'>
                     </td>
              </tr>
               </table></br>
               <p class='text-center buttons'>
                    <a href='javascript:void(0)' class='btn btn-default' id='add-product-price'><i class='fa fa-money'></i> Add to product</a>
               </p>
               <hr/>";

    //Print add to wishlist button
    //TODO: Display login button for wishlist if not logged in
    if (isset($_SESSION['user'])) {
        $currentUser = new profile($_SESSION['user']->getID());
    }
    if (isset($currentUser)) {
        if ($currentUser->isInWishlist($id, $product->getShades()[0]) ) {
            echo "<p class='text-center buttons' id='wishlist-button'>
                    <a href='javascript:void(0)' class='btn btn-default' id='in-wishlist' name='$id'><i class='fa fa-check'></i> Added to wishlist</a>
                </p>
            </div>";
        } else {
            echo "<p class='text-center buttons' id='wishlist-button'>
                    <a href='javascript:void(0)' class='btn btn-default' id='add-to-wishlist' name='$id'><i class='fa fa-heart'></i> Add to wishlist</a>
                </p>
            </div>";
        }
    } else {
        echo "</div>";
    }

    //Print description
    echo "</div>
        </div>
        <div class='box' id='details'>
                <h4>Product details</h4>
                " . $product->getDescription() . "
                <hr>
        </div>";

    //Print comment form
    echo "<div class='box' id='comments-start'>
            <div id='comments'>";
    loadComments();
    echo "</div>
    <!-- /#comments -->";

    loadCommentForm();

    $shade = $product->getShades()[0];
    getDupeDetails($id, $shade, $dupeBrands);
    echo "
        </div>
    </div>
    <!-- /.col-md-9 -->";
};
?>
