<?php

require_once(__DIR__.'/../app/bootstrap.php');

use TweetEat\DependencyInjection\Container;

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
                ),
            ));
        }
    }
    else {
        // tweet could not be processed, remove it
        $tweetCollection->remove($tweet);
    }
}
