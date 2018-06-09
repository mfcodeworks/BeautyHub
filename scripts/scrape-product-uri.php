<?php
    // Get dependencies
    require_once "functions.php";

    try {
        $start = date("H:i:s d/m/Y");
        $time1 = new DateTime($start);
        error_log("[$start] Scrape started ");
        productURIScraper::clearTables();
        for($i = 1; $i < 99; $i++) {
            $scrape = new productURIScraper("https://www.temptalia.com/product-sitemap$i.xml");
            unset($scrape);
        }
        $end = date("H:i:s d/m/Y");
        $time2 = new DateTime($end);
        error_log("[$end] Scrape ended ");
        $diff = $time1->diff($time2);
        error_log("Scrape took: $diff");
    }
    catch(Exception $exc) {
        error_log($exc);
        unset($scraper);
        die($exc);
    }

    // Print product URIs
    echo "Product URIs\n";
    $products[] = productURIScraper::productURIList();
    echo count($products);
    foreach($products as $product) {
        echo "\n" . $product . "\n";
    }
    echo "--------------------------------------------------------------------------";

    // Print scraped URIs
    echo "\nScraped URIs\n";
    $scraped[] = productURIScraper::scrapedURIList();
    echo count($scraped) . "\n\n";
    foreach($scraped as $URI) {
        echo "\n" . $URI . "\n";
    }

    unset($scraper);
?>