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
            $sql = "SELECT * FROM products WHERE ID = '$id';";
            $result = mysqli_query($conn,$sql);

            // If item exists, create. Otherwise, throw exception
            if($result) {
                // Set item ID and other data
                $this->id = $id;

                while($row = mysqli_fetch_assoc($result)) {
                    $this->brand = $row['brand'];
                    $this->name = $row['name'];
                    if(isset($row['shade']) && $row['shade'] != "") $this->shades = $this->makeShadeList($row['shade']);
                    $this->type = $row['product_type'];
                    if(isset($row['img'])) $this->img = $row['img'];
                    $this->rating = $row['rating'];
                    if(isset($row['dupes']) && $row['dupes'] != "") $this->dupes = $this->makeDupeList($row['dupes']);
                    $this->description = $row['description'];
                    if(isset($row['shade_img']) && $row['shade_img'] != "") $this->shade_img = $this->makeShadeImgList($row['shade_img']);
                    if(isset($row['price_site']) && $row['price_site'] != "") $this->price_site = $this->makePriceSiteList($row['price_site']);
                    $this->view_count = $row['view_count'];
                }

                // If no image is set, load a placeholder
                if(!isset($this->img) || $this->img == "" || $this->img == " ") $this->img = "http://via.placeholder.com/323x486";

                // If no description is set, load a coming soon banner
                if($this->description == null || $this->description == "" || $this->description == " ") $this->description = "<p><em>Description coming soon!</em></p>";

                // Close DB connection
                mysqli_close($conn);
            }

            // If no product was found
            else {
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

        // Return shades array
        private function makeShadeList($shade_string) {
            $shade_list = explode(",",$shade_string);
            return sort($shade_list);
        }

        // Return associative dupes array
        private function makeDupeList($dupes_string) {
            // Explode string to array
            $dupes_single_array = explode(",",$dupes_string);
            // Set associative array size(For dupes, single array total/3)
            for($i = 0; $i < count($dupes_single_array); $i += 3) {
                $dupes_list[] = [
                    "thisShade" => $dupes_single_array[$i],
                    "dupeID" => $dupes_single_array[$i+1],
                    "dupeShade" => $dupes_single_array[$i+2]
                ];
            }
            // Return rganized array
            return $dupes_list;
        }

        // Return associative shade image array
        private function makeShadeImgList($shade_img_string) {
            // Explode string to array
            $shade_img_single_array = explode(",",$shade_img_string);
            // Set associative array
            for($i = 0; $i < count($shade_img_single_array); $i += 2) {
                $shade_img_list[] = [
                    "shade" => $shade_img_single_array[$i],
                    "img" => $shade_img_single_array[$i+1]
                ];
            }
            // Return array
            return asort($shade_img_list);
        }

        // Return associative price site array
        private function makePriceSiteList($price_site_string) {
            // Explode string to array
            $price_site_single_array = explode(",",$price_site_string);
            // Set associative array
            for($i = 0; $i < count($price_site_single_array); $i += 3) {
                $price_site_list[] = [
                    "price" => $price_site_single_array[$i],
                    "site" => $price_site_single_array[$i+1],
                    "siteName" => $price_site_single_array[$i+2]
                ];
            }
            // Return array
            return $price_site_list;
        }

        //Destroy
        public function __destroy() {
            if(isset($conn)) mysqli_close($conn);
        }

        // ToString function
        public function __toString() {
            return $this->brand . " " . $this->name;
        }
    }
?>
