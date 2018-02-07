<?php
    if(!defined("ABSPATH")) define( 'ABSPATH', dirname(dirname(__FILE__)) . '/' );
    require_once ABSPATH . "scripts/functions.php";

    class product {
        private $id;
        private $brand;
        private $name;
        private $shades;
        private $type;
        private $img;
        private $rating;
        private $dupes;
        private $description;
        private $shade_img;
        private $price_site;
        private $view_count;

        public function __construct(int $id) {
            // Connect to DB
            $conn = sqlConnect();

            // Build & run SQL query
            $sql = "SELECT * 
                    FROM products 
                    WHERE ID = '$id';";
            $result = mysqli_query($conn,$sql);

            // If item exists, create. Otherwise, throw exception
            if($result) {
                // Set item ID and other data
                $this->id = $id;

                while($row = mysqli_fetch_assoc($result)) {
                    $this->brand = $row['brand'];
                    $this->name = $row['name'];
                    if(isset($row['shade']) && $row['shade'] != "") {
                        $this->shades = json_decode($row['shade']);
                    }
                    $this->type = $row['product_type'];
                    if(isset($row['img'])) {
                        $this->img = $row['img'];
                    }
                    $this->rating = $row['rating'];
                    if(isset($row['dupes']) && $row['dupes'] != "") {
                        $this->dupes = json_decode($row['dupes'],true);
                    }
                    $this->description = $row['description'];
                    if(isset($row['shade_img']) && $row['shade_img'] != "") {
                        $this->shade_img = json_decode($row['shade_img'],true);
                    }
                    if(isset($row['price_site']) && $row['price_site'] != "") {
                        $this->price_site = json_decode($row['price_site'],true);
                    }
                    $this->view_count = $row['view_count'];
                }
                //Sort arrays
                if(isset($this->shades)) sort($this->shades);
                if(isset($this->dupes)) asort($this->dupes);
                if(isset($this->shade_img)) asort($this->shade_img);


                // If no image is set, load a placeholder
                if(!isset($this->img) || $this->img == "" || $this->img == " ") $this->img = "http://via.placeholder.com/323x486";

                // If no description is set, load a coming soon banner
                if($this->description == null || $this->description == "" || $this->description == " ") $this->description = "<p><em>Description coming soon!</em></p>";

                // Close DB connection
                mysqli_close($conn);
            }

            // If no product was found
            else {
                mysqli_close($conn);
                throw new Exception("Product does not exist.");
            }
        }

        // get{} functions
        public function getID() { return $this->id; }
        public function getBrand() { return $this->brand; }
        public function getName() { return $this->name; }
        public function getShades() { return $this->shades; }
        public function getType() { return $this->type; }
        public function getImg() { return $this->img; }
        public function getRating() { return $this->rating; }
        public function getDupes() { return $this->dupes; }
        public function getDescription() { return $this->description; }
        public function getShadeImg() { return $this->shade_img; }
        public function getPriceSite() { return $this->price_site; }
        public function getViewCount() { return $this->view_count; }

        // ToString function
        public function __toString() {
            return $this->brand . " - " . $this->name;
        }
    }
?>
