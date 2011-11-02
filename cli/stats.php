<?php

require_once(__DIR__.'/../app/bootstrap.php');

use TweetEat\DependencyInjection\Container;

$container = new Container();

$database = $container->getDatabase();

$objectColl = $database->getObjectCollection();
$tweetColl = $database->getTweetCollection();

$objects = $objectColl->findAll();

while ($objects->hasNext()) {
    $object = $objects->getNext();
    
    $totalTweets = $tweetColl->collection->count(array(
        'objects._id' => $object['_id'],
    ));

    $positiveTweets = $tweetColl->collection->count(array(
        'objects._id' => $object['_id'],
        'objects.sentiment.rating' => array(
            '$gt' => 0,
        ),
    ));

    $negativeTweets = $tweetColl->collection->count(array(
        'objects._id' => $object['_id'],
        'objects.sentiment.rating' => array(
            '$lt' => 0,
        ),
    ));

    $neutralTweets = $tweetColl->collection->count(array(
        'objects._id' => $object['_id'],
        'objects.sentiment.rating' => 0,
    ));

    echo "\n";
    echo '    Object name: ', $object['name'], "\n";
    echo '       Keywords: ', implode(', ', $object['keywords']), "\n";
    echo '   Total tweets: ', $totalTweets, chr(9), "100.00%\n";
    echo 'Positive tweets: ', $positiveTweets, chr(9), sprintf('%03.2f', round($positiveTweets/$totalTweets*100, 2)), "%\n";
    echo 'Negative tweets: ', $negativeTweets, chr(9), sprintf('%03.2f', round($negativeTweets/$totalTweets*100, 2)), "%\n";
    echo ' Neutral tweets: ', $neutralTweets, chr(9), sprintf("%03.2f", round($neutralTweets/$totalTweets*100, 2)), "%\n";
    echo "\n";
}
