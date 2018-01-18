<?php
    session_start();
    require_once 'functions.php';
    extract($_POST);

    if(isset($_SESSION['user'])) $loginCheck = $_SESSION['user'];
    //If user is logged in, return true
    if(isset($loginCheck) && $loginCheck->getID() != null) echo "true";

    //If login successful, print login form
    else {
        $user = new user($username,$password);
        if($user->getID() != null) {
            echo "
            <div id='comment-form' data-animate='fadeInUp'>
            <h4>Leave a review</h4>

            <form action='javascript:void(0)' enctype='multipart/form-data' id='author-review-form'>
                <div class='row'>

                    <div class='col-sm-6'>
                        <div class='form-group'>
                            <label for='authorRating'>Rating (0-5)
                            </label>
                            <input type='text' class='form-control' id='authorRating' name='authorRating' placeholder='4.6'>
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
                        <button class='btn btn-primary' type='submit'><i class='fa fa-comment-o'></i> Post comment</button>
                    </div>
                </div>

            </form>

        </div>
        <!-- /#comment-form -->";
        }
        //If login failed, return false
        else echo "false";
    }
?>
