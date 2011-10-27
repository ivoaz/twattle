<?php

require_once(__DIR__.'/../app/bootstrap.php');

use TweetEat\DependencyInjection\Container;
use TweetEat\Analyser\SentimentAnalyser;

$container = new Container();

$database = $container->getDatabase();

$collection = $database->getTweetCollection();

$lexicon = array();

$positiveWords = file_get_contents($container['sys.root_dir'].'/data/analyser/sentiment/positive-words.txt');
$negativeWords = file_get_contents($container['sys.root_dir'].'/data/analyser/sentiment/negative-words.txt');

foreach (explode("\n", $positiveWords) as $word) {
    $lexicon[] = array(
        'phrase' => $word,
        'rate' => 1
    );
}

foreach (explode("\n", $negativeWords) as $word) {
    $lexicon[] = array(
        'phrase' => $word,
        'rate' => -1
    );
}

$analyser = new SentimentAnalyser($lexicon, false);

do {
    $tweets = $collection->findWithUndeterminedSentiment();

    foreach ($tweets as $tweet) {
        foreach ($tweet['objects'] as $object) {
            $object['sentiment'] = $analyser->analyse($tweet['original']['text']);

            if ($object['sentiment']['rating'] != 0)
                echo $object['sentiment']['rating'], ' ', $tweet['original']['text'], "\n\n";

            $collection->updateSentiment($tweet['_id'], $object['_id'], $object['sentiment']);
        }
    }

    sleep(1);
} while (true);
