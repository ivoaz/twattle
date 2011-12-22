<?php

require_once(__DIR__.'/../app/bootstrap.php');

use Twattle\DependencyInjection\Container;

$container = new Container();

$database = $container->getDatabase();

$objectColl = $database->getObjectCollection();
$tweetColl = $database->getTweetCollection();

$objects = $objectColl->findAll();

while ($objects->hasNext()) {
    $object = $objects->getNext();
    
    $totalTweets = $tweetColl->collection->count(array(
        'objects' => $object['_id']
    ));

    $positiveTweets = $tweetColl->collection->count(array(
        'objects' => $object['_id'],
        'sentiment.rating' => array('$gt' => 0)
    ));

    $negativeTweets = $tweetColl->collection->count(array(
        'objects' => $object['_id'],
        'sentiment.rating' => array('$lt' => 0)
    ));

    $neutralTweets = $totalTweets - $negativeTweets - $positiveTweets;

    $objectColl->collection->update(array(
        '_id' => $object['_id'],
    ), array(
        '$set' => array(
            'total_tweets' => $totalTweets,
            'positive_tweets' => $positiveTweets,
            'negative_tweets' => $negativeTweets,
            'neutral_tweets' => $neutralTweets,
        )
    ));
}
