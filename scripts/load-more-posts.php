<?php
    require_once "functions.php";
    session_start();
    extract($_POST);
    loadPosts($offset);
?>