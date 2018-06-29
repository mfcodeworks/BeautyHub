<?php
/**
 * Comment class
 * Create new comment
 * 
 */
if (!defined("ABSPATH")) {
    define('ABSPATH', dirname(dirname(__FILE__)) . '/');
}
require_once ABSPATH . "scripts/functions.php";
class Comment
{
    /** 
     * Properties
     */
    private $_id;
    private $_authorID;
    private $_data = [
        "content" => null,
        "media" => [],
        "datetime" => null,
        "reply_to" => null,
        "product_id" => null,
        "post_id" => null,
        "rating" => null
    ];
    /** Create new comment
     */
    public function __construct($id = null) {
        if ($id == null) {
            $this->authorID = $_SESSION['user']->getID();
        } else {
            try {
                $this->getData($id);
            }
            catch(Exception $exc) {
                throw $exc;
            }
        }
    }
    /* Get existing comment data */
    private function _getData($id) {
        $conn = sqlConnect();
        $sql = "SELECT authorID,content,media,datetime,reply_to,product_id,rating
                FROM comments
                WHERE ID = $id;";
        $result = mysqli_query($conn, $sql);
        if ($result) {
            $this->id = $id;
            while ($row = mysqli_fetch_assoc($result)) {
                $this->authorID = $row['authorID'];
                $this->data['content'] = $row['content'];
                $this->data['media'] = json_decode($row['media']);
                $this->data['datetime'] = $row['datetime'];
                $this->data['reply_to'] = $row['reply_to'];
                $this->data['product_id'] = $row['product_id'];
                $this->data['rating'] = $row['rating'];
            }
        } else {
            throw new Exception("Product doesn't exist");
        }
    }
    //{get;} functions
    public function id() { return $this->id; }
    public function author() { return $this->authorID; }
    public function content() { return $this->data["content"]; }
    public function media() { return $this->data["media"]; }
    public function datetime() { return $this->data["datetime"]; }
    public function to() { return $this->data["reply_to"]; }
    public function onProduct() { return $this->data["product_id"]; }
    public function rating() { return $this->data["rating"]; }
    //Save content
    public function saveContent($content) {
        $this->data["content"] = $content;
    }
    //Add path to media attached
    public function addMedia($media_path) {
        $this->data["media"][] = $media_path;
    }
    //Add a reply to comment
    public function replyTo($id) {
        $this->data["reply_to"] = $id;
    }
    //Set comment post
    public function forPost($id) {
        $this->data["post_id"] = $id;
    }
    //Add a product 
    public function forProduct($id) {
        $this->data["product_id"] = $id;
    }
    //Add product rating
    public function setRating($rating) {
        $this->data["rating"] = $rating;
    }
    //Save comment
    public function save() {
        //Connet to DB
        $conn = sqlConnect();
        //Set datetime
        $this->data["datetime"] = getDatetimeNow();
        //Build SQL
        $sql = "INSERT INTO comments(authorID,";
        $values = "VALUES(".$this->authorID.",";
        foreach ($this->data as $meta => $data) {
            if ($data != null && $data != "") {
                switch ($meta) {
                case "media":
                    $data = json_encode($data);
                    $sql .= "$meta,";
                    $values .= "'$data',";
                    break;
                default:
                    $sql .= "$meta,";
                    $values .= "\"$data\",";
                    break;
                }
            }
        }
        //Trim excess
        $sql = trim($sql, ",") . ") ";
        $values = trim($values, ",") . ");";
        $sql = $sql . $values;
        if (mysqli_query($conn, $sql)) {
            mysqli_close($conn);
            return true;
        } else {
            mysqli_close($conn);
            throw new Exception("Couldn't save comment.");
        }
    }
}
?>