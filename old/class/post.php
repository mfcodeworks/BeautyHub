<?php
    /**
     * Post class
     * Create new post
     * 
     */
    if(!defined("ABSPATH")) define( 'ABSPATH', dirname(dirname(__FILE__)) . '/' );
    require_once ABSPATH . "scripts/functions.php";

    class post {
        /**
         * Properties
         */
        private $id;
        private $author;
        private $content;
        private $likes;
        private $media;
        private $album;
        private $datetime;

        //Create/get post
        public function __construct($id = NULL) {
            if($id == NULL) {
                $this->author = $_SESSION['user']->getID();
            }
            else {
                try {
                    $this->getPost($id);
                }
                catch(Exception $exc) {
                    throw $exc;
                }
            }
        }

        private function getPost($id) {
            $conn = sqlConnect();
            $sql = "SELECT *
                    FROM posts
                    WHERE ID = $id;";
            $result = mysqli_query($conn,$sql);
            if($result) {
                $this->id = $id;
                while($row = mysqli_fetch_assoc($result)) {
                    $this->author = $row['author'];
                    $this->content = $row['content'];
                    $this->likes = json_decode($row['likes']);
                    $this->media = json_decode($row['media']);
                    $this->album = $row['album'];
                    $this->datetime = $row['datetime'];
                }
            }
            else {
                throw new Exception("Post does not exist");
            }
        }

        /**
         * {get;set;}
         */
        public function author() {
            return $this->author;
        }

        public function datetime() {
            return $this->datetime;
        }

        public function content($content = NULL) {
            if($content != NULL) {
                $this->content = $content;
            }
            else {
                return $this->content;
            }
        }
        
        public function likes($likes = NULL) {
            if($likes != NULL) {
                if($this->likes != NULL) {
                    array_push($this->likes,$likes);
                }
                else {
                    $this->likes = array($likes);
                }
            }
            else {
                return $this->likes;
            }
        }

        public function media($media = NULL) {
            if($media != NULL) {
                $this->media = $media;
            }
            else {
                return $this->media;
            }
        }

        public function album($album = NULL) {
            if($album != NULL) {
                $this->album = $album;
            }
            else {
                return $this->album;
            }
        }

        /**
         * Unique functions
         */

         public function removeLike($like) {
             if($this->likes != NULL) {
                if(($key = array_search($like,$this->likes)) !== false) {
                    unset($this->likes[$key]);
                }
             }
             else {
                 throw new Exception("Post has no likes");
             }
         }

        /**
         * Save post to DB
         */

        public function save() {
            $this->author = $_SESSION['user']->getID();
            //Connect to DB
            $conn = sqlConnect();
            //Build SQL
            $sql = "INSERT INTO posts(";
            $values = "VALUES(";
            //Build with switch for each class property
            foreach($this as $key => $value) {
                if($value != NULL) {
                    switch($key) {
                        case "likes":
                        case "media":
                        case "comments":
                            $value = json_encode($value);
                            $sql .= "$key,";
                            $values .= "'$value',";
                            break;
                        case "content":
                            $sql .= "$key,";
                            $values .= "\"$value\",";
                            break;
                        default:
                            $sql .= "$key,";
                            $values .= "'$value',";
                            break;
                    }
                }
            }
            $sql = trim($sql,',') . ") " . trim($values,',') . ");";
            if(mysqli_query($conn,$sql)) {
                mysqli_close($conn);
                return true;
            }
            else {
                mysqli_close($conn);
                throw new Exception("Could not save post.\nSQL:\n$sql");
            }
        }

        //Post to string
        public function __toString() {
            $user = $_SESSION['user'];
            $author = new profile($this->author);
            $string = "
            <div class='following-post' name='".$this->id."'>
                <div class='panel panel-default'>
                    <div class='row'>
                        <div class='col-lg-2 col-xs-2'>
                            <p>
                                <a href='profile.php?id=".$author->ID()."'>
                                    <img src='".$author->ProfileImg()."' class='img-responsive img-circle' alt='' style='border: 0.1em solid #585757; margin-top: 1.6em; margin-left: 1em;'>
                                </a>
                            </p>
                        </div>
                        <div class='col-lg-10 col-xs-10'>
                            <p>
                                <h4 style='margin-bottom: 0.1em;'><a href='profile.php?id=".$author->ID()."'>".$author->Username()."</a></h4>";
            if($this->author == $user->getID()) {
                $string.= "<span style='float: right; margin-top: -2rem; margin-right: 2rem;'><a href='javascript:void(0)' onclick='deletePost(".$this->id.")'>Delete<i class='fa fa-times'></i></a></span>";
            }
            $string .= formatDateTime($this->datetime)."
                            </p>
                        </div>
                    </div>";
                    if(isset($this->content)) {
                        $string .= 
                        "<div class='row'>
                            <div class='col-lg-12'>
                                <p style='padding: 0.5rem; padding-left: 3rem;'>".$this->content."</p>
                            </div>
                        </div>";
                    }
            if($this->media != NULL) {
                $string .= "<div class='row' style='padding: 1em;'>";
                switch( count($this->media) ) {
                    case 1:
                        $m = $this->media[0];
                        $string .= "
                                <div class='col-xs-12 col-lg-12 postImg'>
                                    <div class='thumbnail'>
                                        <img class='myImg' src='$m' name='$m'>
                                    </div>
                                </div>
                        ";
                        break;
                    case 2:
                        foreach($this->media as $m) {
                            $string .= "
                                    <div class='col-xs-12 col-lg-6 postImg'>
                                        <div class='thumbnail'>
                                            <img class='myImg' src='$m' name='$m'>
                                        </div>
                                    </div>
                            ";
                        }
                        break;
                    default:
                        foreach($this->media as $m) {
                            $string .= "
                                    <div class='col-xs-12 col-lg-6 postImg'>
                                        <div class='thumbnail'>
                                            <div class='myImg img-preview' style='background-image: url($m);' name='$m'></div>
                                        </div>
                                    </div>
                            ";
                        }
                        break;
                }
                $string .= "</div>";
            }
            $string .= "<hr style='margin-left: 1rem; margin-right: 1rem; border: 0.5px solid #ccc;'>";
            //TODO: Favourite function
            //FIXME: Fix comments
            $string .= "<div class='row post-comments-section' style='margin: 0rem;'>";
            $string .= getPostComments($this->id);
            $string .= loadPostCommentForm($this->id);
            $string .= "</div>
                    </div>
                </div>
            ";
            return $string;
        }
    }

?>