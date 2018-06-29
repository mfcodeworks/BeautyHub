<?php
    require_once 'scripts/functions.php';
    loadHead();
    loadTopBar();
    loadNavBar();
    beginContent();
?>

                <div class="col-md-12">
                    <ul class="breadcrumb">
                        <li><a href="#" class='nav-link'>Home</a>
                        </li>
                        <li>About</li>
                    </ul>

                </div>

                <div class="col-md-3">
                    <!-- *** PAGES MENU ***
 _________________________________________________________ -->
                    <div class="panel panel-default sidebar-menu">

                        <div class="panel-heading">
                            <h3 class="panel-title">Pages</h3>
                        </div>

                        <div class="panel-body">
                            <ul class="nav nav-pills nav-stacked">
                                <li>
                                    <a href="contact.php">Contact page</a>
                                </li>
                                <li>
                                    <a href="about.php">About BeautyHub</a>
                                </li>

                            </ul>

                        </div>
                    </div>

                    <!-- *** PAGES MENU END *** -->


                    <div class="banner">
                        <a href="#">
                        <!-- TODO: Insert Google Ads -->
                            <img src="img/banner.jpg" alt="sales 2014" class="img-responsive">
                        </a>
                    </div>
                </div>

                <div class="col-md-9">


                    <div class="box" id="contact">
                        <h1>About BeautyHub</h1>
                        <p class='lead'>
                            What is BeautyHub?<br>
                            <br>
                            BeautyHub is a paltform for the makeup and beauty community, 
                            that allows everyone to contribute and post.
                            BeautyHub is founded by the NygmaRose in hopes that the beauty comunity can come together to share dupes, 
                            looks, reviews, discounts, ideas, and everything that can help and unite the beauty community across the world as a family.
                        </p>
                    </div>
<?php loadFoot(); ?>
