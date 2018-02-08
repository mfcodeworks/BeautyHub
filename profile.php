<?php
    require_once 'scripts/functions.php';
    session_start();
    extract($_GET);
    //if(!isset($_GET['id']) || $_GET['id'] = "" || $_GET['id'] = " " || $_GET['id'] = "NULL") headerLocation("index.php");

    loadHead();
    loadTopBar();
    loadNavBar();
    beginContent();

    $profile = new profile($_GET['id']);
?>

    <div class='row'>
        <!-- PROFILE PIC COLUMN -->
        <div class='col-lg-3 col-xs-12'>
                <img src='<?php echo $profile->ProfileImg(); ?>' id='profile-pic'/>
                <?php if($profile->isVerified()) echo '<i class="fa fa-check-circle" id="profile-badge"></i>'; ?>
        </div>
        <!-- MAIN INFO COLUMN -->
        <div class='col-lg-6 col-xs-12'>
            <div class='box'>
                <p class='lead'>
                    <?php 
                        echo "<strong>" . $profile->Username() . "</strong>";
                        echo "<button type='button' class='btn btn-primary' id='follow-button' onclick='followProfile(\"" . $profile->ID() . "\")'>Follow&nbsp;<i class='fa fa-plus'></i></button>";
                        echo "<button type='button' class='btn btn-danger' id='follow-button' onclick='reportProfile(\"" . $profile->ID() . "\")'>Report&nbsp;<i class='fa fa-exclamation'></i></button>";
                        echo "<br>" . $profile->Bio() . "<br><br>";
                        //Print selected foundation
                        if($profile->Foundation() != NULL) {
                            echo "<strong>Foundation</strong>";
                            echo "<br>";
                            echo $profile->Foundation();
                            echo "<br><br>";
                        }
                        //Print favourites
                        if($profile->Favourites() != NULL) {
                            echo "<strong>Favourites</strong>";
                            echo "<ul>";
                            foreach($profile->Favourites() as $f) {
                                $product = new product($f['id']);
                                $product_text = $product->getBrand() . " " . $product->getName();
                                if($f['shade'] != "NULL") {
                                    $product_text .= " - Shade " . $f['shade'];
                                }
                                echo "<li style='font-size: 12pt; padding-right: 7em;'>$product_text</li>";
                            }
                            echo "</ul>";
                        }
                    ?>
                </p>
            </div>
        </div>
        <!-- EXTRA INFO COLUMN -->
        <div class='col-lg-3 col-xs-12'>
            <?php
                //Print social links
                if($profile->socialLinks() != NULL) {
                    echo "
                        <div class='box'>
                        <p class='lead text-center'>";
                    echo "<strong>Social Media:</strong><br>";
                    foreach($profile->socialLinks() as $key => $sl) {
                        if($sl != "NULL") {
                            switch($key) {
                                case "facebook": 
                                    $fa = "facebook-square";
                                    break;
                                default:
                                    $fa = $key;
                                    break;
                            }
                            echo "<a href='$sl' class='$key external'><i class='fa fa-$fa' style='font-size: 1.5em;'></i></a>";
                        }
                    }
                }
                //Print wishlist
                /*if($profile->Wishlist() != NULL) {
                    echo "<br><br>";
                    echo "<strong>Wishlist</strong>";
                    echo "<ul>";
                    foreach($profile->Wishlist() as $f) {
                        $product = new product($f['id']);
                        $product_text = $product->getBrand() . " " . $product->getName();
                        if($f['shade'] != "NULL") {
                            $product_text .= " - Shade " . $f['shade'];
                        }
                        echo "<li style='font-size: 12pt; margin-bottom: 1rem;'>$product_text</li>";
                    }
                    echo "</ul>";
                }*/
                ?>
            </p>
            </div>
        </div>
    <!-- </row> -->
    </div>

<?php
    loadFoot();
?>