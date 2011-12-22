<?php

require_once(__DIR__.'/../app/bootstrap.php');

use Twattle\DependencyInjection\Container;

$container = new Container();

$database = $container->getDatabase();

$objectColl = $database->getObjectCollection();

$objects = $objectColl->findAll();

while ($objects->hasNext()) {
    $object = $objects->getNext();
    
    $totalTweets = @$object['total_tweets'];

    $positiveTweets = @$object['positive_tweets'];

    $negativeTweets = @$object['negative_tweets'];

    $neutralTweets = @$object['neutral_tweets'];

    if ($totalTweets > 0) {
        echo "\n";
        echo '    Object name: ', $object['name'], "\n";
        echo '       Keywords: ', implode(', ', $object['keywords']), "\n";
        echo '   Total tweets: ', $totalTweets, chr(9), "100.00%\n";
        echo 'Positive tweets: ', $positiveTweets, chr(9), sprintf('%03.2f', round($positiveTweets/$totalTweets*100, 2)), "%\n";
        echo 'Negative tweets: ', $negativeTweets, chr(9), sprintf('%03.2f', round($negativeTweets/$totalTweets*100, 2)), "%\n";
        echo ' Neutral tweets: ', $neutralTweets, chr(9), sprintf("%03.2f", round($neutralTweets/$totalTweets*100, 2)), "%\n";
        echo "\n";
    }
}
