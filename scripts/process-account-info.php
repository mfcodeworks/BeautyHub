<?php
    require_once 'functions.php';
    session_start();
    extract($_POST);

    //Get user ID
    $userID = $_SESSION['user']->getID();

    //Add profile info
    if(isset($facebook)) {
        if(!addNewInfo($facebook,'facebook_link','users')) {
            echo "$facebook";
            die();
        }
    }
    if(isset($twitter)) {
        if(!addNewInfo($twitter,'twitter_link','users')) {
            echo "$twitter";
            die();
        }
    }
    if(isset($instagram)) {
        if(!addNewInfo($instagram,'instagram_link','users')) {
            echo "$instagram";
            die();
        }
    }
    if(isset($youtube)) {
        if(!addNewInfo($youtube,'youtube_link','users')) {
            echo "$youtube";
            die();
        }
    }
    if(isset($pintrest)) {
        if(!addNewInfo($pintrest,'pintrest_link','users')) {
            echo "$pintrest";
            die();
        }
    }
    if(isset($foundation)) {
        if(!addNewInfo($foundation,'foundation','users')) {
            echo "$foundation";
            die();
        }
    }
    if(isset($favourites)) {
        $favourites = selectAll('ID','products','name',$favourites);
        expandColumn($userID,$productID,'wishlist','users');
        if(!expandColumn($userID,$favourites,'favourites','users')) {
            echo "$favourites";
            die();
        }
    }
    if(isset($bio)) {
        if(!addNewInfo($bio,'bio','users')) {
            echo "$bio";
            die();
        }
    }
    echo "true";
?>
