<?php

require_once(__DIR__.'/../app/bootstrap.php');

use TweetEat\DependencyInjection\Container;
use TweetEat\Analyser\SentimentAnalyser;

$container = new Container();

$database = $container->getDatabase();

$tweetColl = $database->getTweetCollection();
$lexiconColl = $database->getLexiconCollection();

$lexicon = iterator_to_array($lexiconColl->findByLanguage('en'));

$analyser = new SentimentAnalyser($lexicon);

do {
    $tweets = $tweetColl->findWithUndeterminedSentiment();

    foreach ($tweets as $tweet) {
        foreach ($tweet['objects'] as $object) {
            $object['sentiment'] = $analyser->analyse($tweet['original']['text']);

            if (count($object['sentiment']['ngrams'])) {
                echo $object['sentiment']['rating'], ' ', $tweet['original']['text'], "\n\n";
            }

            $tweetColl->updateSentiment($tweet['_id'], $object['_id'], $object['sentiment']);
        }
    }

    sleep(1);
} while (true);
