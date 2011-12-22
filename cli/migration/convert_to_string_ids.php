<?php

require_once(__DIR__.'/../../app/bootstrap.php');

use Twattle\DependencyInjection\Container;

$container = new Container();

$database = $container->getDatabase();

// find tweets with float id
$tweets = $database->getTweetCollection()->collection->find(array(
    '_id' => array(
        '$type' => 1
    )
));

// WARNING, removes old document and inserts new one with string id
foreach ($tweets as $tweet) {
    $database->getTweetCollection()->remove($tweet['_id']);
    $tweet['_id'] = isset($tweet['original']['id_str']) ? $tweet['original']['id_str'] : (string)$tweet['original']['id'];
    $database->getTweetCollection()->insert($tweet);
}
