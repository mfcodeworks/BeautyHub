<?php
    require_once "functions.php";
    extract($_POST);
    $regex = "/(#";
    $regex .= $tag;
    $regex .= "[0-9a-zA-Z]+)/";
    $taglist = [];

    //Connect to DB
    $conn = sqlConnect();
    $sql = "SELECT content
            FROM posts
            WHERE LOCATE('$tag',content) > 0
            LIMIT 15;
            ";
    $result = mysqli_query($conn,$sql);
    if($result) {
        while($row = mysqli_fetch_assoc($result)) {
            $content[] = $row['content'];
        }
        mysqli_close($conn);
        foreach($content as $c) {
            preg_match_all($regex,$c,$matches);
            $matchlist[] = $matches[0];
        }
        foreach($matchlist as $match => $tags) {
            foreach($tags as $ht) {
                if(!in_array($ht,$taglist)) {
                    $taglist[] = $ht;
                }
            }
        }
        die(json_encode($taglist));
    }
    else {
        mysqli_close($conn);
        die("false");
    }

?>