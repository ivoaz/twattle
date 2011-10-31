<?php

require_once(__DIR__.'/../app/bootstrap.php');

use TweetEat\DependencyInjection\Container;

$container = new Container();

$database = $container->getDatabase();

$objects = iterator_to_array($database->getObjectCollection()->findAll(
    array('keywords' => true)
));

do {
    $tweets = $database->getTweetCollection()->findWithUndeterminedObject();

    foreach ($tweets as $tweet) {
        if (!isset($tweet['original']['text'])) {
            // tweet is invalid, delete it
            $database->getTweetCollection()->remove($tweet['_id']);
            continue;
        }

        foreach ($objects as $id => $object) {
            $matched = false;
            foreach ($object['keywords'] as $keyword) {
                if (stripos($tweet['original']['text'], $keyword) !== false) {
                    $matched = true;
                    break;
                }
            }

            if ($matched) {
                $match = array(
                    '_id' => $id,
                );

                $tweet['objects'][] = $match;

                $database->getTweetCollection()->addObject($tweet['_id'], $match);
            }
        }

        if (!isset($tweet['objects'])) {
            // object could not be determined, delete tweet
            $database->getTweetCollection()->remove($tweet['_id']);
        }
    }

    sleep(1);
} while (true);
