<?php
    /**
     * Profile class
     * Get public information for profiles
     */
    if(!defined("ABSPATH")) define( 'ABSPATH', dirname(dirname(__FILE__)) . '/' );
    require_once ABSPATH . "scripts/functions.php";

    class profile {
        private $id;
        private $username;
        private $profile_img;
        private $bio;
        private $verified;
        private $social_links;
        private $foundation;
        private $wishlist;
        private $favourites;
        private $following;

        /**
         *  Get profile information from ID
         * 
         */
        public function __construct($id) {
            //Get user info
            $conn = sqlConnect();
            $sql = "SELECT username,wishlist,social_links,favourites,foundation,verified,bio,profile_img,following
                    FROM users
                    WHERE ID = $id;";
            $result = mysqli_query($conn,$sql);
            if($result) {
                $this->id = $id;
                while($row = mysqli_fetch_assoc($result)) {
                    $this->username = $row['username'];
                    $this->profile_img = $row['profile_img'];
                    $this->bio = $row['bio'];
                    $this->verified = $row['verified'];
                    $this->social_links = json_decode($row['social_links'],true);
                    $this->foundation = json_decode($row['foundation'],true);
                    $this->wishlist = json_decode($row['wishlist'],true);
                    $this->favourites = json_decode($row['favourites'],true);
                    $this->following = json_decode($row['following']);
                }
                mysqli_close($conn);
            }
            else {
                throw new Exception("Profile not found.");
            }
        }

        /**
         * {get;} functions
         * 
         */
        public function ID() { return  $this->id; }
        public function Username() { return $this->username; }
        public function ProfileImg() { return $this->profile_img; }
        public function isFollowing($id) {
            if(in_array($id,$this->following)) {
                return true;
            }
            else {
                return false;
            }
        }
        public function Bio() { return $this->bio; }
        //Check for verification
        public function isVerified() { return $this->verified; }
        public function socialLinks() { return $this->social_links; }
        //Return foundation as text
        public function Foundation() {
            if( isset($this->foundation) && $this->foundation != "") {
                $product = new product($this->foundation['id']);
                $text = $product->getBrand() . " " . $product->getName();
                if( $this->foundation['shade'] != "NULL" && $this->foundation['shade'] != "") {
                    $text .= " - " . $this->foundation['shade'];
                }
                return $text;
            }
        }
        public function Wishlist() { return $this->wishlist; }
        public function isInWishlist($id, $shade) {
            if(!hasData($shade)) {
                $shade = "NULL";
            }
            foreach($this->wishlist as $w) {
                if($w['id'] == $id && $w['shade'] == $shade) {
                    return true;
                }
            }
            return false;
        }
        public function Favourites() { return $this->favourites; }
        //Return social media links
        public function Facebook() {
            if( $this->social_links['facebook'] != "NULL") {
                return $this->social_links['facebook'];
            }
            else {
                return null;
            }
        }
        public function Twitter() { 
            if( $this->social_links['twitter'] != "NULL") {
                return $this->social_links['twitter'];
            }
            else {
                return null;
            }
        }
        public function Instagram() {
            if( $this->social_links['instagram'] != "NULL") {
                return $this->social_links['instagram'];
            }
            else {
                return null;
            }
        }
        public function Youtube() { 
            if( $this->social_links['youtube'] != "NULL") {
                return $this->social_links['youtube'];
            }
            else {
                return null;
            }
        }
        public function Pinterest() { 
            if( $this->social_links['pinterest'] != "NULL") {
                return $this->social_links['pinterest'];
            }
            else {
                return null;
            }
        }

        /**
         * Tools
         *  - Load posts by profile
         */
        public function loadPosts() {
            $conn = sqlConnect();
            $sql = "SELECT ID
                    FROM posts
                    WHERE author = ".$this->id.";
                    ";
            $result = mysqli_query($conn,$sql);
            if($result) {
                while($row = mysqli_fetch_assoc($result)) {
                    $postID[] = $row['ID'];
                }
                if(isset($postID) && count($postID) > 0) {
                    foreach($postID as $p) {
                        $thisPost = new post($p);
                        echo $thisPost;
                    }
                }
                else {
                    mysqli_close($conn);
                    noPosts();
                }
            }
            else {
                mysqli_close($conn);
                noPosts();
            }
        }
        //Echo when profile has no posts
        private function noPosts() {
            echo "<div class='col-lg-12'>
                    <div class='panel panel-primary text-center' style='background-color: #729ae0; border-color: #4b85ef;'>
                        <div class='panel-body' style='width: 90vw;'>
                            <h4>No posts to load</h4>
                        </div>
                    </div>
                </div>";
        }
    }
?>