<?php

require_once(__DIR__.'/../app/bootstrap.php');

use TweetEat\DependencyInjection\Container;
use TweetEat\Analyser\PercentSentimentAnalyser as SentimentAnalyser;

$container = new Container();

$database = $container->getDatabase();

$collection = $database->getTweetCollection();

$analyser = new SentimentAnalyser($container['sys.root_dir'].'/data/analyser/sentiment_keywords.json');

do {
    $tweets = $collection->findWithUndeterminedSentiment();

    foreach ($tweets as $tweet) {
        foreach ($tweet['objects'] as $object) {
            $object['sentiment'] = array(
                'value' => $analyser->analyse($tweet['original']['text'], 'en'),
            );

            $collection->updateSentiment($tweet['_id'], $object['_id'], $object['sentiment']);
        }
    }

    sleep(1);
} while (true);
