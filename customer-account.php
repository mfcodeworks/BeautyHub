<?php
    require_once 'scripts/functions.php';
    session_start();
    if(!isset($_SESSION['user'])) headerLocation('register.php');
    loadHead();
    loadTopBar();
    loadNavBar();
    beginContent();
?>
    <!-- Autocomplete script for items -->
    <script>
    $( document).ready(function() {
        var foundations = [

            <?php

            //Get items to autofill favourite selection
            $conn = sqlConnect();
            $sql = "SELECT ID 
                    FROM products 
                    WHERE product_type = \"Foundation\";";

            $result = mysqli_query($conn,$sql);
            while($row = mysqli_fetch_assoc($result)) {
                $products[] = new product($row['ID']);
            }
            foreach($products as $product) {
                if($product->getShades() !== null) {
                    foreach($product->getShades() as $shade) {
                        echo "\"" . $product . " - " . $shade . "\",";
                    }
                }
                else {
                    echo "\"" . $product . "\",";
                }
            }
            unset($products);
            ?>

        ];
        $('#foundation').autocomplete({
            source: foundations
        });

        var makeup = [

            <?php
            //Get items to autofill favourite selection
            $conn = sqlConnect();
            $sql = "SELECT ID FROM products;";
            $result = mysqli_query($conn,$sql);
            while($row = mysqli_fetch_assoc($result)) {
                $products[] = new product($row['ID']);
            }
            foreach($products as $product) {
                if($product->getShades() !== null) {
                    foreach($product->getShades() as $shade) {
                        echo "\"" . $product . " - " . $shade . "\",";
                    }
                }
                else {
                    echo "\"" . $product . "\",";
                }
            }
            unset($products);
            ?>

        ];
        $('#favourites-input').autocomplete({
            source: makeup
        });
    });
    </script>

                <div class="col-md-12">

                    <ul class="breadcrumb">
                        <li><a href="#" class='nav-link'>Home</a>
                        </li>
                        <li>My account</li>
                    </ul>

                </div>

                <div class="col-md-3">
                    <!-- *** CUSTOMER MENU ***
 _________________________________________________________ -->
                    <div class="panel panel-default sidebar-menu">

                        <div class="panel-heading">
                            <h3 class="panel-title">Customer section</h3>
                        </div>

                        <div class="panel-body">

                            <ul class="nav nav-pills nav-stacked">
                                <!--<li name='customer-orders.php'>
                                    <a href="customer-orders.php"><i class="fa fa-list"></i> My orders</a>
                                </li>-->
                                <li name='customer-wishlist.php'>
                                    <a href="customer-wishlist.php"><i class="fa fa-heart"></i> My wishlist</a>
                                </li>
                                <li name='customer-account.php'>
                                    <a href="customer-account.php?user=<?php echo $_SESSION['user']->getUsername(); ?>"><i class="fa fa-user"></i> My account</a>
                                </li>
                                <li>
                                    <a href="javascript:void(0)" onclick="logout()"><i class="fa fa-sign-out"></i> Logout</a>
                                </li>
                            </ul>
                        </div>

                    </div>
                    <!-- /.col-md-3 -->

                    <!-- *** CUSTOMER MENU END *** -->
                </div>

                <div class="col-md-9">
                    <div class="box">
                        <h1>My account</h1>
                        <p class="lead">Change your personal details or your password here.</p>
                        <!-- <p class="text-muted">Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas.</p> -->

                        <h3>Set profile picture</h3>

                        <form action='javascript:void(0)' id='profile-pic-upload-form'>
                          <div class='row'>
                            <div class='col-sm-12'>
                                <div class='form-group text-center col-sm-12'>
                                    <img src='<?php 
                                                $conn = sqlConnect();
                                                $sql = "SELECT profile_img
                                                        FROM users
                                                        WHERE ID=" . $_SESSION['user']->getID() . ";
                                                        ";
                                                $result = mysqli_query($conn,$sql);
                                                echo mysqli_fetch_assoc($result)['profile_img'];
                                                ?>'
                                        class='img-rounded' alt='Profile Picture' id='profilePicDisplay' style='height: auto; width: auto; max-height: 500px; max-width: 750px;'>
                                </div>
                                <div class='form-group col-sm-12 text-center'>
                                    <label for='profilePicUpload' class='btn btn-default'><i class='fa fa-photo'></i>&nbsp;Set profile picture</label>
                                    <input type='file' class='form-control' id='profilePicUpload' name='profilePicUpload' style='display:none;'>
                                </div>
                            </div>
                            <div class='col-sm-12 text-center'>
                                <div class='form-group'>
                                    <button type='submit' class='btn btn-primary'><i class='fa fa-upload'></i> Submit</button>
                                </div>
                            </div>
                          </div>
                        </form>

                        <hr>

                        <h3>Change password</h3>

                        <form action="javascript:void(0)" id="password-change-form">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="password_old"><i class='fa fa-unlock-alt'></i>&nbsp;Old password</label>
                                        <input type="password" class="form-control" id="password_old">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="password_1"><i class='fa fa-lock'></i>&nbsp;New password</label>
                                        <input type="password" class="form-control" id="password_1">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="password_2"><i class='fa fa-lock'></i>&nbsp;Retype new password</label>
                                        <input type="password" class="form-control" id="password_2">
                                    </div>
                                </div>
                            </div>
                            <!-- /.row -->

                            <div class="col-sm-12 text-center">
                                <button type="submit" class="btn btn-primary"><i class="fa fa-upload"></i> Save new password</button>
                            </div>
                        </form>

                        <hr>

                        <h3>Personal details</h3>
                            <div class="row">
                                <div class="col-sm-12 text-center">
                                    <div class="form-group">
                                        <label for="favourites">Favourite Products</label>
                                        <input type="text" class="form-control" id="favourites-input" name="favourites" placeholder="Kat Von D - Lock-It Foundation">
                                    </div>
                                </div>
                                <div class='col-sm-12 text-center' id='favourites-list'>
                                    <?php
                                        loadFavourites();
                                    ?>
                                </div>
                                <div class="col-sm-12 text-center">
                                    <div class="form-group">
                                        <button class="btn btn-primary" id="add-to-favourites"><i class='fa fa-heart'></i> Add to favourites</button>
                                    </div>
                                </div>
                        <form action="javascript:void(0)" id="account-info-form">

                                <?php
                                    /**
                                     * Get account info
                                     */
                                    $conn = sqlConnect();
                                    $sql = "SELECT foundation,social_links,bio
                                            FROM users
                                            WHERE ID = " . $_SESSION['user']->getID() . ";";
                                    $result = mysqli_query($conn,$sql);
                                    while($row = mysqli_fetch_assoc($result)) {
                                        $links = json_decode($row['social_links'],true);
                                        $foundation = json_decode($row['foundation'],true);
                                        $bio = $row['bio'];
                                    }
                                ?>

                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="facebook_link">Facebook</label>
                                        <a href='<?php if($links['facebook'] != "NULL") echo $links['facebook']; else echo "javascript:void(0)"; ?>' class='facebook external' data-animate-hover='shake'><i class='fa fa-facebook-official'></i></a>
                                        <input type="text" class="form-control" id="facebook_link" placeholder="https://facebook.com/nygmarosebeauty" value="<?php if($links['facebook'] != "NULL") echo $links['facebook']; ?>">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="twitter_link">Twitter</label>
                                        <a href='<?php if($links['twitter'] != "NULL") echo $links['twitter']; else echo "javascript:void(0)"; ?>' class='twitter external' data-animate-hover='shake'><i class='fa fa-twitter'></i></a>
                                        <input type="text" class="form-control" id="twitter_link" placeholder="https://twitter.com/nygmarose" value="<?php if($links['twitter'] != "NULL") echo $links['twitter']; ?>">
                                    </div>
                                </div>
                            </div>
                            <!-- /.row -->

                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="instagram_link">Instagram</label>
                                        <a href='<?php if($links['instagram'] != "NULL") echo $links['instagram']; else echo "javascript:void(0)"; ?>' class='instagram external' data-animate-hover='shake'><i class='fa fa-instagram'></i></a>
                                        <input type="text" class="form-control" id="instagram_link" placeholder="https://instagram.com/nygmarose" value="<?php if($links['instagram'] != "NULL") echo $links['instagram']; ?>">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="youtube_link">YouTube</label>
                                        <a href='<?php if($links['youtube'] != "NULL") echo $links['youtube']; else echo "javascript:void(0)"; ?>' class='youtube external' data-animate-hover='shake'><i class='fa fa-youtube'></i></a>
                                        <input type="text" class="form-control" id="youtube_link" placeholder="https://youtube.com/nygmarose" value="<?php if($links['youtube'] != "NULL") echo $links['youtube']; ?>">
                                    </div>
                                </div>
                            </div>
                            <!-- /.row -->
                            <div class="row">

                                <div class="col-sm-12 text-center">
                                    <div class="form-group">
                                        <label for="pinterest_link">Pinterest</label>
                                        <a href='<?php if($links['pinterest'] != "NULL") echo $links['pinterest']; else echo "javascript:void(0)"; ?>'><i class='fa fa-pinterest'></i></a>
                                        <input type="text" class="form-control" id="pinterest_link" placeholder="https://pinterest.com/nygmarose" value="<?php if($links['pinterest'] != "NULL") echo $links['pinterest']; ?>">
                                    </div>
                                </div>
                                <div class="col-sm-12 text-center">
                                    <div class="form-group">
                                        <label for="foundation">Foundation Used</label>
                                        <a href='javascript:void(0)'><i class='fa fa-star'></i></a>
                                        <input type="text" class="form-control" id="foundation" placeholder="Fenty Beauty - PRO FILT'R Soft Matte Longwear Foundation - 110" value="<?php if($foundation['id'] != "" && $foundation['id'] != "NULL") { $product = new product($foundation['id']); echo $product; if($foundation['shade'] != "NULL") echo " - " . $foundation['shade']; } ?>">
                                    </div>
                                </div>
                                <div class="col-sm-12 text-center">
                                    <div class="form-group">
                                        <label for="bio">Bio&nbsp;<i class='fa fa-newspaper-o'></i></label>
                                        <textarea class="form-control" id="bio" rows="10" palceholder="This is my bio..."><?php if(isset($bio) && $bio != "" && $bio != "NULL") echo $bio; ?></textarea>
                                    </div>
                                </div>
                                <div class="col-sm-12 text-center">
                                    <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Save changes</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
<?php loadFoot(); ?>
