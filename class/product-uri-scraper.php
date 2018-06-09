<?php
    /**
     * Product URI scraper class
     * Scrape product URIs
     */
    if(!defined("ABSPATH")) define( 'ABSPATH', dirname(dirname(__FILE__)) . '/' );
    require_once ABSPATH . "scripts/functions.php";
    use Goutte\Client;

    // Define constants

    /**
     *  Top-level domain for appending to relative links
     *  Example: https://beautybay.com
     */ 
    define("DOMAIN_URI","https://www.temptalia.com");

    /**
     *  Top-level domain name only
     *  Example: beautybay
     */ 
    define("DOMAIN_NAME","temptalia");

    /** 
     * Product URI matches
     * Example URI: beautybay.com/makeup/this-is-a-product
     * Example match: "/makeup/" 
     */
    define("PRODUCT_MATCH",[
        "/product/",
    ]);

    /**
     * Define scrapable URI matches
     * Example URI: domain.com/*.xml
     */
    define("SCRAPE_MATCH",[
        ".xml",
    ]);

    class productURIScraper {

        // Begin URI scrapping
        public function __construct($url) {
            try {
                // Validate URI
                $this->crawlURL($url);
            }
            catch(Exception $exc) {
                print $exc;
            }
        }

        // Scrape URI
        private function crawlURL($url) {

            // Set Instance Client
            $client = new Client();

            // Crawl URL
            try {
                error_log("Crawling link: $url");
                $crawler = $client->request('GET',$url);
                $this->saveCrawledLink($url);
            }
            catch(Exception $exc) {
                print $exc;
                error_log($exc);
            }

            // Save found links to array
            $output = $crawler->filterXPath('//default:loc')->each(function($node) {
                return $node->html();
            });

            // Unset vars, save resources
            unset($crawler);
            unset($client);

            // Check each URI
            if( count($output) > 0 ) {
                foreach($output as $uri) {
                    /**
                     * REMOVED: 
                     * If relative link, append domain
                     * if( strpos($uri,"/") == 0 && strpos($uri,"//") !== 0) {
                     *     $uri = DOMAIN_URI . $uri;
                     * }
                     * // If external link, break loop
                     * else if( (strpos($uri,"//") == 0) || (strpos($uri,DOMAIN_NAME) < 0) ) {
                     *     break;
                     * }
                     */

                    // If URI is a product URL, print & save URI
                    if( $this->isProductLink($uri) == false && $this->matchProductUri($uri) == true ) {
                        try {
                            $this->saveProductLink($uri);
                        }
                        catch(Exception $exc) {
                            print $exc;
                            error_log($exc);
                        }
                    }

                    // *IMPORTANT* If URI has been scraped, don't scrape again
                    // Else, if URI is a proper link or a relative link, scrape URIs
                    /**
                     * CHANGED: 
                     *  This has been removed inplace of an XML scraper
                     *
                     * if( !$this->isCrawledLink($uri) ) {
                     *    $this->crawlURL($uri);
                     * }
                     */
                    if( $this->isScrapableUri($uri) == true ) {
                        // Scrape matching link
                        $scrape = new productURIScraper($uri);
                        // Save resource
                        unset($scrape);
                    }
                }
                // Unset output, save resources
                unset($output);
            }
        }

        // Check if URI contains a product URI match
        private function matchProductUri($uri) {
            foreach(PRODUCT_MATCH as $match) {
                if(strpos($uri,$match) > 0) {
                    error_log("$uri is a product URI");
                    return true;
                }
            }
            error_log("$uri isn't a product URI");
            return false;
        }

        // Check link is scrapable
        private function isScrapableUri($uri) {
            if( $this->isCrawledLink($uri) == false ) {
                foreach(SCRAPE_MATCH as $match) {
                    if(strpos($uri,$match) > -1) {
                        return true;
                    }
                    return false;
                }
            }
            return false;
        }

        // Check if product link already saved
        private function isProductLink($url) {
            $conn = sqlConnect();
            $sql = "SELECT id
                    FROM product_links
                    WHERE link = '$url';
                    ";
            $result = mysqli_query($conn,$sql);
            if( mysqli_num_rows($result) > 0) {
                mysqli_close($conn);
                error_log("$url is already saved product link");
                return true;
            }
            mysqli_close($conn);
            return false;
        }

        // Save product link
        private function saveProductLink($url) {
            $conn = sqlConnect();
            $sql = "INSERT INTO product_links(link)
                    VALUES ('$url');
                    ";
            if(!mysqli_query($conn,$sql)) {
                mysqli_close($conn);
                throw new Exception("Couldn't save product link: $url.");
            };
            mysqli_close($conn);
        }

        // Check if link already crawled
        private function isCrawledLink($url) {
            $conn = sqlConnect();
            $sql = "SELECT id
                    FROM crawled_links
                    WHERE link = '$url';
                    ";
            $result = mysqli_query($conn,$sql);
            if( mysqli_num_rows($result) > 0) {
                mysqli_close($conn);
                error_log("$url has been crawled already");
                return true;
            }
            mysqli_close($conn);
            return false;
        }

        // Save link as crawled
        private function saveCrawledLink($url) {
            $conn = sqlConnect();
            $sql = "INSERT INTO crawled_links(link)
                    VALUES ('$url');
                    ";
            if(!mysqli_query($conn,$sql)) {
                mysqli_close($conn);
                throw new Exception("Couldn't save crawled link: $url.");
            };
            mysqli_close($conn);
        }

        // Return scraped links as array
        public static function scrapedURIList($limit,$offset) { 
            $conn = sqlConnect();
            $sql = "SELECT link
                    FROM crawled_links
                    LIMIT $limit
                    OFFSET $offset;";
            $result = mysqli_query($conn,$sql);
            mysqli_close($conn);
            while($row = mysqli_fetch_assoc($result)) {
                $links[] = $row['link'];
            }
            return $links;
        }

        // Return product links as array
        public static function productURIList($limit,$offset) { 
            $conn = sqlConnect();
            $sql = "SELECT link
                    FROM product_links
                    LIMIT $limit
                    OFFSET $offset;";
            $result = mysqli_query($conn,$sql);
            mysqli_close($conn);
            while($row = mysqli_fetch_assoc($result)) {
                $links[] = $row['link'];
            }
            return $links;
        }

        // Clear link tables
        public static function clearTables() {
            // Log object destruction
            error_log("Clearing scraped URI link tables");
            // SQL connect and truncate any link tables used
            $conn = sqlConnect();
            $sql = "TRUNCATE TABLE crawled_links;
                    ";
            // Execute SQL
            mysqli_query($conn,$sql);
            $sql = "TRUNCATE TABLE product_links;
                    ";
            // Execute SQL and close connection
            mysqli_query($conn,$sql);
            mysqli_close($conn);
        }
    }
?>