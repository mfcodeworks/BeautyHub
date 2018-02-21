<?php
    require_once 'scripts/functions.php';
    session_start();
    //if(!isset($_SESSION['user'])) headerLocation('register.php');
    $user = $_SESSION['user'];

    loadHead();
    loadTopBar();
    loadNavBar();
    beginContent();
?>

    <div class='row'>

        <!-- MARGIN FOR LARGE SCREENS -->
        <div class='col-lg-2'></div>
        <!-- END MARGIN -->

        <!-- MAIN POST BOX BODY -->
        <div class='col-xs-12 col-lg-8 text-center'>
            <div class='panel panel-default'>
                <div class='panel-body' style='padding-bottom: 1em; padding-top: 1em; padding-left: 0em; padding-right: 0em;'>
                    
                    <div class='col-lg-2 col-xs-2'>
                        <p>
                            <img src='<?php $user = new profile($_SESSION['user']->getID()); echo $user->ProfileImg(); ?>' class='img-responsive img-circle' alt='' style='border: 0.1em solid #585757; margin-top: 1.6em;'>
                        </p>
                    </div>
                    <!-- POST BOX FORM -->
                    <div class='col-lg-10 col-xs-10'>
                        <form id='newPostForm' action='javascript:void(0)'>
                            <div class='form-group'>
                                <label for='newPost' style='float: left; margin-left: 1em;' class='h5'>New Post:</label>
                                <textarea placeholder='Todays new post...' id='newPost' name='newPost' class='form-control' style='margin-bottom: 1em;'></textarea>
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
                    <!-- END FORM -->
                </div>
            </div>
        </div>
        <!-- END POST BOX BODY -->
    
        <!-- MARGIN FOR LARGE SCREENS -->
        <div class='col-lg-2'></div>
        <!-- END MARGIN -->

    </div>
    <!-- ./Post Box -->
    <!-- ./Row -->

    <!-- LOAD POSTS FROM FOLLOWING -->
    <div id='following-post-area'>
        <?php
            loadPosts();
        ?>
    </div>

<?php
    loadFoot();
?>