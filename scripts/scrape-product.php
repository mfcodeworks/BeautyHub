<?php
    // Get dependencies
    require_once "functions.php";
    use Goutte\Client;

    // Init. client
    $client = new Client();

    // Set test search
    $search = $_GET['search'];
    $productID = $_GET['id'];

    // Do search
    $searchURI = str_replace(" ","+",$search);
    $crawler = $client->request('GET', 'http://www.google.com/search?q='.$searchURI);

    // Crawl response
    $output = $crawler->filter('.r > a')->each(function($node) {
        $uri = explode('&',$node->attr('href'));
        $uri = explode('=',$uri[0]);
        $uri = $uri[1];
        $links[$node->text()] = $uri;
        return $links;
    });

    // Arrange array of links
    foreach($output as $l) {
        foreach($l as $n => $d)
            $links[ $n ] = $d;
    }

    // Check links for known sites
    foreach($links as $name => $uri) {

        // If link is Sephora
        if( strpos($uri,"sephora") != false && !isset($imageURI)) {

            // Init. new client
            $client = new Client();
            $crawler = $client->request('GET', "$uri");

            // Crawl response
            $imagelink = $crawler->filter('svg.css-8a9gku > image')->each(function($node) {
                $imagelink = $node->attr('xlink:href');
                return $imagelink;
            });

            // If correct link, save link
            foreach($imagelink as $i) {
                if( strpos($i,"main") != false ) $imageURI = parse_url($uri)['host'].$i;
                else break;
            }

            // If has description, save description
            $description = $crawler->filter('div.css-1e532l3')->each(function($node) {
                return $node->text();
            });

            // If no image, save alert for product
            if(!isset($imageURI)) {
                $imageURI = "NULL";
                $log = "Couldn't scrape image link for $search, #$productID.<br>";
            }

            // Check description exists
            if(isset($description)) {

                // Set description
                $description = $description[0];
                // Format decription
                formatSephoraDescrption($description,"What it is:");
                formatSephoraDescrption($description,"What it does:");
                formatSephoraDescrption($description,"What else you need to know:");
            }
            // If description not found, save alert for product
            else {
                $description = "NULL";
                $log = "Couldn't scrape description for $search, #$productID.<br>";
            }
            // If anything not found, email alert for product
            if(isset($log)) mailMessage("<html><body>$log</body></html>","[IMPORTANT] Error Scraping Product");
        }
    }

    // Save info
    $conn = sqlConnect();
    $sql = "UPDATE products
            SET img=\"$imageURI\" description=\"$description\"
            WHERE ID=$productID;";
    if(mysqli_query($conn,$sql)) {
        echo "Product scraped successfully\n";
        mysqli_close($conn);
    }
    else {
        echo "Scrape failed\n";
        mysqli_close($conn);
    }
?>
