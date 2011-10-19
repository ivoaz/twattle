<?php

require_once(__DIR__.'/../app/bootstrap.php');

use TweetEat\DependencyInjection\Container;

$container = new Container();

$database = $container->getDatabase();

$collection = $database->getTweetCollection();

do {
    $tweets = $collection->findWithoutMood();

    foreach ($tweets as $tweet) {
        foreach ($tweet['subjects'] as $subject) {
            // set random mood
            $subject['mood'] = array(
                'value' => rand(-1, 1),
            );
            
            $collection->updateMood($tweet['_id'], $subject['_id'], $subject['mood']['value']);
        }
    }

    sleep(1);
} while (true);
