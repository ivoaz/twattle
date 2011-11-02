<?php

require_once(__DIR__.'/../../app/bootstrap.php');

use TweetEat\DependencyInjection\Container;

$container = new Container();

$database = $container->getDatabase();

$tweetColl = $database->getTweetCollection();

$objectId = $_GET['object'];

$positive = $tweetColl->collection->count(array(
    'objects._id' => $objectId,
    'objects.sentiment.rating' => array(
        '$gt' => 0,
    ),
));

$negative = $tweetColl->collection->count(array(
    'objects._id' => $objectId,
    'objects.sentiment.rating' => array(
        '$lt' => 0,
    ),
));

$neutral = $tweetColl->collection->count(array(
    'objects._id' => $objectId,
    'objects.sentiment.rating' => 0,
));

echo json_encode(array(
    'positive' => $positive,
    'negative' => $negative,
    'neutral' => $neutral,
));
