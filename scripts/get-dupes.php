<?php
require_once 'functions.php';
extract($_POST);

//Get dupe details
if(isset($dupeBrands))
    getDupeDetails($id,$shade,$dupeBrands,NULL);
else
    getDupeDetails($id,$shade,NULL,NULL);
?>
