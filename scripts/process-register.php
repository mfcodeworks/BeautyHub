<?php
    require_once 'functions.php';
    session_start();
    extract($_POST);

    // If make user successful, set logged in and return true
    try { $user = new user($username, $password, $email); }
    catch(Exception $exc) { echo $exc->getMessage(); }

    if(isset($user) && $user->getID() != null) {
        echo "true";
        $message = "
        <html>
            <body>
                <div style='text-align: center;'>
                    <h2>Welcome to BeautyHub</h2>
                    <p>Welcome $username to the beauty community! Make sure to head to <a href='beautyhub.nygmarosebeauty.com/social.php'>BeutyHub Social Feed</a> and find new members of the beauty community to follow!
                    You can find your new account <a href='beautyhub.nygmarose.com/profile.php?id=".$user->getID()."'>here</a> and add new information to start sharing your beauty with the whole community, Enjoy!</p>
                    
                    <footer>Do not reply to this message</footer>
                </div>
            </body>
        </html>";
        mailMessage($message,"Welcome to BeautyHub $username",$email,"welcome@nygmarosebeauty.com");
    }

    // If failed return false
    else {
        echo "false";
    }
?>
