<?php

require_once(__DIR__.'../../../../../app/bootstrap.php');

use TweetEat\DependencyInjection\Container;

$container = new Container();

$database = $container->getDatabase();
$tweetColl = $database->getTweetCollection();

// create criteria
$criteria = array(
    'moderation.sentiment' => array(
        '$exists' => true,
        '$ne' => 0,
    ),
);

// get cursor
$cursor = $tweetColl->collection->find($criteria, array(
    '_id' => true,
    'original.text' => true,
));

// prepare for json
$tweets = iterator_to_array($cursor);

// return json
echo json_encode($tweets);
