<?php
require_once 'scripts/functions.php';
    session_start();
    extract($_GET);


    if(!isset($id)) headerLocation('index.php');
    if(!sqlExists($id,'ID','products')) headerLocation('index.php');
    if(isset($dupeBrands)) $dupeBrands = explode(",",$dupeBrands);

    loadHead();
    loadTopBar();
    loadNavBar();
    beginContent();
    if(isset($dupeBrands)) loadProductDetails($id,$dupeBrands);
    else loadProductDetails($id);
    loadFoot();


function loadProductDetails($id,$dupeBrands=NULL)
{
    $product = new product($id);

    //Define vars
    $_SESSION['product-view']=$product->getName();
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
    if($product->getShadeImg()[0]['shade']  != null) {
        echo "<select class='form-control' id='shade-selector'>";
        foreach($product->getShadeImg() as $si)
            echo "<option value='$si[img],$si[shade]'>$si[shade]</option>";
        echo "</select>
            <div style='margin:15px;'>
                <img src='" . $product->getShadeImg()[0]['img'] . "' alt='' class='img-responsive' id='shade-img'>
            </div>
            <div class='text-center'>
                <a href='javascript:void(0)' class='btn btn-primary' id='add-a-shade'>Add New Shade</a>
            </div>
            </div>
            </div>";
    }
    else echo "<div class='text-center'>
                    <a href='javascript:void(0)' class='btn btn-primary' id='add-a-shade'>Add New Shade</a>
                </div>
            </div>
        </div>";

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
                if(isset($rating) && $rating != "" && $rating != " ") {
                    echo "<h4>Rating:<br/>";
                    for($i=0;$i<round($rating);$i++) echo "<i class='fa fa-star'></i>";
                    echo "</h4>";
                }
                echo "          </h4>
                                <p class='goToDescription'><a href='#details' class='scroll-to'>Scroll to product details, material & care and sizing</a>
                                </p>";

    //Print prices/sites
    if($product->getPriceSite()[0]['price'] != '') {
        echo "<select class='form-control' id='price-site-select'>";
        foreach($product->getPriceSite() as $ps) {
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
    echo "<p class='text-center buttons'>
            <a href='javascript:void(0)' class='btn btn-default' id='add-to-wishlist' name='$id'><i class='fa fa-heart'></i> Add to wishlist</a>
        </p>
    </div>";

    //Print description
    echo "</div>
        </div>
        <div class='box' id='details'>
                <h4>Product details</h4>
                " . $product->getDescription() . "
                <hr>
        </div>";

    //Print comment form
    $conn = sqlConnect();
    $sql = "SELECT DISTINCT id FROM comments WHERE product_name = '" . $product->getName() . "' ORDER BY id ASC;";
    $result = mysqli_query($conn,$sql);
    if($result) {
        while($row = mysqli_fetch_assoc($result)) $comments[] = $row['id'];
    }
    echo "<div class='box' id='comments-start'>
            <div id='comments' data-animate='fadeInUp'>";
    if(isset($comments)) {
        loadComments($comments);
    }
    else echo "
        <div class='row comment'>
            <div class='col-sm-9 col-md-10'>
            <h4>No reviews here yet.</h4>
            </div>
        </div>";
    echo "</div>
        <!-- /#comments -->";

    if(isset($_SESSION['user'])) $user = $_SESSION['user'];
    if(isset($user) && $user->getID() != null) {
        echo "<div id='comment-form' data-animate='fadeInUp'>

                <h4>Leave a review</h4>

                <form action='javascript:void(0)' enctype='multipart/form-data' id='author-review-form'>
                    <div class='row'>

                        <div class='col-sm-6'>
                            <div class='form-group'>
                                <label for='authorRating'>Rating<br><i class='fa fa-star-o' id='star1'></i><i class='fa fa-star-o' id='star2'></i><i class='fa fa-star-o' id='star3'></i><i class='fa fa-star-o' id='star4'></i><i class='fa fa-star-o' id='star5'></i></label>
                                <input type='number' class='form-control' id='authorRating' style='display:none;'>
                            </div>
                        </div>

                    </div>

                    <div class='row'>
                        <div class='col-sm-12'>
                            <div class='form-group'>
                                <label for='authorReview'>Review <span class='required'>*</span>
                                </label>
                                <textarea class='form-control' id='authorReview' name='authorReview' rows='4'></textarea>
                            </div>
                        </div>
                    </div>

                    <div class='row'>
                        <div class='col-sm-12'>
                            <div class='form-group'>
                                <label for='authorImg' class='btn btn-default'>Photos
                                </label>
                                <input type='file' id='authorImg' name='authorImg[]' style='display:none;' multiple>
                            </div>
                        </div>
                    </div>

                    <div class='row'>
                        <div class='col-sm-12 text-right'>
                            <button class='btn btn-primary' type='submit'><i class='fa fa-comment'></i> Post comment</button>
                        </div>
                    </div>

                </form>

            </div>
            <!-- /#comment-form -->
            </div>";
    }
    else echo "
    <div id='comment-form' data-animate='fadeInUp'>
        <h4>Login</h4>
        <form action='javascript:void(0)' id='comment-login-form'>
            <div class='row'>
                <div class='col-sm-6'>
                    <div class='form-group'>
                        <label for='comment-username'>Username
                        </label>
                        <input type='text' class='form-control' id='comment-username' name='comment-username'>
                    </div>
                </div>
            </div>
            <div class='row'>
                <div class='col-sm-6'>
                    <div class='form-group'>
                        <label for='comment-password'>Password
                        </label>
                        <input type='password' class='form-control' id='comment-password' name='comment-password'>
                    </div>
                </div>
            </div>
            <div class='row'>
                <div class='col-sm-6'>
                    <button class='btn btn-primary' type='submit'><i class='fa fa-sign-in'></i>Login</button>
                </div>
            </div>
        </form>
    </div>
    </div>";

    $shade = $product->getShades()[0];
    getDupeDetails($id,$shade,$dupeBrands);
    echo "
        </div>
    </div>
    <!-- /.col-md-9 -->";
    echo "
    <!-- The Modal -->
    <div id='myModal' class='modal'>
        <!-- Modal Content (The Image) -->
        <img class='modal-content' id='myModalImg'>
    </div>";
};
?>
