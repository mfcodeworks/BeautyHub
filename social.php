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
        <div class='col-lg-2'></div>
        <div class='col-xs-12 col-lg-8 text-center'>
            <div class='panel panel-default'>
                <div class='panel-body' style='padding-bottom: 1em; padding-top: 1em; padding-left: 0em; padding-right: 0em;'>
                    <div class='col-lg-2 text-center-xs'>
                        <p>
                            <img src='<?php $user = new profile($_SESSION['user']->getID()); echo $user->ProfileImg(); ?>' class='img-responsive img-circle' alt='' style='border: 0.1em solid #585757;'>
                        </p>
                    </div>
                    <div class='col-lg-10'>
                        <label for='newPost' style='float: left; margin-left: 1em;' class='h5'>New Post:</label>
                        <textarea placeholder='Todays new post...' id='newPost' name='newPost' class='form-control' style='margin-bottom: 1em;'></textarea>
                        <button class='btn btn-primary' style='float: right;' type='button' id='submitNewPost'><i class='fa fa-edit'></i>Post</button>
                        <label for='postPicUpload' class='btn btn-default' style='float:right; margin-right: 1em;'><i class='fa fa-photo'></i>&nbsp;Add Photo's</label>
                        <input type='file' class='form-control' id='postPicUpload' name='postPicUpload' style='display:none;'>

                    </div>
                </div>
            </div>
        </div>
        <div class='col-lg-2'></div>
    </div>
    <!-- ./Post Box -->
    <!-- ./Row -->

<?php
    loadFoot();
?>