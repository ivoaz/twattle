<?php

require_once(__DIR__.'/../app/bootstrap.php');

use TweetEat\DependencyInjection\Container;

$container = new Container();

$database = $container->getDatabase();

$productColl = $database->getProductCollection();
$tweetColl = $database->getTweetCollection();

$products = $productColl->findAll();

foreach ($products as $product) {
    $totalTweets = $tweetColl->collection->count(array(
        'objects._id' => $product['_id'],
        'objects.type' => 'product',
    ));

    $positiveTweets = $tweetColl->collection->count(array(
        'objects._id' => $product['_id'],
        'objects.type' => 'product',
        'objects.sentiment.rating' => array(
            '$gt' => 0,
        ),
    ));

    $negativeTweets = $tweetColl->collection->count(array(
        'objects._id' => $product['_id'],
        'objects.type' => 'product',
        'objects.sentiment.rating' => array(
            '$lt' => 0,
        ),
    ));

    $neutralTweets = $tweetColl->collection->count(array(
        'objects._id' => $product['_id'],
        'objects.type' => 'product',
        'objects.sentiment.rating' => 0,
    ));

    echo "\n";
    echo '   Product name: ', $product['name'], "\n";
    echo '       Keywords: ', implode(', ', $product['keywords']), "\n";
    echo '   Total tweets: ', $totalTweets, chr(9), "100.00%\n";
    echo 'Positive tweets: ', $positiveTweets, chr(9), sprintf('%03.2f', round($positiveTweets/$totalTweets*100, 2)), "%\n";
    echo 'Negative tweets: ', $negativeTweets, chr(9), sprintf('%03.2f', round($negativeTweets/$totalTweets*100, 2)), "%\n";
    echo ' Neutral tweets: ', $neutralTweets, chr(9), sprintf("%03.2f", round($neutralTweets/$totalTweets*100, 2)), "%\n";
    echo "\n";
}

$tweets = $database->getTweetCollection()->collection->find(array(
    'objects.sentiment.value' => array('$gt' => 0)
), array('original.text' => true));

foreach ($tweets as $tweet) {
    echo $tweet['original']['text'] . "\n\n";
}
