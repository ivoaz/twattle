<?php

require_once(__DIR__.'/../app/bootstrap.php');

use TweetEat\DependencyInjection\Container;

$container = new Container();

$database = $container->getDatabase();

$lexiconColl = $database->getLexiconCollection();

$positiveWords = file_get_contents($container['sys.root_dir'].'/data/analyser/sentiment/positive-words.txt');
$negativeWords = file_get_contents($container['sys.root_dir'].'/data/analyser/sentiment/negative-words.txt');

foreach (explode("\n", $positiveWords) as $word) {
    $ngram = array(
        'ngram' => $word,
        'rate' => 1,
        'language' => 'en'
    );

    $lexiconColl->collection->update(array(
        'ngram' => $word
    ), $ngram, array('upsert' => true));
}

foreach (explode("\n", $negativeWords) as $word) {
    $ngram = array(
        'ngram' => $word,
        'rate' => -1,
        'language' => 'en'
    );

    $lexiconColl->collection->update(array(
        'ngram' => $word
    ), $ngram, array('upsert' => true));
}
