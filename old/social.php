<?php
    require_once 'scripts/functions.php';
    session_start();
    //if(!isset($_SESSION['user'])) headerLocation('register.php');
    if(isset($_SESSION['user'])) {
        $user = $_SESSION['user'];
    }

    loadHead();
    loadTopBar();
    loadNavBar();
    beginContent();
?>

    <div class='row'>
        
        <div class='col-lg-2 col-xs-0'><!-- SOON TO BE AD SPACE --></div>

        <!-- MAIN POST BOX BODY -->
        <div class='col-xs-9 col-lg-8'>
            <div class='panel panel-default text-center'>
                <div class='panel-body' style='padding-bottom: 1em; padding-top: 1em; padding-left: 0em; padding-right: 0em;'>
                    <?php
                        if(isset($user)) {
                            $user = new profile($_SESSION['user']->getID());
                            echo "
                            <div class='col-lg-2 col-xs-3'>
                                <p>
                                    <img src='".$user->ProfileImg()."' class='img-responsive img-circle' alt='' style='border: 0.1em solid #585757; margin-top: 2rem;'>
                                </p>
                            </div>
                            <!-- POST BOX FORM -->
                            <div class='col-lg-10 col-xs-9'>
                                <form id='newPostForm' action='javascript:void(0)'>
                                    <div class='form-group'>
                                        <textarea placeholder='Todays new post...' id='newPost' name='newPost' class='form-control' style='height: 7rem;'></textarea>
                                    </div>
                                    <div class='form-group'>
                                        <button class='btn btn-primary' style='float: right;' type='submit' id='submitNewPost'><i class='fa fa-edit'></i>Post</button>
                                    </div>
                                    <div class='form-group'>
                                        <label for='postPicUpload[]' class='btn btn-default' style='float:right; margin-right: 1em;'><i class='fa fa-photo'></i>&nbsp;Add Photo's</label>
                                        <input type='file' class='form-control' id='postPicUpload[]' name='postPicUpload[]' style='display:none;' multiple>
                                    </div>
                                </form>
                            </div>
                            <!-- END FORM -->";
                        }
                        else {
                            echo "
                            <div class='col-lg-12 col-xs-12'>
                                <p class='lead-text'>Please <a href='register.php'>Register</a> to follow and post on BeautyHub</p>
                            </div>";
                        }
                    ?>
                </div>
            </div>
            <?php
                echo "<div id='following-post-area'>";
                if(isset($user)) {
                    loadPosts();
                }
                echo "</div>";
            ?>
        </div>
        <!-- END POST BOX BODY -->

        <div class='col-lg-2 col-xs-3' id='suggested-user-area'>
            <?php
                loadSuggestedUsers();
            ?>
        </div>
        <div class='col-lg-0 col-xs-3'>
            <!-- MOBILE AD SPACE -->
        </div>

    </div>
    <!-- ./Row -->
<!-- ./Container -->
</div>
<!-- ./Content -->
</div>
<!-- ./All -->
</div>
<!-- ./Body -->
</body>
<!-- ./HTML -->
</html>