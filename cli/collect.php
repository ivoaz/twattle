<?php

use TweetEat\Database\Mongo as Database;
use TweetEat\Collector\MongoCollector;
use TweetEat\Streamline\FilterStreamline;

$username = $container['twitter_api_username'];
$password = $container['twitter_api_password'];

/* @var $database Database */
$database = $container['database'];

$collector = new MongoCollector($database->getTweetCollection());

$keywords = $database->getProductCollection()->findKeywords();

$streamline = new FilterStreamline($username, $password, $collector);
$streamline->setTrack($keywords);
$streamline->consume();
