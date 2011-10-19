<?php

require_once(__DIR__.'/../app/bootstrap.php');

use TweetEat\DependencyInjection\Container;
use TweetEat\Analyser\MoodAnalyser;

$container = new Container();

$database = $container->getDatabase();

$collection = $database->getTweetCollection();

$analyser = new MoodAnalyser($container['sys.root_dir'].'/data/analyser/mood_keywords.json');

do {
    $tweets = $collection->findWithoutMood();

    foreach ($tweets as $tweet) {
        foreach ($tweet['subjects'] as $subject) {
            $subject['mood'] = array(
                'value' => $analyser->analyseMood($tweet['original']['text'], 'en'),
            );

            $collection->updateMood($tweet['_id'], $subject['_id'], $subject['mood']['value']);
        }
    }

    sleep(1);
} while (true);
