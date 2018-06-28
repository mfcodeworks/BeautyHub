<?php
    session_start();
    extract($_POST);

    // Convert minutes to seconds
    $timezone_name = timezone_name_from_abbr("", $timezone_offset_minutes*60, false);
    echo $timezone_name;
    $_SESSION['timezone'] = $timezone_name;
?>