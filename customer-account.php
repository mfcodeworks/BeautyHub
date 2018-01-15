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
            $sql = "SELECT name,shade,brand FROM products WHERE product_type = \"Foundation\";";
            $result = mysqli_query($conn,$sql);
            while($row = mysqli_fetch_assoc($result)) {
                $name[] = $row['name'];
                $brand[] = $row['brand'];
                $shade[] = $row['shade'];
            }
            for($i=0;$i<count($name);$i++) {
                $thisShades = explode(",",$shade[$i]);
                if($thisShades[0] != NULL) {
                    foreach($thisShades as $s)
                        $tag[] = $brand[$i]." ".$name[$i]." - ".$s;
                    }
                    else
                        $tag[] = $brand[$i]." ".$name[$i];
                }
                for($i=0;$i<count($tag);$i++) {
                    if($i<count($tag) - 1) echo "\"$tag[$i]\",";
                    else echo "\"$tag[$i]\"";
                }
            ?>
        ];
        $('#foundation').autocomplete({
            source: foundations
        });

        var makeup = [
            <?php
            //Get items to autofill favourite selection
            $conn = sqlConnect();
            $sql = "SELECT name,shade,brand FROM products;";
            $result = mysqli_query($conn,$sql);
            while($row = mysqli_fetch_assoc($result)) {
                $name[] = $row['name'];
                $brand[] = $row['brand'];
                $shade[] = $row['shade'];
            }
            for($i=0;$i<count($name);$i++) {
                $thisShades = explode(",",$shade[$i]);
                if($thisShades[0] != NULL) {
                    foreach($thisShades as $s)
                        $tag[] = $brand[$i]." ".$name[$i]." - ".$s;
                    }
                    else
                        $tag[] = $brand[$i]." ".$name[$i];
                }
                for($i=0;$i<count($tag);$i++) {
                    if($i<count($tag) - 1) echo "\"$tag[$i]\",";
                    else echo "\"$tag[$i]\"";
                }
            ?>
        ];
        $('#favourites').autocomplete({
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
                                    <a href="customer-account.php"><i class="fa fa-user"></i> My account</a>
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
                        <p class="text-muted">Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas.</p>

                        <h3>Set profile picture</h3>

                        <form action='javascript:void(0)' id='profile-pic-upload-form'>
                          <div class='row'>
                            <div class='col-sm-12'>
                              <div class='form-group'>
                                  <label for='addPhoto' class='btn'><i class='fa fa-photo'></i>&nbsp;Set profile picture</label>
                                  <input type='file' class='form-control' id='addPhoto' name='addPhoto' style='display:none;'>
                              </div>
                            </div>
                            <div class='col-sm-12'>
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
                        <form action="javascript:void(0)" id="account-info-form">
                            <div class="row">
                                <div class="col-sm-12 text-center">
                                    <div class="form-group">
                                        <label for="favourites">Favourite Products</label>
                                        <input type="text" class="form-control" id="favourites" name="favourites">
                                    </div>
                                </div>
                                <div class="col-sm-12 text-center">
                                    <div class="form-group">
                                        <button class="btn btn-primary" id="add-to-favourites"><i class='fa fa-heart'></i> Add to favourites</button>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="facebook_link">Facebook</label>
                                        <a href='javascript:void(0)' class='facebook external' data-animate-hover='shake'><i class='fa fa-facebook-official'></i></a>
                                        <input type="text" class="form-control" id="facebook_link">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="twitter_link">Twitter</label>
                                        <a href='javascript:void(0)' class='twitter external' data-animate-hover='shake'><i class='fa fa-twitter'></i></a>
                                        <input type="text" class="form-control" id="twitter_link">
                                    </div>
                                </div>
                            </div>
                            <!-- /.row -->

                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="instagram_link">Instagram</label>
                                        <a href='javascript:void(0)' class='instagram external' data-animate-hover='shake'><i class='fa fa-instagram'></i></a>
                                        <input type="text" class="form-control" id="instagram_link">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="youtube_link">YouTube</label>
                                        <a href='javascript:void(0)' class='youtube external' data-animate-hover='shake'><i class='fa fa-youtube'></i></a>
                                        <input type="text" class="form-control" id="youtube_link">
                                    </div>
                                </div>
                            </div>
                            <!-- /.row -->
                            <div class="row">
                            <!--
                                <div class="col-sm-6 col-md-3">
                                    <div class="form-group">
                                        <label for="city">Company</label>
                                        <input type="text" class="form-control" id="city">
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-3">
                                    <div class="form-group">
                                        <label for="zip">ZIP</label>
                                        <input type="text" class="form-control" id="zip">
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-3">
                                    <div class="form-group">
                                        <label for="state">State</label>
                                        <select class="form-control" id="state"></select>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-3">
                                    <div class="form-group">
                                        <label for="country">Country</label>
                                        <select class="form-control" id="country"></select>
                                    </div>
                                </div>-->

                                <div class="col-sm-12 text-center">
                                    <div class="form-group">
                                        <label for="pintrest_link">Pinterest</label>
                                        <a href='javascript:void(0)'><i class='fa fa-pinterest'></i></a>
                                        <input type="text" class="form-control" id="pintrest_link">
                                    </div>
                                </div>
                                <div class="col-sm-12 text-center">
                                    <div class="form-group">
                                        <label for="foundation">Foundation Used</label>
                                        <a href='javascript:void(0)'><i class='fa fa-star'></i></a>
                                        <input type="text" class="form-control" id="foundation" placeholder="Fenty Beauty PRO FILT'R Soft Matte Longwear Foundation - 110">
                                    </div>
                                </div>
                                <div class="col-sm-12 text-center">
                                    <div class="form-group">
                                        <label for="bio">Bio&nbsp;<i class='fa fa-newspaper-o'></i></label>
                                        <textarea class="form-control" id="bio" rows="10"></textarea>
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
