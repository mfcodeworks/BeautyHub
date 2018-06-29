<?php
require_once 'scripts/functions.php';
if (!isset($_SESSION['user'])) {
    headerLocation('index.php');
}
loadHead();
loadTopBar();
loadNavBar();
beginContent();
$username = $_SESSION['user']->getUsername();
        echo "    <div class='col-md-12'>
                    <!-- Home > Wishlist nav link -->
                    <ul class='breadcrumb'>
                        <li><a href='index.php' class='nav-link'>Home</a>
                        </li>
                        <li>My wishlist</li>
                    </ul>
                </div>
                <div class='col-md-3'>
                    <!-- *** CUSTOMER MENU ***
 _________________________________________________________ -->
                    <div class='panel panel-default sidebar-menu'>
                        <div class='panel-heading'>
                            <h3 class='panel-title'>Customer section</h3>
                        </div>
                        <div class='panel-body'>
                            <ul class='nav nav-pills nav-stacked'>
                                <!--<li name='customer-orders.php'>
                                    <a href='customer-orders.php'><i class='fa fa-list'></i> My orders</a>
                                </li>-->
                                <li name='customer-wishlist.php'>
                                    <a href='customer-wishlist.php'><i class='fa fa-heart'></i> My wishlist</a>
                                </li>
                                <li name='customer-account.php'>
                                    <a href='customer-account.php?user=$username'><i class='fa fa-user'></i> My account</a>
                                </li>
                                <li>
                                    <a href='javascript:void(0)' onclick='logout()'><i class='fa fa-sign-out'></i> Logout</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <!-- /.col-md-3 -->
                    <!-- *** CUSTOMER MENU END *** -->
                </div>

                <!-- Begin wishlist section -->
                <div class='col-md-9' id='wishlist'>
                    <div class='box'>
                        <h1>My wishlist</h1>
                        <p class='lead'>Review all the products you've added to your wshlist.</p>
                    </div>";
        loadWishlistProducts();
        echo "</div>";
        loadFoot();
?>
