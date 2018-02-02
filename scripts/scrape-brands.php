<?php
require_once "functions.php";
use Goutte\Client;

$client = new Client();

$crawler = $client->request('GET', 'http://www.totalbeauty.com/reviews/brands-a-z');

echo "[<br>";
$crawler->filter('.brand > a')->each(function ($node) {
    echo '&nbsp;&nbsp;&nbsp;&nbsp;{"name": "' .$node->text(). '"},<br>';
});
echo "]";
?>
