<?php

if (($lockFile = fopen(__DIR__.'/process.lock', 'w')) === false) {
    exit("Failed to open lock file.\n");
}

if (flock($lockFile, LOCK_EX|LOCK_NB, $wouldBlock) === false || $wouldBlock) {
	exit("Failed to lock.\n");
}

require_once(__DIR__.'/../app/bootstrap.php');

use Twattle\DependencyInjection\Container;

$container = new Container();

$tweetCollection = $container->getDatabase()->getTweetCollection();
$objectCollection = $container->getDatabase()->getObjectCollection();
$processor = $container->getProcessor();

$tweets = $tweetCollection->findForProcessing()->limit($container['processor.max_tweets']);

foreach ($tweets as $tweet) {
    if ($processor->process($tweet)) {
        // update tweet
        $tweetCollection->update($tweet);

        // update stats
        foreach ($tweet['objects'] as $id) {
            if (0 == $tweet['sentiment']['rating']) {
                $sentiment = 'neutral';
            }
            else if ($tweet['sentiment']['rating'] > 0) {
                $sentiment = 'positive';
            }
            else {
                $sentiment = 'negative';
            }

            $objectCollection->collection->update(array(
                '_id' => $id,
            ), array(
                '$inc' => array(
                    'total_tweets' => 1,
                    $sentiment.'_tweets' => 1,
                    'naive_bayesian.positive' => $tweet['sentiment']['naive_bayesian']['positive'],
                    'naive_bayesian.negative' => $tweet['sentiment']['naive_bayesian']['negative'],
                ),
            ));
        }
    }
    else {
        // tweet could not be processed, remove it
        $tweetCollection->remove($tweet);
    }
}

flock($lockFile, LOCK_UN);
fclose($lockFile);
