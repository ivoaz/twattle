<?php

require_once(__DIR__.'../../../../../app/bootstrap.php');

use Twattle\DependencyInjection\Container;

$container = new Container();

$database = $container->getDatabase();
$tweetColl = $database->getTweetCollection();

$tweet = $tweetColl->collection->findOne(array('_id' => $_POST['id']));
var_dump($tweet['_id']);

$tweetColl->collection->update(array(
    '_id' => $_POST['id'],
), array(
    '$set' => array(
        'moderation.sentiment' => (int)$_POST['rating'],
    ),
));

echo json_encode(array('success' => true));
