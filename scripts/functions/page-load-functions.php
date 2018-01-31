<?php
//Load header info
function loadHead()
{
    echo ('
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="robots" content="all,follow">
        <meta name="googlebot" content="index,follow,snippet,archive">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="Obaju e-commerce template">
        <meta name="author" content="Ondrej Svestka | ondrejsvestka.cz | MF APPS & Web Services">
        <meta name="keywords" content="">
        <title>
            BeautyHub : NygmaRose Community Dupe Tool
        </title>
        <meta name="keywords" content="">
        <link href="https://fonts.googleapis.com/css?family=Roboto:400,500,700,300,100" rel="stylesheet" type="text/css">

        <!-- styles -->

        <!-- font-awesome -->
        <link href="css/font-awesome.css" rel="stylesheet">
        <link href="vendor/components/font-awesome/css/font-awesome.css" rel="stylesheet">

        <!-- bootstrap -->
        <!-- <link href="css/bootstrap.min.css" rel="stylesheet"> -->
        <link href="vendor/components/bootstrap/css/bootstrap.css" rel="stylesheet">

        <!-- other -->
        <link href="css/animate.min.css" rel="stylesheet">
        <link href="css/owl.carousel.css" rel="stylesheet">
        <link href="css/owl.theme.css" rel="stylesheet">

        <!-- theme stylesheet -->
        <link href="css/style.classy.css" rel="stylesheet" id="theme-stylesheet">

        <!-- stylesheet with modifications -->
        <link href="css/custom.css" rel="stylesheet">

        <!-- else -->
        <link href="css/bootstrap-social.css" rel="stylesheet">
        <script src="js/respond.min.js"></script>
        <link rel="shortcut icon" href="favicon.png">

        <!-- EXTRA STYLESHEET -->
        <link href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css" rel="stylesheet" >
        <link href="vendor/easy-autocomplete/dist/easy-autocomplete.css" rel="stylesheet">
        <link href="vendor/easy-autocomplete/dist/easy-autocomplete.themes.css" rel="stylesheet">

        <!-- *** SCRIPTS TO INCLUDE *** -->
        <!-- jquery -->
        <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
        <script src="vendor/components/jquery/jquery.js"></script>
        <script src="vendor/components/jquery-migrate/jquery-migrate.js"></script>
        <script src="vendor/components/jqueryui/jquery-ui.js"></script>


        <!-- bootstrap -->
        <script src="vendor/components/bootstrap/js/bootstrap.js"></script>

        <!-- other -->
        <script src="js/jquery.cookie.js"></script>
        <script src="js/waypoints.min.js"></script>
        <script src="js/modernizr.js"></script>
        <script src="js/bootstrap-hover-dropdown.js"></script>
        <script src="js/owl.carousel.min.js"></script>
        <script src="js/front.js"></script>
        <script src="js/lightbox.js"></script>
        <script src="vendor/easy-autocomplete/dist/jquery.easy-autocomplete.js"></script>
        <script src="js/functions.js"></script>
    </head>
    <body>');
};

function loadTopBar()
{
        echo("
        <!-- *** TOPBAR *** -->
        <div id='top'>
            <div class='container'>
                <div class='col-md-6 offer' data-animate='fadeInDown'>
                    <a href='https://nygmarose.com' class='btn btn-success btn-sm' data-animate-hover='shake'>NygmaRose Beauty</a>  <a href='#'>Visit NygmaRose for the latest makeup trends.</a>
                </div>
                <div class='col-md-6' data-animate='fadeInDown'>
                    <ul class='menu'>");
        loadLogin();
        echo            ("<li><a href='contact.php'>Contact</a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class='modal fade' id='login-modal' tabindex='-1' role='dialog' aria-labelledby='Login' aria-hidden='true'>
                <div class='modal-dialog modal-sm'>
                    <div class='modal-content'>
                        <div class='modal-header'>
                            <button type='button' class='close' data-dismiss='modal' aria-hidden='true'>&times;</button>
                            <h4 class='modal-title' id='Login'>Customer login</h4>
                        </div>
                        <div class='modal-body'>
                            <form action='javascript:void(0)' id='login-form'>
                                <div class='form-group'>
                                    <input type='text' class='form-control' id='username' placeholder='username'>
                                </div>
                                <div class='form-group'>
                                    <input type='password' class='form-control' id='password' placeholder='password'>
                                </div>
                                <p class='text-center'>
                                    <button type='submit' class='btn btn-primary'><i class='fa fa-sign-in'></i> Log in</button>
                                </p>
                            </form>
                            <p class='text-center text-muted'>Not registered yet?</p>
                            <p class='text-center text-muted'><a href='register.php'><strong>Register now</strong></a>! It is easy and done in 1&nbsp;minute and gives you access to special discounts and much more!</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- *** TOP BAR END *** -->
        ");
};

function loadCarousel()
{
    echo("
            <div class='col-md-12'>
                <div id='main-slider'>
    ");
    $conn = sqlConnect();
    $sql = "SELECT id,img,link,impression_img FROM carousel;";
    $result = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($result))
    {
        $id[] = $row['id'];
        $img[] = $row['img'];
        $link[] = $row['link'];
        $extra = $row['impression_img'];
    }
    for($i=0;$i<4;$i++)
    {
        echo("
            <div class='item'>
                <a href='$link[$i]'>
                    <img src='$img[$i]' alt='' class='img-responsive' id='full-image'/>
                    <img src='$extra' alt='' style='display:none;' id='full-image'/>
                </a>
            </div>
        ");
    }
    echo("
                </div>
            <!-- /#main-slider -->
            </div>
        </div>
    ");
};

//Load navigation bar info
function loadNavBar()
{
    echo "
        <!-- *** NAVBAR *** -->
        <div class='navbar navbar-default yamm' role='navigation' id='navbar'>
            <div class='container'>
                <div class='navbar-header'>
                    <a class='navbar-brand home' href='index.php'>
                        <img src='img/logo.png' alt='BeautyHub' class='hidden-xs'>
                        <img src='img/logo.png' alt='BeautyHub' class='visible-xs'><span class='sr-only'>Go to homepage</span>
                    </a>
                    <div class='navbar-buttons'>
                        <button type='button' class='navbar-toggle' data-toggle='collapse' data-target='#navigation'>
                            <span class='sr-only'>Toggle navigation</span>
                            <i class='fa fa-align-justify'></i>
                        </button>
                        <button type='button' class='navbar-toggle' data-toggle='collapse' data-target='#search'>
                            <span class='sr-only'>Toggle search</span>
                            <i class='fa fa-search'></i>
                        </button>
                    </div>
                </div>
                <!--/.navbar-header -->
                <div class='navbar-collapse collapse' id='navigation'>
                    <ul class='nav navbar-nav navbar-left'>
                        <li><a href='index.php'>Home</a>
                        </li>
                        <li class='dropdown yamm-fw'>";
    $conn = sqlConnect();
    $sql = "SELECT DISTINCT brand,product_type FROM products;";
    $result = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($result)) {
        $brands[] = $row['brand'];
        $types[] = $row['product_type'];
    }
    echo "<a href='#' class='dropdown-toggle' data-toggle='dropdown' data-hover='dropdown' data-delay='200'>Makeup <b class='caret'></b></a>
                        <ul class='dropdown-menu'>
                            <li>
                                <div class='yamm-content'>
                                    <div class='row'>
                                        <div class='col-sm-6'>
                                            <h5>Brands</h5>
                                            <ul>";
    for($i=0;$i<count($brands);$i++) {
        echo "
                                                    <li><a href='search.php?brand=$brands[$i]'>$brands[$i]</a>
                                                    </li>";
    }
        echo "
                                            </ul>
                                        </div>";
        echo "
                                        <div class='col-sm-6'>
                                            <h5>Types</h5>
                                            <ul>";
    for($i=0;$i<count($types);$i++) {
        echo"
                                                    <li><a href='search.php?type=$types[$i]'>$types[$i]</a>
                                                    </li>";
    }
                                echo "</div>
                                    </div>
                                    <!-- /.yamm-content -->
                                </li>
                            </ul>
                        </li>
                        <li><a href='add-product.php'>Add New Product</a>
                        </li>
                    </ul>
                </div>
                <!--/.nav-collapse -->
                <div class='navbar-buttons'>
                    <!--/.nav-collapse -->
                    <div class='navbar-collapse collapse right' id='search-not-mobile'>
                        <button type='button' class='btn navbar-btn btn-primary' data-toggle='collapse' data-target='#search'>
                            <span class='sr-only'>Toggle search</span>
                            <i class='fa fa-search'></i>
                        </button>
                    </div>
                </div>
                <div class='collapse clearfix' id='search'>
                    <form class='navbar-form' role='search' action='search.php' method='GET'>
                        <div class='input-group'>
                            <input type='text' class='form-control' id='q' name='q' placeholder='Search'/>
                            <span class='input-group-btn'>
    			                     <button type='submit' class='btn btn-primary'><i class='fa fa-search'></i></button>
    		                </span>
                        </div>
                    </form>
                </div>
                <!--/.nav-collapse -->
            </div>
            <!-- /.container -->
        </div>
        <!-- /#navbar -->
        <!-- *** NAVBAR END *** -->";
};

//Begin page main content
function beginContent()
{
    echo("
    <div id='all'>
        <div id='content'>
            <div class='container'>");
};

function loadSideCategories()
{
    echo "<div class='col-md-3'>
            <!-- *** MENUS AND FILTERS *** -->
            <div class='panel panel-default sidebar-menu'>
                <div class='panel-heading'>
                    <h3 class='panel-title'>Categories</h3>
                </div>
                <div class='panel-body'>
                    <ul class='nav nav-pills nav-stacked category-menu'>
                        <li>
                            <a href='search.php'>Brands</a>
                            <ul>";
    //Print brand categories
    $brands = selectAll('brand','products');
    if(isset($brands)) {
        foreach($brands as $b) echo"<li><a href='search.php?brand=$b'>$b</a></li>";
                        echo   "</ul>
                            </li>
                            <li>
                                <a href='search.php'>Type</a>
                                <ul>";
        }
        //Print type categories
        $types = selectAll('product_type','products');
        if(isset($types)) {
            foreach($types as $t) echo"<li><a href='search.php?type=$t'>$t</a></li>";
                            echo  "</ul>
                                </li>
                            </ul>
                        </div>
                    </div>";
        }
};

//Load brand filter on side
function loadSearchFilters($dupeBrands = NULL) {
    $brands = selectAll('brand','products');
    //Print dupe brand filter
    echo "<div class='panel panel-default sidebar-menu'>
            <div class='panel-heading'>
                <h3 class='panel-title'>Dupe Brands</h3>
            </div>
            <div class='panel-body'>
                <form action='javascript:void(0)' id='dupe-brand-filter-selector'>
                    <div class='form-group'>";
    foreach($brands as $b)
        if(isset($dupeBrands) && in_array($b,$dupeBrands))
            echo "    <div class='checkbox'>
                            <label>
                                <input type='checkbox' class='dupe-brand-filter' value='$b' checked/>$b
                            </label>
                      </div>";
        else
            echo "    <div class='checkbox'>
                            <label>
                                <input type='checkbox' class='dupe-brand-filter' value='$b'/>$b
                            </label>
                        </div>";
        echo "         </div>
                    <button type='submit' class='btn btn-default btn-sm btn-primary'><i class='fa fa-pencil'></i> Apply</button>
                </form>
            </div>
        </div>
    </div>";
};

//Load dupe selection
function getDupeDetails($id,$shade,$dupeBrands=NULL,$dupeArray=NULL) {
    echo "<div class='row same-height-row' id='dupe-complete-list'>
        <div class='col-md-3 col-sm-6'>
            <div class='box same-height'>";
    //Set product
    $product = new product($id);

    //If no array is set, echo no dupes found
    if($product->getDupes() == null || $product->getDupes() == "" || $product->getDupes() == " ") {
        echo       "<h3>Dupes for this product</h3>
                    <h5>No dupes for this product yet. Add it <a href='add-product.php?dupe=$id'>here</a></h5>
                </div>
            </div>";
    }

    //If dupes exist
    else {
        //Begin section
        echo "<h3>Dupes for this product</h3>
                <h5>Know another dupe for this product? Add it <a href='add-product.php?id=$id'>here</a></h5>
            </div>
        </div>";

        //Run with dupe array
        $dupeArray = $product->getDupes();
        for( $i = 0; $i < count($dupeArray); $i++)
        {
            if($dupeArray[$i]["thisShade"] == $shade || $shade == NULL)
            {
                $dupe = new product($dupeArray[$i]["dupeID"]);
                $dupeShade = $dupeArray[$i]["dupeShade"];

                //Print list of dupes
                $dupeID = $dupe->getID();
                $dupeName = $dupe->getName();
                $dupeImg = $dupe->getImg();
                $dupeBrand = $dupe->getBrand();

                if(isset($dupeBrands))
                    if(!in_array($dupeBrand,$dupeBrands)) break;

                if(isset($dupeName))
                    echo "<div class='col-md-3 col-sm-6'>
                            <div class='product same-height'>
                                <div>
                                    <div>
                                        <div>
                                            <a href='detail.php?id=$dupeID'>
                                                <img src='$dupeImg' alt='' class='img-responsive'>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <div class='text'>
                                    <a href='detail.php?id=$dupeID' style='color:#333; marggin-bottom:10px;'>
                                        <h3 style='padding-bottom:6em;'>$dupeBrand <br> $dupeName";
                if($dupeShade != "NULL")
                    echo " - Shade $dupeShade</h3>";
                else
                    echo "</h3>";
                echo    "</a>
                            <p class='buttons'>
                                <a href='detail.php?id=$dupeID' class='btn btn-default'>View detail</a>
                            </p>
                        </div>
                    </div>
                <!-- /.product -->
                </div>";
                unset($dupe);
                unset($dupeName);
                unset($dupeImg);
                unset($dupeShade);
                unset($dupeID);
                unset($dupeBrand);
            }
        }
    }
};

//Load most viewed products
function loadTopProducts() {
    $conn = sqlConnect();
    $sql = "SELECT id,name,img FROM products ORDER BY view_count DESC;";
    $result = mysqli_query($conn,$sql);
    if($result) {
        while($row = mysqli_fetch_assoc($result)) {
            $id[] = $row['id'];
            $name[] = $row['name'];
            $img[] = $row['img'];
        }
    }

    //If products in DB > 6, load 6, otherwise load the max possible amount
    $productLoadCount = (count($id) > 6) ? 6 : count($id);

    //Load products
    for($i=0; $i<$productLoadCount; $i++) {
        $thisID = $id[$i];
        $thisName = $name[$i];
        if(!isset($img[$i]) || $img[$i] == "" || $img[$i] == " ") $thisImg = "https://via.placeholder.com/170x220";
        else $thisImg = $img[$i];
        echo "
        <div class='item'>
            <div class='product'>
                <div>
                    <div>
                        <div>
                            <a href='detail.php?id=$thisID'>
                                <img src='$thisImg' alt='$thisName' class='img-responsive'>
                            </a>
                        </div>
                    </div>
                </div>
                <div class='text'>
                    <h3>
                        <a href='detail.php?id=$thisID'>$thisName</a>
                    </h3>
                </div>
                <!-- /.text -->
            </div>
            <!-- /.product -->
        </div>";
    }
};
//Load the comments needed
function loadComments($comments=null) {
    if(isset($comments)) {
        echo "<h4>Reviews</h4>";
        foreach($comments as $c) {
            $conn = sqlConnect();
            $sql = "SELECT * FROM comments WHERE id = $c;";
            $result=mysqli_query($conn,$sql);
            while($row=mysqli_fetch_assoc($result)) {
                $userID = $row['authorID'];
                $comment = $row['comment'];
                $date = $row['date'];
                $imgs = $row['img'];
                $rating = $row['rating'];
            }
            $sql = "SELECT username,profile_img FROM users WHERE ID=$userID;";
            $result = mysqli_query($conn,$sql);
            while($row = mysqli_fetch_assoc($result)) {
                $name = $row['username'];
                $profilePic = $row['profile_img'];
            }
            mysqli_close($conn);
            if(isset($imgs)) {
                $imgArray = explode(',',$imgs);
                $count = count($imgArray);
            }
            echo "
            <div class='row comment' style='padding-bottom:20px;'>
                <div class='col-sm-3 col-md-2 text-center-xs'>
                    <p>
                        <img src='$profilePic' class='img-responsive img-circle' alt=''>
                    </p>
                </div>
                <div class='col-sm-9 col-md-10'>
                    <h5>$name</h5>
                    <p class='posted'><i class='fa fa-clock-o'></i> $date</br>Rating: ";
            for($i=0;$i<round($rating);$i++) echo "<i class='fa fa-star'></i>";
            echo "</p>
                <p>$comment</p>
            </div>";
            if(isset($imgArray)) {
                for($i=0;$i<count($imgArray);$i++) {
                    $photoSource = $imgArray[$i];
                    echo "<div class='col-xs-4'>
                            <img src='$photoSource' alt='' class='thumb img-fluid product-modal' id='myImg$i' name='$i'>
                    </div>";
                }
            }
            echo "
                </div>
            <!-- /.comment -->";
        }
    }
    else {
        $conn = sqlConnect();
        $product = $_SESSION['product-view'];
        $sql = "SELECT DISTINCT id FROM comments WHERE product_name = \"$product\";";
        $result = mysqli_query($conn,$sql);
        while($row = mysqli_fetch_assoc($result)) $comments[] = $row['id'];
        mysqli_close($conn);
        loadComments($comments);
    }
};
//Load category menu for new order
function loadCategories()
{
    echo "<div class='col-lg-3' style='padding-bottom: 15px;'>
            <h1 class='my-4'>Categories</h1>
            <div class='list-group'>";
    $conn = sqlConnect();
    $sql = "SELECT DISTINCT category FROM menu_items;";
    $result = mysqli_query($conn,$sql);
    while($row = mysqli_fetch_assoc($result)) $category[] = $row['category'];
    for($i=0;$i<count($category);$i++)
    {
        if($category[$i]=='noodle-rice') $catName = "Noodles &amp; Rice";
        elseif($category[$i]=='entree') $catName = "Entrée";
        else $catName = ucfirst($category[$i]);
        echo "<a href='index.php?category=$category[$i]' class='list-group-item'>$catName</a>";
    }
    echo "<a href ='index.php?category=other' class='list-group-item'>Other</a>";
    echo "</div>";
    //Load current order items list
    loadOrderList();
    //End section
    echo "</div>
        <!-- /.col-lg-3 -->";
};
//load login page login.php
function loadLogin()
{
    if(isset($_SESSION['user']) && $_SESSION['user']->getUsername() != null) $user = $_SESSION['user']->getUsername();
    if(isset($user)) echo("
        <li><a href='customer-account.php?user=$user'>$user</a>
        </li>
        <li><a href='javascript:void(0)' onclick='logout()'>Logout</a>
        </li>");
    else echo("
        <li><a href='#' data-toggle='modal' data-target='#login-modal'>Login</a>
        </li>
        <li><a href='register.php'>Register</a>
        </li>");
};
//Load footer info
function loadFoot()
{
    echo ("
    </div>
    <!-- /.container -->
    </div>
    <!-- /#content -->
    <!-- *** FOOTER *** -->
    <div id='footer'>
        <div class='container'>
            <div class='row'>
                <div class='col-md-3 col-sm-6'>
                    <h4>Pages</h4>
                    <ul>
                        <li><a href='text.php'>About us</a>
                        </li>
                        <li><a href='text.php'>Terms and conditions</a>
                        </li>
                        <li><a href='faq.php'>FAQ</a>
                        </li>
                        <li><a href='contact.php'>Contact us</a>
                        </li>
                    </ul>
                    <hr>
                    <h4>User section</h4>
                    <ul>");
    loadLogin();
    echo            ("
                    </ul>
                    <hr class='hidden-md hidden-lg hidden-sm'>
                </div>
                <!-- /.col-md-3 -->
                <div class='col-md-3 col-sm-6'>
                    <h4>Top categories</h4>
                    <h5>Men</h5>
                    <ul>
                        <li><a href='search.php'>T-shirts</a>
                        </li>
                        <li><a href='search.php'>Shirts</a>
                        </li>
                        <li><a href='search.php'>Accessories</a>
                        </li>
                    </ul>
                    <h5>Ladies</h5>
                    <ul>
                        <li><a href='search.php'>T-shirts</a>
                        </li>
                        <li><a href='search.php'>Skirts</a>
                        </li>
                        <li><a href='search.php'>Pants</a>
                        </li>
                        <li><a href='search.php'>Accessories</a>
                        </li>
                    </ul>
                    <hr class='hidden-md hidden-lg'>
                </div>
                <!-- /.col-md-3 -->
                <div class='col-md-3 col-sm-6'>
                    <h4>Where to find us</h4>
                    <p><strong>Obaju Ltd.</strong>
                        <br>13/25 New Avenue
                        <br>New Heaven
                        <br>45Y 73J
                        <br>England
                        <br>
                        <strong>Great Britain</strong>
                    </p>
                    <a href='contact.php'>Go to contact page</a>
                    <hr class='hidden-md hidden-lg'>
                </div>
                <!-- /.col-md-3 -->
                <div class='col-md-3 col-sm-6'>
                    <h4>Get the news</h4>
                    <p class='text-muted'>Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas.</p>
                    <form>
                        <div class='input-group'>
                            <input type='text' class='form-control'>
                            <span class='input-group-btn'>
                                <button class='btn btn-default' type='button'>Subscribe!</button>
                            </span>
                        </div>
                        <!-- /input-group -->
                    </form>
                    </hr>
                    <h4>Stay in touch</h4>
                    <p class='social'>
                        <a href='#' class='facebook external' data-animate-hover='shake'><i class='fa fa-facebook'></i></a>
                        <a href='#' class='twitter external' data-animate-hover='shake'><i class='fa fa-twitter'></i></a>
                        <a href='#' class='instagram external' data-animate-hover='shake'><i class='fa fa-instagram'></i></a>
                        <a href='#' class='gplus external' data-animate-hover='shake'><i class='fa fa-google-plus'></i></a>
                        <a href='#' class='email external' data-animate-hover='shake'><i class='fa fa-envelope'></i></a>
                    </p>
                </div>
                <!-- /.col-md-3 -->
            </div>
            <!-- /.row -->
        </div>
        <!-- /.container -->
    </div>
    <!-- /#footer -->
    <!-- *** FOOTER END *** -->
    <!-- *** COPYRIGHT *** -->
    <div id='copyright'>
        <div class='container'>
            <div class='col-md-6'>
                <p class='pull-left'>© 2017 NygmaRose</p>
            </div>
            <div class='col-md-6'>
                <p class='pull-right'>Template by <a href='https://bootstrapious.com/e-commerce-templates'>Bootstrapious</a> & <a href='https://fity.cz'>Fity</a>
                     <!-- Not removing these links is part of the license conditions of the template. Thanks for understanding :) If you want to use the template without the attribution links, you can do so after supporting further themes development at https://bootstrapious.com/donate  -->
                </p>
            </div>
        </div>
    </div>
    <!-- *** COPYRIGHT END *** -->
</div>
<!-- /#all -->
</body>
</html>");
};
?>
