<?php

require_once(__DIR__.'/../app/bootstrap.php');

use TweetEat\DependencyInjection\Container;

$container = new Container();

$database = $container->getDatabase();

$products = iterator_to_array($database->getProductCollection()->findAll());

do {
    $tweets = $database->getTweetCollection()->findWithUndeterminedObject();

    foreach ($tweets as $tweet) {
        if (!isset($tweet['original']['text'])) {
            // tweet is invalid, delete it
            $database->getTweetCollection()->remove($tweet['_id']);
            continue;
        }

        foreach ($products as $product) {
            $matched = false;
            foreach ($product['keywords'] as $keyword) {
                if (stripos($tweet['original']['text'], $keyword) !== false) {
                    $matched = true;
                    break;
                }
            }

            if ($matched) {
                $object = array(
                    '_id' => $product['_id'],
                    'type' => 'product',
                );

                $tweet['objects'][] = $object;

                $database->getTweetCollection()->addObject($tweet['_id'], $object);
            }
        }

        if (!isset($tweet['objects'])) {
            // object could not be determined, delete tweet
            $database->getTweetCollection()->remove($tweet['_id']);
        }
    }

    sleep(1);
} while (true);
