<?php
    // Get dependencies
    require_once "functions.php";
    use Goutte\Client;
    use ForceUTF8\Encoding;

    // Init. client
    $client = new Client();

    // Set test search
    $search = $_GET['search'];
    $search = str_replace(" ","+",$search);

    // Do search
    $crawler = $client->request('GET', 'http://www.google.com/search?q='.$search);

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
        if( strpos($uri,"sephora") != false && !isset($imageURI) && !isset($descriptionText)) {

            // Init. new client
            $client = new Client();
            $crawler = $client->request('GET', "$uri");

            // Crawl response
            $imagelink = $crawler->filter('svg.css-8a9gku > image')->each(function($node) {
                $imagelink = $node->attr('xlink:href');
                return $imagelink;
            });

            // If correct link, save link
            if(isset($imagelink) && $imagelink != NULL) {
                foreach($imagelink as $i) {
                    if( strpos($i,"main") != false ) $imageURI = parse_url($uri)['host'].$i;
                }
            }

            // If has description, save description
            $description = $crawler->filter('div.css-1e532l3')->each(function($node) {
                return $node->text();
            });

            // Check description exists
            if(isset($description) && $description != NULL) {

                // Set description
                $descriptionText = $description[0];
                // Format decription
                $descriptionText = formatSephoraDescription($descriptionText,"What it is:");
                $descriptionText = formatSephoraDescription($descriptionText,"What it does:");
                $descriptionText = formatSephoraDescription($descriptionText,"What else you need to know:");
                $descriptionText = str_replace("","-",$descriptionText);
            }
        }
    }

    // If description not found, save alert for product
    if(!isset($descriptionText) || $descriptionText == "") {
        $descriptionText = "NULL";
        $log = "Couldn't scrape description for $search.<br>";
    }

    // If no image, save alert for product
    if(!isset($imageURI) || $imageURI == "") {
        $imageURI = "NULL";
        $log = "Couldn't scrape image link for $search.<br>";
    }

    // If anything not found, email alert for product
    if(isset($log)) mailMessage("<html><body>$log</body></html>","[IMPORTANT] Error Scraping Product");

    // Return info
    echo json_encode($array = [
        "img" => "https://" . $imageURI,
        "description" => str_replace('"',"'",$descriptionText),
    ])
?>
