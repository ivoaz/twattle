<?php

require_once(__DIR__.'/../app/bootstrap.php');

use TweetEat\DependencyInjection\Container;
use TweetEat\Collector\MongoCollector;
use TweetEat\Streamline\FilterStreamline;

$container = new Container();

$username = $container['twitter.api_username'];
$password = $container['twitter.api_password'];

$database = $container->getDatabase();

$collector = new MongoCollector($database->getTweetCollection());

$keywords = $database->getProductCollection()->findTopicalKeywords();

if (empty($keywords)) {
    exit("The are no topical products at the time.\n");
}

$streamline = new FilterStreamline($username, $password, $collector);
$streamline->setTrack($keywords);
$streamline->consume();
