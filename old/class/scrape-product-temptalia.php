<?php
    /** 
     * temptalia.com product scraper class
     * 
     */
    if(!defined("ABSPATH")) define( 'ABSPATH', dirname(dirname(__FILE__)) . '/' );
    require_once ABSPATH . "scripts/functions.php";
    use Goutte\Client;

    class temptaliaURIScraper {

        // Properties
        private $brand;
        private $name;
        private $type;
        private $img;
        private $description;
        private $shade;
        private $shadeImg;

        // { get; }
        public function brand() {
            return $this->brand;
        }
        public function name() {
            return $this->name;
        }
        public function type() {
            return $this->type;
        }
        public function img() {
            return $this->img;
        }
        public function description() {
            return $this->description;
        }
        public function shade() {
            return $this->shade;
        }
        public function shadeImg() {
            return $this->shadeImg;
        }

        public function __construct($url) {

            // Run product scraper
            $productURI = explode("/product/",$url)[1];
            try {
                if( substr_count($productURI,"/") > 1 ) {
                    $this->scrapeProductShade($url);
                }
                else{
                    $this->scrapeProduct($url);
                }
            }
            // Catch errors
            catch(Exception $exc) {
                throw $exc;
            }
        }

        private function scrapeProduct($uri) {
            // Set instance client
            $client = new Client();

            // Scrape URI
            try {
                error_log("Scraping $uri");
                $crawler = $client->request('GET',$uri);
            }
            catch(Exception $exc) {
                throw $exc;
            }

            // Try scraping info, if exception thrown continue
            try {
                $this->brand = $crawler->filter('h3.light > span.text-gray')->text();
            }
            catch(Exception $exc){
            }
            try {
                $this->name = $crawler->filter('h1.product-title > a')->text();
            }
            catch(Exception $exc){
            }
            try {
                $this->description = $crawler->filter('div.content > p')->last()->text();
            }
            catch(Exception $exc){
            }
            try {
                $this->img = $crawler->filter('div.mb-5 > img')->attr('src');
            }
            catch(Exception $exc){
            }
            try {
                $this->type = $crawler->filter('li.breadcrumb-item > a')->last()->text();
            }
            catch(Exception $exc){
            }

            // Try saving product
            try {
                $this->saveProduct();
            }
            catch(Exception $exc) {
                throw $exc;
            }
        }

        private function scrapeProductShade($uri) {
            // Set instance client
            $client = new Client();

            // Scrape URI
            try {
                error_log("Scraping $uri for shade");
                $crawler = $client->request('GET',$uri);
            }
            catch(Exception $exc) {
                throw $exc;
            }

            // Try scraping info, if exception thrown continue
            try {
                 $this->brand = $crawler->filter('h3.light > span.text-gray')->text();
            }
            catch(Exception $exc) {
            }
            try {
                 $this->name = $crawler->filter('h3.light > span.thin')->text();
            }
            catch(Exception $exc) {
            }
            try {
                 $this->description = $crawler->filter('div.content > p')->last()->text();
            }
            catch(Exception $exc) {
            }
            try {
                 $this->shade = $crawler->filter('h1.product-title > a')->text();
            }
            catch(Exception $exc) {
            }
            try {
                 $this->shadeImg = $crawler->filter('div.mb-5 > img')->attr('src');
            }
            catch(Exception $exc) {
            }
            try {
                 $this->type = $crawler->filter('li.breadcrumb-item > a')->last()->text();
            }
            catch(Exception $exc) {
            }

            // Try saving product
            try {
                $this->saveProductShade();
            }
            catch(Exception $exc) {
                throw $exc;
            }
        }

        private function saveProductShade() {
            // Set vars
            $brand = $this->brand;
            $name = $this->name;
            $shade = $this->shade;
            $shadeImg = $this->shadeImg;
            $type = $this->type;
            $description = $this->description;

            // Get product shade
            $conn = sqlConnect();
            $sql = "SELECT ID,shade_img
                    FROM products
                    WHERE name = \"$name\"
                    AND brand = \"$brand\";";
            $result = mysqli_query($conn,$sql);

            // If product exists
            if(mysqli_num_rows($result) > 0) {
                // Get shade image array
                while($row = mysqli_fetch_assoc($result)) {
                    $id = $row["ID"];
                    $shadeImgArray = json_decode($row["shade_img"]);
                }
                // Add new shade
                $shadeImgArray[] = [
                    "shade" => $shade,
                    "img" => $shadeImg
                ];
                // Save array
                $shadeImgArray = json_encode($shadeImgArray);
                $sql = "UPDATE products
                        SET shade_img = '$shadeImgArray'
                        WHERE ID = $id;";
                if(!mysqli_query($conn,$sql)) {
                    error_log("Couldn't update product: [$id] $brand $name");
                }
            }
            else {
                // Add shade
                $shadeImgArray[] = [
                    "shade" => $shade,
                    "img" => $shadeImg
                ];
                // Save array
                $shadeImgArray = json_encode($shadeImgArray);

                $sql = "INSERT INTO products(brand,name,product_type,shade_img,description)
                        VALUES(\"$brand\",\"$name\",\"$type\",'$shadeImgArray',\"$description\");";
                if(!mysqli_query($conn,$sql)) {
                    error_log("Couldn't add product $brand $name");
                }
                
            }
        }

        private function saveProduct() {
            // Set vars
            $brand = $this->brand;
            $name = $this->name;
            $description = $this->description;
            $img = $this->img;
            $type = $this->type;

            // If product doesn't exist, save product
            if($this->productExists() == false){
                $conn = sqlConnect();
                $sql = "INSERT INTO products(brand,name,description,img,product_type)
                        VALUES(\"$brand\",\"$name\",\"$description\",\"$img\",\"$type\");";
                if(!mysqli_query($conn,$sql)) {
                    mysqli_close($conn);
                    error_log("Couldn't save product $name");
                }
                mysqli_close($conn);
            }

        }

        private function productExists() {
            // Set vars
            $brand = $this->brand;
            $name = $this->name;

            $conn = sqlConnect();
            $sql = "SELECT ID
                    FROM products
                    WHERE name = \"$name\"
                    AND brand = \"$brand\";";
            $result = mysqli_query($conn,$sql);
            if(mysqli_num_rows($result) > 0) {
                mysqli_close($conn);
                return true;
            }
            mysqli_close($conn);
            return false;
        }
    }

?>