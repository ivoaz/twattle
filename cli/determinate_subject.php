<?php

require_once(__DIR__.'/../app/bootstrap.php');

use TweetEat\DependencyInjection\Container;

$container = new Container();

$database = $container->getDatabase();

$products = iterator_to_array($database->getProductCollection()->findAll());

do {
    $tweets = $database->getTweetCollection()->findWithoutSubject();

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
                $subject = array(
                    '_id' => $product['_id'],
                    'type' => 'product',
                );

                $tweet['subjects'][] = $subject;

                $database->getTweetCollection()->addSubject($tweet['_id'], $subject);
            }
        }

        if (!isset($tweet['subjects'])) {
            // subject could not be determinated, delete tweet
            $database->getTweetCollection()->remove($tweet['_id']);
        }
    }

    sleep(1);
} while (true);
