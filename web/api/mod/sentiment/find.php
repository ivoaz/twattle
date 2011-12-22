<?php

require_once(__DIR__.'../../../../../app/bootstrap.php');

use Twattle\DependencyInjection\Container;

$container = new Container();

$database = $container->getDatabase();
$tweetColl = $database->getTweetCollection();

// create criteria
$criteria = array(
    'moderation.sentiment' => array(
        '$exists' => false,
    ),
);

// get cursor
$cursor = $tweetColl->collection->find($criteria, array(
    '_id' => true,
    'original_text' => true,
));

// sort results
$cursor->sort(array(
    '_id' => 1,
));

// limit results
$limit = isset($_GET['limit']) ? $_GET['limit'] : 10;
$cursor->limit($limit);

// paginate results
if (isset($_GET['page'])) {
    $skip = $limit*($_GET['page']-1);
    if ($skip < 0) $skip = 0;
    $cursor->skip($skip);
}

// prepare for json
$tweets = iterator_to_array($cursor);

// return json
echo json_encode($tweets);
