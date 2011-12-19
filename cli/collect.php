<?php

require_once(__DIR__.'/../app/bootstrap.php');

use TweetEat\DependencyInjection\Container;

$container = new Container();

$database = $container->getDatabase();

$keywords = $database->getObjectCollection()->findTopicalKeywords();

if (empty($keywords)) {
    exit("The are no topical objects right now.\n");
}

$streamline = $container->getStreamline();
$streamline->setTrack($keywords);
$streamline->consume();
